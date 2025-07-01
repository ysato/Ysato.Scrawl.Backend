<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\PostRequest;
use App\Http\Responders\Threads\PostResponder;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PostAction extends Controller
{
    public function __construct(private readonly PostResponder $responder)
    {
    }

    public function __invoke(PostRequest $request): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $thread = $this->createThread($user, $request);

        return $this->responder->response($thread);
    }

    private function createThread(User $user, PostRequest $request): Thread
    {
        return $user->threads()->create([
            'title' => $request->validated('title'),
            'is_closed' => false,
        ]);
    }
}
