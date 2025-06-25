<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

use function assert;
use function count;
use function is_string;

class GetActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testReturnsThreadDetailSuccessfully(): void
    {
        $response = $this->getJson('/threads/101');

        $response->assertStatus(200);

        // 基本Thread情報検証
        $response->assertJsonPath('id', 101);
        $response->assertJsonPath('title', 'New Thread');
        $response->assertJsonPath('is_closed', false);

        // scratchesデータの検証
        /** @var array<int, array<string, mixed>> $scratches */
        $scratches = $response->json('scratches');

        // 古い順ソート検証（複数scratchesがある場合のみ）
        if (count($scratches) > 1) {
            for ($i = 0; $i < count($scratches) - 1; $i++) {
                $currentCreatedAt = $scratches[$i]['created_at'];
                assert(is_string($currentCreatedAt));
                $nextCreatedAt = $scratches[$i + 1]['created_at'];
                assert(is_string($nextCreatedAt));
                $this->assertLessThanOrEqual($nextCreatedAt, $currentCreatedAt);
            }
        }

        // OpenAPI準拠検証は自動実行されます
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
