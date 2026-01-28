<?php

namespace App\Application\Services;

use App\Application\Contracts\FriendshipRepositoryInterface;
use App\Domain\Friendship\Enums\FriendshipStatus;
use App\Domain\Friendship\Models\Friendship;
use App\Domain\User\Models\User;
use App\Presentation\Events\FriendRequestAccepted;
use App\Presentation\Events\FriendRequestSent;
use Illuminate\Support\Facades\DB;

class FriendshipService
{
    public function __construct(
        private readonly FriendshipRepositoryInterface $friendshipRepository
    ) {}

    /**
     * Send a friend request.
     *
     * @throws \Exception
     */
    public function sendFriendRequest(User $user, User $friend): Friendship
    {
        // Validation
        if ($user->id === $friend->id) {
            throw new \Exception('You cannot send a friend request to yourself.');
        }

        // Check if already friends
        if ($this->friendshipRepository->areFriends($user, $friend)) {
            throw new \Exception('You are already friends with this user.');
        }

        // Check if request already exists
        if ($this->friendshipRepository->hasPendingRequest($user, $friend)) {
            throw new \Exception('Friend request already sent.');
        }

        // Check if there's a pending request from the other user
        $existingRequest = $this->friendshipRepository->findBetweenUsers($user, $friend);

        if ($existingRequest && $existingRequest->user_id === $friend->id && $existingRequest->isPending()) {
            // If the other user already sent a request, accept it instead
            $this->acceptFriendRequest($existingRequest);

            return $existingRequest;
        }

        return DB::transaction(function () use ($user, $friend) {
            $friendship = $this->friendshipRepository->create([
                'user_id' => $user->id,
                'friend_id' => $friend->id,
                'status' => FriendshipStatus::PENDING,
            ]);

            // Dispatch event
            event(new FriendRequestSent($friendship));

            return $friendship;
        });
    }

    /**
     * Accept a friend request.
     *
     * @throws \Exception
     */
    public function acceptFriendRequest(Friendship $friendship): bool
    {
        if (! $friendship->isPending()) {
            throw new \Exception('This friend request is no longer pending.');
        }

        $result = $this->friendshipRepository->accept($friendship);

        if ($result) {
            // Dispatch event
            event(new FriendRequestAccepted($friendship));
        }

        return $result;
    }

    /**
     * Reject a friend request.
     *
     * @throws \Exception
     */
    public function rejectFriendRequest(Friendship $friendship): bool
    {
        if (! $friendship->isPending()) {
            throw new \Exception('This friend request is no longer pending.');
        }

        return $this->friendshipRepository->reject($friendship);
    }

    /**
     * Unfriend a user.
     */
    public function unfriend(User $user, User $friend): bool
    {
        $friendship = $this->friendshipRepository->findBetweenUsers($user, $friend);

        if (! $friendship) {
            throw new \Exception('Friendship not found.');
        }

        return $this->friendshipRepository->delete($friendship);
    }

    /**
     * Cancel a friend request.
     */
    public function cancelFriendRequest(Friendship $friendship): bool
    {
        if (! $friendship->isPending()) {
            throw new \Exception('This friend request cannot be cancelled.');
        }

        return $this->friendshipRepository->delete($friendship);
    }

    /**
     * Get friendship status between two users.
     */
    public function getFriendshipStatus(User $user, User $friend): ?string
    {
        if ($this->friendshipRepository->areFriends($user, $friend)) {
            return 'friends';
        }

        $friendship = $this->friendshipRepository->findBetweenUsers($user, $friend);

        if (! $friendship) {
            return 'none';
        }

        if ($friendship->isPending()) {
            return $friendship->user_id === $user->id ? 'pending_sent' : 'pending_received';
        }

        if ($friendship->isRejected()) {
            return 'rejected';
        }

        return null;
    }

    /**
     * Get mutual friends between two users.
     */
    public function getMutualFriends(User $user, User $otherUser)
    {
        $userFriends = $user->friends()->pluck('users.id');
        $otherUserFriends = $otherUser->friends()->pluck('users.id');

        $mutualFriendIds = $userFriends->intersect($otherUserFriends);

        return User::whereIn('id', $mutualFriendIds)->get();
    }
}
