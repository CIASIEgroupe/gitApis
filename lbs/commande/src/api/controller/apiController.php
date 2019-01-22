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
			$commandes = Commande::all();
			$data = [
				"type" => "collection",
				"count" => count($commandes),
				"commandes" => $commandes
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			return json_encode("erreur commandes()");
		}
	}
}