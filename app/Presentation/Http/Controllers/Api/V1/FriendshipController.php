<?php

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Application\Services\FriendshipService;
use App\Application\Services\UserService;
use App\Domain\Friendship\Models\Friendship;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\FriendshipResource;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function __construct(
        private readonly FriendshipService $friendshipService,
        private readonly UserService $userService
    ) {}

    /**
     *     path="/api/v1/friends",
     *     tags={"Friendship"},
     *     security={{"apiAuth":{}}},
     *     summary="Get authenticated user's friends list",

     * )
     */
    public function index(Request $request): JsonResponse
    {
        $friends = $this->userService->getUserFriends($request->user());

        return response()->json([
            'data' => UserResource::collection($friends),
            'meta' => [
                'total' => $friends->count(),
            ],
        ]);
    }

    /**
     *     path="/api/v1/friends/pending",
     *     tags={"Friendship"},
     *     security={{"apiAuth":{}}},
     *     summary="Get pending friend requests sent by user",

     * )
     */
    public function pending(Request $request): JsonResponse
    {
        $pendingRequests = $this->userService->getPendingFriendRequests($request->user());

        return response()->json([
            'data' => UserResource::collection($pendingRequests),
            'meta' => [
                'total' => $pendingRequests->count(),
            ],
        ]);
    }

    /**
     *     path="/api/v1/friends/requests",
     *     tags={"Friendship"},
     *     security={{"apiAuth":{}}},
     *     summary="Get friend requests to accept",

     * )
     */
    public function requests(Request $request): JsonResponse
    {
        $friendships = Friendship::where('friend_id', $request->user()->id)
            ->where('status', 'pending')
            ->with('user')
            ->get()
            ->filter(function ($friendship) use ($request) {
                // Only return if the current user is the receiver and user_id is the sender
                return $friendship->user_id !== $request->user()->id;
            });

        return response()->json([
            'data' => $friendships->map(function ($friendship) {
                return [
                    'friendship_id' => $friendship->id,
                    'id' => $friendship->user_id,
                    'name' => $friendship->user->name,
                    'email' => $friendship->user->email,
                    'profile_picture' => $friendship->user->profile_picture,
                    'bio' => $friendship->user->bio,
                    'created_at' => $friendship->user->created_at,
                    'updated_at' => $friendship->user->updated_at,
                ];
            })->values(),
            'meta' => [
                'total' => $friendships->count(),
            ],
        ]);
    }

    /**
     *     path="/api/v1/friends",
     *     tags={"Friendship"},
     *     security={{"apiAuth":{}}},
     *     summary="Send friend request",


     *             required={"friend_id"},

     *         )
     *     ),

     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'friend_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $friend = $this->userService->getUserById($validated['friend_id']);

        if (! $friend) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        try {
            $friendship = $this->friendshipService->sendFriendRequest(
                $request->user(),
                $friend
            );

            return response()->json([
                'message' => 'Friend request sent successfully',
                'data' => new FriendshipResource($friendship),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     *     path="/api/v1/friends/{friendshipId}/accept",
     *     tags={"Friendship"},
     *     security={{"apiAuth":{}}},
     *     summary="Accept a friend request",


     * )
     */
    public function accept(Request $request, int $friendshipId): JsonResponse
    {
        $friendship = Friendship::find($friendshipId);

        if (! $friendship) {
            return response()->json([
                'message' => 'Friend request not found',
            ], 404);
        }

        // Check if status is pending
        if ($friendship->status !== 'pending') {
            return response()->json([
                'message' => 'This friend request is no longer pending.',
            ], 422);
        }

        // Authorization - only the receiver can accept
        if ($friendship->friend_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $this->friendshipService->acceptFriendRequest($friendship);

            return response()->json([
                'message' => 'Friend request accepted',
                'data' => new FriendshipResource($friendship->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject a friend request.
     */
    public function reject(Request $request, int $friendshipId): JsonResponse
    {
        $friendship = Friendship::find($friendshipId);

        if (! $friendship) {
            return response()->json([
                'message' => 'Friend request not found',
            ], 404);
        }

        // Authorization - only the receiver can reject
        if ($friendship->friend_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $this->friendshipService->rejectFriendRequest($friendship);

            return response()->json([
                'message' => 'Friend request rejected',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Unfriend a user.
     */
    public function destroy(Request $request, int $userId): JsonResponse
    {
        $friend = $this->userService->getUserById($userId);

        if (! $friend) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        try {
            $this->friendshipService->unfriend($request->user(), $friend);

            return response()->json([
                'message' => 'Successfully unfriended',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get friendship status with another user.
     */
    public function status(Request $request, int $userId): JsonResponse
    {
        $user = $this->userService->getUserById($userId);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $status = $this->friendshipService->getFriendshipStatus(
            $request->user(),
            $user
        );

        return response()->json([
            'data' => [
                'status' => $status,
            ],
        ]);
    }

    /**
     * Get mutual friends.
     */
    public function mutualFriends(Request $request, int $userId): JsonResponse
    {
        $user = $this->userService->getUserById($userId);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $mutualFriends = $this->friendshipService->getMutualFriends(
            $request->user(),
            $user
        );

        return response()->json([
            'data' => UserResource::collection($mutualFriends),
            'meta' => [
                'total' => $mutualFriends->count(),
            ],
        ]);
    }
}
