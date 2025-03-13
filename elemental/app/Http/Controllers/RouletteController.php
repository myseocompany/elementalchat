<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouletteController extends Controller
{
    public function index()
    {
        return view('roulette.index');
    }
}