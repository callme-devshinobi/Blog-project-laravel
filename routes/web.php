<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostAssistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBlogController;
use App\Http\Controllers\SettingsController;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ---------- Main site ----------
Route::get('/', [BlogController::class, 'home']);
Route::get('/posts', [BlogController::class, 'allPosts']);
Route::get('/categories', [BlogController::class, 'categoryIndex']);
Route::get('/show/{slug}', [BlogController::class, 'categoryShow']);
Route::get('/post/{id}', [BlogController::class, 'showPost']);
Route::get('/search', [BlogController::class, 'search'])->name('search');

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

// ---------- Blogs directory + individual blogs (public) ----------
Route::get('/blogs', [PublicBlogController::class, 'index'])->name('blogs.index');

Route::prefix('@{blog:subdomain}')->group(function () {
    Route::get('/',            [PublicBlogController::class, 'show'])->name('blog.show');
    Route::get('/posts',       [PublicBlogController::class, 'allPosts'])->name('blog.posts');
    Route::get('/categories',  [PublicBlogController::class, 'categoryIndex'])->name('blog.categories');
    Route::get('/show/{slug}', [PublicBlogController::class, 'categoryShow'])->name('blog.category');
    Route::get('/post/{slug}', [PublicBlogController::class, 'post'])->name('blog.post');
    Route::get('/about',       [PublicBlogController::class, 'about'])->name('blog.about');
    Route::get('/contact',     [PublicBlogController::class, 'contact'])->name('blog.contact');
    Route::get('/search',      [PublicBlogController::class, 'search'])->name('blog.search');
});

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- Dashboard (logged-in users only) ----------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('dashboard');
    Route::get('/posts/create', [BlogController::class, 'create'])->name('posts.create');
    Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
    Route::get('/posts/drafts', [BlogController::class, 'drafts'])->name('posts.drafts');
    Route::post('/posts/{id}/publish', [BlogController::class, 'publish'])->name('posts.publish');
    Route::get('/posts/published', [BlogController::class, 'published'])->name('posts.published');
    Route::delete('/posts/{id}', [BlogController::class, 'destroy'])->name('posts.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::delete('/settings/account', [SettingsController::class, 'destroy'])->name('settings.destroy');
    Route::put('/settings/domain', [SettingsController::class, 'saveDomain'])->name('settings.domain');
    Route::post('/settings/domain/verify', [SettingsController::class, 'verifyDomain'])->name('settings.domain.verify');
    Route::delete('/settings/domain', [SettingsController::class, 'removeDomain'])->name('settings.domain.destroy');
    Route::put('/settings/ai', [SettingsController::class, 'saveAiKey'])->middleware('throttle:6,1')->name('settings.ai');
    Route::delete('/settings/ai', [SettingsController::class, 'removeAiKey'])->name('settings.ai.destroy');

    Route::post('/posts/assist', [PostAssistController::class, 'suggest'])
        ->middleware('throttle:10,1')      // 10 requests per minute per user
        ->name('posts.assist');
    Route::post('/posts/generate', [PostAssistController::class, 'generate'])
        ->middleware('throttle:5,1')
        ->name('posts.generate');
});

// ---------- Admin area ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::post('/users/{user}/revoke-admin', [AdminUserController::class, 'revokeAdmin'])->name('users.revoke');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('blogs');
    Route::get('/blogs/{blog}', [AdminBlogController::class, 'show'])->name('blogs.show');
    Route::put('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('blogs.update');
    Route::post('/blogs/{blog}/suspend', [AdminBlogController::class, 'suspend'])->name('blogs.suspend');
    Route::post('/blogs/{blog}/unsuspend', [AdminBlogController::class, 'unsuspend'])->name('blogs.unsuspend');
    Route::delete('/blogs/{blog}/domain', [AdminBlogController::class, 'removeDomain'])->name('blogs.domain.destroy');

    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts');
    Route::get('/posts/{post}', [AdminPostController::class, 'show'])->name('posts.show');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::post('/posts/{post}/unpublish', [AdminPostController::class, 'unpublish'])->name('posts.unpublish');
    Route::post('/posts/{post}/restore', [AdminPostController::class, 'restore'])->name('posts.restore');
    Route::post('/posts/{post}/clear-flag', [AdminPostController::class, 'clearFlag'])->name('posts.clear');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('/subscriptions/grant', [AdminSubscriptionController::class, 'grant'])->name('subscriptions.grant');
    Route::get('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/extend', [AdminSubscriptionController::class, 'extend'])->name('subscriptions.extend');
    Route::post('/subscriptions/{subscription}/expire', [AdminSubscriptionController::class, 'expire'])->name('subscriptions.expire');
 
    
});

// ---------- Used by the web server to decide which domains may get HTTPS certificates ----------
Route::get('/internal/domain-check', function (Request $request) {
    abort_unless($request->ip() === '127.0.0.1', 404);

    $domain = strtolower($request->query('domain', ''));

    $blog = Blog::with('user')
        ->where('custom_domain', $domain)
        ->whereNotNull('custom_domain_verified_at')
        ->whereNull('suspended_at')
        ->first();

    $ok = in_array($domain, config('devnotes.main_hosts'), true)
        || ($blog && $blog->user?->hasPro());

    abort_unless($ok, 404);

    return response('ok');
});