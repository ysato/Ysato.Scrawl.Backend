<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAction extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = Thread::with('user')
            ->orderBy('created_at', 'desc');

        // ページネーション
        $threads = $query->paginate(
            perPage: $request->integer('per_page', 20),
            page: $request->integer('page', 1)
        );

        return response()->json([
            'data' => $threads->items(),
            'meta' => [
                'current_page' => $threads->currentPage(),
                'per_page' => $threads->perPage(),
                'total' => $threads->total(),
                'last_page' => $threads->lastPage(),
            ],
            'links' => [
                'first' => $threads->url(1),
                'last' => $threads->url($threads->lastPage()),
                'prev' => $threads->previousPageUrl(),
                'next' => $threads->nextPageUrl(),
            ],
        ]);
    }
}