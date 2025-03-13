<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerMetaData extends Model{

	protected $table = 'customer_metadata_semantics';


	public static function getOptions($id){
		return CustomerMetaData::where('parent_id', $id)->get();
	}

    /*
    protected $table = 'customer_metadatas';

    protected $fillable = [
        'customer_id',
        'customer_metadata_semantic_id',
        'value',
    ];
    */

}



