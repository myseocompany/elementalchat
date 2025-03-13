<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Point;
use Mail;
use App\Customer;

class RouletteController extends Controller{
    function create(){
        return view('customers.roulette.create');
    }

    function store(Request $request){
        //    $count = $this->isEqual($request);
        $model = $this->getSimilar($request);
        
        if ($model->count() == 0) {
            $model = $this->storeFromRequest($request);
            return view('customers.roulette.play', compact('model'));  
    
        }else{
            $model = $model[0];
            return view('customers.roulette.create');
            }
    
        
        }

    function storeFromRequest(Request $request){


     /*  if($model = Customer::where(
            function ($query) use ($request) 
            {
             if (isset($request->phone)  && ($request->phone != null))
                $query = $query->orwhere('phone', $request->phone);
            if (isset($request->phone)  && ($request->phone != null))
                $query = $query->orwhere('phone2', $request->phone);

            if (isset($request->phone2)  && ($request->phone2 != null))
                $query = $query->orwhere('phone', $request->phone2);
            if (isset($request->phone2)  && ($request->phone2 != null))
                $query = $query->orwhere('phone2', $request->phone2);
            
            if (isset($request->email)  && ($request->email != null))
                    $query = $query->orwhere('email', $request->email);
            }) == null)
            {*/
                $model = new Customer;
                $model->name = $request->name;
                $model->document = $request->document;
                $model->position = $request->position;
                $model->business = $request->business;
                $model->phone = $request->phone;
                $model->phone2 = $request->phone2;
                $model->email = $request->email;
                $model->notes = $request->notes;
                $model->kpi = $request->kpi;
                $model->address = $request->address;
                $model->city = $request->city;
                $model->country = $request->country;
                $model->department = $request->department;
                $model->bought_products = $request->bought_products;
                if(isset($request->status_id)&& ($request->status_id!=null))
                    $model->status_id = $request->status_id;
                else
                    $model->status_id = 1;
                $model->user_id = $request->user_id;
                if(isset($request->source_id)&& ($request->source_id!=null))
                    $model->source_id = $request->source_id;
                else
                    $model->source_id = 7;

                $model->technical_visit = $request->technical_visit;
                $model->project_id = $request->project_id;
                $model->birthday = $request->birthday;
                //$model->first_installment_date = $request->first_installment_date;

                //datos de contacto
                $model->contact_name = $request->contact_name;
                $model->contact_phone2 = $request->contact_phone2;
                $model->contact_email = $request->contact_email;
                $model->contact_position = $request->contact_position;
                $model->scoring = $request->scoring;
                $model->score = $request->score;

                if(isset($request->meta_gender_id)&& ($this->isValidMeta($request->meta_gender_id)))
                    $model->meta_gender_id = $request->meta_gender_id;
                if(isset($request->meta_economic_activity_id)&& ($this->isValidMeta($request->meta_economic_activity_id)))
                    $model->meta_economic_activity_id = $request->meta_economic_activity_id;
                if(isset($request->meta_income_id)&& ($this->isValidMeta($request->meta_income_id)))
                    $model->meta_income_id = $request->meta_income_id;
                /*
                if(isset($request->meta_investment_id)&& ($this->isValidMeta($request->meta_investment_id)))
                    $model->meta_investment_id = $request->meta_investment_id;
                */

                

                if ($model->save()) {
                    /*
                    $this->storeMetaDataFromSelect($model, 1, $request->meta_house_mates_id);
                    $this->storeMetaDataFromSelect($model, 2, $request->meta_funding_source_id);
                    $this->storeMetaDataFromSelect($model, 3, $request->meta_final_fundig_source_id);
                    */

                    //$this->sendWelcomeMail($model);
                }
            /*}
            else
            {
                $model = Customer::find($request->cid);
                $model->scoring = $request->scoring;
                $model->scorr = $request->scorr;
                
                
                $model->save();
             
            }*/
        return $model;         
        
    }

    function play (Request $request){

        
        $model = Customer::find($request->cid);
        $model->scoring = $request->scoring;
        
        
        $model->save();
     

      return view('customers.roulette.play', compact('model'));           
      
    }
    public function getSimilar($request)
    {
        $model = Customer::where(
            // Búsqueda por...
            function ($query) use ($request) 
            {
             /*   if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone', $request->phone);
                if (isset($request->phone)  && ($request->phone != null))
                    $query = $query->orwhere('phone2', $request->phone);

                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone', $request->phone2);
                if (isset($request->phone2)  && ($request->phone2 != null))
                    $query = $query->orwhere('phone2', $request->phone2);

                if (isset($request->email)  && ($request->email != null))
                    $query = $query->orwhere('email', $request->email);
            */
                if (isset($request->score)  && ($request->score != null))
                    $query = $query->orwhere('score', $request->score);
            
            }
        )
            ->get();
        return $model;
    }

    public function  retailPlay()
    {
        return view('customers.roulette.retail');
    }

    public function  igPlay(){

        $json = '{
            "version": "v2",
            "content": {
              "messages": [
                {
                  "type": "text",
                  "text": "simple text with button",
                  "buttons": [
                    {
                      "type": "url",
                      "caption": "External link",
                      "url": "https://manychat.com"
                    }
                  ]
                }
              ],
              "actions": [],
              "quick_replies": []
            }
          }';
        $str = json_decode($json);
        return response()->json($str);
    }

  
}