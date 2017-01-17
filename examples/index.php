<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

$app = new App([
    'settings' => [
        // On/Off whoops error
        'debug'               => true,

        // Set default whoops editor
        'whoops.editor'       => 'sublime',

        // Display call stack in orignal slim error when debug is off
        'displayErrorDetails' => true,
    ]
]);

if ($app->getContainer()->settings['debug'] === false) {
    // Custom error handler for slim application when debug is off
    $container['errorHandler'] = function($c) {
        return function($request, $response, $exception) use ($c) {
            $data = [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'trace'   => explode("\n", $exception->getTraceAsString()),
            ];

            return $c->get('response')
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
        };
    };
}else{
    // Custom whoops handler and replace the default error handler to whoops
    /*
    $simplyErrorHandler = function($exception, $inspector, $run) {
        $message = $exception->getMessage();
        $title   =  $inspector->getExceptionName();

        echo "{$title} -> {$message}";

        exit;
    };

    $customWhoopsHandlers = [$simplyErrorHandler];

    $app->add(new WhoopsMiddleware($app, $customWhoopsHandlers));
    */

    $app->add(new WhoopsMiddleware($app));
}

// Throw exception, Named route does not exist for name: hello
$app->get('/', function($request, $response, $args) {
	return $this->router->pathFor('hello');
});

// Working example
/*
$app->get('/hello', function($request, $response, $args) {
    $response->write("Hello Slim");
    return $response;
})->setName('hello');
*/

$app->run();
