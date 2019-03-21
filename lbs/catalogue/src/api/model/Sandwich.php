<?php
namespace catalogue\api\model;

class Sandwich extends \Illuminate\Database\Eloquent\Model{
	protected $table = "sandwich";
	protected $primaryKey = "id";
	public $timestamps = false;
	public function categories(){ 
		return $this->belongsToMany('catalogue\api\model\Categorie', 'sand2cat', 'sand_id', 'cat_id'); 
	}
}