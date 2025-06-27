<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostAction extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->validateRequest($request);

        $user = $request->user();
        assert($user instanceof User);
        $thread = $this->createThread($user, $request);

        return $this->buildCreatedResponse($thread);
    }

    private function createThread(User $user, Request $request): Thread
    {
        return $user->threads()->create([
            'title' => $request->input('title'),
            'is_closed' => false,
        ]);
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
    }

    private function buildCreatedResponse(Thread $thread): JsonResponse
    {
        return response()->json([
            'id' => $thread->id,
            'title' => $thread->title,
            'is_closed' => $thread->is_closed,
            'created_at' => $thread->created_at->toIso8601String(),
            'user_id' => $thread->user_id,
            'last_scratch_created_at' => $thread->last_scratch_created_at?->toIso8601String(),
            'last_closed_at' => $thread->last_closed_at?->toIso8601String(),
        ], 201)
            ->header('Location', "/threads/{$thread->id}");
    }
}
