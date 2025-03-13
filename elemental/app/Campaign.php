<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
	function customers(){
        return $this->belongsToMany('App\Audience');
    }
}