<?php

declare(strict_types=1);

namespace Feature\Threads\Thread;

use Database\Seeders\ThreadTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\ValidatesOpenApiSpec;

class PutActionTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesOpenApiSpec;

    protected string $seeder = ThreadTestSeeder::class;

    public function testUpdatesThreadSuccessfully(): void
    {
        $requestData = [
            'title' => 'Updated Test Thread Title',
            'is_closed' => true,
        ];

        $response = $this->putJson('/threads/101', $requestData);

        $response->assertStatus(204);
        $this->assertDatabaseHas('threads', [
            'id' => 101,
            'title' => 'Updated Test Thread Title',
            'is_closed' => true,
        ]);
    }

    public function testReturns404WhenThreadNotFound(): void
    {
        $requestData = [
            'title' => 'Updated Title',
            'is_closed' => true,
        ];

        $response = $this->putJson('/threads/999', $requestData);

        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/problem+json');
    }

    public function testValidationFailsWhenTitleMissing(): void
    {
        $requestData = ['is_closed' => true];

        $response = $this->putJson('/threads/101', $requestData);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.title.0', 'The title field is required.');
    }

    public function testValidationFailsWhenIsClosedMissing(): void
    {
        $requestData = ['title' => 'Valid Title'];

        $response = $this->putJson('/threads/101', $requestData);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.is_closed.0', 'The is closed field is required.');
    }
}
