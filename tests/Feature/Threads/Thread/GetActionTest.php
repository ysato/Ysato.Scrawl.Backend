<?php

declare(strict_types=1);

namespace Tests\Feature\Threads\Thread;

use App\Models\Thread;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

class GetActionTest extends TestCase
{
    #[Test]
    public function スレッドが存在する場合は詳細データを返す(): void
    {
        // Arrange: Factory-Firstでテストデータを作成
        /** @psalm-var User $user */
        $user = User::factory()->create();
        $thread = Thread::factory()->for($user)->create();

        // Act: スレッド詳細取得APIを実行
        $response = $this->getJson("/threads/{$thread->id}");

        // Assert: ThreadDetailスキーマに従った詳細データが返されることを検証
        $response->assertStatus(200);
        $response->assertJsonPath('id', $thread->id);
        $response->assertJsonPath('user_id', $thread->user_id);
        $response->assertJsonPath('title', $thread->title);
        $response->assertJsonPath('is_closed', $thread->is_closed);
        $response->assertJsonPath('created_at', $thread->created_at->toIso8601String());
        $response->assertJsonPath('last_scratch_created_at', $thread->last_scratch_created_at?->toIso8601String());
        $response->assertJsonPath('last_closed_at', $thread->last_closed_at?->toIso8601String());
        $response->assertJsonStructure(['scratches']);
        $response->assertJsonPath('user.id', $user->id);
        $response->assertJsonPath('user.name', $user->name);
    }

    #[Test]
    public function 存在しないスレッドにアクセスした場合は404エラーを返す(): void
    {
        // Arrange: 存在しないスレッドIDを指定
        $nonExistentThreadId = 99999;

        // Act: 存在しないスレッドの詳細取得を試行
        $response = $this->getJson("/threads/{$nonExistentThreadId}");

        // Assert: 404 Not Foundが返されることを検証
        $response->assertStatus(404);
    }
}
