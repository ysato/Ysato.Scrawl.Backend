<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\Scratch;
use App\Models\Thread;

trait CreatesScratches
{
    private const DEFAULT_SCRATCH_COUNT = 3;
    private const MINIMUM_SCRATCH_COUNT = 1;
    private const MAX_RANDOM_SCRATCH_COUNT = 5;
    protected function createScratches(Thread $thread, int $count = self::DEFAULT_SCRATCH_COUNT): void
    {
        Scratch::factory()
            ->count($count)
            ->for($thread)
            ->create();
    }

    protected function assignRandomScratchesToThreads(): void
    {
        // 既存のスレッドにランダムなスクラッチを追加
        Thread::all()->each(function ($thread) {
            $this->createScratches($thread, rand(0, self::MAX_RANDOM_SCRATCH_COUNT));
        });
    }

    protected function guaranteeMinimumScratchRequirements(): void
    {
        // テスト用：スクラッチを持つスレッドが最低1つ存在することを保証
        $threadsWithoutScratches = Thread::doesntHave('scratches')->get();

        if ($threadsWithoutScratches->isNotEmpty()) {
            // スクラッチがないスレッドの最初の1つに最低限のスクラッチを作成
            $firstThreadWithoutScratches = $threadsWithoutScratches->first();
            $this->createScratches($firstThreadWithoutScratches, self::MINIMUM_SCRATCH_COUNT);
        }
    }
}
