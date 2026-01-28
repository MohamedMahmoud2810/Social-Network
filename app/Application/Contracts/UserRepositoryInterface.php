<?php

namespace App\Application\Contracts;

use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update a user.
     */
    public function update(User $user, array $data): bool;

    /**
     * Delete a user.
     */
    public function delete(User $user): bool;

    /**
     * Search users by name or email.
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get user's friends.
     */
    public function getFriends(User $user): Collection;

    /**
     * Get user's pending friend requests.
     */
    public function getPendingFriendRequests(User $user): Collection;

    /**
     * Get friend requests waiting for user's response.
     */
    public function getFriendRequestsToAccept(User $user): Collection;

    /**
     * Get user suggestions (non-friends).
     */
    public function getSuggestions(User $user, int $limit = 10): Collection;
}
