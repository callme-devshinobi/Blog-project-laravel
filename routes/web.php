<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', [BlogController::class, 'home']);
Route::get('/posts', [BlogController::class, 'allPosts']);
Route::get('/categories', [BlogController::class, 'categoryIndex']);
Route::get('/show/{slug}', [BlogController::class, 'categoryShow']);
Route::get('/post/{id}', [BlogController::class, 'showPost']);


Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts/create', [BlogController::class, 'create'])->name('posts.create');
    Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
    Route::get('/posts/drafts', [BlogController::class, 'drafts'])->name('posts.drafts');
    Route::post('/posts/{id}/publish', [BlogController::class, 'publish'])->name('posts.publish');
    Route::get('/posts/published', [BlogController::class, 'published'])->name('posts.published');
    Route::delete('/posts/{id}', [BlogController::class, 'destroy'])->name('posts.destroy');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});