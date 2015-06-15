<?php
namespace Zeuxisoo\Whoops\Provider\Slim;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsErrorHandler;

class WhoopsMiddleware {

    public function __invoke($request, $response, $next) {
        $app       = $next;
        $container = $app->getContainer();
        $settings  = $container['settings'];

        if (isset($settings['debug']) === true && $settings['debug'] === true) {
            // Enable PrettyPageHandler with editor options
            $prettyPageHandler = new PrettyPageHandler();

            if (empty($settings['whoops.editor']) === false) {
                $prettyPageHandler->setEditor($settings['whoops.editor']);
            }

            // Enable JsonResponseHandler when request is AJAX
            $jsonResponseHandler = new JsonResponseHandler();
            $jsonResponseHandler->onlyForAjaxRequests(true);

            // Add more information to the PrettyPageHandler
            $prettyPageHandler->addDataTable('Slim Application', [
                'Application Class' => get_class($app),
                'Script Name'       => $app->environment->get('SCRIPT_NAME'),
                'Request URI'       => $app->environment->get('PATH_INFO') ?: '<none>',
            ]);

            $prettyPageHandler->addDataTable('Slim Application (Request)', array(
                'Accept Charset'  => $app->request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                'Content Charset' => $app->request->getContentCharset() ?: '<none>',
                'Path'            => $app->request->getUri()->getPath(),
                'Query String'    => $app->request->getUri()->getQuery() ?: '<none>',
                'HTTP Method'     => $app->request->getMethod(),
                'Base URL'        => (string) $app->request->getUri(),
                'Scheme'          => $app->request->getUri()->getScheme(),
                'Port'            => $app->request->getUri()->getPort(),
                'Host'            => $app->request->getUri()->getHost(),
            ));

            // Set Whoops to default exception handler
            $whoops = new \Whoops\Run;
            $whoops->pushHandler($prettyPageHandler);
            $whoops->pushHandler($jsonResponseHandler);
            $whoops->register();

            $container['errorHandler'] = function($c) use ($whoops) {
                return new WhoopsErrorHandler($whoops);
            };

            //
            $container['whoops'] = $whoops;
        }

        return $app($request, $response);
    }

}
