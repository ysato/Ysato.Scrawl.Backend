<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\Thread;

use function now;

trait CreatesThreads
{
    protected function createStandardThreads(): void
    {
        // 基本的なスレッドセット（30件 - ページネーション発生）
        Thread::factory()->count(5)->create(['user_id' => 1]);
        Thread::factory()->count(25)->create(['user_id' => 1]);

        // 順序テスト用の特定日付スレッド
        Thread::factory()->create([
            'id' => 100,
            'user_id' => 1,
            'title' => 'Old Thread',
            'created_at' => now()->subDays(2),
        ]);

        Thread::factory()->create([
            'id' => 101,
            'user_id' => 1,
            'title' => 'New Thread',
            'created_at' => now()->subDays(1),
        ]);
    }

    protected function createLimitedThreads(): void
    {
        // 少数スレッド（5件 - ページネーションなし）
        Thread::factory()->count(5)->create(['user_id' => 1]);
    }

    protected function createNoThreads(): void
    {
        // スレッドは作成しない（空データセット用）
    }

    protected function createThreadsDataset(int $count = 10): void
    {
        Thread::factory()->count($count)->create(['user_id' => 1]);
    }
}