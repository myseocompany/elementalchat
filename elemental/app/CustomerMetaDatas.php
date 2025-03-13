<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerMetaDatas extends Model{
    protected $table = 'customer_metadatas';

    protected $fillable = [
        'customer_id',
        'customer_metadata_semantic_id',
        'value',
    ];
}