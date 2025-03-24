<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Action;
use Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Namu\WireChat\Traits\Chatable;

class Customer extends Authenticatable
{
    use Chatable;


    // Custom logic for allowing chat creation
    public function canCreateChats(): bool
    {
        return true;
    }


    /**
     * Accessor Returns the URL for the user's cover image (used as an avatar).
     * Customize this based on your avatar field.
     */
    public function getCoverUrlAttribute(): ?string
    {
        // $image = null;
        // if($this->image_url)
        //     $image = $this->image_url;
        //return $this->image_url??null;  // Adjust 'avatar_url' to your field
        return asset('storage/' . $this->image_url)??null;
    }

 
    public function searchChatables(string $query): Collection
    {

        /* users = User::where('name', 'LIKE', "%{$query}%")
        ->limit(20)
        ->get(); */

        $customers = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get();

        // Merge them into one collection
        //return $users->merge($customers);
        return $customers;
        
    }
        
    
    protected $fillable = [
        'name',
        'document',
        'position',
        'area_code',
        'phone',
        'phone2',
        'contact_phone2',
        'email',
        'address',
        'city',
        'country',
        'department',
        'business',
        'business_document',
        'business_phone',
        'business_area_code',
        'business_email',
        'business_address',
        'business_city',
        'image_url'
        
        
    ];
    function references(){
        return $this->hasMany('App\Models\Reference');
    }

 


    function actions(){
        return $this->hasMany('App\Models\Action');
    }

    function customer_files(){
        return $this->hasMany('App\Models\CustomerFile');
    }

    function files(){
        return $this->hasMany('App\Models\CustomerFile');
    }

    public function status(){
    	return $this->belongsTo('App\Models\CustomerStatus');
    }

    function user(){
        return $this->belongsTo('App\Models\User');
    }

    function updated_user(){
        return $this->belongsTo('App\Models\User', 'updated_user_id', 'id');
    }

      function source(){
        return $this->belongsTo('App\Models\CustomerSource', 'source_id' , 'id');
    }

    function product(){
        return $this->belongsTo('App\Models\Product');
    }

    // function employee_files(){
    // 	return $this->hasMany('App\Models\EmployeeFile');
    // }

    public function searchableAs(){
        return 'employee_id';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }

    public function countActions(){
        $count = Action::where('customer_id','=',$this->id)->count();

        return $count;
    }

    public function countInActions(){
        $count = Action::join('action_types', 'action_types.id', 'type_id')
            ->where('outbound', '=', 0)
            ->where('customer_id','=',$this->id)
            
            ->count();

        return $count;
    }

    public function countOutActions(){
        $count = Action::leftJoin('action_types', 'action_types.id', 'type_id')
            ->where('customer_id','=',$this->id)
            ->where('outbound', '=', 1)
            ->whereNotNull('creator_user_id')
            ->count();

        return $count;
    }

    public function getId(){
        
        return $this->id;
    }

    public function createdDays(){
        $created = new Carbon\Carbon($this->created_at);
        $now = Carbon\Carbon::now();
        $difference = ($created->diff($now)->days < 1)
            ? 'hoy'
            : $created->diffInDays($now) . ' dias';
        return $difference;
    }





    public function phoneAsCode($phone){
        
        if(strlen($phone)>10)
            return true;
        else
            return false;
    }
    
    public function getPhoneWith57($phone){
        if(strlen($phone)>10)
            return $phone;
        elseif( strlen($phone) == 10 )
            return "57".$phone;
        else
            return "";
    }
    
    public function getPhoneStr(){
        $phone = "";
        if(isset($this->phone))
            $phone = $this->phone;
        elseif(isset($this->phone2))
            $phone = $this->phone2;
        return $phone;
    }

    public function cleanPhone($phone){
        $newPhone = $phone;
        $str = substr($phone, 0, 3);
        if(substr($phone, 0, 3) == "p:+")
            $newPhone = substr($phone, 3, strlen($phone));
        if(substr($phone, 0, 1) == "+")
            $newPhone = substr($phone, 1, strlen($phone));

        $newPhone =str_replace(' ', '', $newPhone);
        return $newPhone;
    }

    public function hasAValidPhone(){
        
        $phone = $this->cleanPhone($this->getPhoneStr());
        if($this->phoneAsCode($phone)){
            /*
            $number = substr($phone, -10);
            $ind = str_replace($number, "", $phone);
            if ($ind =='+57' || $ind=="57" || $ind == "54")
                return true;
            else
                return false;
            */
                //echo $phone."*";
                return true;

        }else{
            //echo $phone."_";
            if( $phone=="" || strlen($phone) < 10)
                return false;
            else
                return true; 
        }     
    }

    public function getPhone(){
        $phone = "";
        $phone =  $this->getPhoneWith57($this->cleanPhone($this->getPhoneStr()) ) ;
        return $phone;
    }

