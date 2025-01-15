<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GreetController extends Controller
{
    public function showGreetings(){
        return view('greetings');
    }
}
