<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Category;

class OrderTransaction extends Model{
    function payment(){
        return $this->belongsTo('App\Payment');
    }
}