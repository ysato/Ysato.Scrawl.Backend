<?php

use App\Http\Controllers\Threads\GetAction;
use Illuminate\Support\Facades\Route;

// Threads API
Route::get('/threads', GetAction::class);