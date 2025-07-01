<?php

declare(strict_types=1);

namespace App\Http\Responders\Threads\Thread;

use App\Models\Thread;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

class GetResponder
{
    public function __construct(private readonly ResponseFactory $factory)
    {
    }

    public function response(Thread $thread): JsonResponse
    {
        return $this->factory->json([
            'id' => $thread->id,
            'user_id' => $thread->user_id,
            'title' => $thread->title,
            'is_closed' => $thread->is_closed,
            'created_at' => $thread->created_at->toIso8601String(),
            'last_scratch_created_at' => $thread->last_scratch_created_at?->toIso8601String(),
            'last_closed_at' => $thread->last_closed_at?->toIso8601String(),
            'scratches' => $thread->scratches->map(function ($scratch) {
                return [
                    'id' => $scratch->id,
                    'thread_id' => $scratch->thread_id,
                    'content' => $scratch->content,
                    'created_at' => $scratch->created_at->toIso8601String(),
                    'updated_at' => $scratch->updated_at->toIso8601String(),
                ];
            }),
            'user' => [
                'id' => $thread->user->id,
                'name' => $thread->user->name,
            ],
        ]);
    }
}
