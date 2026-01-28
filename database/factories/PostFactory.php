<?php

namespace Database\Factories;

use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Post\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => fake()->paragraphs(rand(1, 3), true),
            'image_path' => fake()->optional(0.3)->randomElement([
                'post-images/'.fake()->uuid().'.jpg',
            ]),
        ];
    }

    /**
     * Indicate that the post should have an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'post-images/'.fake()->uuid().'.jpg',
        ]);
    }

    /**
     * Indicate that the post should not have an image.
     */
    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => null,
        ]);
    }
}
