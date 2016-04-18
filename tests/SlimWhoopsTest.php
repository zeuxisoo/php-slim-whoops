<?php
use \Slim\App;
use \Slim\Http\Environment;
use \Slim\Http\Uri;
use \Slim\Http\Body;
use \Slim\Http\Headers;
use \Slim\Http\Request;
use \Slim\Http\Response;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

class Stackable {
    use \Slim\MiddlewareAwareTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
        return $res->write('Center');
    }
}

class MessageTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        ob_start();
    }

    public function tearDown() {
        ob_end_clean();
    }

    public function testLoadNormal() {
        $app = new App();
        $app->add(new WhoopsMiddleware($app->getContainer()));
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

    public function testInstantiationAsString(){

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
        $app          = new App;

        $next = function($req, $res){
            return $res;
        };

        $mw1 = function($req, $res, $next) {
            return $req->withAttribute('some_attribute', 'some_value');
        };

        // This is how Slim resolves a class name when registering a middleware
        $mw2 = $app->getContainer()->get('callableResolver')->resolve('Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware');
        $this->assertInstanceOf('Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware', $mw2);

        $response1 = $mw1($req, $res, $mw2);
        $this->assertEquals( $response1->getAttribute('some_attribute'), 'some_value' );

    }

    public function testException() {
        $app = new App();
        $app->add(new WhoopsMiddleware($app->getContainer()));
        $app->get('/foo', function ($req, $res) use ($app) {
            return $this->router->pathFor('index');
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

        $this->setExpectedException('\RuntimeException');

        $app($req, $res);
    }

    public function testMiddlewareIsWorkingAndEditorIsSet() {
        $app = new App([
            'settings' => [
                'debug' => true,
                'whoops.editor' => 'sublime',
            ]
        ]);
        $container = $app->getContainer();
        $container['environment'] = function () {
            return Environment::mock([
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/foo',
                'REQUEST_METHOD' => 'GET'
            ]);
        };

        $app->get('/foo', function ($req, $res, $args) {
            return $res;
        });

        $app->add(new WhoopsMiddleware($container));

        // Invoke app
        $response = $app->run();

        // Get added whoops handlers
        $handlers = $container['whoops']->getHandlers();

        // Only 1 will got because the JSON handler will not added if it is not ajax request
        $this->assertEquals(1, count($handlers));
        $this->assertEquals('subl://open?url=file://test_path&line=169', $handlers[0]->getEditorHref('test_path', 169));
    }
}
