<?php
namespace commande\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \commande\api\model\Commande as Commande;
use \commande\api\model\Item as Item;

class Controller{
	private $container; 
	public function __construct($container){
		$this->container = $container;
	}

	public function commandes(Request $request, Response $response, array $args){
		try{
			$commandes = Commande::select(["id", "nom", "created_at", "livraison", "status"])->orderBy("livraison", "asc")->orderBy("created_at", "asc");
			if($request->getParam("s")){
				$commandes = $commandes->where("status", "=", $request->getParam("s"));
			}
			$size = 10;
			if($request->getParam("size")){
				$size = $request->getParam("size");
			}
			$commandes = $commandes->limit($size);
			$p = 0;
			$count = count($commandes);
			if($request->getParam("page")){
				if(intdiv($count, $size) <= $request->getParam("page")){
					$p = intdiv($count, $size);
				}
				$commandes = $commandes->skip(($size * $request->getParam("page")) - $size);
			}
			$commandes = $commandes->get();
			$c = [];
			foreach ($commandes as $commande) {
				$element = [
					"command" => $commande,
					"links" => [
						"self" => [
							"href" => "/commands/".$commande->id
						]
					]
				];
				array_push($c, $element);
			}
			$page_prev = $request->getParam("page") - 1;
			if($request->getParam("page") < 0){
				$page_prev == 0;
			}
			$data = [
				"type" => "collection",
				"count" => $count,
				"size" => $size,
				"links" => [
					"next" => "/commands?page=".($request->getParam("page") + 1)."&size=".$size,
					"prev" => "/commands?page=".($page_prev)."&size=".$size,
					"last" => "/commands?page=".intdiv($count, $size)."&size=".$size,
					"first" => "/commands?page=1&size=".$size
				],
				"commandes" => $c
			];			
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function commande(Request $request, Response $response, array $args){
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
			$response = $response->withHeader('Content-type', 'application/json; charesponseet=utf-8')->withStatus(200);
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "ressouce non disponible /commands/".$args["id"]
			];
			$response = $response->withHeader('Content-type', 'application/json; charesponseet=utf-8')->withStatus(404);
			$response->getBody()->write(json_encode($data));
			return $response;	
		}
	}

	public function updateState(Request $request, Response $response, array $args){
		$body = json_decode($request->getBody());
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
			$response = $response->withHeader('Content-type', 'application/json; charesponseet=utf-8')->withStatus(403);
			$response->getBody()->write(json_encode($data));
			return $response;	
		}
	}
}