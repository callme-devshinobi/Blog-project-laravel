<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        // Normalise before validating so "HTMLCoder" becomes "htmlcoder"
        $request->merge(['subdomain' => strtolower(trim($request->subdomain ?? ''))]);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'terms'    => 'accepted',
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                'unique:blogs,subdomain',
                Rule::notIn([
                    'www', 'admin', 'api', 'app', 'mail', 'blog', 'blogs', 'dashboard',
                    'login', 'logout', 'register', 'signup', 'post', 'posts',
                    'support', 'help', 'about', 'contact', 'terms', 'privacy',
                ]),
            ],
        ]);

        // Create the user and their blog together; if either fails, neither is saved
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],   // hashed automatically
            ]);

            Blog::create([
                'user_id'   => $user->id,
                'subdomain' => $data['subdomain'],
                'title'     => $user->name . "'s Blog",
            ]);

            return $user;
        });

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