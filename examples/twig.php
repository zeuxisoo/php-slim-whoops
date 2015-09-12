<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

$app = new App([
    'settings' => [
        'debug'         => true,
        'whoops.editor' => 'sublime'
    ]
]);

$app->add(new WhoopsMiddleware);

$container = $app->getContainer();
$container->register(new \Slim\Views\Twig('./views', [
    'debug' => true,
    'cache' => './cache/views'
]));

$twig = $container->get('view')->getEnvironment();
$twig->addExtension(new Twig_Extension_Debug());

// Work
// $app->get('/', function($request, $response, $args) use ($app) {
//     return $this->view->render($response, 'test.html', [
//         'name' => "Tester"
//     ]);
// });

// Exception
$app->get('/', function($request, $response, $args) use ($app) {
    return $this->view->render($response, 'noExists.html');
});

$app->run();
