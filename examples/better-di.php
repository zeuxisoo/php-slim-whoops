<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

$app = new App([
    'settings' => [
        // On/Off whoops error
        'debug' => true,

        // Set default whoops editor
        'whoops.editor' => 'sublime',

        // Set page title
        'whoops.page_title' => 'Custom page title',

        // Display call stack in orignal slim error when debug is off
        'displayErrorDetails' => true,
    ]
]);

// First middleware, break the lifecycle
$app->add(function ($req, $res, $next) {
    exit('Make sure it will display first');

    return $next($request, $response);
});

// Add the whoops middleware
$app->add(new WhoopsMiddleware($app));

// This will not execute when first middleware are added
$app->get('/', function ($req, $res) {
    exit('Make sure it will not display before remote the first middleware');
});

$app->run();
