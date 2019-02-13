<?php
namespace lbs\commande\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\commande\api\model\Commande as Commande;
use \lbs\commande\api\model\Item as Item;

class apiController{
	private $container; 
	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function commandes(Request $rq, Response $rs, array $args){
		try{
			$count = Commande::all();
			$data = [
				"type" => "collection",
				"count" => count($count)
			];
			$commands = Commande::select(["id", "nom", "created_at", "livraison", "status"])->orderBy("livraison", "asc")->orderBy("created_at", "asc");
			if(isset($_GET["s"])){
				$commands = $commands->where("status", "=", $_GET["s"]);
			}
			$size = 10;
			if(isset($_GET["size"])){
				$size = $_GET["size"];
				$data["size"] = $size;
			}
			$commands = $commands->limit($size);
			$p = 0;
			if(isset($_GET["page"])){
				$page = $_GET["page"];
				if($page < 0){
					$p = 0;
				}
				if(intdiv(count($count), $size) <= $page){
					$p = intdiv(count($count), $size);
				}
				$commands = $commands->skip($p * $size);			
				$page_next = $page + 1;
				$page_prev = $page - 1;
				if($page < 0){
					$page_prev == 0;
				}
				$data["links"] = [
					"next" => "/commands?page=".($page_next)."&size=".$size,
					"prev" => "/commands?page=".($page_prev)."&size=".$size,
					"last" => "/commands?page=".intdiv(count($count), $size)."&size=".$size,
					"first" => "/commands?page=0&size=".$size
				];
			}			
			$commands = $commands->get();
			$c = [];
			foreach ($commands as $commande) {
				$element = ["command" => $commande, "links" => ["self" => ["href" => ["/command/".$commande->id]]]];
				array_push($c, $element);
			}
			$data["commands"] = $c;
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "Collection non disponible /commands"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function commande(Request $rq, Response $rs, array $args){
		try{
			$commande = Commande::select(["id", "created_at", "livraison", "nom", "mail", "montant"])->findOrFail($args["id"]);
			$data = [
				"type" => "resource",
				"links" => [
					"self" => "/commands/".$commande->id,
					"items" => "/commands/".$commande->id."/items"
				]
			];
			$data["command"] = $commande;
			$data["items"] = [];
			$items = Item::select(["uri", "libelle", "tarif", "quantite"])->where("command_id", "=", $commande->id)->get();
			foreach ($items as $item){
				array_push($data["items"], $item);
			}
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "ressouce non disponible /commands/".$args["id"]
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function updateState(Request $rq, Response $rs, array $args){
		$body = json_decode($rq->getBody());
		$commande = Commande::find($args["id"]);
		if($body->status >= $commande->status){
			$commande->status = $body->status;
			$commande->save();
		}
		else{
			$data = [
				"type" => "error",
				"error" => "403",
				"message" => "Impossible de modifier la ressource ".$args["id"]
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(403);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}
}