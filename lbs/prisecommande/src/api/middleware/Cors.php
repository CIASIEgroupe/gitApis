<?php
namespace prisecommande\api\middleware;

class Cors{
	protected $container;

	public function __construct($container){
		$this->container = $container;
	}

	public function __invoke($request, $response, $next){
		$response = $next($request, $response);
		$response = $response->withHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
			->withHeader("Access-Control-Allow-Headers", "X-Resquest-With, Content-Type, Accept, Origin, Authorization")
			->withHeader("Access-Control-Allow-Origin", "*")
			->withHeader("Origin", "http://api.prisecommande.local");	
		return $response;
	}
}
