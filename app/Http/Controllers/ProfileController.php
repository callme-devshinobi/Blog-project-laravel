<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $blog = $user->blog;
        abort_if(! $blog, 403, 'You need a blog first.');

        return view('profile', compact('user', 'blog'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $blog = $user->blog;
        abort_if(! $blog, 403, 'You need a blog first.');

        // letters, numbers and . - _ / @ only, with an optional http(s):// start.
        // This blocks things like "javascript:..." in link fields.
        $link = 'regex:/^(https?:\/\/)?[\w.\-\/@]+$/';

        $data = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:255'],
            'blog_title'    => ['nullable', 'string', 'max:60'],
            'avatar'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'bio'           => ['nullable', 'string', 'max:300'],
            'tech_stack'    => ['nullable', 'array', 'max:15'],
            'tech_stack.*'  => ['string', 'max:30'],
            'write_about'   => ['nullable', 'string', 'max:1000'],
            'why_write'     => ['nullable', 'string', 'max:1000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-\s]+$/'],
            'github'        => ['nullable', 'string', 'max:255', $link],
            'linkedin'      => ['nullable', 'string', 'max:255', $link],
            'twitter'       => ['nullable', 'string', 'max:255', $link],
        ]);

        // ---- Photo ----
        $isLocal = fn ($path) => $path && ! Str::startsWith($path, ['http://', 'https://']);

        if ($request->hasFile('avatar')) {
            if ($isLocal($user->avatar)) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($isLocal($user->avatar)) Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        }

        // ---- Name (and display_name, unless it was customised) ----
        $oldName = $user->name;

        $user->name = $data['name'];

        if (blank($user->display_name) || $user->display_name === $oldName) {
            $user->display_name = $data['name'];
        }

        $user->save();

        // ---- Blog name ----
        // empty            -> "[Full name]'s Blog"
        // still the old default and the name changed -> follow the new name
        $oldDefault = $oldName . "'s Blog";
        $newDefault = $data['name'] . "'s Blog";
        $title      = trim($data['blog_title'] ?? '');

        if ($title === '' || ($title === $oldDefault && $oldName !== $data['name'])) {
            $title = $newDefault;
        }

        // ---- Tech stack: trim, drop blanks and duplicates ----
        $stack = collect($data['tech_stack'] ?? [])
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique(fn ($t) => Str::lower($t))
            ->values()
            ->all();

        // ---- Links: turn "github.com/me" into "https://github.com/me" ----
        $toUrl = fn ($v) => $v ? (preg_match('~^https?://~i', $v) ? $v : 'https://' . $v) : null;

        $twitter = $data['twitter'] ?? null;
        if ($twitter && ! preg_match('~^https?://~i', $twitter)) {
            $handle  = ltrim(preg_replace('~^(www\.)?(x|twitter)\.com/~i', '', $twitter), '@');
            $twitter = 'https://x.com/' . $handle;
        }

        // forceFill skips $fillable, so a missing entry there can't silently drop a field
        $blog->forceFill([
            'title'         => $title,
            'bio'           => $data['bio'] ?? null,
            'tech_stack'    => $stack,
            'write_about'   => $data['write_about'] ?? null,
            'why_write'     => $data['why_write'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'github'        => $toUrl($data['github'] ?? null),
            'linkedin'      => $toUrl($data['linkedin'] ?? null),
            'twitter'       => $twitter,
        ])->save();

        return redirect()->route('profile')->with('success', 'Profile saved.');
    }
}