<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Laravel\Scout\Searchable;

class Action extends Model
{
    

    public function type(){
        return $this->belongsTo('App\Models\ActionType');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }
    public function creator(){
        return $this->belongsTo('App\Models\User','creator_user_id');
    }

    public function email(){
        return $this->belongsTo('App\Models\Email','object_id','id');
    }

    public function getCustomerName(){
        $str = "Sin cliente";
        if(isset($this->customer))
            $str = $this->customer->name;
        return $str;
    }
    public function getTypeName(){
        $str = "Sin accion";
        if(isset($this->type))
            $str = $this->type->name;
        return $str;
    }
    public function getCreatorName(){
        $str = "Automatico";
        if(isset($this->creator))
            $str = $this->creator->name;
        return $str;
    }

    public function getEmailSubject(){
        $str = "NA";
        if(isset($this->email))
            $str = $this->email->subject;
        return $str;
            
    }

    public function getDescription(){
        $str = $this->note;
        if(isset($this->email))
            $str = $this->email->subject;
        return $str;
            
    }

    public static function saveAction($uid, $eid, $aid){
		$model = new Action;
        $model->customer_id = $uid;
        $model->object_id = $eid;
        $model->type_id = $aid;
        date_default_timezone_set('America/Bogota');
        $date = date('Y-m-d H:i:s');
        $model->delivery_date= $date;

        $model->save();
        
	}

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
}
