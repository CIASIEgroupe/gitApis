<?php
namespace lbs\prisecommande\api\controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use GuzzleHttp\Client as GuzzleClient;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\prisecommande\api\model\Commande as Commande;
use \lbs\prisecommande\api\model\Item as Item;
use \lbs\prisecommande\api\model\Client as Client;
use \lbs\prisecommande\api\middleware\Token as Token;
use \lbs\prisecommande\api\middleware\TokenJWT as TokenJWT;
class apiController{
	private $container;

	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function newCommand(Request $rq, Response $rs, array $args){
		//création client GuzzleHttp
		$client = new GuzzleClient([
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

	public function command(Request $rq, Response $rs, array $args){
		$data = Token::check($rq, $rs, $args);
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(intval($data["status"]));
		$rs->getBody()->write(json_encode($data));
		return $rs;
	}

	public function updateDate(Request $rq, Response $rs, array $args){
		$command = Commande::where("id", "=", $args["id"])->first();
		$body = json_decode($rq->getBody());	
		$command->livraison = date_create($body->date);	
		$command->save();
	}

	public function updatePay(Request $rq, Response $rs, array $args){
		$command = Commande::where("id", "=", $args["id"])->first();
		$body = json_decode($rq->getBody());	
		$command->ref_paiement = $body->ref_paiement;
		$command->date_paiement = $body->date_paiement;
		$command->mode_paiement = $body->mode_paiement;	
		$command->save();
	}

	public function register(Request $rq, Response $rs, array $args){
		$body = json_decode($rq->getBody());
		if(Client::where("mail", "=", $body->mail)->first() == null){
			$client = new Client();
			$client->mail = $body->mail;
			$client->password = password_hash($body->password, PASSWORD_DEFAULT);
			$client->save();
		}
		else{
			$data = [
				"type" => "error",
				"error" => "400",
				"message" => "Mail déjà utilisé"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(400);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function login(Request $rq, Response $rs, array $args){
		$body = json_decode($rq->getBody());
		$clientVerif = Client::where("mail", "=", $body->mail)->first();
		if(isset($rq->getHeader("Authorization")[0]) && $rq->getHeader("Authorization")[0] == "Basic"){
			if(password_verify($body->password, $clientVerif->password)){
				$data = [
					"token" => TokenJWT::new($clientVerif->id)
				];
				$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			}
			else{
				$data = [
					"type" => "error",
					"error" => "401",
					"message" => "Mail ou mot de passe erroné"
				];
				$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(401);
			}
		}
		else{
			$data = [
				"type" => "error",
				"error" => "401",
				"message" => "No authorization header present"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(401);
		}
		$rs->getBody()->write(json_encode($data));
		return $rs;	
	}

	public function profile(Request $rq, Response $rs, array $args){
		$authorization = explode(":", $rq->getHeader("Authorization")[0]);
		if(isset($authorization) && $authorization[0] == "Bearer"){
			$jwt = TokenJWT::decode($authorization[1]);
			if($jwt){
				$client = Client::select(["id", "mail", "created_at", "cumul"])->where("id", "=", $jwt->id)->first();
				$data = [
					"client" => $client,
					"links" => [
						"self" => "/client/".$jwt->id,
						"commands" => "/client/".$jwt->id."/commands"
					]
				];
				$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			}
			else{
				$data = [
					"type" => "error",
					"error" => "401",
					"message" => "Wrong token"
				];
				$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(401);
			}
		}
		else{
			$data = [
				"type" => "error",
				"error" => "401",
				"message" => "No authorization header present"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(401);
		}
		$rs->getBody()->write(json_encode($data));
		return $rs;	
	}

	public function commands(Request $rq, Response $rs, array $args){
		$commands = Commande::select(["id", "created_at", "updated_at", "livraison", "montant", "remise", "token", "status"])->where("client_id", "=", $args["id"])->orderBy("created_at", "asc")->get();
		$data["commands"] = $commands;
		$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
		$rs->getBody()->write(json_encode($data));
		return $rs;
	}
}