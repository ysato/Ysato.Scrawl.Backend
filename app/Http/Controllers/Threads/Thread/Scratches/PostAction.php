<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread\Scratches;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\Thread\Scratches\PostRequest;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;

class PostAction extends Controller
{
    public function __invoke(Thread $thread, PostRequest $request): JsonResponse
    {
        $scratch = $thread
            ->scratches()
            ->create($request->validated());

        return response()
            ->json($scratch, 201)
            ->header('Location', "/threads/{$thread->id}/scratches/{$scratch->id}");
    }
}
