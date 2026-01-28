<?php

namespace App\Application\Contracts;

use App\Domain\Friendship\Models\Friendship;
use App\Domain\User\Models\User;

interface FriendshipRepositoryInterface
{
    /**
     * Find a friendship by ID.
     */
    public function findById(int $id): ?Friendship;

    /**
     * Find friendship between two users.
     */
    public function findBetweenUsers(User $user, User $friend): ?Friendship;

    /**
     * Create a friend request.
     */
    public function create(array $data): Friendship;

    /**
     * Update a friendship.
     */
    public function update(Friendship $friendship, array $data): bool;

    /**
     * Delete a friendship.
     */
    public function delete(Friendship $friendship): bool;

    /**
     * Accept a friend request.
     */
    public function accept(Friendship $friendship): bool;

    /**
     * Reject a friend request.
     */
    public function reject(Friendship $friendship): bool;

    /**
     * Check if users are friends.
     */
    public function areFriends(User $user, User $friend): bool;

    /**
     * Check if there's a pending request from user to friend.
     */
    public function hasPendingRequest(User $user, User $friend): bool;
}
