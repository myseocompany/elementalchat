<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MekanoProduct extends Model
{
    protected $fillable = [
        'name',
        'reference',
        'price',
        'discount',
        'quantity',
        'category_id',
        'category_code',
        'type_id',
        'status_id',
        'location',
        'notes',
        'image_url'
    ];

    public function status()
    {
        return $this->belongsTo('App\Models\ProductStatus');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function getURL()
    {
        return str_replace("%", "_", str_replace(" ", "-", $this->name)). ".png";
    }

    public function getPrice()
    {
        $price = $this->price;
        return '$ ' . number_format($price, 0, ',', '.');
    }
}
