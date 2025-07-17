<?php

declare(strict_types=1);

namespace Tests\Support;

use function count;

readonly class ImplementationStatus
{
    /**
     * @param array<EndpointState> $expectedStates
     * @param array<EndpointState> $implementedStates
     * @param array<EndpointState> $missingStates
     */
    public function __construct(
        public array $expectedStates,
        public array $implementedStates,
        public array $missingStates,
    ) {
    }

    public function getImplementationRate(): float
    {
        $total = $this->getTotalCount();

        if ($total === 0) {
            return 0.0;
        }

        return $this->getImplementedCount() / $total;
    }

    public function getTotalCount(): int
    {
        return count($this->expectedStates);
    }

    public function getImplementedCount(): int
    {
        return count($this->implementedStates);
    }

    public function getMissingCount(): int
    {
        return count($this->missingStates);
    }

    public function isFullyImplemented(): bool
    {
        return $this->getMissingCount() === 0;
    }
}
