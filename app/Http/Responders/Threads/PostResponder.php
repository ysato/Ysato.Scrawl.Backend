<?php

declare(strict_types=1);

namespace App\Http\Responders\Threads;

use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;

class PostResponder
{
    public function __construct(private readonly ResponseFactory $factory)
    {
    }

    public function response(Thread $thread): JsonResponse
    {
        return $this->factory->json(
            [
                'id' => $thread->id,
                'user_id' => $thread->user_id,
                'title' => $thread->title,
                'is_closed' => $thread->is_closed,
                'created_at' => $thread->created_at->toIso8601String(),
                'last_scratch_created_at' => $thread->last_scratch_created_at?->toIso8601String(),
                'last_closed_at' => $thread->last_closed_at?->toIso8601String(),
            ],
            201,
            ['Location' => "/threads/{$thread->id}"]
        );
    }
}
