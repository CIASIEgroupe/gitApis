<?php
namespace lbs\prisecommande\api\controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use GuzzleHttp\Client;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\prisecommande\api\model\Commande as Commande;
use \lbs\prisecommande\api\model\Item as Item;
use \lbs\prisecommande\api\middleware\Token as Token;
class apiController{
	private $container;

	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function newCommand(Request $rq, Response $rs, array $args){
		//création client GuzzleHttp
		$client = new \GuzzleHttp\Client([
			'base_uri' => 'http://api.catalogue.local'
		]);
		//création objet commande
		$body = json_decode($rq->getBody());
		$commande = new Commande();
		//création uuid avec Ramsey
		$commande->id = Uuid::uuid1();
		$commande->token = Token::new();
		$commande->nom = $body->nom;
		$commande->mail = $body->mail;
		$commande->livraison = date_create($body->livraison->date." ".$body->livraison->heure);		
		$commande->status = Commande::$created;		
		$montant = 0;
		foreach ($body->items as $item) {
			$responseGuzzle = $client->get($item->uri);
			$bodyGuzzle = json_decode($responseGuzzle->getBody());
			$i = new Item();
			$i->uri = $item->uri;
			$i->libelle = $bodyGuzzle->sandwich->nom;
			$i->tarif = $bodyGuzzle->sandwich->prix;
			$i->quantite = $item->q;
			$i->command_id = $commande->id;
			$i->save();
			$montant += $i->tarif;
		}
		$commande->montant = $montant;
		$commande->save();
		$data["commande"] = array("nom" => $commande->nom, "mail" => $commande->mail, "livraison" => array("date" => $commande->livraison->format("d-m-Y"), "heure" => $commande->livraison->format("h:i")), "id" => $commande->id, "token" => $commande->token, "montant" => $montant, "items" => $body->items);
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
		$rs->getBody()->write(json_encode($data));
		return $rs;
	}

	public function commande(Request $rq, Response $rs, array $args){
		$data = Token::check($rq, $rs, $args);
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(intval($data["status"]));
		$rs->getBody()->write(json_encode($data));
		return $rs;
	}

	public function updateDate(Request $rq, Response $rs, array $args){
		$commande = Commande::where("id", "=", $args["id"])->first();
		$body = json_decode($rq->getBody());	
		$commande->livraison = date_create($body->date);	
		$commande->save();
	}

	public function updatePay(Request $rq, Response $rs, array $args){
		$commande = Commande::where("id", "=", $args["id"])->first();
		$body = json_decode($rq->getBody());	
		$commande->ref_paiement = $body->ref_paiement;
		$commande->date_paiement = $body->date_paiement;
		$commande->mode_paiement = $body->mode_paiement;	
		$commande->save();
	}
}