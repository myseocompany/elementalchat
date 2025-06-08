<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSnapshot extends Model
{
    protected $table = 'products_snapshots';

    protected $fillable = [
        'reference', 'name', 'price', 'quantity', 'created_at'
    ];

    public $timestamps = false; // Usamos manualmente el timestamp en `created_at`
}
