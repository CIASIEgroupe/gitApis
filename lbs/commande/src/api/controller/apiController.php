<?php
namespace lbs\commande\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\commande\api\model\Commande as Commande;

class apiController{
	private $container; 
	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function commandes(Request $rq, Response $rs, array $args){
		try{
			if($rq->getAttribute("s")){
				$commands = Commande::select(["id", "nom", "created_at", "livraison", "status"])->where("status", "=", $rq->getQueryParam("s"))->orderBy("livraison", "asc")->orderBy("created_at", "asc")->get();
			}
			else{
				$commands = Commande::select(["id", "nom", "created_at", "livraison", "status"])->orderBy("livraison", "asc")->orderBy("created_at", "asc")->get();
			}
			$c = [];
			foreach ($commands as $commande) {
				$element = ["command" => $commande, "links" => ["self" => ["href" => ["/command/".$commande->id]]]];
				array_push($c, $element);
			}
			$data = [
				"type" => "collection",
				"count" => count($commands),
				"commands" => $c
			];			
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
			$commande = Commande::findOrFail($args["id"]);
			$data = [
				"type" => "resource",
				"categorie" => $commande
			];			
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
}