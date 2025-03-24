<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerMetaData extends Model{

	protected $table = 'customer_metadata_semantics';


	public static function getOptions($id){
		return CustomerMetaData::where('parent_id', $id)->get();
	}
}