<?php

declare(strict_types=1);

namespace Tests\Support;

use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use RuntimeException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

use function base_path;
use function file_exists;
use function strtoupper;

class OpenApiTracker
{
    private static self|null $instance = null;

    private readonly EndpointStateExtractor $extractor;

    private readonly ImplementationTracker $tracker;

    private function __construct()
    {
        $openApiSpec = $this->loadOpenApiSpec();
        $this->extractor = new EndpointStateExtractor($openApiSpec);
        $this->tracker = new ImplementationTracker($openApiSpec);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function loadOpenApiSpec(): OpenApi
    {
        $specPath = base_path(env('OPENAPI_SPEC_PATH', 'openapi.yaml'));

        if (! file_exists($specPath)) {
            throw new RuntimeException("OpenAPI specification file not found: {$specPath}");
        }

        return Reader::readFromYamlFile($specPath);
    }

    public function recordImplementedEndpoint(string $method, string $path, int $statusCode): void
    {
        $this->tracker->recordImplementedEndpoint($method, $path, $statusCode);
    }

    public function hasImplementedEndpoints(): bool
    {
        return $this->tracker->hasImplementedStates();
    }

    public function displayImplementationStatus(): void
    {
        $expectedStates = $this->extractor->extractStates();

        $this->renderStatusTable($expectedStates);
    }

    /**
     * @param array<string, EndpointState> $expectedStates
     *
     * @return void
     */
    protected function renderStatusTable(array $expectedStates): void
    {
        $output = new ConsoleOutput();
        $table = new Table($output);

        $table->setHeaders(['STATUS', 'METHOD', 'ENDPOINT', 'RESPONSE']);

        foreach ($expectedStates as $state) {
            $isImplemented = $this->tracker->isImplemented($state);
            $statusIcon = $isImplemented ? '✅' : '❌';

            $table->addRow([
                $statusIcon,
                strtoupper($state->method),
                $state->path,
                $state->statusCode,
            ]);
        }

        $table->render();
    }
}
