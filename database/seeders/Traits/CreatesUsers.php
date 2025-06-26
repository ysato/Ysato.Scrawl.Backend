<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\User;

trait CreatesUsers
{
    private const int PRIMARY_TEST_USER_ID = 1;

    protected function createUsers(): void
    {
        User::factory()->create([
            'id' => self::PRIMARY_TEST_USER_ID,
            'name' => 'Test User',
        ]);
    }

    protected function createMultipleUsers(int $count = 5): void
    {
        User::factory()->count($count)->create();
    }
}
