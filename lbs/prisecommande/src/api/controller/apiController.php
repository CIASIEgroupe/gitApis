<?php
namespace lbs\prisecommande\api\controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use GuzzleHttp\Client;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\prisecommande\api\model\Commande as Commande;
use \lbs\prisecommande\api\middleware\Token as Token;
class apiController{
	private $container;
	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function newCommand(Request $rq, Response $rs, array $args){
		$body = json_decode($rq->getBody());
		$commande = new Commande();
		$commande->id = Uuid::uuid1();
		$commande->token = Token::new();
		$commande->nom = $body->nom;
		$commande->mail = $body->mail;
		date_default_timezone_set("Europe/Paris");
		$commande->livraison = date_create($body->livraison->date." ".$body->livraison->heure);		
		$commande->status = Commande::$created;
		//$commande->save();
		var_dump($body->items);
		foreach ($body->items as $item) {
			$url = "http://api.catalogue.local:10080".$item->uri;
			
		}
		$data["commande"] = array("nom" => $commande->nom, "mail" => $commande->mail, "livraison" => array("date" => $commande->livraison->format("d-m-Y"), "heure" => $commande->livraison->format("h:i")), "id" => $commande->id, "token" => $commande->token, "montant" => 0);
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
		$rs->getBody()->write(json_encode($data));
		//return $rs;
	}

	public function commande(Request $rq, Response $rs, array $args){
		$id = $args["id"];
		$data = Token::check($rq, $rs, $args);
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(intval($data["status"]));
		$rs->getBody()->write(json_encode($data));
		return $rs;
	}
}