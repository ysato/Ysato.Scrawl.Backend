<?php

declare(strict_types=1);

use App\Http\Controllers\Threads;
use App\Http\Controllers\Threads\Thread;
use App\Http\Controllers\Threads\Thread\Scratches;
use Illuminate\Support\Facades\Route;

// Threads API
Route::get('/threads', Threads\GetAction::class);
Route::post('/threads', Threads\PostAction::class);
Route::get('/threads/{thread}', Thread\GetAction::class);
Route::put('/threads/{thread}', Thread\PutAction::class);
Route::delete('/threads/{thread}', Thread\DeleteAction::class);

// Scratches API
Route::post('/threads/{thread}/scratches', Scratches\PostAction::class);
Route::put('/threads/{thread}/scratches/{scratch}', Scratches\PutAction::class);
