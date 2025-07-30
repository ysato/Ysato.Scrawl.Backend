<?php

declare(strict_types=1);

namespace Tests\Support;

use cebe\openapi\spec\OpenApi;
use League\OpenAPIValidation\PSR7\PathFinder;

use function strtolower;

class ImplementationTracker
{
    /** @var array<string, bool> */
    private array $implementedStates = [];

    public function __construct(private readonly OpenApi $openApiSpec)
    {
    }

    public function recordImplementedEndpoint(string $method, string $path, int $statusCode): void
    {
        $resolvedPath = $this->resolveOpenApiPath($path, $method);
        $state = new EndpointState($method, $resolvedPath, (string)$statusCode);
        $this->recordState($state);
    }

    public function recordState(EndpointState $state): void
    {
        $this->implementedStates[(string)$state] = true;
    }

    public function isImplemented(EndpointState $state): bool
    {
        return isset($this->implementedStates[(string)$state]);
    }

    public function hasImplementedStates(): bool
    {
        return ! empty($this->implementedStates);
    }

    /**
     * 実際のリクエストパスからOpenAPI仕様のパスパターンを解決
     */
    protected function resolveOpenApiPath(string $actualPath, string $method): string
    {
        $pathFinder = new PathFinder($this->openApiSpec, $actualPath, strtolower($method));

        $matches = $pathFinder->search();

        return ! empty($matches) ? $matches[0]->path() : $actualPath;
    }
}
