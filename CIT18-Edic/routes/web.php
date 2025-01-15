<?php

use Illuminate\Support\Facades\Route;
use App/Http/Controller/GreetController;

Route::get('/', function () {
    return view('welcome');
});

Route:get('/greetings',[GreetController::class, 'showGreeting';])