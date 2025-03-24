<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{

	function customers(){
        return $this->belongsToMany('App\Customer');
    }
}