<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Product;

class Category extends Model{
	public function getCategoryChildren(){
		
		$model = Category::where('parent_id', $this->id)->get();
		$str = "";
		foreach($model as $item){
			$str .= $item->id ." ".$item->getCategoryChildren();
		}

		return $str;
	}

    public function products(){
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id')
                    ->where('status_id', 1);
    }
}