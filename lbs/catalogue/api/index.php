<?php
/**
 * File:  index.php
 * Creation Date: 17/10/2018
 * description:
 *
 * @author:
 */
require_once "../src/vendor/autoload.php";
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$config = ['settings' => [
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'db' => [
        'driver' => 'mysql',
        'host' => 'db',
        'database' => 'catalogue_lbs',
        'username' => 'catalogue_lbs',
        'password' => 'catalogue_lbs',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci'
    ]
]];

$app = new \Slim\App($config);

$c = $app->getContainer();

$container["notFoundHandler"] = function ($c) {
    return function ($request, $response) use ($c) {
        throw new Exception("Api Not Found", 404);
    };
};

$container["notAllowedHandler"] = function ($c) {
    return function ($request, $response) use ($c) {
        throw new Exception("Method Not Allowed", 405);
    };
};

$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file("conf/conf.ini"));
$db->setAsGlobal();
$db->bootEloquent();

$app->get('/categories[/]', function (Request $request, Response $response, array $args) {
	$controller = new lbs\catalogue\api\controller\apiController($this);
	return $controller->categories($request, $response, $args);
});

$app->get('/categories/{id}[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    return $controller->categorie($request, $response, $args);
});

$app->get('/sandwichs[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    return $controller->sandwichs($request, $response, $args);
});

$app->get('/sandwichs/{id}[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    return $controller->sandwich($request, $response, $args);
});

$app->get('/categories/{id}/sandwichs[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    return $controller->categorieSandwich($request, $response, $args);
});

$app->post('/categories/new[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    $categorie = $controller->newCategorie($request, $response, $args);
    return $response->withJson($categorie, 201);
});

$app->put('/categories/update[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\catalogue\api\controller\apiController($this);
    $categorie = $controller->updateCategorie($request, $response, $args);
    return $response->withJson($categorie, 200);

});

$app->run();