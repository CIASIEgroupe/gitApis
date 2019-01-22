<?php
namespace lbs\catalogue\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\catalogue\api\model\Categorie as Categorie;
use \lbs\catalogue\api\model\Sandwich as Sandwich;
class apiController{
	private $container;
	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}

	public function categories(Request $rq, Response $rs, array $args){
		try{
			$categories = Categorie::all();
			$data = [
				"type" => "collection",
				"count" => count($categories),
				"categories" => $categories
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "Collection non disponnible /categories"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
	}

	public function categorie(Request $rq, Response $rs, array $args){
		try{
			$categorie = Categorie::findOrFail($args["id"]);
			$data = [
				"type" => "resource",
				"categorie" => $categorie
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "ressouce non disponible /categorie/".$args["id"]
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function sandwichs(Request $rq, Response $rs, array $args){
		//echo ($_GET['prixMax']);
		try{
			$sandwichs = Sandwich::all();
			$data = [
				"type" => "collection",
				"count" => count($sandwichs),
				"sandwichs" => $sandwichs
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			return json_encode("erreur sandwichs()");
		}
	}

	public function sandwich(Request $rq, Response $rs, array $args){
		try{
			$sandwich = Categorie::findOrFail($args["id"]);
			$data = [
				"type" => "resource",
				"sandwich" => $sandwich
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "ressouce non disponible /sandwich/".$args["id"]
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function categorieSandwich(Request $rq, Response $rs, array $args){
		try{
			$categorie = Categorie::join('sand2cat', 'sand2cat.cat_id', '=', 'categorie.id')
				->join('sandwich', 'sand2cat.sand_id', '=', 'sandwich.id')
				->where('categorie.id', '=', $args["id"])
				->get();
			$data = [
				"type" => "resource",
				"categorie" => $categorie
			];			
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);
			$rs->getBody()->write(json_encode($data));
			return $rs;
		}
		catch(\Exception $e){
			$data = [
				"type" => "error",
				"error" => "404",
				"message" => "ressouce non disponible /categories/".$args["id"]."/sandwichs"
			];
			$rs = $rs->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(404);
			$rs->getBody()->write(json_encode($data));
			return $rs;	
		}
	}

	public function newCategorie(Request $rq, Response $rs, array $args){
		$parsedBody = $rq->getParsedBody();
		$categorie = new Categorie();
		$categorie->nom = $parsedBody["nom"];
		$categorie->description = $parsedBody["description"];
		$categorie->save();
		return $categorie;
	}

	public function updateCategorie(Request $rq, Response $rs, array $args){
		$parsedBody = $rq->getParsedBody();
		$categorie = Categorie::find($parsedBody["id"]);
		$categorie->nom = $parsedBody["nom"];
		$categorie->description = $parsedBody["description"];
		$categorie->save();
		return $categorie;
	}
}
