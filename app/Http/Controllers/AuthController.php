<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show the signup page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle the signup form
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create($data);   // the password is hashed automatically

        Auth::login($user);            // log them in straight away
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // Show the login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle the login form
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => 'These details do not match our records.'])
            ->onlyInput('email');
    }

    // Log out
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}