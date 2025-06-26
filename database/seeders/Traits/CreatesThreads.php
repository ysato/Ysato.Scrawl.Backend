<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\Thread;
use App\Models\User;

use function now;

trait CreatesThreads
{
    private const int PAGINATION_THREAD_COUNT = 30;
    private const int SMALL_THREAD_COUNT = 5;
    private const int ORDER_TEST_OLD_THREAD_ID = 100;
    private const int ORDER_TEST_NEW_THREAD_ID = 101;
    private const int PRIMARY_TEST_USER_ID = 1;
    private const int DEFAULT_THREAD_COUNT = 10;
    protected function createPaginationTestThreads(): void
    {
        $user = User::findOrFail(self::PRIMARY_TEST_USER_ID);

        // 基本的なスレッドセット（30件 - ページネーション発生）
        Thread::factory()
            ->count(self::SMALL_THREAD_COUNT)
            ->for($user)
            ->create();
        Thread::factory()
            ->count(self::PAGINATION_THREAD_COUNT - self::SMALL_THREAD_COUNT)
            ->for($user)
            ->create();

        // 順序テスト用の特定日付スレッド
        Thread::factory()
            ->for($user)
            ->create([
                'id' => self::ORDER_TEST_OLD_THREAD_ID,
                'title' => 'Old Thread',
                'created_at' => now()->subDays(2),
            ]);

        Thread::factory()
            ->for($user)
            ->create([
                'id' => self::ORDER_TEST_NEW_THREAD_ID,
                'title' => 'New Thread',
                'created_at' => now()->subDays(1),
            ]);
    }

    protected function createLimitedThreads(): void
    {
        $user = User::findOrFail(self::PRIMARY_TEST_USER_ID);

        // 少数スレッド（5件 - ページネーションなし）
        Thread::factory()
            ->count(self::SMALL_THREAD_COUNT)
            ->for($user)
            ->create();
    }

    protected function createNoThreads(): void
    {
        // スレッドは作成しない（空データセット用）
    }

    protected function createCustomThreadSet(int $count = self::DEFAULT_THREAD_COUNT): void
    {
        $user = User::findOrFail(self::PRIMARY_TEST_USER_ID);

        Thread::factory()
            ->count($count)
            ->for($user)
            ->create();
    }
}
