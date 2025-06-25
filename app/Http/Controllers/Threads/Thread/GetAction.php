<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads\Thread;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

class GetAction extends Controller
{
    public function __invoke(Thread $thread): JsonResponse
    {
        $thread->load(['user', 'scratches' => function (HasMany $query): void {
            $query->orderBy('created_at', 'asc');
        }]);

        return response()->json($thread);
    }
}