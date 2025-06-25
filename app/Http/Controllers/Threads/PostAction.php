<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\PostRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PostAction extends Controller
{
    public function __invoke(PostRequest $request): JsonResponse
    {
        $user = User::findOrFail(1);

        $thread = $user
            ->threads()
            ->create([
                'title' => $request->validated('title'),
            ]);

        return response()->json($thread, 201, [
            'Location' => "/threads/{$thread->id}",
        ]);
    }
}
