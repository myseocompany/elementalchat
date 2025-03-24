<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Category;
use App\OrderProduct;


class Order extends Model
{
    protected $fillable = [
        'order_id', 'customer_id', 'product_id', 'invoice_id', 'quantity', 'updated_user_id',
        'price', 'shippingCharges', 'shipperCode', 'IVA', 'IVAReturn', 
        'status_id', 'user_id', 'referal_user_id', 
        'authorizationResult', 'authorizationCode', 'errorCode', 
        'errorMessage', 'phone', 'added_at', 'notes', 'delivery_date', 
        'delivery_name', 'delivery_email', 'delivery_address', 
        'delivery_phone', 'delivery_to', 'delivery_from', 'delivery_message', 
        'payment_form', 'payment_id', 'session_id', 'created_at', 'updated_at', 'user_ip', 'user_agent',
        'request_url', 'request_data', 'unique_machine'
    ];
    

    public static function boot()
    {
        
        parent::boot();

        static::updating(function ($order) {
            $orderHistoryData = $order->getOriginal();
            $orderHistoryData['order_id'] = $orderHistoryData['id'];
            unset($orderHistoryData['id']);

            OrderHistory::create($orderHistoryData);
        });
        
    }

    

	function customer(){
        return $this->belongsTo('App\Customer');
    }
    function User(){
        return $this->belongsTo('App\User')->where('status_id', 1);
        
    }

    function updatedUser(){
        return $this->belongsTo('App\User', 'updated_user_id');
        
    }

    function Referal(){
        return $this->belongsTo('App\User')->where('status_id', 3);
        
    }

    function referal_user(){
        return $this->belongsTo('App\User');
        
    }

	function OrderStatus(){
        return $this->belongsTo('App\OrderStatus');
    }
    function payment(){
        return $this->belongsTo('App\Payment');
    }

    function status(){
        return $this->belongsTo('App\OrderStatus');
    }

    function products(){
    	return $this->belongsToMany('App\Product', 'order_products');	
    }

    function productList(){
    	return $this->hasMany('App\OrderProduct');	
    }

    function transactions(){
    	return $this->hasMany('App\OrderTransaction');	
    }

    function getTotal(){
    	$total = 0;


        if(isset($this->productList)){
    		foreach ($this->productList as $item) {

                $total+= ((100-$item->discount)/100) * $item->price * $item->quantity;
    			
    		}
    	}

    	return $total;
    }

    function countItems(){
    	$total = 0;


        if(isset($this->productList)){
    		foreach ($this->productList as $item) {

    			$total += 1 ;
    		}
    	}

    	return $total;
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id');
    }

}




