<?php
namespace lbs\prisecommande\api\model;

class Commande extends \Illuminate\Database\Eloquent\Model{
	protected $table = "commande";
	protected $primaryKey = "id";
	public $incrementing = false;
	public static $created = 1;
	public static $payed = 2;
	public static $inProgress = 3;
	public static $avaible = 4;
	public static $deliver = 5;
}