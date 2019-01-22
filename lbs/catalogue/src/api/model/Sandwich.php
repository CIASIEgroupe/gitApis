<?php
namespace lbs\catalogue\api\model;

class Sandwich extends \Illuminate\Database\Eloquent\Model{
	protected $table = "sandwich";
	protected $primaryKey = "id";
	public $timestamps = false;
}