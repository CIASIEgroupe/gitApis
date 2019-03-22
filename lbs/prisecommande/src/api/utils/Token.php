<?php
namespace prisecommande\api\utils;
use \lbs\prisecommande\api\model\Commande as Commande;
use \lbs\prisecommande\api\model\Item as Item;
class Token{
	public static function new(){
		$bin = random_bytes(32);
		$token = bin2hex($bin);
		return $token;
	}

	public static function check($request){
		try{
			$tokenURL = $request->getParam("token");
			if($tokenURL != null){
				return $tokenURL;
			}
			$tokenHeader = $request->getHeader("X-lbs-token");
			if($tokenHeader != null){
				return $tokenHeader;
			}
			return false;
		}
		catch(\Exception $e){
			return false;
		}
	}
}