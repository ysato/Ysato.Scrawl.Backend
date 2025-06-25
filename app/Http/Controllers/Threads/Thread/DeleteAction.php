<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Http\Response;

class DeleteAction extends Controller
{
    public function __invoke(Thread $thread): Response
    {
        $thread->delete();

        return response()->noContent();
    }
}