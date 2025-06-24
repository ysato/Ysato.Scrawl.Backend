<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\PostRequest;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;

class PostAction extends Controller
{
    public function __invoke(PostRequest $request): JsonResponse
    {
        $thread = Thread::create([
            'user_id' => 1,
            'title' => $request->validated()['title'],
            'is_closed' => false,
            'created_at' => now(),
            'last_scratch_created_at' => null,
            'last_closed_at' => null,
        ]);

        return response()->json($thread, 201, [
            'Location' => "/threads/{$thread->id}",
        ]);
    }
}