    public function getScoringToNumber(){
        $pos = 0;
        $score = array('d', 'c', 'b', 'a');
        if(isset($this->scoring_profile) && ($this->scoring_profile!=""))
        $pos = array_search($this->scoring_profile, $score)+1;
        return $pos;
    }




    public function getPhoneUS(){
        $phone = "";
        $phone =  $this->getPhoneWith1($this->cleanPhoneUS($this->getPhoneStrUS()) ) ;
        return $phone;
    }

    public function getPhoneWith1($phone){
        if(strlen($phone)>10)
            return $phone;
        elseif( strlen($phone) == 10 )
            return "1".$phone;
        else
            return "";
    }
    
    public function cleanPhoneUS($phone){
        $newPhone = $phone;
        $str = substr($phone, 0, 3);
        if(substr($phone, 0, 3) == "p:+")
            $newPhone = substr($phone, 3, strlen($phone));
        if(substr($phone, 0, 1) == "+")
            $newPhone = substr($phone, 1, strlen($phone));

        $newPhone =str_replace(' ', '', $newPhone);
        return $newPhone;
    }

    public function getPhoneStrUS(){
        $phone = "";
        if(isset($this->phone))
            $phone = $this->phone;
        elseif(isset($this->phone2))
            $phone = $this->phone2;
        return $phone;


    }

    public function getLastUserAction(){
        $model = Action::where("customer_id", $this->id)->orderBy('created_at', 'desc')->first();;

        return $model;
    }

    public function isBanned(){
        $model = Action::where('type_id', 31)
                        ->where('customer_id',"=", $this->id)->first();
        $is_banned = false;
        if($model)
            $is_banned = true;
        return $is_banned;
    }


    public function getName() {
        if (!empty($this->name)) {
            return $this->name;
        } elseif (!empty($this->business)) {
            return $this->business;
        } else {
            return 'Sin nombre'; 
        }

    }
    

    public function getInitials()
    {

        $str = $this->getName();

        $words = explode(' ', $str);
        $initials = '';

        if (count($words) > 0 && strlen($words[0]) > 0) {
            $initials .= $words[0][0]; // Asegura que la primera palabra no esté vacía
        }

        if (count($words) > 1 && strlen($words[1]) > 0) {
            $initials .= $words[1][0]; // Asegura que la segunda palabra no esté vacía
        }

        return strtoupper($initials); // Devuelve las iniciales en mayúsculas
    }



    public function getStatusColor()
    {
        $str = "'#000000'";
        if(isset($this->status))
            $str = $this->status->color;
        // Suponiendo que el color está almacenado en una propiedad `color` del estado relacionado
        return $str; // Devuelve un color predeterminado si no hay estado
    }

    /**
     * Devuelve el número de teléfono ajustado, asegurando consistencia en el prefijo.
     *
     * @return string El número de teléfono ajustado con prefijo internacional, si es necesario.
     */
    public function getPhoneWP()
    {
        $phone = $this->phone; // Asume que 'phone' es el campo principal.

        if (empty($phone)) {
            // Si 'phone' está vacío, intenta con 'phone2'.
            $phone = $this->phone2 ?? '';
        }

        if (empty($phone)) {
            return ''; // Retorna una cadena vacía si ambos campos están vacíos.
        }

        // Limpia el número de teléfono de caracteres no numéricos, excepto el '+' inicial.
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        // Si el número tiene 12 dígitos y comienza con '57', retorna el número sin el '+'.
        if (preg_match('/^57\d{10}$/', $cleanPhone)) {
            return $cleanPhone;
        }

        // Si el número incluye el '+', lo remueve.
        if (substr($cleanPhone, 0, 1) === '+') {
            return substr($cleanPhone, 1);
        }

        // Para números con 10 dígitos, asume que son colombianos y añade '57'.
        if (strlen($cleanPhone) == 10) {
            return '57' . $cleanPhone;
        }

        // Para cualquier otro caso, retorna el número sin modificaciones.
        return $cleanPhone;
    
    }

    public function getGenderNameAttribute()
    {
        $genders = [
            'F' => 'Femenino',
            'M' => 'Masculino',
            'U' => 'Desconocido'
        ];

        return $genders[$this->gender] ?? 'Desconocido';
    }

    public function audiences()
    {
        return $this->belongsToMany(Audience::class, 'audience_customer');
    }

    public static function findByNormalizedPhone(string $inputPhone): ?self
    {
        $normalized = preg_replace('/[^0-9]/', '', $inputPhone); // limpia cualquier símbolo
        $last10 = substr($normalized, -10); // nos quedamos solo con los últimos 10 dígitos

        return self::whereRaw("RIGHT(REGEXP_REPLACE(phone, '[^0-9]', ''), 10) = ?", [$last10])
            ->orWhereRaw("RIGHT(REGEXP_REPLACE(phone2, '[^0-9]', ''), 10) = ?", [$last10])
            ->first();
    }

}
