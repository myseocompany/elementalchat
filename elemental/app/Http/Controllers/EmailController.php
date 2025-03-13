<?php

// TRUJILLO

namespace App\Http\Controllers;

use DB;
use Mail;
use File;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Email;
use App\Customer;
use Carbon\Carbon;
use App\EmailQueue;
use App\Action;
use App\SendWithData;
use App\User;
use App\Audience;

use Illuminate\Mail\Mailable;

class EmailController extends Controller
{
	public function index() {        
	    $model = Email::orderBy("id", "DESC")->
            where('type_id', 1)->get();
	    return view('emails.index', compact('model'));
	}

	public function destroy($id) {
	    $model = Email::find($id);
        $model->delete();

	    return redirect('/emmails');
	}

	public function show($id) {
	    $model = Email::find($id);

	    $actions = Action::where('object_id', '=', $id)
	        ->get();

	    return view('emails.show', compact('model', 'actions'));
	}

	public function edit($id)	{
	    $model = Email::find($id);
        $audiences = Audience::where('type_id', 1)->get();

	    return view('emails.edit', compact('model', 'audiences'));
	}

	public function update(Request $request){    
	    $model =  Email::find($request->id);
	    $count=0;
	    
	    $model->sended_at = Carbon::now();
	    $model->subject= $request->subject;
	    if(isset($request->scheduled_at) && ($request->scheduled_at!=""))
	        $model->scheduled_at = $request->scheduled_at;
	    $model->content=$request->content;
	    $model->view = $model->getFileRouter($model);
        $model->audience_id = $request->audience_id;
        $model->from_email = "saladeventas@trujillogutierrez.com.co";
        $model->from_name = "Constructora Trujillo Gutierrez";
        

        //dd($model);


	    if($request->save != null){
	    //id 1 para mensajes en estado borrador
	        $status = 1;
	    
	    }else if($request->programming != null){
	    //id 2 para mensajes en estado programado
	        $status = 2;
	        
	    }else if($request->send != null){
	    //id 3 para mensajes en estado enviado
	        $status = 3;        
	    }
	    
	    $this->saveView($model);

	    $model->save();   
	    return redirect('/emails/'.$model->id.'/show');
	}

	public function create() {
	    $model = Email::orderBy("id", "DESC")->get();
        $audiences = Audience::where('type_id', 1)->get();
	    return view('emails.create', compact('model', 'audiences'));
	}

	public function storeEmail(Request $request) {
	    
	    $model = new \App\Email();
	    $count=0;
	    
	    $model->sended_at = Carbon::now();
	    $model->subject= $request->subject;
	    $model->scheduled_at = Carbon::now();
	    $model->content=$request->content;
        $model->audience_id = $request->audience_id;
	    $model->view="emails.";

	    if($request->save != null){
	    //id 1 para mensajes en estado borrador
	        $status = 1;
	    
	    }else if($request->programming != null){
	    //id 2 para mensajes en estado programado
	        $status = 2;
	        
	    }else if($request->send != null){
	    //id 3 para mensajes en estado enviado
	        $status = 3;            
	    }
	    $model->save();

	    /*
	    $model->view="emails.".$model->id;
	    $model->save();
	    */
	    $this->saveView($model);
	    
	    $model->view = $model->getFileRouter($model);
	    $model->save();   
	    
	    return redirect('/emails');
	}
    
    public function storeAudience($id){
        $email = Email::find($id);
        $model=$email->audience->customers;
        
        $count = 1;
        if(!is_null($email->sended_at))
			$sended_at=$email->sended_at;
		else
			$sended_at=0;

        if($model)
            foreach($model as $item){
                Email::addEmailQueue($email, $item, $count*10,$sended_at);
                $count++;
            }
        return redirect('/emails/'.$id.'/show');
    }

