<?php

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\PostRepositoryInterface;
use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentPostRepository implements PostRepositoryInterface
{
    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post
    {
        return Post::with(['user', 'comments.user', 'likes.user'])->find($id);
    }

    /**
     * Create a new post.
     */
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * Update a post.
     */
    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    /**
     * Delete a post.
     */
    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }

    /**
     * Get all posts by a user.
     */
    public function getUserPosts(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Post::where('user_id', $user->id)
            ->with(['user', 'comments.user', 'likes.user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get news feed for a user (posts from friends).
     */
    public function getNewsFeed(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->newsFeed()->paginate($perPage);
    }

    /**
     * Get posts liked by a user.
     */
    public function getLikedPosts(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Post::whereHas('likes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['user', 'comments.user', 'likes.user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get trending posts (most liked in last 7 days).
     */
    public function getTrendingPosts(int $perPage = 15): LengthAwarePaginator
    {
        return Post::withCount('likes')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('likes_count')
            ->with(['user', 'comments.user', 'likes.user'])
            ->paginate($perPage);
    }

    /**
     * Search posts by content.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Post::where('content', 'like', "%{$query}%")
            ->with(['user', 'comments.user', 'likes.user'])
            ->latest()
            ->paginate($perPage);
    }
}
