<?php
namespace Zeuxisoo\Whoops\Provider\Slim;

use Slim\App as SlimApp;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Zeuxisoo\Whoops\Provider\Slim\WhoopsGuard;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsErrorHandler;

class WhoopsMiddleware {

    private $app      = null;
    private $handlers = [];

    public function __construct(SlimApp $app = null, array $handlers = []) {
        $this->app      = $app;
        $this->handlers = $handlers;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        $app = $this->app !== null ? $this->app : $next;

        $whoops = new WhoopsGuard();
        $whoops->setApp($app);
        $whoops->setRequest($request);
        $whoops->setHandlers($this->handlers);
        $whoops->install();

        return $app($request, $response);
    }

}
