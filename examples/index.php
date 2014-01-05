<?php
require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

use Slim\Slim;

$app = new Slim();
$app->config('debug', true);
$app->config('whoops.editor', 'sublime');
$app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);
$app->get('/', function() use ($app) {
	$app->urlFor('index');  // Throw exception, not defined route named 'index'
});
$app->run();
