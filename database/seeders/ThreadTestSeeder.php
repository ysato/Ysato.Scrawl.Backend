<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Traits\CreatesScratches;
use Database\Seeders\Traits\CreatesThreads;

class ThreadTestSeeder extends BaseTestSeeder
{
    use CreatesScratches;
    use CreatesThreads;

    public function run(): void
    {
        parent::run();

        $this->createPaginationTestThreads();
        $this->assignRandomScratchesToThreads();
        $this->guaranteeMinimumScratchRequirements();
    }

    public function runWithLimitedData(): void
    {
        parent::run();

        $this->createLimitedThreads();
    }

    public function runWithEmptyData(): void
    {
        parent::run();

        $this->createNoThreads();
    }
}
