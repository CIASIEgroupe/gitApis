<?php
require_once "../src/vendor/autoload.php";
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$config = ['settings' => [
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false
]];

$app = new \Slim\App($config);

$container = $app->getContainer();

$container['ok'] = function ($container) {
    $response = $container->response->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(200);  
    return $response;
};

$container['created'] = function ($container) {
    $response = $container->response->withHeader('Content-type', 'application/json; charset=utf-8')->withStatus(201);  
    return $response;
};

$container['noContent'] = function ($container) {
    $response = $container->response->withStatus(204);
    return $response;
};

$container['badRequest'] = function ($container) {
    $response = $container->response->withHeader('Content-type', "application/json; charset=utf-8")->withStatus(400);
    $data = [
        "type" => "error",
        "error" => "400",
        "message" => "Bad Request ".$container->request->getUri()->getPath()
    ];
    $response->getBody()->write(json_encode($data));
    return $response;
};

$container['notFound'] = function ($container) {
    $response = $container->response->withHeader('Content-type', "application/json; charset=utf-8")->withStatus(404);
    $data = [
        "type" => "error",
        "error" => "404",
        "message" => "Ressource indisponible: ".$container->request->getUri()->getPath()
    ];
    $response->getBody()->write(json_encode($data));
    return $response;
};

/*$container['notAllowed'] = function ($container) {
    $response = $container->response->withHeader('Content-type', "application/json; charset=utf-8")->withStatus(405);
    $data = [
        "type" => "error",
        "error" => "405",
        "message" => "Méthode pas autorisée: ".$container->request->getMethod." ".$container->request->getUri()->getPath()
    ];
    $response->getBody()->write(json_encode($data));
    return $response;
};*/

/*$container["errorHandler"] = function($container){
    return function($request, $response, $exception) use ($container){
        return $response->withStatus(500)
            ->withHeader("Content-Type", "text/html")
            ->write("Une erreur dans le code !");
    };
};*/

$container["Controller"] = function($container){
    return new \catalogue\api\controller\Controller($container);
};

$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection(parse_ini_file("conf/conf.ini"));
$db->setAsGlobal();
$db->bootEloquent();

require __DIR__."/routes.php";

$app->add(new \catalogue\api\middleware\Cors($container));

$app->run();
