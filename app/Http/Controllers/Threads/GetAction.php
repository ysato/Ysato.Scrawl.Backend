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
        $threads = Thread::with('user')
            ->withCount('scratches')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(perPage: 10);

        $self = null;
        if ($threads->cursor()) {
            $self = $request->fullUrlWithQuery(['cursor' => $threads->cursor()->encode()]);
        }

        $prev = null;
        if ($threads->previousCursor()) {
            $prev = $request->fullUrlWithQuery(['cursor' => $threads->previousCursor()->encode()]);
        }

        $next = null;
        if ($threads->nextCursor()) {
            $next = $request->fullUrlWithQuery(['cursor' => $threads->nextCursor()->encode()]);
        }

        return response()->json([
            'self' => $self,
            'prev' => $prev,
            'next' => $next,
            'items' => $threads->items(),
        ]);
    }
}
