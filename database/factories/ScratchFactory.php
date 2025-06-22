<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Scratch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scratch>
 */
class ScratchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => 1,
            'content' => $this->faker->paragraphs(2, true),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year'),
        ];
    }
}
