<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitorStore extends Model
{
    protected $fillable = ['name', 'franchise_id', 'opened_year'];

    public function franchise()
    {
        return $this->belongsTo('App\Models\Franchise');
    }
}
