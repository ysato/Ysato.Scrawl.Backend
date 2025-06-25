<?php

declare(strict_types=1);

use App\Http\Controllers\Threads\GetAction;
use App\Http\Controllers\Threads\PostAction;
use App\Http\Controllers\Threads\Thread\PutAction;
use Illuminate\Support\Facades\Route;

// Threads API
Route::get('/threads', GetAction::class);
Route::post('/threads', PostAction::class);
Route::put('/threads/{thread}', PutAction::class);
