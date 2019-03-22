<?php
namespace catalogue\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \catalogue\api\model\Categorie as Categorie;
use \catalogue\api\model\Sandwich as Sandwich;
use \catalogue\api\model\Sand2Cat as Sand2Cat;

class Controller{
	protected $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function categories(Request $request, Response $response, array $args){
		try{
			$categories = Categorie::all();
			$data = [
				"type" => "collection",
				"count" => count($categories),
				"locale" => "fr-FR",
				"categories" => $categories
			];
			$response->getBody()->write(json_encode($data));
			return $this->container->ok;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function categorie(Request $request, Response $response, array $args){
		try{
			$data = [
				"type" => "resource",
				"date" => date("d-m-Y"),
				"categorie" => Categorie::findOrFail($args["id"]),
				"links" => [
					"sandwichs" => $request->getUri()->getPath()."/sandwichs",
					"self" => $request->getUri()->getPath()
				]
			];			
			$response->getBody()->write(json_encode($data));
			return $this->container->ok;
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function newCategorie(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			$categorie = new Categorie();
			$categorie->nom = $body->nom;
			$categorie->description = $body->description;
			$categorie->save();
			$data = [
				"type" => "resource",
				"date" => date("d-m-Y"),
				"locale" => "fr-FR",
				"categorie" => $categorie
			];
			$response = $this->container->created;
			$response->getBody()->write(json_encode($data));
			$response = $response->withHeader("Location", "/categories/".$categorie->id);
			return $response;
		}
		catch(\Exception $e){
			return $this->container->badRequest;	
		}
	}

	public function updateCategorie(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			if($body != null){
				$categorie = Categorie::findOrFail($args["id"]);
				$categorie->nom = $body->nom;
				$categorie->description = $body->description;
				$categorie->save();
				$data = [
					"type" => "resource",
					"date" => date("d-m-Y"),
					"locale" => "fr-FR",
					"categorie" => $categorie
				];
				$response->getBody()->write(json_encode($data));
				return $this->container->ok;
			}
			else{
				return $this->container->badRequest;
			}
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function categoriesSandwichs(Request $request, Response $response, array $args){
		try{
			$categories = Categorie::all();
			$data = [
				"type" => "collection",
				"count" => count($categories),
				"locate" => "fr-FR",
				"categories" => $categories
			];			
			foreach($data["categories"] as $categorie){
				$sandwichs = $categorie->sandwichs()->get();
				foreach($sandwichs as $sandwich){
					unset($sandwich->pivot);
				}
				$categorie->sandwichs = $sandwichs;
			}	
			$response->getBody()->write(json_encode($data));
			return $this->container->ok;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function categorieSandwichs(Request $request, Response $response, array $args){
		try{
			$data = [
				"type" => "resource",
				"locale" => "fr-FR",
				"categorie" => Categorie::findOrFail($args["id"])
			];
			$data["categorie"]->sandwichs = $data["categorie"]->sandwichs()->get();
			foreach($data["categorie"]->sandwichs as $sandwich){
				unset($sandwich->pivot);
			}
			$response->getBody()->write(json_encode($data));
			return $this->container->ok;
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function sandwichs(Request $request, Response $response, array $args){
		try{
			$limit = 2;
			$sandwichs = Sandwich::limit($limit);
			if($request->getParam("typePain")){
				$sandwichs = $sandwichs->where("type_pain", "like", "%".$request->getParam("typePain")."%");
			}
			if($request->getParam("prixMax")){
				$sandwichs = $sandwichs->where("prix", "<", $request->getParam("prixMax"));
			}
			if($request->getParam("page")){
				$sandwichs = $sandwichs->skip(($limit * $request->getParam("page")) - $limit);
			}
			$sandwichs = $sandwichs->get();
			if($sandwichs->isEmpty()){
				return $this->container->notFound;
			}
			$data = [
				"type" => "collection",
				"count" => count($sandwichs),
				"locate" => "fr-FR",
				"sandwichs" => $sandwichs
			];			
			$response = $this->container->ok;
			$response->getBody()->write(json_encode($data));
			return $response;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}

	public function sandwich(Request $request, Response $response, array $args){
		try{
			$data = [
				"type" => "resource",
				"locale" => "fr-FR",
				"sandwich" => Sandwich::findOrFail($args["id"])
			];			
			$response->getBody()->write(json_encode($data));
			return $this->container->ok;
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function newSandwich(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			$sandwich = new Sandwich;
			$sandwich->nom = $body->nom;
			$sandwich->description = $body->description;
			$sandwich->type_pain = $body->type_pain;
			$sandwich->prix = $body->prix;
			$sandwich->save();
			$data = [
				"type" => "resource",
				"locale" => "fr-FR",
				"sandwich" => $sandwich
			];
			$response = $this->container->created;
			$response->getBody()->write(json_encode($data));
			$response = $response->withHeader("Location", "/sandwich/".$sandwich->id);
			return $response;
		}
		catch(\Exception $e){
			return $this->container->badRequest;	
		}
	}

	public function updateSandwich(Request $request, Response $response, array $args){
		try{
			$body = json_decode($request->getBody());
			if($body != null){
				$sandwich = Sandwich::findOrFail($args["id"]);
				$sandwich->nom = $body->nom;
				$sandwich->description = $body->description;
				$sandwich->type_pain = $body->type_pain;
				$sandwich->prix = $body->prix;
				$sandwich->save();
				$data = [
					"type" => "resource",
					"locale" => "fr-FR",
					"sandwich" => $sandwich
				];
				$response->getBody()->write(json_encode($data));
				return $this->container->ok;
			}
			else{
				return $this->container->badRequest;
			}
		}
		catch(\Exception $e){
			return $this->container->notFound;	
		}
	}

	public function deleteSandwich(Request $request, Response $response, array $args){
		try{
			$sandwich = sandwich::findOrFail($args['id']);
			$sandwich->delete();
			return $this->container->noContent;
		}
		catch(\Exception $e){
			return $this->container->notFound;
		}
	}
}