<?php
declare(strict_types=1);

namespace Zeuxisoo\Whoops\Slim\Tests;

use Psr\http\Message\RequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Equip\Dispatch\MiddlewareCollection;
use Zeuxisoo\Whoops\Slim\WhoopsMiddleware;

class WhoopsMiddlewareTest extends TestCase {

    public function testInstall() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "https://example.com/");

        $default = function(RequestInterface $request) {
            $response = (new ResponseFactory)->createResponse();
            $response->getBody()->write("Success");

            return $response;
        };

        $collection = new MiddlewareCollection([
            new WhoopsMiddleware()
        ]);

        $response = $collection->dispatch($request, $default);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Success", $response->getBody());
    }

}
