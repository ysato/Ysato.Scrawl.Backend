<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread;

use App\Http\Responders\Threads\Thread\GetResponder;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;

class GetAction
{
    public function __construct(private readonly GetResponder $responder)
    {
    }

    public function __invoke(Thread $thread): JsonResponse
    {
        $thread->load(['user', 'scratches']);

        return $this->responder->response($thread);
    }
}
