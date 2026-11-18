<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->suspended_at) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'This account has been suspended. Please contact support.']);
        }

        return $next($request);
    }
}