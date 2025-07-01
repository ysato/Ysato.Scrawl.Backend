<?php

declare(strict_types=1);

use App\Http\Controllers\Threads;
use Illuminate\Support\Facades\Route;

Route::get('/threads', Threads\GetAction::class);
Route::get('/threads/{thread}', Threads\Thread\GetAction::class);
Route::post('/threads', Threads\PostAction::class)->middleware('auth:sanctum');
