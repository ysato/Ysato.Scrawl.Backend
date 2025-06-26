<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread\Scratches;

use App\Http\Controllers\Controller;
use App\Http\Requests\Threads\Thread\Scratches\PutRequest;
use App\Models\Scratch;
use App\Models\Thread;
use Illuminate\Http\Response;

class PutAction extends Controller
{
    public function __invoke(Thread $thread, Scratch $scratch, PutRequest $request): Response
    {
        $scratch->update($request->validated());

        return response()->noContent();
    }
}
