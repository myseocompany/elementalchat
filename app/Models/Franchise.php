<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    protected $fillable = ['name', 'color'];

    public function competitorStores()
    {
        return $this->hasMany('App\Models\CompetitorStore');
    }
}
