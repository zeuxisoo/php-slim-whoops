<?php
declare(strict_types=1);

namespace Zeuxisoo\Whoops\Slim;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Whoops\Run as WhoopsRun;
use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class WhoopsGuard {

    protected $settings = [];
    protected $request  = null;
    protected $handlers = [];

    /**
     * Instance the whoops guard object
     *
     * @param array $settings
     */
    public function __construct($settings = []) {
        $this->settings = array_merge([
            'enable' => true,
            'editor' => '',
            'title'  => '',
        ], $settings);
    }

    /**
     * Set the server request object
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function setRequest(ServerRequestInterface $request): void {
        $this->request = $request;
    }

    /**
     * Set the custom handlers for whoops
     *
     * @param array $handlers
     * @return void
     */
    public function setHandlers(array $handlers): void {
        $this->handlers = $handlers;
    }

    /**
     * Install the whoops guard object
     *
     * @return WhoopsRun|null
     */
    public function install(): ?WhoopsRun {
        if ($this->settings['enable'] === false) {
            return null;
        }

        // Enable PrettyPageHandler with editor options
        $prettyPageHandler = new PrettyPageHandler();

        if (empty($this->settings['editor']) === false) {
            $prettyPageHandler->setEditor($this->settings['editor']);
        }

        if (empty($this->settings['title']) === false) {
            $prettyPageHandler->setPageTitle($this->settings['title']);
        }

        // Add more information to the PrettyPageHandler
        $contentCharset = '<none>';
        if (
            method_exists($this->request, 'getContentCharset') === true &&
            $this->request->getContentCharset() !== null
        ) {
            $contentCharset = $this->request->getContentCharset();
        }

        $prettyPageHandler->addDataTable('Slim Application', [
            'Version'         => SlimApp::VERSION,
            'Accept Charset'  => $this->request->getHeader('ACCEPT_CHARSET') ?: '<none>',
            'Content Charset' => $contentCharset,
            'HTTP Method'     => $this->request->getMethod(),
            'Path'            => $this->request->getUri()->getPath(),
            'Query String'    => $this->request->getUri()->getQuery() ?: '<none>',
            'Base URL'        => (string) $this->request->getUri(),
            'Scheme'          => $this->request->getUri()->getScheme(),
            'Port'            => $this->request->getUri()->getPort(),
            'Host'            => $this->request->getUri()->getHost(),
        ]);

        // Set Whoops to default exception handler
        $whoops = new \Whoops\Run;
        $whoops->pushHandler($prettyPageHandler);

        // Enable JsonResponseHandler when request is AJAX
        if (Misc::isAjaxRequest() === true){
            $whoops->pushHandler(new JsonResponseHandler());
        }

        // Add each custom handler to whoops handler stack
        if (empty($this->handlers) === false) {
            foreach($this->handlers as $handler) {
                $whoops->pushHandler($handler);
            }
        }

        $whoops->register();

        return $whoops;
    }

}
