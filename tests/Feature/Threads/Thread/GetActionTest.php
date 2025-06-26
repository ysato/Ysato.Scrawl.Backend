<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use App\Models\Thread;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

class GetActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    private function getTestThread(): Thread
    {
        $thread = Thread::first();
        $this->assertNotNull($thread, 'At least one thread should exist from seeder');

        return $thread;
    }

    public function testReturnsThreadDetailSuccessfully(): void
    {
        $thread = $this->getTestThread();

        $response = $this->getJson("/threads/{$thread->id}");

        $response->assertStatus(200);

        $response->assertJsonPath('id', $thread->id);
        $response->assertJsonPath('title', $thread->title);
        $response->assertJsonPath('is_closed', $thread['is_closed']);

        /** @var array<int, array<string, mixed>> $scratchesArray */
        $scratchesArray = $response->json('scratches');
        $scratches = new Collection($scratchesArray);

        $scratches
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
