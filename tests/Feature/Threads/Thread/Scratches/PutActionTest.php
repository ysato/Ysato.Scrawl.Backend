<?php

declare(strict_types=1);

namespace Feature\Threads\Thread\Scratches;

use App\Models\Scratch;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

class PutActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    private function getTestScratch(): Scratch
    {
        $scratch = Scratch::first();
        $this->assertNotNull($scratch, 'At least one scratch should exist from seeder');

        return $scratch;
    }

    public function testUpdatesScratchSuccessfully(): void
    {
        $scratch = $this->getTestScratch();

        $response = $this->putJson(
            "/threads/{$scratch->thread_id}/scratches/{$scratch->id}",
            ['content' => 'Updated scratch content'],
        );

        $response->assertStatus(204);
        $this->assertDatabaseHas('scratches', [
            'id' => $scratch->id,
            'content' => 'Updated scratch content',
        ]);
    }

    public function testValidationFailsWhenContentMissing(): void
    {
        $scratch = $this->getTestScratch();

        $response = $this->putJson("/threads/{$scratch->thread_id}/scratches/{$scratch->id}", ['content' => '']);

        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $scratch = $this->getTestScratch();

        $response = $this->putJson(
            "/threads/999/scratches/{$scratch->id}",
            ['content' => 'Updated content'],
        );

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function testReturns404WhenScratchNotFound(): void
    {
        $scratch = $this->getTestScratch();

        $response = $this->putJson(
            "/threads/{$scratch->thread_id}/scratches/999",
            ['content' => 'Updated content'],
        );

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }
}
