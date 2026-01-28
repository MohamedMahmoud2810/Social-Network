<?php

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Application\DTOs\UpdateProfileDTO;
use App\Application\Services\UserService;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     *     path="/api/v1/users/{userId}",
     *     tags={"Profile"},
     *     summary="Get a user's profile details",


     * )
     */
    public function show(Request $request, string $userId): JsonResponse
    {
        $user = $this->userService->getUserById((int) $userId);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }

    /**
     *     path="/api/v1/profile",
     *     tags={"Profile"},
     *     summary="Update authenticated user's profile",
     *     security={{"apiAuth":{}}},


     *             mediaType="multipart/form-data",




     *             )
     *         )
     *     ),

     * )
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'bio' => ['sometimes', 'string', 'max:500'],
            'profile_picture' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:204800'],
            'remove_profile_picture' => ['sometimes', 'boolean'],
        ]);

        \Log::info('Profile update validated data', ['validated' => $validated]);

        $dto = UpdateProfileDTO::fromArray($validated);
        
        \Log::info('Profile update DTO', [
            'name' => $dto->name,
            'email' => $dto->email,
            'bio' => $dto->bio,
            'profilePicture' => $dto->profilePicture ? 'file' : null,
        ]);
        
        $user = $this->userService->updateProfile($request->user(), $dto);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $this->userService->changePassword($user, $validated['new_password']);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Get friend suggestions.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $suggestions = $this->userService->getFriendSuggestions(
            $request->user(),
            $request->input('limit', 10)
        );

        return response()->json([
            'data' => UserResource::collection($suggestions),
        ]);
    }

    /**
     * Search users.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $users = $this->userService->searchUsers(
            $request->input('q'),
            $request->input('per_page', 15)
        );

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }
}
