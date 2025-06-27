<?php

declare(strict_types=1);

namespace Tests\Feature\Threads;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Override;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

use function assert;
use function is_int;
use function str_repeat;

class PostActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Red: 基準時刻を設定（タイムゾーン付きISO形式）
        // @link https://github.com/briannesbitt/Carbon/issues/2481
        Date::setTestNow('2024-01-01T12:00:00+09:00');

        /** @psalm-var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    #[Override]
    protected function tearDown(): void
    {
        // Red: Date::setTestNow()のクリーンアップ
        Date::setTestNow();

        parent::tearDown();
    }

    public function testCanCreateThreadWithValidData(): void
    {
        // Arrange: Factory-Firstでユーザーを作成し、有効なリクエストデータを準備
        $requestData = ['title' => 'テスト用スレッドタイトル'];

        // Act: スレッド作成APIを実行
        $response = $this->actingAs($this->user)->postJson('/threads', $requestData);

        // Assert: スレッドが正常に作成されることを検証
        $response->assertStatus(201);

        // Refactor: OpenAPI仕様の必須フィールド完全検証
        $threadId = $response->json('id');
        assert(is_int($threadId));
        $response->assertJsonPath('id', $threadId);
        $response->assertJsonPath('title', 'テスト用スレッドタイトル');
        $response->assertJsonPath('is_closed', false);
        $response->assertJsonPath('user_id', $this->user->id);
        $response->assertJsonPath('created_at', '2024-01-01T12:00:00+09:00');
        $response->assertJsonPath('last_scratch_created_at', null);
        $response->assertJsonPath('last_closed_at', null);

        // Refactor: Locationヘッダーの検証（OpenAPI仕様で必須）
        $response->assertHeader('Location', '/threads/' . $threadId);

        // データベースにスレッドが保存されていることを確認
        $this->assertDatabaseHas('threads', [
            'id' => $threadId,
            'title' => 'テスト用スレッドタイトル',
            'is_closed' => false,
            'user_id' => $this->user->id,
            'created_at' => '2024-01-01T12:00:00+09:00',
            'last_scratch_created_at' => null,
            'last_closed_at' => null,
        ]);
    }

    public function testValidationErrorWhenTitleIsMissing(): void
    {
        // Arrange: 無効なリクエストデータを準備
        $requestData = [];

        // Act: 無効なデータでスレッド作成APIを実行（リクエスト検証をスキップ）
        $response = $this->withoutRequestValidation()
            ->actingAs($this->user)
            ->postJson('/threads', $requestData);

        // Assert: バリデーションエラーが返されることを検証
        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonPath('title', 'The given data was invalid.');
        $response->assertJsonPath('errors.title.0', 'The title field is required.');
    }

    public function testUnauthorizedWhenNoAuthentication(): void
    {
        // Arrange: 認証なしの状態で有効なリクエストデータを準備（ユーザー作成しない）
        $requestData = ['title' => 'テスト用スレッドタイトル'];

        // Act: 認証なしでスレッド作成APIを実行
        $response = $this->postJson('/threads', $requestData);

        // Assert: 認証エラーが返されることを検証
        $response->assertStatus(401);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonPath('title', 'Unauthenticated.');
    }

    public function testValidationErrorWhenTitleTooLong(): void
    {
        // Arrange: 255文字を超えるタイトルを準備
        $longTitle = str_repeat('a', 256); // 256文字（制限を1文字超える）
        $requestData = ['title' => $longTitle];

        // Act: 長すぎるタイトルでスレッド作成APIを実行（リクエスト検証をスキップ）
        $response = $this->withoutRequestValidation()
            ->actingAs($this->user)
            ->postJson('/threads', $requestData);

        // Assert: バリデーションエラーが返されることを検証
        $response->assertStatus(422);
        $response->assertHeader('Content-Type', 'application/problem+json');
        $response->assertJsonPath('title', 'The given data was invalid.');
        $response->assertJsonPath('errors.title.0', 'The title field must not be greater than 255 characters.');
    }
}