    public function getCustomersZeroActions(){ 
        $model = DB::table('actions')
            ->select('customers.id', 'project_id', DB::raw('COUNT(actions.id)'))
            ->rightJoin('customers', 'customers.id', '=', 'actions.customer_id')          
            ->whereNotNull('email')
            ->where('email', '<>', 'NA')  
            ->where('email', '<>', 'SIN CORREO ELECTRÓNICO')
            ->groupBy('customers.id', 'project_id')
            ->having( DB::raw('COUNT(actions.id)'), '=', '0')
            ->get();
        dd($model);  


        foreach($model as $item){
            Email::addEmailQueue($email, $item, $count*10,$sended_at);
            $count++;
            if(project_id==1)
                $sended_at=1;
            if(project_id==2)
                $sended_at=3;
        }  
    }

    public function getCustomersByStatus($id){

        // $model = DB::table('customers')
        //     ->select('customers.id', 'name', 'email', DB::raw('COUNT(actions.id)'))
        //     ->leftJoin('actions', 'customers.id','=','customer_id')
        //     ->whereNotNull('email')
        //     ->where('email','<>','')
        //     ->where('email','<>',' ')
        //     ->where('status_id','=',$id)
        //     ->groupBy('customers.id')
        //     ->groupBy('name')
        //     ->groupBy('email')
        //     ->having(DB::raw('COUNT(actions.id)'),"=",'0')
        //     ->get();

        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->where('status_id','=',$id)
            ->get();

            return $model;
    }

    public function getCustomersByCountry($country){
        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->whereRaw('LOWER(`country`) LIKE ? ',[trim(strtolower($country)).'%'])
            ->get();

            return $model;
    }
    
    public function getCustomersByCity($city){
        $model = DB::table('customers')
            ->select('customers.id', 'name', 'email')
            ->whereNotNull('email')
            ->where('email','<>','')
            ->where('email','<>',' ')
            ->where('email','<>','null@null.es')
            ->where('email','<>','null@null.com')
            ->where('email','<>','NUll@nul.es')
            ->where('email','<>','notiene@gmail.com')
            ->where('email','<>','nodio@gmail.com')
            ->where('email','<>','noenvio@gmail.com')
            ->where('email','<>','No@valido.com')
            ->where('email','<>','No@valido.com')
            ->whereRaw('LOWER(`city`) LIKE ? ',[trim(strtolower($city)).'%'])
            ->get();
            return $model;
    }

    public function getCustomersBySource($src){
    	$model = DB::table('customers')
    	   ->select('customers.id', 'name', 'email')
    	   ->where('source_id','=',$src)
    	   ->get();

    	   return $model;
    }

    public function getDelay($seconds){
        $current_time = Carbon::now(-5);

        return $current_time->addSeconds($seconds);
    }

    public function send(){

        $model = EmailQueue::
            where('available_at','<',Carbon::now())
            ->where('email','<>', '')
            ->where('email','<>', ' ')
            ->whereNotNull('email')
            //->whereNull('status_id')
            ->get();
        
        //dd($model);

        $max = 10;
        $count = 0;
        foreach($model as $item){
			//dd($item->email);
            if($count<$max){
               
                if (filter_var($item->email, FILTER_VALIDATE_EMAIL)) {

                    $count = Email::sendUserEmail($item->user_id, $item->subject, $item->view, $item->email_id); 
                    //dd($count); 
                    $cid = $item->user_id;
                    $eid = $item->email_id;

                    if($count==0){
                        Action::saveAction($cid, $eid, 2);
                    }else{
                        Action::saveAction($cid, $eid, 5);
                    }         
                    $this->destroyEmailQueue($item->id);
                    echo "<div>Enviado ".$item->email."</div>";
                }else{
                    echo "<div style='color:red'>No Enviado ".$item->email."</div>";
                    $this->destroyEmailQueue($item->id);

                }
            }
            
        }
    }

    public function destroyEmailQueue($id){
        $model = EmailQueue::find($id);
        //$model->status_id = 1; // borrado
        //$model->save();
        $model->delete();

    }

