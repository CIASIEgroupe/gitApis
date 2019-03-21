<?php
namespace prisecommande\api\controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use GuzzleHttp\Client as GuzzleClient;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \prisecommande\api\model\Commande as Commande;
use \prisecommande\api\model\Item as Item;
use \prisecommande\api\model\Client as Client;
use \prisecommande\api\middleware\Token as Token;
use \prisecommande\api\middleware\TokenJWT as TokenJWT;

class Controller{
	private $container;

	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function newCommand(Request $request, Response $response, array $args){
		try{
			$client = new GuzzleClient([
				'base_uri' => 'http://api.catalogue.local'
			]);
			$body = json_decode($request->getBody());
			$commande = new Commande();
			$commande->id = Uuid::uuid1();
			$commande->token = Token::new();
			$commande->nom = $body->nom;
			$commande->mail = $body->mail;
			$commande->livraison = date_create($body->livraison->date." ".$body->livraison->heure);		
			$commande->status = Commande::$created;
			$tokenJWT = TokenJWT::check($request);
			if($tokenJWT){
				$commande->client_id = $tokenJWT->data;
			}
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
			$data["commande"] = [
				"nom" => $commande->nom,
				"mail" => $commande->mail,
				"livraison" => [
					"date" => $commande->livraison->format("d-m-Y"),
					"heure" => $commande->livraison->format("h:i")
				],
				"id" => $commande->id,
				"token" => $commande->token,
				"montant" => $montant,
				"items" => $body->items
			];
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->badRequest;
		}
	}

	public function command(Request $request, Response $response, array $args){
		try{
			$command = Commande::select(["id", "created_at", "updated_at", "livraison", "montant", "remise", "token", "status"])->firstOrFail($args["id"])
			$data["command"] = $command;
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function updateDate(Request $request, Response $response, array $args){
		try{			
			$body = json_decode($request->getBody());
			$command = Commande::firstOrFail($args["id"]);
			$command->livraison = date_create($body->date);	
			$command->save();
			return $this->container->noContent;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function updatePay(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			$command = Commande::firstOrFail($args["id"]);
			$command->ref_paiement = $body->ref_paiement;
			$command->date_paiement = $body->date_paiement;
			$command->mode_paiement = $body->mode_paiement;
			$tokenJWT = TokenJWT::check($request);
			if($tokenJWT){
				$command->remise = $body->remise;
				$client = Client::firstOrFail($tokenJWT->data);
				$client->cumul = 0;
				$client->save();
			}
			$command->save();
			return $this->container->noContent;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function register(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			if(Client::where("mail", "=", $body->mail)->first() == null){
				$client = new Client();
				$client->mail = $body->mail;
				$client->password = password_hash($body->password, PASSWORD_DEFAULT);
				$client->save();
				return $this->container->created;
			}
			else{
				$response = $this->container->badRequest;
				$data = [
					"type" => "error",
					"error" => "400",
					"message" => "Mail déjà utilisé"
				];
				$response->getBody()->write(json_encode($data));
				return $response;	
			}
		}
		catch(\Exception $e){
			return $this->container->badRequest;
		}
	}

	public function login(Request $request, Response $response, array $args){
		$body = json_decode($request->getBody());
		$clien_verif = Client::where("mail", "=", $body->mail)->first();
		if($client != null && password_verify($body->password, $client->password)){
			unset($client->password);			
			$data = ["client" => $client->login];
			$tokenJWT = TokenJWT::new($client->id);
			$response = $this->container->ok;
			$response = $response->withHeader("Authorization", "Bearer ".$tokenJWT);
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		return $this->container->unauthorized;
	}

	public function profile(Request $request, Response $response, array $args){
		try{
			$tokenJWT = TokenJWT::check($request);
			if(!$tokenJWT){
				return $this->container->noHeader;
			}
			$client = Client::select(["id", "mail", "cumul", "created_at"])->findOrFail($tokenJWT->data);
			$data = [
				"client" => $client,
				"links" => [
					"self" => "/client/".$jwt->id,
					"commands" => "/client/".$jwt->id."/commands"
				]
			];
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function commands(Request $request, Response $response, array $args){
		try{
			$tokenJWT = TokenJWT::check($request);
			if(!$tokenJWT){
				return $this->container->noHeader;
			}
			$commands = Commande::select(["id", "created_at", "updated_at", "livraison", "montant", "remise", "token", "status"])->where("client_id", "=", $args["id"])->orderBy("created_at", "asc")->get();
			$data["commands"] = $commands;
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}
}