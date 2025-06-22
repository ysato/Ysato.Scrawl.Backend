<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Traits\CreatesUsers;
use Illuminate\Database\Seeder;

class BaseTestSeeder extends Seeder
{
    use CreatesUsers;

    public function run(): void
    {
        $this->createUsers();
    }
}