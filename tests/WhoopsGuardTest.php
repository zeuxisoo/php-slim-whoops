<?php
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Body;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;

use Whoops\Handler\JsonResponseHandler as WhoopsJsonResponseHandler;

use Zeuxisoo\Whoops\Provider\Slim\WhoopsGuard;

class WhoopsGuardTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        // ob_start();
    }

    public function tearDown() {
        // ob_end_clean();
    }

    public function testLoadNormal() {
        $app = new App();

        $container = $app->getContainer();

        $whoopsGuard = new WhoopsGuard();
        $whoopsGuard->setApp($app);
        $whoopsGuard->setRequest($container['request']);
        $whoopsGuard->setHandlers([]);
        $whoopsGuard->install();

        $app->get('/foo', function ($req, $res) {
            $res->write('It is work');
            return $res;
        });

        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo',
            'REQUEST_METHOD' => 'GET',
        ]);

        $uri          = Uri::createFromEnvironment($env);
        $headers      = Headers::createFromEnvironment($env);
        $cookies      = [];
        $serverParams = $env->all();
        $body         = new Body(fopen('php://temp', 'r+'));
        $req          = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $res          = new Response();

        $resOut = $app($req, $res);

        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $resOut);
        $this->assertEquals('It is work', (string)$res->getBody());
    }

    public function testCustomException() {
        $app = new App();

        $container = $app->getContainer();

        $whoopsGuard = new WhoopsGuard();
        $whoopsGuard->setApp($app);
        $whoopsGuard->setRequest($container['request']);
        $whoopsGuard->setHandlers([]);
        $whoopsGuard->install();

        $app->get('/foo', function ($req, $res) use ($app) {
            throw new Exception('Hello');

            $res->write('It is work');
            return $res;
        });

        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/foo',
            'REQUEST_METHOD' => 'GET',
        ]);

        $uri          = Uri::createFromEnvironment($env);
        $headers      = Headers::createFromEnvironment($env);
        $cookies      = [];
        $serverParams = $env->all();
        $body         = new Body(fopen('php://temp', 'r+'));
        $req          = new Request('GET', $uri, $headers, $cookies, $serverParams, $body);
        $res          = new Response();

        $this->setExpectedException('\Exception');

        $app($req, $res);
    }

    public function testJsonResponseHandlerWasInstalledWhenAjaxSent() {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        $app = new App([
            'settings' => [
                'debug' => true,
            ]
        ]);

        $container = $app->getContainer();

        $whoopsGuard = new WhoopsGuard();
        $whoopsGuard->setApp($app);
        $whoopsGuard->setRequest($container['request']);
        $whoopsGuard->setHandlers([]);
        $whoopsGuard->install();

        $handlers = $container['whoops']->getHandlers();

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);

        $this->assertInstanceOf(WhoopsJsonResponseHandler::class, $handlers[1]);
    }

}
