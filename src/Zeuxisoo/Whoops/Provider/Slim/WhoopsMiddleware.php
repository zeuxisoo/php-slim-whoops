<?php
namespace Zeuxisoo\Whoops\Provider\Slim;

use Slim\App as SlimApp;
use Whoops\Util\Misc;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsErrorHandler;

class WhoopsMiddleware {

    private $app      = null;
    private $handlers = [];

    public function __construct(SlimApp $app = null, array $handlers = []) {
        $this->app      = $app;
        $this->handlers = $handlers;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        $app         = $this->app !== null ? $this->app : $next;
        $container   = $app->getContainer();
        $settings    = $container['settings'];
        $environment = $container['environment'];

        if (isset($settings['debug']) === true && $settings['debug'] === true) {
            // Enable PrettyPageHandler with editor options
            $prettyPageHandler = new PrettyPageHandler();

            if (empty($settings['whoops.editor']) === false) {
                $prettyPageHandler->setEditor($settings['whoops.editor']);
            }

            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable('Slim Application', [
                'Application Class' => get_class($app),
                'Script Name'       => $environment->get('SCRIPT_NAME'),
                'Request URI'       => $environment->get('PATH_INFO') ?: '<none>',
            ]);

            $prettyPageHandler->addDataTable('Slim Application (Request)', array(
                'Accept Charset'  => $request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                'Content Charset' => $request->getContentCharset() ?: '<none>',
                'Path'            => $request->getUri()->getPath(),
                'Query String'    => $request->getUri()->getQuery() ?: '<none>',
                'HTTP Method'     => $request->getMethod(),
                'Base URL'        => (string) $request->getUri(),
                'Scheme'          => $request->getUri()->getScheme(),
                'Port'            => $request->getUri()->getPort(),
                'Host'            => $request->getUri()->getHost(),
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

            $container['phpErrorHandler'] = $container['errorHandler'] = function() use ($whoops) {
                return new WhoopsErrorHandler($whoops);
            };

            $container['whoops'] = $whoops;
        }

        return $app($request, $response);
    }

}
