<?php
namespace lbs\catalogue\api\model;

class Categorie extends \Illuminate\Database\Eloquent\Model{
	protected $table = "categorie";
	protected $primaryKey = "id";
	public $timestamps = false;

	public function sandwiches(){ return $this->belongsToMany('lbs\catalogue\api\model\Sandwich', 'sand2cat', 'cat_id', 'sand_id'); }
}