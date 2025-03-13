<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use DB;
use App\Product;
use App\ProductType;
use App\ProductStatus;
use App\Category;


class ProductController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

	public function index(Request $request){
        if(!isset($request->type_id))
            $request->type_id == 1;

        $model = $this->getModel($request);
        $group = $this->groupModel($request);
        $statuses = ProductStatus::all();
        $types = ProductType::where(function($query)use($request){
            if(isset($request->category_id))
                $query->where('category_id', $request->category_id);
        })->orderBy('weight', 'ASC')->orderBy('category_id', 'ASC')->get();
        $root_types = ProductType::orderBy('weight', 'ASC')->whereNull('parent_id')->get();
        
        $categories = Category::all();

        
        //dd($request->type_id );
        return view('products.index', compact('request', 'model','group', 'statuses', 'categories', 'types', 'root_types' ));
    }

    public function getModel($request){	

        $model = Product::where(
        		
                // Búsqueda por...
                function ($query) use ($request) {
                    if (isset($request->from_date)  && ($request->from_date != null)){

                        $query = $query->whereBetween('products.updated_at', [$request->from_date, $request->to_date." 23:59:59"]);
                    }
                    
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('products.status_id', $request->status_id);
                    
                    if (isset($request->category_id)  && ($request->category_id != null)){
                        //dd( $request->category_id );
                        if($request->category_id==1){
                            $query->whereIn('category_id', [1, 2, 3]);
                            
                        }
                        elseif($request->category_id==4)
                            $query->whereIn('category_id', [4, 5, 6]);
                        else
                            $query = $query->where('category_id', $request->category_id);
                    }
                    if (isset($request->type_id)  && ($request->type_id != null)){
                        if($request->type_id==11 || $request->type_id==10)
                            $query = $query->whereIn('type_id', ProductType::getChilds( ProductType::getId($request->type_id) )); 
                        else   
                            $query = $query->where('type_id', $request->type_id);
                    }
                    
                    if (isset($request->keyword)  && ($request->keyword != null)){

                    	$query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery->orwhere('products.name', "like", "%$request->keyword%");
                            $innerQuery->orwhere('products.registration', "like", "%$request->keyword%");
                    
                        }
                        );
                    }
                
                }


            )
        	->select(DB::raw('id, cast(products.name as UNSIGNED) as int_name, name, registration, status_id, price, type_id, updated_at, category_id'))
            ->orderBy('name', 'ASC')
            ->get();
        
        return $model;
    }

    public function groupModel($request){	
        $model = Product::rightJoin('product_statuses','product_statuses.id', 'status_id')
        	->where(
        		
                // Búsqueda por...
                function ($query) use ($request) {
                    
                    if (isset($request->status_id)  && ($request->status_id != null))
                        $query = $query->where('products.status_id', $request->status_id);

                    if (isset($request->category_id)  && ($request->category_id != null))
                        if($request->category_id==1)
                            $query->whereIn('category_id', [2, 3]);
                        elseif($request->category_id==4)
                            $query->whereIn('category_id', [5, 6]);
                        else
                            $query = $query->where('category_id', $request->category_id);


                    if (isset($request->type_id)  && ($request->type_id != null))
                        if($request->type_id==11 || $request->type_id==10)
                            $query->whereIn('type_id', ProductType::getChilds( ProductType::getId($request->type_id) )); 
                        else   
                            $query->where('type_id', $request->type_id);
                    
                    if (isset($request->search)  && ($request->search != null))
                    	$query = $query->where(
                        function ($innerQuery) use ($request) {
                            $innerQuery = $innerQuery->orwhere('products.name', "like", "%" . $request->search . "%");
                    
                        }
                    );
                
                }

            )
            ->select(DB::raw('product_statuses.id as status_id, product_statuses.name, product_statuses.color, count(products.id) as count, sum(products.price) as price'))
            ->groupBy('product_statuses.id')
            ->groupBy('product_statuses.name')
            ->groupBy('product_statuses.color')
            ->groupBy('weight')
			->orderBy('weight', 'ASC')
            ->get();
        
        return $model;
    }


    public function show($id){
        $model = Product::find($id);
        // dd($model);
        $statuses = ProductStatus::all();
        $types = ProductType::all();
        $categories = Category::whereNotNull('parent_id')->get();

        return view('products.show', compact('model','statuses', 'categories', 'types' ));
    }

    public function edit($id){
        $model = Product::find($id);

        $statuses = ProductStatus::all();
        $types = ProductType::all();
        $categories = Category::all();

        return view('products.edit', compact('model','statuses', 'categories', 'types' ));
    }

    public function update($id, Request $request){
    	$model = Product::find($id);
        
        $model->location = $request->location;
        $model->name = $request->name;
        $model->registration = $request->registration;
        $model->category_id = $request->category_id;
        $model->type_id = $request->type_id;
        $model->VIS = $request->VIS;
        $model->built_area = $request->built_area;
        $model->status_id = $request->status_id;
        $model->private_area = $request->private_area;
        $model->price = $request->price;
        $model->finishes = $request->finishes;
        $model->price_semi_finished = $request->price_semi_finished;
        $model->price_fully_finished = $request->price_fully_finished;
        $model->price_black_work = $request->price_black_work;
        $model->notes = $request->notes;
        $model->height_over_price = $request->height_over_price;

        $model->save();

        
        return redirect('/products/'.$id.'/show');
    }

    public function create(){
        
        $statuses = ProductStatus::all();
        $types = ProductType::all();
        $categories = Category::whereNotNull('parent_id')->get();

        return view('products.create', compact('statuses', 'categories', 'types' ));
    }

    public function store(Request $request){

        $model = new Product;
        $model->location = $request->location;
        $model->name = $request->name;
        $model->registration = $request->registration;
        
        if(intval($request->category_id))
            $model->category_id = $request->category_id;
        
        if(intval($request->type_id))
            $model->type_id = $request->type_id;
        $model->VIS = $request->VIS;
        $model->built_area = $request->built_area;
        if(intval($request->status_id))
            $model->status_id = $request->status_id;
        $model->private_area = $request->private_area;
        $model->price = $request->price;
        $model->finishes = $request->finishes;
        $model->price_semi_finished = $request->price_semi_finished;
        $model->price_fully_finished = $request->price_fully_finished;
        $model->price_black_work = $request->price_black_work;
        $model->notes = $request->notes;
        $model->height_over_price = $request->height_over_price;

        $model->save();

        
        return redirect('/products/'.$model->id.'/show');
    }

    /// importador
    public function import(){
        
        $statuses = ProductStatus::all();
        $types = ProductType::all();
        $categories = Category::whereNotNull('parent_id')->get();

        return view('products.import', compact('statuses', 'categories', 'types' ));
    }


    public function uploadFile($request){
        $data = "";
        $path = "";
        if($request->hasFile('file')){
            
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $data = array_slice($data, 1);


            /*
            dd($data);

            $file     = $request->file('file');
            $path = $file->getClientOriginalName();

            $destinationPath = 'public/imports/'.$request->customer_id;
            
            $res = $file->move($destinationPath,$path);

            */
            
        }  

        return $data;
    }

    function str_getcsv($n){
        return $n;
    }

    public function getIdFromName($name, $table){
        $id = 0;
        $model = DB::table($table)->where('name', '=', $name)->first();
        if($model)
            $id = $model->id;
        else{
            echo "<h4 style='color:red'>No existe :'".$name."'' en la tabla ".$table."</h4>";
        }

        return $id;
    }


    public function getIdFromNameAndCategory($name, $cid, $table){
        $id = 0;
        $model = DB::table($table)->where('name', '=', $name)
                ->where('category_id', '=', $cid)
                ->first();
        if($model)
            $id = $model->id;
        else{
            echo "<h4 style='color:red'>No existe :".$name." ".$table."</h4>";
        }

        return $id;
    }



    public function createFromArray($array){


        
        $NAME = 0;
        $PRICE = 1;
        $STATUS = 2;
        $CATEGORY = 3;
        $TYPE = 4;
        $REGITRATION = 5;
        $BUILT_AREA = 6;
        $PRIVATE_AREA = 7;
        $FINAL_PRICE_FULL_FINISHES = 8;
        $FINAL_PRICE_COMBO1 = 9;
        $FINAL_PRICE_BLACK_WORK = 10;
        $SET_CATEGORY = 11;
        $LOCATION = 12;
        $NOTES = 13;


        $category_id = $this->getIdFromName($array[$CATEGORY], 'categories');
        $status_id = $this->getIdFromName($array[$STATUS],'product_statuses');
        //dd($array[$STATUS]);
        $type_id = 0;
        if(!(isset($array[$SET_CATEGORY]) && ($array[$SET_CATEGORY]!="")))
            $type_id = $this->getIdFromName($array[$TYPE], 'product_types');
        else
            $type_id = $this->getIdFromNameAndCategory($array[$TYPE], $category_id, 'product_types');

        $name = "";
        if(isset($array[$NAME]))
            $name = $array[$NAME];

                // busco el producto para ver si existe
        $model = Product::where('name', '=', $name)
            ->where('category_id', '=', $category_id)
            ->where(function($query) use ($array, $SET_CATEGORY, $type_id ){
                if(!(isset($array[$SET_CATEGORY]) && ($array[$SET_CATEGORY]!="")))
                    $query->where('type_id', '=', $type_id);

            })
            //
            ->first();

        //dd($model);

        if(!$model){
            $model = new Product;
            $model->name = $name;
        }

        //****** ASIGNO VALORES AL ARRAY ****/

        
        if(intval($status_id))
            $model->status_id = $status_id;
        if(intval($category_id))
            $model->category_id = $category_id;
        if(intval($type_id))
            $model->type_id = $type_id;
        

        //$model->price = 0;
        if(isset($array[$PRICE])&&$array[$PRICE]!="" ) 
            $model->price = $array[$PRICE];
        
        $model->registration = "";
        if(isset($array[$REGITRATION]))
            $model->registration = $array[$REGITRATION];

        //$model->built_area = 0;
        if(isset($array[$BUILT_AREA])&& ($array[$BUILT_AREA]!=""))
            $model->built_area = $array[$BUILT_AREA];

        //$model->private_area = 0;
        if(isset($array[$PRIVATE_AREA]) && $array[$PRIVATE_AREA]!="")
            $model->private_area = $array[$PRIVATE_AREA];

        //$model->price_black_work = 0;
        if(isset($array[$FINAL_PRICE_BLACK_WORK]) && $array[$FINAL_PRICE_BLACK_WORK] !="" )
            $model->price_black_work = $array[$FINAL_PRICE_BLACK_WORK];

        //$model->price_semi_finished = 0;
        if(isset($array[$FINAL_PRICE_COMBO1])&&$array[$FINAL_PRICE_COMBO1]!="")
            $model->price_semi_finished = $array[$FINAL_PRICE_COMBO1];

        //$model->price_fully_finished = 0;
        if(isset($array[$FINAL_PRICE_FULL_FINISHES])&&$array[$FINAL_PRICE_FULL_FINISHES]!="" )
            $model->price_fully_finished = $array[$FINAL_PRICE_FULL_FINISHES];

        //$model->price_fully_finished = 0;
        if(isset($array[$LOCATION])&&$array[$LOCATION]!="" )
            $model->location = $array[$LOCATION];

        //$model->price_fully_finished = 0;
        if(isset($array[$NOTES])&&$array[$NOTES]!="" )
            $model->notes = $array[$NOTES];

        //dd($model);
        if(!is_null($model->category_id))
            $model->save();

    }

    public function bulkStore(Request $request){

        $data = $this->uploadFile($request);
        $cont = 1;
        foreach($data as $array){
            echo "<br>"+$cont + ". ";
            $cont++;
            $this->createFromArray($array);
        }

        //return redirect('/products/');
    }
}