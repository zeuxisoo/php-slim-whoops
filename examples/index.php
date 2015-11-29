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

// Throw exception, Named route does not exist for name: hello
$app->get('/', function($request, $response, $args) {
	return $this->router->pathFor('hello');
});

// $app->get('/hello', function($request, $response, $args) {
//     $response->write("Hello Slim");
//     return $response;
// })->setName('hello');

$app->run();
