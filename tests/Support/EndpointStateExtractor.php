<?php

declare(strict_types=1);

namespace Tests\Support;

use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;

use function is_string;

class EndpointStateExtractor
{
    public function __construct(private readonly OpenApi $spec)
    {
    }

    /** @return array<string, EndpointState> */
    public function extractStates(): array
    {
        $methods = $this->getSupportedMethods();

        $states = [];
        /** @var PathItem $pathItem */
        foreach ($this->spec->paths as $path => $pathItem) {
            if (! is_string($path)) {
                continue;
            }

            foreach ($methods as $method) {
                $operation = $pathItem->{$method};
                if (! $operation instanceof Operation || $operation->responses === null) {
                    continue;
                }

                foreach ($operation->responses as $statusCode => $_) {
                    $state = new EndpointState($method, $path, $statusCode);
                    $states[(string)$state] = $state;
                }
            }
        }

        return $states;
    }

    /**
     * @return string[]
     */
    protected function getSupportedMethods(): array
    {
        return ['get', 'post', 'put', 'patch', 'delete', 'head', 'options'];
    }
}
