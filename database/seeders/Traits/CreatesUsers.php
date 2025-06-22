<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\User;

trait CreatesUsers
{
    protected function createUsers(): void
    {
        User::factory()->create([
            'id' => 1,
            'name' => 'Test User',
        ]);
    }

    protected function createMultipleUsers(int $count = 5): void
    {
        User::factory()->count($count)->create();
    }
}