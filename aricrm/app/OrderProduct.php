<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Category;

class OrderProduct extends Model{

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'discount',
        'sale_type_id',
        'source_id',
        'customer_frequency_id',
        'total',
    ];
    
    function actions(){
        return $this->hasMany('App\Action');
    }

    function products(){
    	return $this->hasMany('App\Product');	
    }

    function product(){
    	return $this->belongsTo('App\Product');	
    }
    function user(){
    	return $this->belongsTo('App\User');	
    }
    function saleType(){
    	return $this->belongsTo('App\SaleType');	
    }

    function order(){
    	return $this->belongsTo('App\Order');
    }
}