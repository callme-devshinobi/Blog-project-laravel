<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\AiCredential;
use App\Models\ActivityLog;
use App\Services\UserAiProvider;

class SettingsController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
    
        return view('settings', [
            'blog'            => $user->blog,
            'publishedCount'  => $user->posts()->published()->count(),
            'draftCount'      => $user->posts()->where('status', 'draft')->count(),
            'isPro'           => $user->hasPro(),
            'subscription'    => $user->activeSubscription(),
            'credential'      => $user->aiCredential,
            'providers'       => config('devnotes.ai.providers'),
            'defaultProvider' => config('devnotes.ai.default_provider'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        // the password is hashed automatically (same as at signup)
        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'Password updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirm'  => ['required', Rule::in(['delete my account'])],
        ]);

        $user = $request->user();

        try {
            app(\App\Services\Paystack::class)->cancelAllFor($user);
        } catch (\Throwable $e) {
            logger()->warning('Could not cancel a subscription before deleting an account', ['user' => $user->id, 'error' => class_basename($e)]);
        
            return back()->withErrors(['password' => 'We couldn\'t cancel your paid subscription, so your account was not deleted. Cancel it from Billing and try again.']);
        }

        DB::transaction(function () use ($user) {
            // remove uploaded cover images (not image links)
            foreach ($user->posts as $post) {
                if ($post->featured_image && ! Str::startsWith($post->featured_image, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($post->featured_image);
                }
            }

            $user->posts()->delete();
            $user->blog?->delete();
            $user->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }

    public function saveDomain(Request $request)
{
    if (! $request->user()->hasPro()) {
        return back()->withErrors(['domain' => 'Custom domains are part of the Pro plan.']);
    }
    $blog = $request->user()->blog;
    abort_if(! $blog, 403);

    // clean up what they typed: "https://MyBlog.com/" -> "myblog.com"
    $domain = strtolower(trim($request->input('domain', '')));
    $domain = preg_replace('~^https?://~', '', $domain);
    $domain = rtrim(explode('/', $domain)[0], '.');
    $request->merge(['domain' => $domain]);

    $request->validate([
        'domain' => [
            'required',
            'max:253',
            'regex:/^(?=.{4,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/',
            Rule::unique('blogs', 'custom_domain')->ignore($blog->id),
            function ($attribute, $value, $fail) {
                foreach (config('devnotes.main_hosts') as $host) {
                    if ($value === $host || Str::endsWith($value, '.' . $host)) {
                        $fail('That domain can\'t be used.');
                    }
                }
            },
        ],
    ]);

    // a new or changed domain must be verified again
    if ($blog->custom_domain !== $domain) {
        $blog->forceFill([
            'custom_domain'             => $domain,
            'custom_domain_verified_at' => null,
            'domain_token'              => 'devnotes-verify-' . Str::random(32),
        ])->save();
    }

    return back()->with('success', 'Domain saved. Add the DNS records below, then click Verify.');
}

public function verifyDomain(Request $request)
{
    if (! $request->user()->hasPro()) {
        return back()->withErrors(['domain' => 'Custom domains are part of the Pro plan.']);
    }
    $blog = $request->user()->blog;
    abort_if(! $blog || ! $blog->custom_domain, 404);

    $records = @dns_get_record('_devnotes-verify.' . $blog->custom_domain, DNS_TXT) ?: [];
    $found   = collect($records)->pluck('txt')->contains($blog->domain_token);

    if (! $found) {
        return back()->withErrors(['domain' => 'We couldn\'t find the TXT record yet. DNS changes can take a while, so try again in a few minutes.']);
    }

    $blog->forceFill(['custom_domain_verified_at' => now()])->save();

    return back()->with('success', 'Domain verified! Visitors to ' . $blog->custom_domain . ' will now see your blog.');
}

public function removeDomain(Request $request)
{
    $blog = $request->user()->blog;
    abort_if(! $blog, 403);

    $blog->forceFill([
        'custom_domain'             => null,
        'custom_domain_verified_at' => null,
        'domain_token'              => null,
    ])->save();

    return back()->with('success', 'Custom domain removed.');
}
public function saveAiKey(Request $request)
{
    $user = $request->user();

    if (! $user->hasPro()) {
        return back()->withErrors(['api_key' => 'Connecting your own AI key is part of the Pro plan.']);
    }

    $data = $request->validate([
        'provider' => ['required', Rule::in(array_keys(config('devnotes.ai.providers')))],
        'api_key'  => ['required', 'string', 'min:20', 'max:300', 'regex:/^\S+$/'],
    ], ['api_key.regex' => 'The key can\'t contain spaces.']);

    $key = $data['api_key'];

    $credential = AiCredential::updateOrCreate(
        ['user_id' => $user->id],
        [
            'provider'        => $data['provider'],
            'api_key'         => $key,
            'last_four'       => substr($key, -4),
            'last_checked_at' => null,
            'last_check_ok'   => null,
        ]
    );

    // try the key once; it stays saved either way
    $user->setRelation('aiCredential', $credential);
    $ok = UserAiProvider::test($user);
    $credential->forceFill(['last_checked_at' => now(), 'last_check_ok' => $ok])->save();

    ActivityLog::record('ai.key_saved', $user->name . ' connected an AI key (' . $data['provider'] . ')', $user);

    if (! $ok) {
        return back()->withErrors(['api_key' => 'The key was saved, but a test request failed. Check that it belongs to the provider you chose and that billing is enabled with them.']);
    }

    return back()->with('success', 'Your AI key is connected and working.');
}

public function removeAiKey(Request $request)
{
    $user = $request->user();

    $user->aiCredential?->delete();

    ActivityLog::record('ai.key_removed', $user->name . ' removed their AI key', $user);

    return back()->with('success', 'Your AI key was removed.');
}
}