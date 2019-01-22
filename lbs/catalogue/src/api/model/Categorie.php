<?php
namespace lbs\catalogue\api\model;

class Categorie extends \Illuminate\Database\Eloquent\Model{
	protected $table = "categorie";
	protected $primaryKey = "id";
	public $timestamps = false;
}