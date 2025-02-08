<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\GreetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return 'Hello, Laravel';
});

Route::get('/greetings',[GreetController::class, 'showGreetings']);

Route::resource('tasks', TaskController::class);