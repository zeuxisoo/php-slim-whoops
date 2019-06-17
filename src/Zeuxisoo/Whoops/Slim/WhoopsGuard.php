<?php
namespace Zeuxisoo\Whoops\Slim;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class WhoopsGuard {

    protected $settings = [];
    protected $request  = null;
    protected $handlers = [];

    public function __construct($settings = []) {
        $this->settings = array_merge([
            'enable' => true,
            'editor' => '',
            'title'  => '',
        ], $settings);
    }

    public function setRequest(ServerRequestInterface $request) {
        $this->request = $request;
    }

    public function setHandlers(array $handlers) {
        $this->handlers = $handlers;
    }

    public function install() {
        if ($this->settings['enable'] === true) {
            // Enable PrettyPageHandler with editor options
            $prettyPageHandler = new PrettyPageHandler();

            if (empty($this->settings['editor']) === false) {
                $prettyPageHandler->setEditor($this->settings['editor']);
            }

            if (empty($this->settings['title']) === false) {
                $prettyPageHandler->setPageTitle($this->settings['title']);
            }

            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable('Slim Application', [
                'Version' => SlimApp::VERSION,
            ]);

            $prettyPageHandler->addDataTable('Slim Application (Request)', array(
                'Accept Charset'  => $this->request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                'Content Charset' => $this->request->getContentCharset() ?: '<none>',
                'Path'            => $this->request->getUri()->getPath(),
                'Query String'    => $this->request->getUri()->getQuery() ?: '<none>',
                'HTTP Method'     => $this->request->getMethod(),
                'Base URL'        => (string) $this->request->getUri(),
                'Scheme'          => $this->request->getUri()->getScheme(),
                'Port'            => $this->request->getUri()->getPort(),
                'Host'            => $this->request->getUri()->getHost(),
            ));

            // Set Whoops to default exception handler
            $whoops = new \Whoops\Run;
            $whoops->pushHandler($prettyPageHandler);

            // Enable JsonResponseHandler when request is AJAX
            if (Misc::isAjaxRequest()){
                $whoops->pushHandler(new JsonResponseHandler());
            }

            // Add each custom handler to whoops handler stack
            if (empty($this->handlers) === false) {
                foreach($this->handlers as $handler) {
                    $whoops->pushHandler($handler);
                }
            }

            $whoops->register();

            $errorHandler = function() use ($whoops) {
                return new WhoopsErrorHandler($whoops);
            };

            return $whoops;
        }
    }

}