<?php

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\FriendshipRepositoryInterface;
use App\Domain\Friendship\Enums\FriendshipStatus;
use App\Domain\Friendship\Models\Friendship;
use App\Domain\User\Models\User;

class EloquentFriendshipRepository implements FriendshipRepositoryInterface
{
    /**
     * Find a friendship by ID.
     */
    public function findById(int $id): ?Friendship
    {
        return Friendship::with(['user', 'friend'])->find($id);
    }

    /**
     * Find friendship between two users.
     */
    public function findBetweenUsers(User $user, User $friend): ?Friendship
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)
                ->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)
                ->where('friend_id', $user->id);
        })->first();
    }

    /**
     * Create a friend request.
     */
    public function create(array $data): Friendship
    {
        return Friendship::create($data);
    }

    /**
     * Update a friendship.
     */
    public function update(Friendship $friendship, array $data): bool
    {
        return $friendship->update($data);
    }

    /**
     * Delete a friendship.
     */
    public function delete(Friendship $friendship): bool
    {
        return (bool) $friendship->delete();
    }

    /**
     * Accept a friend request.
     */
    public function accept(Friendship $friendship): bool
    {
        return $friendship->accept();
    }

    /**
     * Reject a friend request.
     */
    public function reject(Friendship $friendship): bool
    {
        return $friendship->reject();
    }

    /**
     * Check if users are friends.
     */
    public function areFriends(User $user, User $friend): bool
    {
        return Friendship::where(function ($query) use ($user, $friend) {
            $query->where('user_id', $user->id)
                ->where('friend_id', $friend->id);
        })->orWhere(function ($query) use ($user, $friend) {
            $query->where('user_id', $friend->id)
                ->where('friend_id', $user->id);
        })->where('status', FriendshipStatus::ACCEPTED)->exists();
    }

    /**
     * Check if there's a pending request from user to friend.
     */
    public function hasPendingRequest(User $user, User $friend): bool
    {
        return Friendship::where('user_id', $user->id)
            ->where('friend_id', $friend->id)
            ->where('status', FriendshipStatus::PENDING)
            ->exists();
    }
}
