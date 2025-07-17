<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Ysato\Catalyst\ValidatesOpenApiSpec;

trait TracksOpenApiImplementation
{
    use ValidatesOpenApiSpec;

    /**
     * @param string                  $method
     * @param string                  $uri
     * @param array<array-key, mixed> $parameters
     * @param array<array-key, mixed> $cookies
     * @param array<array-key, mixed> $files
     * @param array<array-key, mixed> $server
     * @param string|null             $content
     *
     * @return TestResponse<Response>
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        // 親のcall()メソッドを呼び出し
        $response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        $this->recordImplementedEndpointState($method, $uri, $response->getStatusCode());

        return $response;
    }

    protected function recordImplementedEndpointState(string $method, string $path, int $statusCode): void
    {
        OpenApiTracker::getInstance()->recordImplementedEndpoint($method, $path, $statusCode);
    }
}
