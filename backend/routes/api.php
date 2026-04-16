<?php

use App\Http\Controllers\Api\HelloApiController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', [HelloApiController::class, 'hello']);
Route::get('/items', [HelloApiController::class, 'items']);
