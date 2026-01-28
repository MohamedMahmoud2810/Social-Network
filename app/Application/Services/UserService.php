<?php

namespace App\Application\Services;

use App\Application\Contracts\UserRepositoryInterface;
use App\Application\DTOs\UpdateProfileDTO;
use App\Domain\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get a user by ID.
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(User $user, UpdateProfileDTO $dto): User
    {
        $data = $dto->toArray();

        \Log::info('Updating user profile', [
            'user_id' => $user->id,
            'data' => $data,
        ]);

        // Handle profile picture upload
        if ($dto->profilePicture instanceof UploadedFile) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $dto->profilePicture->store('profile-pictures', 'public');
            $data['profile_picture'] = $path;
        }

        // Handle profile picture removal
        if ($dto->removeProfilePicture && $user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $data['profile_picture'] = null;
        }

        $result = $this->userRepository->update($user, $data);
        
        \Log::info('User profile update result', [
            'user_id' => $user->id,
            'result' => $result,
        ]);

        return $user->fresh();
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->update($user, [
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Search for users.
     */
    public function searchUsers(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->search($query, $perPage);
    }

    /**
     * Get user's friends.
     */
    public function getUserFriends(User $user): Collection
    {
        return $this->userRepository->getFriends($user);
    }

    /**
     * Get pending friend requests sent by user.
     */
    public function getPendingFriendRequests(User $user): Collection
    {
        return $this->userRepository->getPendingFriendRequests($user);
    }

    /**
     * Get friend requests waiting for user's response.
     */
    public function getFriendRequestsToAccept(User $user): Collection
    {
        return $this->userRepository->getFriendRequestsToAccept($user);
    }

    /**
     * Get friend suggestions for user.
     */
    public function getFriendSuggestions(User $user, int $limit = 10): Collection
    {
        return $this->userRepository->getSuggestions($user, $limit);
    }

    /**
     * Delete user account.
     */
    public function deleteAccount(User $user): bool
    {
        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        return $this->userRepository->delete($user);
    }
}
