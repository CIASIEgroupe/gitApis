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

$app->run();
