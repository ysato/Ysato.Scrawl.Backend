<?php

declare(strict_types=1);

namespace Feature\Threads;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Ysato\Catalyst\ValidatesOpenApiSpec;

use function assert;
use function is_int;
use function is_string;
use function str_repeat;

class PostActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    private const int MAX_TITLE_LENGTH = 255;

    public function testCreatesThreadSuccessfully(): void
    {
        $requestData = ['title' => 'Test Thread Title'];

        $response = $this->postJson('/threads', $requestData);

        $response->assertStatus(201);
        $response->assertJsonPath('title', 'Test Thread Title');
        $response->assertJsonPath('user_id', 1);
        $response->assertJsonPath('is_closed', false);
        $threadId = $response->json('id');
        assert(is_string($threadId) || is_int($threadId));
        $response->assertHeader('Location', "/threads/$threadId");

        $this->assertDatabaseHas('threads', [
            'title' => 'Test Thread Title',
            'user_id' => 1,
            'is_closed' => false,
        ]);
    }

    public function testValidationFailsWhenTitleMissing(): void
    {
        $response = $this
            ->withoutRequestValidation()
            ->postJson('/threads', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title' => ['The title field is required.']]);
    }

    public function testValidationFailsWhenTitleTooLong(): void
    {
        $requestData = ['title' => str_repeat('a', self::MAX_TITLE_LENGTH + 1)];

        $response = $this
            ->withoutRequestValidation()
            ->postJson('/threads', $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(
            ['title' => ['The title field must not be greater than 255 characters.']],
        );
    }

    public function testValidationFailsWhenTitleNotString(): void
    {
        $requestData = ['title' => 123];

        $response = $this
            ->withoutRequestValidation()
            ->postJson('/threads', $requestData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title' => ['The title field must be a string.']]);
    }
}
