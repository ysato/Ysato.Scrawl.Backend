<?php

declare(strict_types=1);

namespace Feature\Threads\Thread\Scratches;

use App\Models\Thread;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

use function assert;
use function is_int;
use function is_string;

class PostActionTest extends TestCase
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

    public function testCreatesScratchSuccessfully(): void
    {
        $thread = $this->getTestThread();

        $response = $this->postJson(
            "/threads/{$thread->id}/scratches",
            ['content' => 'Test scratch content in Markdown'],
        );

        $response->assertStatus(201);
        $response->assertJsonPath('content', 'Test scratch content in Markdown');
        $response->assertJsonPath('thread_id', $thread->id);

        $scratchId = $response->json('id');
        assert(is_string($scratchId) || is_int($scratchId));
        $response->assertHeader('Location', "/threads/{$thread->id}/scratches/$scratchId");

        $this->assertDatabaseHas('scratches', [
            'content' => 'Test scratch content in Markdown',
            'thread_id' => $thread->id,
        ]);
    }

    public function testValidationFailsWhenContentMissing(): void
    {
        $thread = $this->getTestThread();

        $response = $this
            ->withoutRequestValidation()
            ->postJson("/threads/{$thread->id}/scratches", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content' => ['The content field is required.']]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $response = $this->postJson(
            '/threads/999/scratches',
            ['content' => 'Test scratch content'],
        );

        $response->assertStatus(404);
    }
}
