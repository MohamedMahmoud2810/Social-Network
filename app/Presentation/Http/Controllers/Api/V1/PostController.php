<?php

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Application\DTOs\CreatePostDTO;
use App\Application\DTOs\UpdatePostDTO;
use App\Application\Services\PostService;
use App\Domain\User\Models\User;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Get news feed.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $posts = $this->postService->getNewsFeed($request->user(), $perPage);

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     *     path="/api/v1/posts",
     *     tags={"Posts"},
     *     security={{"apiAuth":{}}},
     *     summary="Create post",


     *             mediaType="multipart/form-data",

     *                 required={"content"},


     *             )
     *         )
     *     ),

     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $dto = CreatePostDTO::fromArray([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'image' => $request->file('image'),
        ]);

        $post = $this->postService->createPost($dto);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => new PostResource($post->load(['user', 'comments.user', 'likes.user'])),
        ], 201);
    }

    /**
     *     path="/api/v1/posts/{id}",
     *     tags={"Posts"},
     *     summary="Get single post",


     * )
     */
    public function show(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'data' => new PostResource($post),
        ]);
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        // Authorization
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:51200'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $dto = UpdatePostDTO::fromArray([
            'content' => $validated['content'],
            'image' => $request->file('image'),
            'remove_image' => $validated['remove_image'] ?? false,
        ]);

        $post = $this->postService->updatePost($post, $dto);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($post->load(['user', 'comments.user', 'likes.user'])),
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        // Authorization
        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->postService->deletePost($post);

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }

    /**
     * Get posts by a specific user.
     */
    public function userPosts(Request $request, int $userId): JsonResponse
    {
        $user = User::find($userId);

        if (! $user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $perPage = $request->input('per_page', 15);
        $posts = $this->postService->getUserPosts($user, $perPage);

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Get trending posts.
     */
    public function trending(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $posts = $this->postService->getTrendingPosts($perPage);

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Search posts.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $posts = $this->postService->searchPosts(
            $request->input('q'),
            $request->input('per_page', 15)
        );

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}
