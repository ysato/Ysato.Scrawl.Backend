<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

use function collect;

class GetActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testReturnsThreadDetailSuccessfully(): void
    {
        $response = $this->getJson('/threads/101');

        $response->assertStatus(200);

        $response->assertJsonPath('id', 101);
        $response->assertJsonPath('title', 'New Thread');
        $response->assertJsonPath('is_closed', false);

        /** @var array<int, array<string, mixed>> $scratches */
        $scratches = $response->json('scratches');

        collect($scratches)
            ->sliding(2)
            ->each(function (Collection $pair) {
                /** @var array<string, mixed> $current */
                $current = $pair->first();
                /** @var array<string, mixed> $next */
                $next = $pair->last();
                $this->assertLessThanOrEqual(
                    $next['created_at'],
                    $current['created_at'],
                    'Scratches should be ordered by created_at asc',
                );
            });
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $response = $this->getJson('/threads/999');

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJson([
            'title' => 'Resource not found.',
            'status' => 404,
            'detail' => 'The requested resource was not found.',
        ]);
    }
}
