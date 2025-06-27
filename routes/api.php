<?php

declare(strict_types=1);

use App\Http\Controllers\Threads;
use Illuminate\Support\Facades\Route;

Route::get('/threads', Threads\GetAction::class);
