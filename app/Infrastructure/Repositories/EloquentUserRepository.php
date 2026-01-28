<?php

namespace App\Infrastructure\Repositories;

use App\Application\Contracts\UserRepositoryInterface;
use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Search users by name or email.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Get user's friends.
     */
    public function getFriends(User $user): Collection
    {
        return $user->friends;
    }

    /**
     * Get user's pending friend requests.
     */
    public function getPendingFriendRequests(User $user): Collection
    {
        return $user->pendingFriendRequests;
    }

    /**
     * Get friend requests waiting for user's response.
     */
    public function getFriendRequestsToAccept(User $user): Collection
    {
        return $user->friendRequestsToAccept;
    }

    /**
     * Get user suggestions (non-friends).
     */
    public function getSuggestions(User $user, int $limit = 10): Collection
    {
        $friendIds = $user->friends()->pluck('users.id')->toArray();
        $pendingRequestIds = $user->sentFriendRequests()
            ->where('status', 'pending')
            ->pluck('friend_id')
            ->toArray();

        $excludeIds = array_merge($friendIds, $pendingRequestIds, [$user->id]);

        return User::whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
