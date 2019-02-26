<?php
namespace lbs\prisecommande\api\middleware;
use \lbs\prisecommande\api\model\Commande as Commande;
use \lbs\prisecommande\api\model\Item as Item;
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
				$commande = Commande::where("id", "=", $id)->where("token", "=", $token)->firstOrFail();
				$date = date_create($commande->livraison);
				$items = Item::where("command_id", "=", $commande->id)->get();
				$data["type"] = "resource";
				$data["status"] = "200";
				$data["links"] = array("self" => "/commands/".$commande->id, "items" => "/commands/".$commande->id."/items");
				$data["commande"] = array("id" => $commande->id, "livraison" => $date->format("d-m-Y h:i"), "nom" => $commande->nom, "mail" => $commande->mail, "status" => $commande->status, "montant" => $commande->montant);
				$data["commande"]["items"] = array();
				foreach ($items as $item){
					array_push($data["commande"]["items"], array("uri" => $item->uri, "libelle" => $item->libelle, "tarif" => $item->tarif, "quantite" => $item->quantite));
				}		
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