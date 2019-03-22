<?php
namespace commande\api\model;

class Commande extends \Illuminate\Database\Eloquent\Model{
	protected $table = "commande";
	protected $primaryKey = "id";
	public $timestamps = false;
	public $incrementing = false;
}