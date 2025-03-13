<?php

// trujillo

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Audience;
use App\User;
use App\Customer;
use App\AudienceCustomer;
use App\CustomerStatus;
use App\CustomerSource;
use App\Campaign;
use App\CampaignMessage;
use Carbon;
use DB;

class AudienceController extends Controller{

	public function __construct(){   $this->middleware('auth'); }
	
	public function index(Request $request){
		$model = Audience::all();
		$campaigns = Campaign::all();
		return view('audiences.index', compact('model','campaigns'));
	}
 
	public function create(Request $request){
		$model = $this->filterModel($request);

		return view('audiences.create', compact('model', 'request'));
	}

	public function filterModel(Request $request){   
        
        $dates = $this->getDates($request);

        //        $model = Customer::wherein('customers.status_id', $statuses)
        $model = Customer::
            leftJoin('actions', 'actions.customer_id', 'customers.id')
            ->leftJoin('action_types', 'actions.type_id', 'action_types.id')
            ->select('customers.id','customers.status_id', 'customers.user_id','customers.source_id', 'customers.created_at', 'customers.updated_at',
        'customers.name','customers.phone','customers.email', DB::raw('(count(if(outbound=0, actions.created_at, null))) / 
(now()-max(if(outbound=0, actions.created_at, null)))   as kpi'), DB::raw('max(if(outbound=0, actions.created_at, null)) as last_inbound_date'))
            ->where(
            // Búsqueda por...
            function ($query) use ($request, $dates) {
                //if(isset($request->from_date) && ($request->from_date!=null)){
                        
                    if ( (isset($request->created_updated) &&  ($request->created_updated=="updated")) ) 
                        $query->whereBetween('customers.updated_at', $dates);
                    else
                        $query->whereBetween('customers.created_at', $dates);
                    
                
                if (isset($request->user_id)  && ($request->user_id != null))
                    $query->where('user_id', $request->user_id);
                if (isset($request->source_id)  && ($request->source_id != null))
                    $query->where('source_id', $request->source_id);
                
                if (isset($request->kpi)  && ($request->kpi != null))
                    $query->where('kpi', $request->kpi);
                if (isset($request->status_id)  && ($request->status_id != null))
                    $query->where('customers.status_id', $request->status_id);
                
                if(isset($request->search)){
                    $query = $query->where(
                        function ($query) use ($request) {
                            
                            $search = html_entity_decode($request->search );
                            
                            $query->orwhere('customers.name', "like", "%" . $search . "%");
                            $query->orwhere('customers.email',   "like", "%" . $search . "%");
                            $query->orwhere('customers.document', "like", "%" . $search . "%");
                            $query->orwhere('customers.position', "like", "%" . $search . "%");
                            $query->orwhere('customers.business', "like", "%" . $search . "%");
                            $query->orwhere('customers.phone',   "like", "%" . $search . "%");
                            $query->orwhere('customers.phone2',   "like", "%" . $search . "%");
                            $query->orwhere('customers.notes',   "like", "%" . $search . "%");
                            $query->orwhere('customers.city',    "like", "%" . $search . "%");
                            $query->orwhere('customers.country', "like", "%" . $search . "%");
                            $query->orwhere('customers.bought_products', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_name', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_phone2', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_email', "like", "%" . $search . "%");
                            $query->orwhere('customers.contact_position', "like", "%" . $search . "%");                            
                        }
                    );
                }                
            }
        )   ->groupBy('customers.id','customers.status_id', 'customers.email', 
        'customers.name','customers.phone','customers.source_id','customers.user_id','customers.created_at', 'customers.updated_at' )
            ->orderBy('customers.status_id', 'asc')
            ->orderByRaw('(count(if(outbound=0, actions.created_at, null))) / 
(now()-max(if(outbound=0, actions.created_at, null))) DESC')
            ->orderBy('customers.created_at', 'desc')
            ->get();

        return $model;
    }

    public function getDates($request){
        $to_date = Carbon\Carbon::today()->subDays(0); // ayer
        $from_date = Carbon\Carbon::today()->subDays(0);

        if(isset($request->from_date) && ($request->from_date!=null)){
                        
            $from_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->from_date);
            $to_date = Carbon\Carbon::createFromFormat('Y-m-d', $request->to_date);
        }

        $to_date = $to_date->format('Y-m-d')." 23:59:59";
        $from_date = $from_date->format('Y-m-d');
        $data = array($from_date, $to_date);

        //dd($data);
        return $data; 
    }

    public function store(Request $request){
    	$model = new Audience;

    	$model->name = $request->name;
    	$model->save();

    	return redirect('/audiences/'.$model->id."/customers");
    }
	
	public function getUsers(){
        return  User::orderBy('name')
            ->where('users.status_id', 1)
            ->get();
    }
    public function createCustomers($id, Request $request){

    	$audience = Audience::find($id);

		$model = $this->filterModel($request);

        $str = "";
        foreach ($request->all() as $key => $value)
        	$str .= $key."=".$value."&";
        $str = substr($str, 0, -1);
        $str = str_replace("&all=true", "", $str);

        $model->queryString = $str;

		$customer_options = CustomerStatus::all();
		$users = $this->getUsers();
        $sources = CustomerSource::all();
        
		return view('audiences.create_customers', compact('audience', 'id', 'model', 'request', 'customer_options', 'users', 'sources' ));
    }

    function storeCustomers($id, Request $request){
    	// si los tiene todos seleccionados
    	//dd($request->customer_id);
    	// guardo todo los registros
    	$data = Array();
    	foreach ($request->customer_id as $key => $value) {
    		$model = AudienceCustomer::where('customer_id', $value)
    			->where('audience_id', $id)
    			->first();
    		if(!$model)
    			$data[] = array("audience_id"=>$id, "customer_id"=>$value);    			
    	}

    	AudienceCustomer::insert($data);
    	
    	return redirect('/audiences/'.$id."/customers");
    	
    }

    function destroyCustomer($aid, $cid){
		$model = AudienceCustomer::where('customer_id', $cid)
			->where('audience_id', $aid)
			->first();
		$model->delete();

    	return redirect('/audiences/'.$aid."/customers");
    }

    public function show($id, Request $request){
        $audience = Audience::find($id);        
        $model = Customer::select("customers.id as id", "phone", "phone2", "name")
            //->where('status_id', '=', 1)
            ->join('audience_customer','customers.id', 'audience_customer.customer_id')
            ->where('audience_id',$id)
            
            ->orderBy('customers.created_at', 'DESC')
            ->whereNull('sended_at')
            ->paginate(500);
        
        $customer_options = CustomerStatus::all();
        $users = $this->getUsers();
        $sources = CustomerSource::all();
        $request->audience_id = $id;

        $statuses = $this->getStatuses($request, 1);

        $customersGroup = $this->countFilterCustomers($request, $statuses); 

        return view('audiences.show', compact('model','audience', 'id','request', 'customer_options', 'users', 'sources', 'customersGroup'));

    }

    public function showRpa($id, $cid){
        $campaignMessage = CampaignMessage::where('campaign_id',$cid)->get();
        $campaign = Campaign::find($cid);
        $audience = Audience::find($id);
        $model = Customer::select("customers.id as id", "phone", "phone2", "name", "project_id")
            //->where('status_id', '=', 1)
            ->where('audience_id',$id)
            ->join('audience_customer','customers.id', 'audience_customer.customer_id')
            ->orderBy('customers.created_at', 'DESC')
            ->whereNull('sended_at')
            ->paginate(5000);
        return view('audiences.show_rpa', compact('model','audience','campaign', 'campaignMessage'));
    }

            public function getStatuses(Request $request, $step){
            $statuses ="";
            if(isset($request->from_date)||($request->from_date!="") )
                $statuses = $this->getAllStatusID($step);
            else
                $statuses = $this->getStatusID($request, $step);
            return $statuses;

        }
    function getAllStatusID($stage_id){

    $res = array();
    $model = CustomerStatus::where('stage_id', $stage_id)->orderBy("weight", "ASC")->get();
       //$model = CustomerStatus::all();

    foreach($model as $item)
        $res[] = $item->id;
    return $res;
}


    function getStatusID($request, $stage_id){
        $url = $request->fullurl();
        $paramenters = explode("&", $url);
        $res = array();
        foreach($paramenters as $key=>$value)
        {
            if(strpos($value, "status_id")!==false && (str_replace("status_id=", "", $value)!=0)){
                $res[] = str_replace("status_id=", "", $value);
            }
        }
        if(!count($res)){

           $model = CustomerStatus::where("stage_id", $stage_id)
           ->orderBy("weight", "ASC")
           ->get();
           //$model = CustomerStatus::all();

           foreach($model as $item)
            $res[] = $item->id;

    }

    return $res;
}

    public function countFilterCustomers($request, $statuses){


            //$customersGroup = Customer::all();
            
            $customersGroup = Customer::wherein('customers.status_id', $statuses)
            ->rightJoin("audience_customer", 'audience_customer.customer_id', '=', 'customers.id')
            ->join("customer_statuses", "customer_statuses.id", "customers.status_id")
            ->where("audience_customer.audience_id", $request->audience_id)
            ->select(DB::raw('customers.status_id as status_id, count(customers.id) as count'))
            ->groupBy('status_id')
            ->groupBy('weight')

            ->orderBy('weight','ASC')

            ->get();

            foreach ($customersGroup as $item){
                $included = false;
                foreach ($statuses as $status => $value) {
                    if($value == $item->status_id){
                       $included = true;
                   }
               }
               if($included){
                $item->color = CustomerStatus::getColor($item->status_id);
                $item->name = CustomerStatus::getName($item->status_id);
                $item->id = $item->status_id;
            }
        

        }
        

        return $customersGroup;
    }



    public function whatsapp($id, $cid){
        $campaign = Campaign::find($cid);
        $campaignMessage = CampaignMessage::where('campaign_id',$cid)->get();
        $audience = Audience::find($id);        
        $model = Customer::select("customers.id as id", "phone", "phone2", "name")
            //->where('status_id', '=', 1)
            ->where('audience_id',$id)
            ->join('audience_customer','customers.id', 'audience_customer.customer_id')
            ->orderBy('customers.created_at', 'DESC')
            ->whereNull('sended_at')
            ->paginate(50);
        //dd($model);   
        
        //var_dump($model);

        return view('audiences.whatsapp', compact('model','audience','campaign','campaignMessage'));

    }

}