<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\Thread\PutRequest;
use App\Models\Thread;
use Illuminate\Http\Response;

class PutAction extends Controller
{
    public function __invoke(Thread $thread, PutRequest $request): Response
    {
        $thread->update($request->validated());

        return response()->noContent();
    }
}
