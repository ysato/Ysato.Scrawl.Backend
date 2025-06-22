<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        // 新しい階層型Seederを使用
        $this->call([
            ThreadTestSeeder::class,
        ]);
    }
}
