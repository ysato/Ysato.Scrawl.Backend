<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

class DeleteActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testDeletesThreadSuccessfully(): void
    {
        $response = $this->deleteJson('/threads/101');

        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => 101]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $response = $this->deleteJson('/threads/999');

        $response->assertStatus(404);
    }
}
