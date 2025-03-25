<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
	function customers(){
        return $this->belongsToMany('App\Models\Audience');
    }

    function messages(){
        return $this->hasMany('App\Models\CampaignMessage', 'campaign_id', 'id');
    }
}