<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class GetAction extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Refactor: コードエクセレンス - 可読性と保守性向上
        $threads = $this->getThreadsWithRelations();

        return response()->json($this->buildPaginatedResponse($threads));
    }

    /**
     * @return Collection<int, Thread>
     */
    private function getThreadsWithRelations(): Collection
    {
        return Thread::with('user')
            ->withCount('scratches')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param Collection<int, Thread> $threads
     * @return array<string, mixed>
     */
    private function buildPaginatedResponse(Collection $threads): array
    {
        return [
            'items' => $threads->map(fn(Thread $thread) => $this->transformToThreadListItem($thread)),
            'self' => null,
            'prev' => null,
            'next' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transformToThreadListItem(Thread $thread): array
    {
        $user = $thread->user;
        assert($user !== null);

        return [
            'id' => $thread->id,
            'title' => $thread->title,
            'is_closed' => $thread->is_closed,
            'user_id' => $thread->user_id,
            'created_at' => $thread->created_at->toIso8601String(),
            'last_scratch_created_at' => $thread->last_scratch_created_at?->toIso8601String(),
            'last_closed_at' => $thread->last_closed_at?->toIso8601String(),
            'scratches_count' => $thread->scratches_count,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ];
    }
}