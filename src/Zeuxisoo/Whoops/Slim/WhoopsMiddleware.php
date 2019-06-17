<?php
namespace Zeuxisoo\Whoops\Slim;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class WhoopsMiddleware implements MiddlewareInterface {

    protected $settings = [];
    protected $handlers = [];

    public function __construct(array $settings = [], array $handlers = []) {
        $this->settings = $settings;
        $this->handlers = $handlers;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $whoopsGuard = new WhoopsGuard($this->settings);
        $whoopsGuard->setRequest($request);
        $whoopsGuard->setHandlers($this->handlers);
        $whoopsGuard->install();

        return $handler->handle($request);
    }

}
