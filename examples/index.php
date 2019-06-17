<?php
require dirname(__DIR__).'/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

// Instance
$app = AppFactory::create();

// Middleware
$app->add(new WhoopsMiddleware());

// Route parser
$routeParser = $app->getRouteCollector()->getRouteParser();

// Routes
$app->get('/', function (Request $request, Response $response, $args) use ($routeParser) {
    $response->getBody()->write(file_get_contents('template/index.php'));

    return $response;
});

$app->get('/url-path-not-exists', function(Request $request, Response $response, $args) use ($routeParser) {
    return $routeParser->urlFor('hello');
});

// Run
$app->run();
