<?php

declare(strict_types=1);

namespace App\Http\Responders\Threads;

use App\Models\Thread;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

class GetResponder
{
    public function __construct(private readonly ResponseFactory $factory, private readonly Request $request)
    {
    }

    /** @param CursorPaginator<int, Thread> $threads */
    public function response(CursorPaginator $threads): JsonResponse
    {
        $self = null;
        if ($threads->cursor()) {
            $self = $this->request->fullUrlWithQuery(['cursor' => $threads->cursor()->encode()]);
        }

        $prev = null;
        if ($threads->previousCursor()) {
            $prev = $this->request->fullUrlWithQuery(['cursor' => $threads->previousCursor()->encode()]);
        }

        $next = null;
        if ($threads->nextCursor()) {
            $next = $this->request->fullUrlWithQuery(['cursor' => $threads->nextCursor()->encode()]);
        }

        return $this->factory->json([
            'items' => $threads->map(fn(Thread $thread) => [
                'id' => $thread->id,
                'user_id' => $thread->user_id,
                'title' => $thread->title,
                'is_closed' => $thread->is_closed,
                'created_at' => $thread->created_at->toIso8601String(),
                'last_scratch_created_at' => $thread->last_scratch_created_at?->toIso8601String(),
                'last_closed_at' => $thread->last_closed_at?->toIso8601String(),
                'scratches_count' => $thread->scratches_count,
                'user' => [
                    'id' => $thread->user->id,
                    'name' => $thread->user->name,
                ],
            ]),
            'self' => $self,
            'prev' => $prev,
            'next' => $next,
        ]);
    }
}
