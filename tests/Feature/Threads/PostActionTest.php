<?php

declare(strict_types=1);

namespace Tests\Feature\Threads;

use App\Models\User;
use Illuminate\Support\Facades\Date;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

use function str_repeat;

class PostActionTest extends TestCase
{
    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // @link https://github.com/briannesbitt/Carbon/issues/2481
        Date::setTestNow('2024-01-01T12:00:00+09:00');

        /** @psalm-var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    #[Override]
    protected function tearDown(): void
    {
        Date::setTestNow();

        parent::tearDown();
    }

    #[Test]
    public function 有効なデータでスレッドを作成できる(): void
    {
        // Arrange: Factory-Firstでユーザーを作成し、有効なリクエストデータを準備
        $requestData = ['title' => 'テスト用スレッドタイトル'];

        // Act: スレッド作成APIを実行
        $response = $this->actingAs($this->user)->postJson('/threads', $requestData);

        // Assert: OpenAPIにより自動検証されるため、ビジネスロジックに関する検証のみ実施
        // データベースにスレッドが保存されていることを確認
        $this->assertDatabaseHas('threads', [
            'user_id' => $this->user->id,
            'title' => 'テスト用スレッドタイトル',
            'is_closed' => false,
            'last_scratch_created_at' => null,
            'last_closed_at' => null,
        ]);

        $response->assertJsonFragment(['title' => 'テスト用スレッドタイトル']);
    }

    #[Test]
    public function タイトルが未入力の場合はバリデーションエラーになる(): void
    {
        // Arrange: 無効なリクエストデータを準備
        $requestData = [];

        // Act: 無効なデータでスレッド作成APIを実行（リクエスト検証をスキップ）
        $response = $this->withoutRequestValidation()
            ->actingAs($this->user)
            ->postJson('/threads', $requestData);

        // Assert: OpenAPIにより自動検証されるため、ビジネスロジックに関する検証のみ実施
        $response->assertJsonPath('title', 'The given data was invalid.');
        $response->assertJsonPath('errors.title.0', 'The title field is required.');
    }

    #[Test]
    public function 認証なしの場合は未認証エラーになる(): void
    {
        // Arrange: 認証なしの状態で有効なリクエストデータを準備（ユーザー作成しない）
        $requestData = ['title' => 'テスト用スレッドタイトル'];

        // Act: 認証なしでスレッド作成APIを実行
        $response = $this->postJson('/threads', $requestData);

        // Assert: OpenAPIにより自動検証されるため、ビジネスロジックに関する検証のみ実施
        $response->assertJsonPath('title', 'Unauthenticated.');
    }

    #[Test]
    public function タイトルが長すぎる場合はバリデーションエラーになる(): void
    {
        // Arrange: 255文字を超えるタイトルを準備
        $longTitle = str_repeat('a', 256); // 256文字（制限を1文字超える）
        $requestData = ['title' => $longTitle];

        // Act: 長すぎるタイトルでスレッド作成APIを実行（リクエスト検証をスキップ）
        $response = $this->withoutRequestValidation()
            ->actingAs($this->user)
            ->postJson('/threads', $requestData);

        // Assert: OpenAPIにより自動検証されるため、ビジネスロジックに関する検証のみ実施
        $response->assertJsonPath('title', 'The given data was invalid.');
        $response->assertJsonPath('errors.title.0', 'The title field must not be greater than 255 characters.');
    }
}
