<?php

use App\Http\Controllers\Blade\HelloBladeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/blade/hello');
});

Route::get('/blade/hello', [HelloBladeController::class, 'hello']);
