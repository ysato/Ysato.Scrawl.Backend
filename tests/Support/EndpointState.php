<?php

declare(strict_types=1);

namespace Tests\Support;

use function strtoupper;

readonly class EndpointState
{
    public function __construct(
        public string $method,
        public string $path,
        public string|int $statusCode,
    ) {
    }

    protected function getKey(): string
    {
        return implode(' ', [strtoupper($this->method), $this->path, $this->statusCode]);
    }

    public function __toString(): string
    {
        return $this->getKey();
    }
}
