<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Khill\Lavacharts\Lavacharts;
use DB;
use App\Task;

use App\User;
use App\Project;
use App\ProjectStatus;
use App\TaskStatus;
use Carbon\Carbon;
use App\CustomerStatus;
use App\ActionType;
use App\Action;
use App\Customer;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    function getStartAndEndDate($week, $year)
    {

        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7*$week)-$day)*24*3600;
        $return[0] = date('Y-m-d', $time);
        $time += 6*24*3600;
        $return[1] = date('Y-m-d', $time);
        //dd($return);
        return $return;
    }
    public function index2()
    {
        ///, week(updated_at) as week
        $tasks = \DB::table('tasks')
                     ->select(DB::raw('week(due_date) as week ,  count(*) as pr'))
                     ->where('status_id', '<>', 2)
                     ->groupBy('week')
                     ->get();


        $data = \Lava::DataTable();


        $data
            ->addDateColumn('Year')
            ->addNumberColumn('Points');
        
        
        foreach($tasks as $item){
           $data->addRow([
                $this->getStartAndEndDate($item->week, 2017)[0], intval($item->pr)
            ]);
        }
                   
                   

        \Lava::AreaChart('data', $data, [
            'title' => 'Data Growth',
            'legend' => [
                'position' => 'in'
            ]
        ]);


        return view('reports.index');
    }

    public function index(){


            $model = \DB::table('tasks')
                     ->select(DB::raw('year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->groupBy('year', 'week')
                     ->get();
            $users = Task::
                     select(DB::raw('user_id, year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->groupBy('year', 'week','user_id')
                     ->get();
        foreach ($users as $item){
            
            $item->name = User::getName($item->user_id);
        }

        return view('reports.index', compact('model','users'));
    
    }


    public function weeksByTeam(Request $request){
        $users = User::where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2 ])
                     ->get();
        $weeks = 1;
        $to_date = Carbon::today()->subDays(0); // ayer
        $from_date = Carbon::today()->subDays(7*4);

        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date);
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date);
        }

        $weeks = $from_date->diffInWeeks($to_date);
        
        $weeks_array = array();
        
        for( $i=0; $i<$weeks; $i++ ){
            $weeks_array[] = 
                Array($from_date->format('Y-m-d'), $from_date->addDays(7)->format('Y-m-d'));
        }

        $status_array = Array(3, 6, 56, 57);
        $data = array();

        // ->where('user_id', $user->id)
                 
        for($i=0; $i<$weeks; $i++ ){
            $tasks = DB::table('tasks')
                 ->whereIn('status_id', $status_array)
                 ->whereBetween('due_date', $weeks_array[$i])
                 ->sum('points');
            $data[] = $tasks;
        }
    
        //dd($weeks_array[0]);
        
            
        $controller = $this;

        return view('reports.weeks_by_team', compact( 
            'controller', 'request', 'users', 'weeks' ,'from_date', 'to_date', 'data', 'weeks_array'));
    
    }

    

    public function userCustomerStatus(Request $request){
        // obtengo los usuarios activos
        $dates_array = $this->getDates($request);

        $users = User::where('status_id', '=', 1)
                    ->whereIn('id', [3,6,7,9])
                     ->get();
 
        $data = array();
        $customer_statuses = CustomerStatus::where('stage_id',1)->orderBy('weight', 'ASC')->get();
        foreach ($users as $user){
            $user_data = array();

            foreach($customer_statuses as $status){
                $model = DB::table('customers')
                     ->whereBetween('updated_at', $dates_array)
                     ->where('user_id', $user->id)
                     ->where('status_id', $status->id)
                     ->count('id');
                    $user_data[] = $model;
                    //dd($from_date);
            }
            
        
            $data[] = $user_data;

        }

      
        
            
        $controller = $this;

        return view('reports.user_status', compact( 
            'controller', 'request', 'users' , 'data', 'customer_statuses'));
    
    }

    public function getTimeArray($dates_array){

        $from = $dates_array[0];
        $to = $dates_array[1];
        $from  = Carbon::createFromFormat('Y-m-d', $from);
        $to  = Carbon::createFromFormat('Y-m-d H:i:s', $to);
        $span = $from->diffInMonths($to);

        $time_array = array();
        
        for( $i=0; $i<$span; $i++ ){
            $time_array[] = 
                Array($from->format('Y-m-d'), $from->addMonths(1)->format('Y-m-d'));
        }
        return $time_array;       
    }
    

    public function getUsersFromDates($date_array){
        $users_id = Action::distinct()->select('creator_user_id')
            ->whereBetween('created_at', $date_array)
            ->whereNotNull('creator_user_id')
            ->get();


        $users_id = $this->elocuentToArray2($users_id);


        $users = User::where('status_id', '=', 1)
                    ->whereIn('id', $users_id)
                     ->get();
        return $users;
    }
    public function customersTime(Request $request){
        // obtengo los usuarios activos
        $dates_array = $this->getDatesMontly($request);



        $customer_statuses = Customer::distinct()->select('status_id')
            ->whereBetween('created_at', $dates_array)
            ->get();

        $time_array = $this->getTimeArray($dates_array);

        $customer_statuses = $this->elocuentToArrayStatus($customer_statuses);
        
        $users = $this->getUsersFromDates($dates_array);
 
        $data = array();
        $customer_statuses = CustomerStatus::where('stage_id',1)->orderBy('weight', 'ASC')->get();
        
        //dd($time_array);
        foreach ($time_array as $time){
            $user_data = array();

            foreach($customer_statuses as $status){
                $model = DB::table('customers')
                     ->whereBetween('created_at', $time)
                     ->where('status_id', $status->id)
                     ->count('id');
                    $user_data[] = $model;
                    
            }
            
        
            $data[] = $user_data;

        }

      
        
            
        $controller = $this;

        return view('reports.customers_time', compact( 
            'controller', 'request', 'users', 'time_array' , 'data', 'customer_statuses'));
    
    }

    function elocuentToArray($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->type_id;

        }
        return $array;
    }

    function elocuentToArray2($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->creator_user_id;

        }
        return $array;
    }

    function elocuentToArrayStatus($model){
        $array = Array();
        foreach ($model as $item) {
            $array[] = $item->status_id;

        }
        return $array;
    }

    public function getDates($request){
        $to_date = Carbon::today()->subDays(0); // ayer
        $from_date = Carbon::today()->subDays(7);


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date." 00:00:00");
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
        }

        $date_array = 
            Array($from_date->format('Y-m-d'), $to_date->addHours(23)->addMinutes(59)->addSeconds(59)->format('Y-m-d H:i:s'));
        return $date_array;
    }

    public function getDatesMontly($request){
        $to_date = Carbon::today()->subMonths(0); // ayer
        $from_date = Carbon::today()->subMonths(3);


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->to_date." 00:00:00");
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
        }

        $date_array = 
            Array($from_date->format('Y-m-d'), $to_date->addHours(23)->addMinutes(59)->addSeconds(59)->format('Y-m-d H:i:s'));
        return $date_array;
    }


    public function userCustomerActions(Request $request){
        // obtengo los usuarios activos
        

        
        $date_array = $this->getDates($request);

        
    
        $users_id = Action::distinct()->select('creator_user_id')
            ->whereBetween('created_at', $date_array)
            ->whereNotNull('creator_user_id')
            ->get();


        $users_id = $this->elocuentToArray2($users_id);


        $users = User::where('status_id', '=', 1)
                    ->whereIn('id', $users_id)
                     ->get();
 

        $data = array();


        $types_id = Action::distinct()->select('type_id')
            ->whereBetween('created_at', $date_array)
            ->whereNotNull('creator_user_id')
            ->get();

        $types_id = $this->elocuentToArray($types_id);
        
        $action_types = ActionType::whereIn('id', $types_id)
            ->get();
        
        
        foreach ($users as $user){
            $user_data = array();

            foreach($action_types as $type){
                $model = DB::table('actions')
                     ->whereBetween('created_at', $date_array)
                     ->where('creator_user_id', $user->id)
                     ->where('type_id', $type->id)
                     ->count('id');
                    $user_data[] = $model;
                    //dd($from_date);
            }
            
        
            $data[] = $user_data;

        }

        //dd($date_array[0]);
        
            
        $controller = $this;

        return view('reports.user_action', compact( 
            'controller', 'request', 'users', 'data', 'date_array', 'action_types'));
    
    }


    public function daysByUser(Request $request){
        // obtengo los usuarios activos
        $users = User::where('status_id', '=', 1)
                    ->whereIn('role_id', [1,2 ])
                     ->get();
        $days = 1;
        $to_date = Carbon::today()->subDays(0); // ayer
        $from_date = Carbon::today()->subDays(1);


        if(isset($request->from_date) && ($request->from_date!=null)){
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date);
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $request->from_date." 00:00:00");
        }

        
        $days = $from_date->diffInDays($to_date);

        if($days < 1){
            $days = 1;
        }
        
        $days_array = array();
        
        for( $i=0; $i<$days; $i++ ){
            $from = $from_date;
            $from->addDays(1);    

            $days_array[] = Array(
                $from->format('Y-m-d'), 
                $from->addHours(23)->addMinutes(59)->addSeconds(59)->format('Y-m-d H:i:s'));
        }

        $status_array = Array(3, 6, 56, 57);
        $data = array();

        
        foreach ($users as $user){
            $user_data = array();

            for($i=0; $i<$days; $i++ ){
                $tasks = DB::table('tasks')
                     ->whereIn('status_id', $status_array)
                     ->whereBetween('due_date', $days_array[$i])
                     ->where('user_id', $user->id)
                     ->sum('points');
                $user_data[] = $tasks;
            }
            $data[] = $user_data;

        }

        //dd($weeks_array[0]);
        
            
        $controller = $this;

        return view('reports.days_by_user', compact( 
            'controller', 'request', 'users', 'days' ,'from_date', 'to_date', 'data', 'days_array'));
    
    }

    //Manuel 2018-10-31
    public function projectsTaskByStatuses(Request $request){
        $user_id = 1;
        if(isset($request->user_id)){
            $user_id = $request->user_id;
            
        }
        $projects = Project::where("status_id", "=", "3")->orderBy("name", "asc")->get();
        $task_statuses = TaskStatus::all();
        $users = User::all();
        $model = Task::
                     select(DB::raw('user_id, year(due_date) as year, week(due_date) as week ,  sum(points) as sum_points'))
                     ->where('status_id', '=', 3)
                     ->groupBy('year', 'week','user_id')
                     ->orderBy('year', 'desc')
                    ->orderBy('week', 'desc')
                 
                     ->get();
        foreach ($model as $item){
            
            $item->name = User::getName($item->user_id);
        }
        $graph = $model;
            
        $controller = $this;

        return view('reports.projects', compact('model','request', 'graph','controller','users','projects','task_statuses'));
    
    }


}
