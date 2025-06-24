<?php

declare(strict_types=1);

namespace Feature\Threads;

use App\Models\Thread;
use App\Models\User;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

class GetActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testReturnsPaginatedThreadsList(): void
    {
        $response = $this->getJson('/threads');

        $response->assertStatus(200);
    }

    public function testSupportsCursorPagination(): void
    {
        $response = $this->getJson('/threads?cursor=example_cursor');

        $response->assertStatus(200);
    }

    public function testReturnsThreadsOrderedByCreatedAtDesc(): void
    {
        $response = $this->getJson('/threads');

        $response->assertStatus(200);
        $response->assertJsonPath('items.0.id', 101);
        $response->assertJsonPath('items.1.id', 100);
    }

    public function testPaginationLinksWhenNoResults(): void
    {
        // データを削除してから空データセットSeederを実行
        Thread::query()->delete();
        User::query()->delete();

        $seeder = new ThreadTestSeeder();
        $seeder->runWithEmptyData();

        $response = $this->getJson('/threads');

        $response->assertStatus(200);
        $response->assertJsonPath('self', null);
        $response->assertJsonPath('prev', null);
        $response->assertJsonPath('next', null);
        $response->assertJsonPath('items', []);
    }

    public function testPaginationLinksWithLimitedResults(): void
    {
        // 1ページに収まる件数でのテスト（next/prevはnull）
        Thread::query()->delete();
        User::query()->delete();

        $seeder = new ThreadTestSeeder();
        $seeder->runWithLimitedData();

        $response = $this->getJson('/threads');

        $response->assertStatus(200);
        $response->assertJsonPath('self', null);
        $response->assertJsonPath('prev', null);
        $response->assertJsonPath('next', null);
    }
}
