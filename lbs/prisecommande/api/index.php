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
        'host' => 'db_commande',
        'database' => 'commande_lbs',
        'username' => 'commande_lbs',
        'password' => 'commande_lbs',
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

$app->post('/commands[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\prisecommande\api\controller\apiController($this);
    return $controller->newCommand($request, $response, $args);
});

$app->get('/commands/{id}[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\prisecommande\api\controller\apiController($this);
    return $controller->commande($request, $response, $args);
});

$app->get('/commands/{id}/items[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\prisecommande\api\controller\apiController($this);
    return $controller->items($request, $response, $args);
});

$app->put('/commands/{id}/date[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\prisecommande\api\controller\apiController($this);
    return $controller->updateDate($request, $response, $args);
});

$app->put('/commands/{id}/pay[/]', function (Request $request, Response $response, array $args) {
    $controller = new lbs\prisecommande\api\controller\apiController($this);
    return $controller->updatePay($request, $response, $args);
});


$app->run();