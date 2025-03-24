<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Point;
use Mail;
use App\Customer;

class RouletteController extends Controller
{

    function play (Request $request){
        $model = Customer::find($request->uid);
        $model->scoring = $request->scoring;
        
        
        $model->save();
        

        return view('customers.roulette.play', compact('model'));           

    }
     
}