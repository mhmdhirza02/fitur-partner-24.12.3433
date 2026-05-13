<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index(){
        return view('admin.dashboard');
    }

    function indexEvent(){
        return view('admin.events');
    }

    function indexTransaction(){
        return view('admin.transactions');
    }
}
