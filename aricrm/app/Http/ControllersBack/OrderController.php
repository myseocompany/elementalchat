<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Order;
use App\OrderStatus;
use App\User;
use App\Category;
use App\Customer;
use App\OrderTransaction;
use App\Product;
use App\ProductType;
use App\Config;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller{
    public function __construct(){   $this->middleware('auth'); }

	public function index(Request $request, $sid){
        $request->status_id = $sid;
        $model = $this->getModel($request);
        $group = $this->groupModel($request);
        $statuses = OrderStatus::find($sid);
        $users = User::all();
        $categories = Category::whereNotNull('parent_id')->get();

        return view('orders.index', compact('request', 'model','group', 'statuses', 'categories', 'users' ));
    }

    public function getModel($request){	
        $model = Order::join('products', 'products.id', 'product_id')

        	->where(
        		
                // Búsqueda por...
                function ($query) use ($request) {
                    
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('orders.status_id', $request->status_id);
                    if (isset($request->category_id)  && ($request->category_id != null))
                        $query = $query->where('category_id', $request->category_id);
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->where('user_id', $request->user_id);
                    
                    if (isset($request->search)  && ($request->search != null))
                    	$query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('products.name', "like", "%" . $request->search . "%");
                    
                        }
                    );
                
                }


            )
        	->select(DB::raw('cast(products.name as UNSIGNED) as int_name, name, registration, orders.status_id, user_id, category_id, orders.id, product_id, parking_id, deposit_id, customer_id, good_standing_certificate'))
            ->get();
        
        return $model;
    }

    public function groupModel($request){	
        $model = Order::rightJoin('product_statuses','product_statuses.id', 'status_id')
        	->where(
        		
                // Búsqueda por...
                function ($query) use ($request) {
                    
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('orders.status_id', $request->status_id);

                    if (isset($request->category_id)  && ($request->category_id != null))
                        $query = $query->where('category_id', $request->category_id);
                    if (isset($request->user_id)  && ($request->user_id != null))
                        $query = $query->where('user_id', $request->user_id);
                    
                    if (isset($request->search)  && ($request->search != null))
                    	$query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('orders.name', "like", "%" . $request->search . "%");
                    
                        }
                    );
                
                }

            )
            ->select(DB::raw('product_statuses.id as status_id, product_statuses.name, product_statuses.color, count(orders.id) as count'))
            ->groupBy('product_statuses.id')
            ->groupBy('product_statuses.name')
            ->groupBy('product_statuses.color')
            ->groupBy('weight')
			->orderBy('weight', 'ASC')
            ->get();
        
        return $model;
    }


    public function show($id){
        $model = Order::find($id);

        $debits = OrderTransaction::where('order_id', $id)
        	->where('debit',  '>', 0)
        	->get();

        $credits = OrderTransaction::where('order_id', $id)
        	->where('credit', '>', 0)
        	->get();
        
        $transactions = OrderTransaction::where('order_id', $id)
        	->orderby('date', 'asc')
        	->get();

        $products = Product::all();
        $product_types = ProductType::all();

        return view('orders.show', compact('model', 'debits', 'credits', 'transactions', 'products', 'product_types'));
    }


    public function quote($id){
        $model = Order::find($id);

        $debits = OrderTransaction::where('order_id', $id)
            ->where('debit',  '>', 0)
            ->get();

        $credits = OrderTransaction::where('order_id', $id)
            ->where('credit', '>', 0)
            ->get();
        
        $transactions = OrderTransaction::where('order_id', $id)
            ->orderby('date', 'asc')
            ->get();

        $products = Product::all();
        $product_types = ProductType::all();        


        return view('orders.quote', compact('model', 'debits', 'credits', 'transactions', 'products', 'product_types'));
    }




    public function store_payment_plan($id, Request $request){

        $model = new OrderTransaction;
        $model->date = $request->date;
        $model->description = $request->description;
        $model->internal_id = $request->internal_id;
        $model->debit = $request->debit;

        $model->save();

        return redirect('/orders/'.$id.'/show');
    }

    public function store_payment($id, Request $request){

        $model = new OrderTransaction;
        $model->date = $request->date;
        $model->description = $request->description;
        $model->internal_id = $request->internal_id;
        $model->credit = $request->credit;

        $model->save();

        return redirect('/orders/'.$id.'/show');
    }


    public function edit($id){
        $model = Order::find($id);

        $statuses = OrderStatus::all();
        //$types = OrderType::all();
        $categories = Category::whereNotNull('parent_id')->get();
        //$finish_status = [];
        $customers = Customer::orderBy('name', 'ASC')->get();

        $products = Product::where('type_id', "=", 1)->where('status_id', '=', 1)->get();
        $deposits = Product::where('type_id', "=", 2)->where('status_id', '=', 1)->get();
        $parkings = Product::whereIn('type_id', [3,4,5,6])->where('status_id', '=', 1)->get();
        

        //$customers = Customer::where('status_id','=', 8)->orderBy('name', 'ASC')->get();
        
        return view('orders.edit', compact('model','statuses', 'categories', 'customers', 'products', 'parkings', 'deposits'));
    }

   

   

    public function store_pagos(Request $request){
        return $request->all();
    }

    public function getProductTypes($parent_id){

    }
    public function create($cid){
        
        
       
    }

    public function store(Request $request)
    {
    	$sid = $request->status_id;

        $model = new Order;

        $model->status_id = $sid;
        $model->user_id = Auth::id();

        $model->subsidy = $request->subsidy;
        $model->subsidy_status = $request->subsidy_status;
        $model->subsidy_date = $request->subsidy_date;
        $model->balance_subsidy = $request->balance_subsidy;
        $model->initial_installment_subsidy = $request->initial_installment_subsidy;
        $model->initial_installment_percent = $request->initial_installment_percent;
        
        $model->initial_installment_subsidy = $request->initial_installment_subsidy;
        
        
        $model->down_payment = $request->down_payment;
        $model->savings_value = $request->savings;

        // precios digitados
        $model->price_black_work = $request->price_black_work;
        $model->price_semi_finished = $request->price_semi_finished;
        $model->price_fully_finished = $request->price_fully_finished;
        $model->deposit_price = $request->deposit_price;
        $model->parking_price = $request->parking_price;


        
        
        //Credit.
        $model->credit = $request->credit;
        $model->credit_status = $request->credit_status;
        $model->credit_value = $request->credit_value;

        //Otros
        $model->real_state_note = $request->real_state_note;
        $model->releases = $request->releases;
        $model->fiduciary_commission = $request->fiduciary_commission;

        //Titular
        $model->customer_id = $request->customer_id;
        //dd($request->parking_id);
        // product
        if(is_numeric($request->product_id) && ($request->product_id!="")){
        	if($sid==2){
	        	$this->changeStatusProduct($model->product_id, 1);
	        	$this->changeStatusProduct($request->product_id, 3);
        	}
        	$model->product_id = $request->product_id;
        	
        }else{
        	if($sid==2){
	        	$this->changeStatusProduct($model->product_id, 1);
        	}
        	$model->product_id = null;
        }
        $model->initial_installment = $request->initial_installment;
        $model->finishes_value = $request->finishes_value;
        $model->discount_interest = $request->discount_interest;

        
        //Adicionales
		if(is_numeric($request->parking_id) && ($request->parking_id!="")){
			if($sid==2){
	        
        	$this->changeStatusProduct($model->parking_id, 1);
        	$this->changeStatusProduct($request->parking_id, 3);
        	}
        	$model->parking_id = $request->parking_id;
		}else{
			if($sid==2){
	        
			$this->changeStatusProduct($model->parking_id, 1);
        	}
        	$model->parking_id = null;
        }
        if(is_numeric($request->deposit_id) && ($request->deposit_id!="")){
        	if($sid==2){
	        $this->changeStatusProduct($model->deposit_id, 1);
        	$this->changeStatusProduct($request->deposit_id, 3);
        	}
        	$model->deposit_id = $request->deposit_id;
        }else{
        	if($sid==2){
	        $this->changeStatusProduct($model->deposit_id, 1);
        	}
        	$model->deposit_id = null;
        }
        
        $model->notes = $request->notes;
        $model->save();
        //dd($model);
        
        $path = '/orders/sid/'.$model->status_id;
        //dd($path);
        return redirect($path);

        //return $request->all();
 
    }

    public function changeStatusProduct($pid, $sid){
    	$model = Product::find($pid);
    	if($model){
	    	$model->status_id = $sid;

	    	$model->save();
    	}
    }

    public function update($id, Request $request){
        $model = Order::find($id);
        //Subsidy:
        $model->subsidy = $request->subsidy;
        $model->subsidy_status = $request->subsidy_status;
        $model->subsidy_date = $request->subsidy_date;
        $model->subsidy_balance = $request->subsidy_balance;
        $model->subsidy_initial_installment = $request->subsidy_initial_installment;
        
        //Credit.
        $model->credit = $request->credit;
        $model->credit_status = $request->credit_status;
        $model->credit_value = $request->credit_value;

        //Otros
        $model->real_state_note = $request->real_state_note;
        $model->releases = $request->releases;
        $model->fiduciary_commission = $request->fiduciary_commission;

        //Titular
        $model->customer_id = $request->customer_id;
        //dd($request->parking_id);
        // product
        if(is_numeric($request->product_id) && ($request->product_id!="")){
        	$this->changeStatusProduct($model->product_id, 1);
        	$this->changeStatusProduct($request->product_id, 3);
        	$model->product_id = $request->product_id;
        	
        }else{
        	$this->changeStatusProduct($model->product_id, 1);
        	$model->product_id = null;
        }
        $model->initial_installment = $request->initial_installment;
        $model->finishes_value = $request->finishes_value;
        $model->discount_interest = $request->discount_interest;


        //Adicionales
		if(is_numeric($request->parking_id) && ($request->parking_id!="")){
        	$this->changeStatusProduct($model->parking_id, 1);
        	$this->changeStatusProduct($request->parking_id, 3);
        	$model->parking_id = $request->parking_id;
		}else{
			$this->changeStatusProduct($model->parking_id, 1);
        	$model->parking_id = null;
        }
        if(is_numeric($request->deposit_id) && ($request->deposit_id!="")){
        	$this->changeStatusProduct($model->deposit_id, 1);
        	$this->changeStatusProduct($request->deposit_id, 3);
        	
        	$model->deposit_id = $request->deposit_id;
        }else{
        	$this->changeStatusProduct($model->deposit_id, 1);
        	$model->deposit_id = null;
        }


        
        /*
        $model->name = $request->name;
        $model->registration = $request->registration;
        $model->category_id = $request->category_id;
        $model->user_id = $request->user_id;
        $model->built_area = $request->built_area;
        $model->status_id = $request->status_id;
        $model->VIS = $request->VIS;
        $model->private_area = $request->private_area;
        $model->price = $request->price;
        $model->height_over_price = $request->height_over_price;
        */

        $model->save();

        
        return redirect('/orders/'.$id.'/show');
    }
}