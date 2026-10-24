<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'home']);
Route::get('/posts', [BlogController::class, 'allPosts']);
Route::get('/categories', [BlogController::class, 'categoryIndex']);
Route::get('/show/{slug}', [BlogController::class, 'categoryShow']);
Route::get('/post/{id}', [BlogController::class, 'showPost']);

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});