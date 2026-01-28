<?php

namespace Tests\Feature;

use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_create_a_post(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/posts', [
                'content' => 'This is a test post',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Post created successfully',
            ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'content' => 'This is a test post',
        ]);
    }

    /** @test */
    public function user_can_create_a_post_with_image(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('post.jpg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->post('/api/v1/posts', [
                'content' => 'Post with image',
                'image' => $image,
            ]);

        $response->assertStatus(201);
        Storage::disk('public')->assertExists('post-images/'.$image->hashName());
    }

    /** @test */
    public function user_can_update_their_own_post(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/posts/{$post->id}", [
                'content' => 'Updated content',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post updated successfully',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Updated content',
        ]);
    }

    /** @test */
    public function user_cannot_update_another_users_post(): void
    {
        $anotherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/posts/{$post->id}", [
                'content' => 'Trying to update',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_own_post(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post deleted successfully',
            ]);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function user_can_view_news_feed(): void
    {
        // Create some posts
        Post::factory(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'user',
                        'likes_count',
                        'comments_count',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /** @test */
    public function post_content_is_required(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/posts', [
                'content' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function post_image_must_be_valid_image_file(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf');

        $response = $this->actingAs($this->user, 'sanctum')
            ->post('/api/v1/posts', [
                'content' => 'Post with invalid file',
                'image' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }

    /** @test */
    public function user_can_search_posts(): void
    {
        Post::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'Laravel is awesome',
        ]);

        Post::factory()->create([
            'user_id' => $this->user->id,
            'content' => 'PHP is great',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/posts/search?q=Laravel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function unauthenticated_user_cannot_create_post(): void
    {
        $response = $this->postJson('/api/v1/posts', [
            'content' => 'Test post',
        ]);

        $response->assertStatus(401);
    }
}
