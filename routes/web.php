<?php

use App\Presentation\Http\Controllers\Web\AuthController;
use App\Presentation\Http\Controllers\Web\FriendshipController;
use App\Presentation\Http\Controllers\Web\HomeController;
use App\Presentation\Http\Controllers\Web\PostController;
use App\Presentation\Http\Controllers\Web\ProfileController;
use App\Presentation\Http\Controllers\Web\NotificationsController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [PostController::class, 'search'])->name('search');
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications');
    
    Route::get('/posts/trending', [PostController::class, 'trending'])->name('posts.trending');
    Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::get('/friends', [FriendshipController::class, 'index'])->name('friends.index');
    Route::get('/friends/requests', [FriendshipController::class, 'requests'])->name('friends.requests');
    Route::get('/friends/suggestions', [FriendshipController::class, 'suggestions'])->name('friends.suggestions');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});