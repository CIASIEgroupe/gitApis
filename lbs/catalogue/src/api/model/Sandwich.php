<?php
namespace lbs\catalogue\api\model;

class Sandwich extends \Illuminate\Database\Eloquent\Model{
	protected $table = "sandwich";
	protected $primaryKey = "id";
	public $timestamps = false;
	public function categories(){ return $this->belongsToMany('lbs\catalogue\api\model\Categorie', 'sand2cat', 'sand_id', 'cat_id'); }
}