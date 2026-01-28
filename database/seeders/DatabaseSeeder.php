<?php

namespace Database\Seeders;

use App\Domain\Friendship\Enums\FriendshipStatus;
use App\Domain\Friendship\Models\Friendship;
use App\Domain\Post\Models\Comment;
use App\Domain\Post\Models\Like;
use App\Domain\Post\Models\Post;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $users = User::factory(20)->create();

        // Create a test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'bio' => 'This is a test user account for development.',
        ]);

        $allUsers = $users->push($testUser);

        // Create friendships
        foreach ($allUsers as $user) {
            // Each user sends 3-5 friend requests
            $potentialFriends = $allUsers->where('id', '!=', $user->id)->random(rand(3, 5));

            foreach ($potentialFriends as $friend) {
                // Avoid duplicate friendships
                $existingFriendship = Friendship::where(function ($query) use ($user, $friend) {
                    $query->where('user_id', $user->id)
                        ->where('friend_id', $friend->id);
                })->orWhere(function ($query) use ($user, $friend) {
                    $query->where('user_id', $friend->id)
                        ->where('friend_id', $user->id);
                })->first();

                if (! $existingFriendship) {
                    Friendship::create([
                        'user_id' => $user->id,
                        'friend_id' => $friend->id,
                        'status' => fake()->randomElement([
                            FriendshipStatus::PENDING,
                            FriendshipStatus::ACCEPTED,
                            FriendshipStatus::ACCEPTED,
                            FriendshipStatus::ACCEPTED, // More accepted than pending
                        ]),
                    ]);
                }
            }
        }

        // Create posts
        foreach ($allUsers as $user) {
            Post::factory(rand(2, 8))
                ->create(['user_id' => $user->id]);
        }

        $posts = Post::all();

        // Create comments
        foreach ($posts as $post) {
            $commenters = $allUsers->random(rand(0, 5));

            foreach ($commenters as $commenter) {
                Comment::factory()->create([
                    'user_id' => $commenter->id,
                    'post_id' => $post->id,
                ]);
            }
        }

        // Create likes
        foreach ($posts as $post) {
            $likers = $allUsers->random(rand(0, 10));

            foreach ($likers as $liker) {
                Like::create([
                    'user_id' => $liker->id,
                    'post_id' => $post->id,
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Test user email: test@example.com');
        $this->command->info('Test user password: password');
    }
}
