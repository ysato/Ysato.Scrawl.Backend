<?php

declare(strict_types=1);

namespace Feature\Threads\Thread\Scratches;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

use function assert;
use function is_int;
use function is_string;

class PostActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testCreatesScratchSuccessfully(): void
    {
        $requestData = ['content' => 'Test scratch content in Markdown'];

        $response = $this->postJson('/threads/101/scratches', $requestData);

        $response->assertStatus(201);
        $response->assertJsonPath('content', 'Test scratch content in Markdown');
        $response->assertJsonPath('thread_id', 101);

        $scratchId = $response->json('id');
        assert(is_string($scratchId) || is_int($scratchId));
        $response->assertHeader('Location', "/threads/101/scratches/$scratchId");

        $this->assertDatabaseHas('scratches', [
            'content' => 'Test scratch content in Markdown',
            'thread_id' => 101,
        ]);
    }

    public function testValidationFailsWhenContentMissing(): void
    {
        $response = $this
            ->withoutRequestValidation()
            ->postJson('/threads/101/scratches', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content' => ['The content field is required.']]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $requestData = ['content' => 'Test scratch content'];

        $response = $this->postJson('/threads/999/scratches', $requestData);

        $response->assertStatus(404);
    }
}
