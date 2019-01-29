<?php
namespace lbs\prisecommande\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\catalogue\api\model\Categorie as Categorie;
use \lbs\catalogue\api\model\Sandwich as Sandwich;
class apiController{
	private $container;
	public function __construct(\Slim\Container $container){
		$this->container = $container;
	}
}
