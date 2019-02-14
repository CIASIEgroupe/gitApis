<?php
namespace lbs\prisecommande\api\middleware;
use \lbs\prisecommande\api\model\Commande as Commande;
class Token{
	public static function new(){
		$bin = random_bytes(32);
		$token = bin2hex($bin);
		return $token;
	}

	public static function check($rq, $rs, $args){
		$id = $args["id"];
		$token = $rq->getHeader("X-lbs-token")[0];
		if($token != null){
			try{
				$data["status"] = "200";
				$commande = Commande::where("id", "=", $id)->where("token", "=", $token)->firstOrFail();
				$date = date_create($commande->livraison);
				$data["commande"] = array("nom" => $commande->nom, "mail" => $commande->mail, "livraison" => array("date" => $date->format("d-m-Y"), "heure" => $date->format("h:i")));				
			}
			catch(\Exception $e){
				$data = [
					"type" => "error",
					"status" => "403",
					"message" => "Mauvais token"
				]; 
			}	
		}
		else{
			$data = [
				"type" => "error",
				"status" => "401",
				"message" => "Token absent"
			];
		}
		return $data	;
	}
}