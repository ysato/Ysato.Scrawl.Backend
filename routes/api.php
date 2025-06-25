<?php

declare(strict_types=1);

use App\Http\Controllers\Threads\GetAction;
use App\Http\Controllers\Threads\PostAction;
use App\Http\Controllers\Threads\Thread\DeleteAction;
use App\Http\Controllers\Threads\Thread\GetAction as ThreadGetAction;
use App\Http\Controllers\Threads\Thread\PutAction;
use Illuminate\Support\Facades\Route;

// Threads API
Route::get('/threads', GetAction::class);
Route::post('/threads', PostAction::class);
Route::get('/threads/{thread}', ThreadGetAction::class);
Route::put('/threads/{thread}', PutAction::class);
Route::delete('/threads/{thread}', DeleteAction::class);
