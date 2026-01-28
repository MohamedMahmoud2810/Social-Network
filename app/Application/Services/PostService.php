<?php

namespace App\Application\Services;

use App\Application\Contracts\PostRepositoryInterface;
use App\Application\DTOs\CreatePostDTO;
use App\Application\DTOs\UpdatePostDTO;
use App\Domain\Post\Models\Comment;
use App\Domain\Post\Models\Like;
use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use App\Presentation\Events\PostCreated;
use App\Presentation\Events\PostLiked;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * Get a post by ID.
     */
    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    /**
     * Create a new post.
     */
    public function createPost(CreatePostDTO $dto): Post
    {
        return DB::transaction(function () use ($dto) {
            $data = [
                'user_id' => $dto->userId,
                'content' => $dto->content,
            ];

            // Handle image upload
            if ($dto->image) {
                $path = $dto->image->store('post-images', 'public');
                $data['image_path'] = $path;
            }

            $post = $this->postRepository->create($data);

            // Dispatch event
            event(new PostCreated($post));

            return $post;
        });
    }

    /**
     * Update a post.
     */
    public function updatePost(Post $post, UpdatePostDTO $dto): Post
    {
        $data = ['content' => $dto->content];

        // Handle image upload
        if ($dto->image) {
            // Delete old image if exists
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }

            $path = $dto->image->store('post-images', 'public');
            $data['image_path'] = $path;
        }

        // Handle image removal
        if ($dto->removeImage && $post->image_path) {
            Storage::disk('public')->delete($post->image_path);
            $data['image_path'] = null;
        }

        $this->postRepository->update($post, $data);

        return $post->fresh();
    }

    /**
     * Delete a post.
     */
    public function deletePost(Post $post): bool
    {
        // Delete associated image
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        return $this->postRepository->delete($post);
    }

    /**
     * Get posts by a user.
     */
    public function getUserPosts(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getUserPosts($user, $perPage);
    }

    /**
     * Get news feed for a user.
     */
    public function getNewsFeed(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getNewsFeed($user, $perPage);
    }

    /**
     * Add a comment to a post.
     */
    public function addComment(Post $post, User $user, string $content): Comment
    {
        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'content' => $content,
        ]);

        // You can dispatch CommentAdded event here

        return $comment->load('user');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(Comment $comment): bool
    {
        return (bool) $comment->delete();
    }

    /**
     * Like a post.
     */
    public function likePost(Post $post, User $user): ?Like
    {
        // Check if already liked
        if ($post->isLikedBy($user)) {
            return null;
        }

        $like = $post->likes()->create([
            'user_id' => $user->id,
        ]);

        // Dispatch event
        event(new PostLiked($post, $user));

        return $like;
    }

    /**
     * Unlike a post.
     */
    public function unlikePost(Post $post, User $user): bool
    {
        return (bool) $post->likes()
            ->where('user_id', $user->id)
            ->delete();
    }

    /**
     * Get users who liked a post.
     */
    public function getPostLikes(Post $post)
    {
        return $post->likes()->with('user')->get();
    }

    /**
     * Search posts.
     */
    public function searchPosts(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->search($query, $perPage);
    }

    /**
     * Get trending posts.
     */
    public function getTrendingPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getTrendingPosts($perPage);
    }
}