    public function sendUserEmail($cid, $subject, $view, $eid){
        //dd($cui);
        $model = Customer::find($cid);


        if($model){

            $email = Email::find($eid);

            $emailcontent = array (
                'subject' => $subject,
                'emailmessage' => 'Este es el contenido',
                'customer_id' => $cid,
                'email_id' => $eid,
                'name' => $model->name,
                'view' => $view,
                'to' => $model->email,
                'from_email' => $email->from_email,
                'from_name' => $email->from_name,
                'model' => $model,


             );
            Mail::to($model->email)->send(new SendWithData($emailcontent));
	/*
	            Mail::send($view, $emailcontent, function ($message) use ($model, $subject){
	                    $message->subject($subject);

	                    $message->to($model->email);
	            });
	 */           
            if(count(Mail::failures())>0){
                Action::saveAction($cid, $eid, 5);
            }else{
                Action::saveAction($cid, $eid, 2);
            }
         
        }  
    }
        public function saveView($model){

        
        
        $html = $model->content;
        $unregister = '<p style="font-size:11px!important;margin:0;line-height:120%"> Si desea cancelar su suscripción por favor haga <a href="'.env('APP_DOMAIN').'customers/{!! $customer_id !!}/unsubscribe" style="color:#333333;text-decoration:underline" rel="nofollow" target="_blank">clic aquí</a>. </p>';
        $rand = md5(uniqid(rand(), true));
        $track = env('APP_DOMAIN').'customers/{!! $customer_id !!}/actions/trackEmail/{!! $email_id !!}/'.$rand;
        $pixel = '<img id="{!! $customer_id !!}" src="'.$track.'" >';
        //$pixel = ':<img src="'.$track.'" >:';
        
        //$pixel = '<img id="imgPixel" style="display:none;" src="'.env('APP_DOMAIN').'customers/actions/trackEmail?track={!! $email_id !!}" width="0" height="0">';
        
        //$pixel .= '<a href="'.env('APP_DOMAIN').'customers/{!! $customer_id !!}/actions/trackEmail/{!! $email_id !!}" >segui</a>';         


        $html .= $unregister;
        $html .= $pixel;
        $url = $model->getPathToView($model);

        //dd($url);
        
        File::put($url, $html);

    }   
    public function testMail(){
        $data = "Datos";
        $message = "Prueba";
        $user = User::find(7);


        $to_address = "nicolas@myseocompany.co";
        $subject = "This goes in the subject line of the email!";
        $message = "This is the body of the email.\n\n";
        $message .= "More body: probably a variable.\n";
        $headers = "From: trujillo@quirky.com.co\r\n";
        mail("$to_address","$subject","$message","$headers");
        echo "Mail Sent.";

        
        
        Mail::raw("Contenido mail", function ($message) use ($user){

            $message->subject('test quirky');

            $message->to('nicolas@myseocompany.co');

        });
        
        
    }

    public function store(Request $request)
    {
        //
        $model = new \App\Email();
        $count=0;
        
        $model->sended_at = Carbon::now();
        $model->subject= $request->subject;
        $model->scheduled_at = Carbon::now();
        $model->content=$request->content;
        $model->from_email = "saladeventas@trujillogutierrez.com.co";
        $model->from_name = "Constructora Trujillo Gutierrez";
        $model->view="emails.";

        if($request->save != null){
        //id 1 para mensajes en estado borrador
            $status = 1;
        
        }else if($request->programming != null){
        //id 2 para mensajes en estado programado
            $status = 2;
            
        }else if($request->send != null){
        //id 3 para mensajes en estado enviado
            $status = 3;
            
        }
        $model->type_id = 1; // plantilla de envios
        $model->save();

        /*
        $model->view="emails.".$model->id;
        $model->save();
        */
        $this->saveView($model);
        
        $model->view = $model->getFileRouter($model);
        $model->save();   
        
        return redirect('/emails');
    }

}