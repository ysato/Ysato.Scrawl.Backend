<?php

declare(strict_types=1);

namespace Tests\Feature\Threads;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Override;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

use function now;

class GetActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Refactor: 基準時刻を設定（各テストで相対時間を使用）
        Date::setTestNow('2024-01-01 12:00:00');
    }

    #[Override]
    protected function tearDown(): void
    {
        // Refactor: Date::setTestNow()のクリーンアップ
        Date::setTestNow();

        parent::tearDown();
    }

    public function testCanGetEmptyThreadsList(): void
    {
        // Arrange: 空のデータベース状態（RefreshDatabaseによりクリーン）
        // データ準備なし - 空の状態をテスト

        // Act: スレッド一覧取得APIを実行
        $response = $this->get('/threads');

        // Assert: 期待される空の結果を検証
        $response->assertStatus(200);
        $response->assertJsonPath('items', []);
        $response->assertJsonPath('self', null);
        $response->assertJsonPath('prev', null);
        $response->assertJsonPath('next', null);
    }

    public function testCanGetThreadsListWithData(): void
    {
        // Arrange: Factory-Firstでテストデータを作成
        /** @psalm-var User $user */
        $user = User::factory()->create();
        $thread = Thread::factory()->for($user)->create();

        // Act: スレッド一覧取得APIを実行
        $response = $this->get('/threads');

        // Assert: 作成したデータが正しく取得できることを検証
        $response->assertStatus(200);
        $response->assertJsonPath('items.0.id', $thread->id);
        $response->assertJsonPath('items.0.title', $thread->title);
        $response->assertJsonPath('items.0.user.id', $user->id);
        $response->assertJsonPath('items.0.user.name', $user->name);
        $response->assertJsonPath('items.0.scratches_count', 0);
    }

    public function testThreadsAreOrderedByCreatedAtDesc(): void
    {
        // Arrange: 時間を制御して異なるcreated_atのスレッドを作成
        /** @psalm-var User $user */
        $user = User::factory()->create();

        // 古いスレッドを作成（1時間前）
        Date::setTestNow(now()->subHour());
        $olderThread = Thread::factory()->for($user)->create();

        // 新しいスレッドを作成（基準時刻）
        Date::setTestNow(now()->addHour());
        $newerThread = Thread::factory()->for($user)->create();

        // Act: スレッド一覧取得APIを実行
        $response = $this->get('/threads');

        // Assert: created_at降順でソートされていることを検証
        $response->assertStatus(200);
        $response->assertJsonPath('items.0.id', $newerThread->id);
        $response->assertJsonPath('items.1.id', $olderThread->id);
    }
}
