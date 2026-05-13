<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    function index(){
    
    }

    function show(){
        return view('event-detail');
    }

    function checkout(){
        return view('checkout');
    }

    function ticket(){
        return view('ticket');
    }
}
