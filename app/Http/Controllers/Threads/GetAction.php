<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Http\Responders\Threads\GetResponder;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

class GetAction extends Controller
{
    public function __construct(private readonly GetResponder $responder)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $threads = $this->getThreadsWithRelations();

        return $this->responder->response($threads);
    }

    /** @return CursorPaginator<int, Thread> */
    private function getThreadsWithRelations(): CursorPaginator
    {
        return Thread::with('user')
            ->withCount('scratches')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(20);
    }
}
