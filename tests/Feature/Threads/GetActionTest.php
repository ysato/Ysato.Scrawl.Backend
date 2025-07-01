<?php

declare(strict_types=1);

namespace Tests\Feature\Threads;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\Testing\TestResponse;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

use function collect;
use function now;

class GetActionTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // @link https://github.com/briannesbitt/Carbon/issues/2481
        Date::setTestNow('2024-01-01T12:00:00+09:00');
    }

    #[Override]
    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function スレッドが存在しない場合は空のリストを返す(): void
    {
        // Arrange: 空のデータベース状態（RefreshDatabaseによりクリーン）
        // データ準備なし - 空の状態をテスト

        // Act: スレッド一覧取得APIを実行
        $response = $this->getJson('/threads');

        // Assert: 期待される空の結果を検証
        $response->assertStatus(200);
        $response->assertJsonPath('items', []);
        $response->assertJsonPath('self', null);
        $response->assertJsonPath('prev', null);
        $response->assertJsonPath('next', null);
    }

    #[Test]
    public function スレッドが存在する場合は正しい形式でデータを返す(): void
    {
        // Arrange: Factory-Firstでテストデータを作成
        $user = $this->createUser();
        $thread = $this->createThreadForUser($user);

        // Act: スレッド一覧取得APIを実行
        $response = $this->getJson('/threads');

        // Assert: 作成したデータが正しく取得できることを検証
        $response->assertStatus(200);
        $this->assertThreadListItemContent($response, 0, $thread, $user);
    }

    #[Test]
    public function スレッドは作成日時の降順でソートされて返される(): void
    {
        // Arrange: 明示的に異なる時刻でスレッドを作成
        $user = $this->createUser();

        Date::setTestNow('2024-01-01 10:00:00');
        $this->createThreadForUser($user);

        Date::setTestNow('2024-01-01 11:00:00');
        $this->createThreadForUser($user);

        // Act: スレッド一覧取得APIを実行
        $response = $this->getJson('/threads');

        // Assert: created_at降順でソートされていることを検証
        $response->assertStatus(200);
        $this->assertThreadsOrderedByCreatedAtDesc($response);
    }

    #[Test]
    public function スレッドが20件を超える場合は最初の20件のみ返す(): void
    {
        // Arrange: 25個のスレッドを作成（ページサイズ20を超える）
        $user = $this->createUser();
        $this->createThreadsWithTimestamps($user, 25);

        // Act: 最初のページを取得
        $response = $this->getJson('/threads');

        // Assert: 最初のページの検証
        $response->assertStatus(200);
        $response->assertJsonCount(20, 'items'); // ページサイズは20
        $response->assertJsonPath('self', null); // 最初のページではselfはnull
        $response->assertJsonPath('prev', null); // 最初のページではprevはnull
        $this->assertNotNull($response->json('next')); // nextページが存在
        $this->assertThreadsOrderedByCreatedAtDesc($response);
    }

    #[Test]
    public function カーソルを使用した2ページ目が正しいデータとリンクを返す(): void
    {
        // Arrange: 25個のスレッドを作成
        $user = $this->createUser();
        $this->createThreadsWithTimestamps($user, 25);

        // Act1: 最初のページを取得してnextカーソルを取得
        $firstPageResponse = $this->getJson('/threads');
        $nextCursor = $firstPageResponse->json('next');
        $this->assertIsString($nextCursor);

        // Act2: nextカーソルを使って2ページ目を取得
        $secondPageResponse = $this->getJson($nextCursor);

        // Assert: 2ページ目の検証
        $secondPageResponse->assertStatus(200);
        $secondPageResponse->assertJsonCount(5, 'items'); // 残り5件
        $this->assertNotNull($secondPageResponse->json('self')); // 2ページ目ではselfが存在
        $this->assertNotNull($secondPageResponse->json('prev')); // prevページが存在
        $secondPageResponse->assertJsonPath('next', null); // 最後のページではnextはnull
        $this->assertThreadsOrderedByCreatedAtDesc($secondPageResponse);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }

    private function createThreadForUser(User $user): Thread
    {
        return Thread::factory()->for($user)->create();
    }

    /** @return Collection<int, Thread> */
    private function createThreadsWithTimestamps(User $user, int $count): Collection
    {
        return Thread::factory()
            ->for($user)
            ->count($count)
            ->sequence(static function (Sequence $sequence) {
                Date::setTestNow(now()->addMinutes($sequence->index));

                return [];
            })
            ->createMany();
    }

    /** @param TestResponse<JsonResponse> $response */
    private function assertThreadListItemContent(TestResponse $response, int $index, Thread $thread, User $user): void
    {
        $response->assertJsonPath("items.{$index}.id", $thread->id);
        $response->assertJsonPath("items.{$index}.title", $thread->title);
        $response->assertJsonPath("items.{$index}.is_closed", $thread->is_closed);
        $response->assertJsonPath("items.{$index}.user_id", $thread->user_id);
        $response->assertJsonPath("items.{$index}.created_at", $thread->created_at->toIso8601String());
        $response->assertJsonPath(
            "items.{$index}.last_scratch_created_at",
            $thread->last_scratch_created_at?->toIso8601String(),
        );
        $response->assertJsonPath("items.{$index}.last_closed_at", $thread->last_closed_at?->toIso8601String());
        $response->assertJsonPath("items.{$index}.scratches_count", 0);
        $response->assertJsonPath("items.{$index}.user.id", $user->id);
        $response->assertJsonPath("items.{$index}.user.name", $user->name);
    }

    /** @param TestResponse<JsonResponse> $response */
    private function assertThreadsOrderedByCreatedAtDesc(TestResponse $response): void
    {
        /** @var array<array{created_at: string}> $items */
        $items = $response->json('items');

        collect($items)
            ->sliding()
            ->each(function ($pair) {
                /** @var array{created_at: string} $current */
                $current = $pair->first();
                /** @var array{created_at: string} $next */
                $next = $pair->last();

                $this->assertGreaterThanOrEqual($next['created_at'], $current['created_at']);
            });
    }
}
