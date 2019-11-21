<?php
declare(strict_types=1);

namespace Zeuxisoo\Whoops\Slim\Tests;

use Slim\Psr7\Factory\ServerRequestFactory;
use Whoops\Run as WhoopsRun;
use Zeuxisoo\Whoops\Slim\WhoopsGuard;

class WhoopsGuardTest extends TestCase {

    public function testShouldReturnWhoops() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "http://example.com/");

        $guard = new WhoopsGuard();
        $guard->setRequest($request);

        $whoops = $guard->install();

        $this->assertInstanceOf(WhoopsRun::class, $whoops);
    }

    public function testShouldNotReturnWhoopsWhenDisabled() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "http://example.com/");

        $guard = new WhoopsGuard([
            'enable' => false,
        ]);
        $guard->setRequest($request);

        $whoops = $guard->install();

        $this->assertNull($whoops);
    }

    public function testSetCustomHandlers() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "http://example.com/");

        $guard = new WhoopsGuard();
        $guard->setRequest($request);
        $guard->setHandlers([
            function($exception, $inspector, $run) {
                $message = $exception->getMessage();
                $title   = $inspector->getExceptionName();

                echo "{$title} -> {$message}";
                exit;
            }
        ]);

        $whoops = $guard->install();

        $this->assertInstanceOf(WhoopsRun::class, $whoops);

        // Current handlers: prettyPageHandler, customHandler
        $this->assertEquals(2, count($whoops->getHandlers()));
    }

    public function testSetEditor() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "http://example.com/");

        $guard = new WhoopsGuard([ 'editor' => 'sublime', ]);
        $guard->setRequest($request);

        $whoops = $guard->install();

        $prettyPageHandler = $whoops->getHandlers()[0];

        $this->assertEquals(
            $prettyPageHandler->getEditorHref('/foo/bar.php', 10),
            'subl://open?url=file://%2Ffoo%2Fbar.php&line=10'
        );
    }

    public function testPageTitle() {
        $request = (new ServerRequestFactory)->createServerRequest("GET", "http://example.com/");

        $guard = new WhoopsGuard([ 'title' => 'Hello World', ]);
        $guard->setRequest($request);

        $whoops = $guard->install();

        $prettyPageHandler = $whoops->getHandlers()[0];

        $this->assertEquals($prettyPageHandler->getPagetitle(), 'Hello World');
    }

}
