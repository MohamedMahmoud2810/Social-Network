<?php

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Application\Services\PostService;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     *     path="/api/v1/posts/{postId}/like",
     *     tags={"Likes"},
     *     security={{"apiAuth":{}}},
     *     summary="Like post",

     *         name="postId",
     *         in="path",
     *         required=true,

     *     ),

     * )
     */
    public function store(Request $request, int $postId): JsonResponse
    {
        $post = $this->postService->getPostById($postId);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $like = $this->postService->likePost($post, $request->user());

        if (! $like) {
            return response()->json([
                'message' => 'You have already liked this post',
            ], 422);
        }

        return response()->json([
            'message' => 'Post liked successfully',
            'data' => [
                'likes_count' => $post->fresh()->likes()->count(),
            ],
        ], 201);
    }

    /**
     *     path="/api/v1/posts/{postId}/unlike",
     *     tags={"Likes"},
     *     security={{"apiAuth":{}}},
     *     summary="Unlike post",

     *         name="postId",
     *         in="path",
     *         required=true,

     *     ),

     * )
     */
    public function destroy(Request $request, int $postId): JsonResponse
    {
        $post = $this->postService->getPostById($postId);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $this->postService->unlikePost($post, $request->user());

        return response()->json([
            'message' => 'Post unliked successfully',
            'data' => [
                'likes_count' => $post->fresh()->likes()->count(),
            ],
        ]);
    }

    /**
     * Get users who liked a post.
     */
    public function index(int $postId): JsonResponse
    {
        $post = $this->postService->getPostById($postId);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $likes = $this->postService->getPostLikes($post);

        return response()->json([
            'data' => UserResource::collection($likes->pluck('user')),
            'meta' => [
                'total' => $likes->count(),
            ],
        ]);
    }
}
