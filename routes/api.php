<?php

use App\Presentation\Http\Controllers\Api\V1\AuthController;
use App\Presentation\Http\Controllers\Api\V1\CommentController;
use App\Presentation\Http\Controllers\Api\V1\FriendshipController;
use App\Presentation\Http\Controllers\Api\V1\LikeController;
use App\Presentation\Http\Controllers\Api\V1\PostController;
use App\Presentation\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Profile
    Route::get('/users/search', [ProfileController::class, 'search']);
    Route::get('/users/suggestions', [ProfileController::class, 'suggestions']);
    Route::get('/users/{userId}', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

    // Posts
    Route::get('/posts', [PostController::class, 'index']); // News feed
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/trending', [PostController::class, 'trending']);
    Route::get('/posts/search', [PostController::class, 'search']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::get('/users/{userId}/posts', [PostController::class, 'userPosts']);

    // Comments
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('/comments/{commentId}', [CommentController::class, 'destroy']);

    // Likes
    Route::get('/posts/{postId}/likes', [LikeController::class, 'index']);
    Route::post('/posts/{postId}/like', [LikeController::class, 'store']);
    Route::delete('/posts/{postId}/unlike', [LikeController::class, 'destroy']);

    // Friendships
    Route::get('/friends', [FriendshipController::class, 'index']);
    Route::get('/friends/pending', [FriendshipController::class, 'pending']);
    Route::get('/friends/requests', [FriendshipController::class, 'requests']);
    Route::post('/friends/request', [FriendshipController::class, 'store']);
    Route::post('/friends/{friendshipId}/accept', [FriendshipController::class, 'accept']);
    Route::post('/friends/{friendshipId}/reject', [FriendshipController::class, 'reject']);
    Route::delete('/friends/{userId}', [FriendshipController::class, 'destroy']);
    Route::get('/friends/status/{userId}', [FriendshipController::class, 'status']);
    Route::get('/friends/mutual/{userId}', [FriendshipController::class, 'mutualFriends']);
});
