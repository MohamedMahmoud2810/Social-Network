<?php

namespace App\Application\Contracts;

use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post;

    /**
     * Create a new post.
     */
    public function create(array $data): Post;

    /**
     * Update a post.
     */
    public function update(Post $post, array $data): bool;

    /**
     * Delete a post.
     */
    public function delete(Post $post): bool;

    /**
     * Get all posts by a user.
     */
    public function getUserPosts(User $user, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get news feed for a user (posts from friends).
     */
    public function getNewsFeed(User $user, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts liked by a user.
     */
    public function getLikedPosts(User $user, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get trending posts.
     */
    public function getTrendingPosts(int $perPage = 15): LengthAwarePaginator;

    /**
     * Search posts by content.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}
