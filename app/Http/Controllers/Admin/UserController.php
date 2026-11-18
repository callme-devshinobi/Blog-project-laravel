<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim(str_replace(['%', '_'], '', (string) $request->query('q', '')));
        $filter = $request->query('filter');

        $users = User::with('blog')
            ->withCount('posts')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            }))
            ->when($filter === 'admins', fn ($query) => $query->where('is_admin', true))
            ->when($filter === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'filter'));
    }

    public function show(User $user)
    {
        $user->load('blog');

        return view('admin.users.show', [
            'user'      => $user,
            'posts'     => $user->posts()->with('blog')->latest()->take(8)->get(),
            'postCount' => $user->posts()->count(),
            'logs'      => ActivityLog::where('user_id', $user->id)->latest('id')->take(10)->get(),
        ]);
    }

    public function suspend(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return $this->refuse('You can\'t suspend your own account.');
        }
        if ($user->is_admin) {
            return $this->refuse('Remove this person\'s admin rights before suspending them.');
        }

        $user->forceFill(['suspended_at' => now()])->save();
        ActivityLog::record('user.suspended', $user->name . ' was suspended', $user);
        $user->blog?->forceFill(['suspended_at' => now(), 'suspended_reason' => 'Account suspended'])->save();
        return back()->with('success', $user->name . ' is suspended and can no longer sign in.');
    }

    public function unsuspend(User $user)
    {
        $user->forceFill(['suspended_at' => null])->save();
        ActivityLog::record('user.unsuspended', $user->name . ' was reinstated', $user);
        if ($user->blog?->suspended_reason === 'Account suspended') {
            $user->blog->forceFill(['suspended_at' => null, 'suspended_reason' => null])->save();
        }
        return back()->with('success', $user->name . ' can sign in again.');
    }

    public function revokeAdmin(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return $this->refuse('You can\'t remove your own admin rights here. Use: php artisan admin:make ' . $user->email . ' --revoke');
        }
        if (! $user->is_admin) {
            return $this->refuse($user->name . ' is not an admin.');
        }

        $user->forceFill(['is_admin' => false])->save();
        ActivityLog::record('admin.revoked', 'Admin rights removed from ' . $user->name, $user);

        return back()->with('success', 'Admin rights removed from ' . $user->name . '.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return $this->refuse('You can\'t delete your own account from the admin area. Use Settings instead.');
        }
        if ($user->is_admin) {
            return $this->refuse('Remove this person\'s admin rights before deleting the account.');
        }

        $request->validate(
            [
                'password' => ['required', 'current_password'],
                'confirm'  => ['required', Rule::in([$user->email])],
            ],
            ['confirm.in' => 'Type the account\'s email address exactly to confirm.']
        );

        $name    = $user->name;
        $isLocal = fn ($path) => $path && ! Str::startsWith($path, ['http://', 'https://']);

        DB::transaction(function () use ($user, $isLocal) {
            foreach ($user->posts as $post) {
                if ($isLocal($post->featured_image)) {
                    Storage::disk('public')->delete($post->featured_image);
                }
            }
            if ($isLocal($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->posts()->delete();
            $user->blog?->delete();
            $user->delete();
        });

        ActivityLog::record('user.deleted', 'Account deleted by an admin: ' . $name);

        return redirect()->route('admin.users')->with('success', $name . '\'s account was deleted.');
    }

    private function refuse(string $message)
    {
        return back()->withErrors(['action' => $message]);
    }
}