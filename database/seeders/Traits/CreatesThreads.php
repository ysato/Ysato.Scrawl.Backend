<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\Thread;
use App\Models\User;

use function now;

trait CreatesThreads
{
    protected function createStandardThreads(): void
    {
        $user = User::findOrFail(1);

        // 基本的なスレッドセット（30件 - ページネーション発生）
        Thread::factory()
            ->count(5)
            ->for($user)
            ->create();
        Thread::factory()
            ->count(25)
            ->for($user)
            ->create();

        // 順序テスト用の特定日付スレッド
        Thread::factory()
            ->for($user)
            ->create([
                'id' => 100,
                'title' => 'Old Thread',
                'created_at' => now()->subDays(2),
            ]);

        Thread::factory()
            ->for($user)
            ->create([
                'id' => 101,
                'title' => 'New Thread',
                'created_at' => now()->subDays(1),
            ]);
    }

    protected function createLimitedThreads(): void
    {
        $user = User::findOrFail(1);

        // 少数スレッド（5件 - ページネーションなし）
        Thread::factory()
            ->count(5)
            ->for($user)
            ->create();
    }

    protected function createNoThreads(): void
    {
        // スレッドは作成しない（空データセット用）
    }

    protected function createThreadsDataset(int $count = 10): void
    {
        $user = User::findOrFail(1);

        Thread::factory()
            ->count($count)
            ->for($user)
            ->create();
    }
}
