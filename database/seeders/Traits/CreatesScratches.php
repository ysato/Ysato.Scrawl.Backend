<?php

declare(strict_types=1);

namespace Database\Seeders\Traits;

use App\Models\Scratch;
use App\Models\Thread;

trait CreatesScratches
{
    protected function createScratches(Thread $thread, int $count = 3): void
    {
        Scratch::factory()
            ->count($count)
            ->for($thread)
            ->create();
    }

    protected function createScratchesForAllThreads(): void
    {
        // 既存のスレッドにランダムなスクラッチを追加
        Thread::all()->each(function ($thread) {
            $this->createScratches($thread, rand(0, 5));
        });
    }
}
