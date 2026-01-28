<?php

namespace App\Presentation\Http\Controllers\Api\V1;

use App\Application\Services\PostService;
use App\Domain\Post\Models\Comment;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     *     path="/api/v1/posts/{postId}/comments",
     *     tags={"Comments"},
     *     summary="Get all comments for a specific post",


     * )
     */
    public function index(int $postId): JsonResponse
    {
        $post = $this->postService->getPostById($postId);

        if (! $post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $comments = $post->comments()->with('user')->latest()->get();

        return response()->json([
            'data' => CommentResource::collection($comments),
        ]);
    }

    /**
     *     path="/api/v1/posts/{postId}/comments",
     *     tags={"Comments"},
     *     security={{"apiAuth":{}}},
     *     summary="Add comment",


     *             required={"content"},

     *         )
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

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $this->postService->addComment(
            $post,
            $request->user(),
            $validated['content']
        );

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, int $commentId): JsonResponse
    {
        $comment = Comment::find($commentId);

        if (! $comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        // Authorization
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => new CommentResource($comment->load('user')),
        ]);
    }

    /**
     * Delete a comment.
     */
    public function destroy(Request $request, int $commentId): JsonResponse
    {
        $comment = Comment::find($commentId);

        if (! $comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        // Authorization
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->postService->deleteComment($comment);

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
