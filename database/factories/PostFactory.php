<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => Thread::factory(),
            'name' => fake()->optional()->name(),
            'body' => fake()->realText(),
        ];
    }
}
