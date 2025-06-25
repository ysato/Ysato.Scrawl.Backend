<?php

declare(strict_types=1);

namespace Feature\Threads;

use App\Models\Thread;
use App\Models\User;
use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

use function collect;
use function count;

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

        /** @var array<int, array<string, mixed>> $items */
        $items = $response->json('items');
        $this->assertGreaterThan(0, count($items), 'Should have at least one thread');

        collect($items)
            ->sliding(2)
            ->each(function (Collection $pair) {
                /** @var array<string, mixed> $current */
                $current = $pair->first();
                /** @var array<string, mixed> $next */
                $next = $pair->last();
                $this->assertGreaterThanOrEqual(
                    $next['created_at'],
                    $current['created_at'],
                    'Threads should be ordered by created_at desc',
                );
            });
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
