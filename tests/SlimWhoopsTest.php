<?php
use Slim\App;
use Slim\Http\Environment;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;

class MessageTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        ob_start();
    }

    public function tearDown() {
        ob_end_clean();
    }

    public function testLoadNormal() {
        $app = new App();
        $app['environment'] = function () {
            return Environment::mock([
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/foo',
                'REQUEST_METHOD' => 'GET'
            ]);
        };

        // Set get method and response
        $app->get('/foo', function ($req, $res, $args) {
            $res->write('It is work');
            return $res;
        });

        // Set middleware
        $app->add(new WhoopsMiddleware);

        // Invoke app
        ob_start();
        $response = $app->run();
        ob_end_clean();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('It is work', (string) $response->getBody());
    }

    public function testException() {
        $app = new App();
        $app['environment'] = function () {
            return Environment::mock([
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/foo',
                'REQUEST_METHOD' => 'GET'
            ]);
        };

        $app->get('/foo', function ($req, $res, $args) {
            throw new \Exception('Test Message', 100);
            return $res;
        });

        // Set middleware
        $app->add(new WhoopsMiddleware);

        // Invoke app
        ob_start();
        $response = $app->run();
        ob_end_clean();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Test Message', (string) $response->getBody());
    }

    public function testMiddlewareIsWorkingAndEditorIsSet() {
        $app = new App([
            'debug' => true,
            'whoops.editor' => 'sublime',
        ]);

        $app['environment'] = function () {
            return Environment::mock([
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/foo',
                'REQUEST_METHOD' => 'GET'
            ]);
        };

        $app->get('/foo', function ($req, $res, $args) {
            return $res;
        });

        $app->add(new WhoopsMiddleware);

        // Invoke app
        $response = $app->run();

        // Get added whoops handlers
        $handlers = $app['whoops']->getHandlers();

        $this->assertEquals(2, count($handlers));
        $this->assertEquals('subl://open?url=file://test_path&line=169', $handlers[0]->getEditorHref('test_path', 169));
    }
}
