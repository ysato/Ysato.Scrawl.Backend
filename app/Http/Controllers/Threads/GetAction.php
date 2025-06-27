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
            'items' => $threads,
            'self' => null,
            'prev' => null,
            'next' => null,
        ];
    }
}