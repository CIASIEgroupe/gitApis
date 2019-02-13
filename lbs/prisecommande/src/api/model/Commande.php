<?php
namespace lbs\prisecommande\api\model;

class Commande extends \Illuminate\Database\Eloquent\Model{
	protected $table = "commande";
	protected $primaryKey = "id";
	public $incrementing = false;
}