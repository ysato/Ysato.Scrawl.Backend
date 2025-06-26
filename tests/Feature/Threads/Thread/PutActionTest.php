<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use App\Models\Thread;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

class PutActionTest extends TestCase
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

    public function testUpdatesThreadSuccessfully(): void
    {
        $thread = $this->getTestThread();

        $response = $this->putJson("/threads/{$thread->id}", [
            'title' => 'Updated Test Thread Title',
            'is_closed' => true,
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseHas('threads', [
            'id' => $thread->id,
            'title' => 'Updated Test Thread Title',
            'is_closed' => true,
        ]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $response = $this->putJson('/threads/999', [
            'title' => 'Updated Title',
            'is_closed' => true,
        ]);

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function testValidationFailsWhenTitleMissing(): void
    {
        $thread = $this->getTestThread();

        $response = $this->putJson("/threads/{$thread->id}", ['is_closed' => true]);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.title.0', 'The title field is required.');
    }

    public function testValidationFailsWhenIsClosedMissing(): void
    {
        $thread = $this->getTestThread();

        $response = $this->putJson("/threads/{$thread->id}", ['title' => 'Valid Title']);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.is_closed.0', 'The is closed field is required.');
    }
}
