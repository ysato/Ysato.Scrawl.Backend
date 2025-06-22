<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'is_closed' => false,
            'created_at' => fake()->dateTime(),
            'last_scratch_created_at' => null,
            'last_closed_at' => null,
        ];
    }
}
