<?php
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

class MessageTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        ob_start();
    }

    public function tearDown() {
        ob_end_clean();
    }

    public function testLoadNormal() {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/foo'
        ));

        $app = new \Slim\Slim();
        $app->get('/foo', function () {
            echo "It is work";
        });

        $middleware = new WhoopsMiddleware();
        $middleware->setApplication($app);
        $middleware->setNextMiddleware($app);
        $middleware->call();

        $this->assertEquals("It is work", $app->response()->body());
        $this->assertEquals(200, $app->response()->status());
    }

    public function testException() {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/foo'
        ));

        $app = new \Slim\Slim();
        $app->get('/foo', function () {
            throw new \Exception('Test Message', 100);
        });

        $middleware = new WhoopsMiddleware();
        $middleware->setApplication($app);
        $middleware->setNextMiddleware($app);
        $middleware->call();

        $this->assertEmpty($app->response()->body());
        $this->assertEquals(500, $app->response()->status());
    }

    public function testSetEditor() {
        \Slim\Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/foo'
        ));

        $app = new \Slim\Slim();
        $app->config('whoops.editor', 'sublime');
        $app->get('/foo', function () {
            echo "It is work";
        });

        $middleware = new WhoopsMiddleware();
        $middleware->setApplication($app);
        $middleware->setNextMiddleware($app);
        $middleware->call();

        $this->assertEquals('subl://open?url=file://test_path&line=168', $app->whoopsPrettyPageHandler->getEditorHref('test_path', 168));
    }
}
