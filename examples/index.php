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

$app->get('/', function() use ($app) {
    // echo "Hello World";
	$app->router->urlFor('index');  // Throw exception, not defined route named 'index'
});

$app->run();
