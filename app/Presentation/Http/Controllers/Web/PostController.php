<?php

namespace App\Presentation\Http\Controllers\Web;

use App\Application\Services\PostService;
use App\Presentation\Http\Controllers\Controller;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Show trending posts.
     */
    public function trending()
    {
        $posts = $this->postService->getTrendingPosts(15);
        return view('posts.trending', ['posts' => $posts]);
    }

    /**
     * Search posts.
     */
    public function search()
    {
        return view('posts.search');
    }

    /**
     * Show single post.
     */
    public function show(int $id)
    {
        $post = $this->postService->getPostById($id);
        if (!$post) {
            abort(404);
        }
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Store a new post.
     */
    public function store()
    {
        return view('posts.create');
    }

    /**
     * Update a post.
     */
    public function update(int $id)
    {
        $post = $this->postService->getPostById($id);
        if (!$post) {
            abort(404);
        }
        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Delete a post.
     */
    public function destroy(int $id)
    {
        $post = $this->postService->getPostById($id);
        if (!$post) {
            abort(404);
        }
        $this->postService->deletePost($post);
        return redirect('/')->with('success', 'Post deleted successfully');
    }
}
