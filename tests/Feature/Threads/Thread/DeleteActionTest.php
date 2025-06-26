<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use App\Models\Thread;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

class DeleteActionTest extends TestCase
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

    public function testDeletesThreadSuccessfully(): void
    {
        $thread = $this->getTestThread();

        $response = $this->deleteJson("/threads/{$thread->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $response = $this->deleteJson('/threads/999');

        $response->assertStatus(404);
    }
}
