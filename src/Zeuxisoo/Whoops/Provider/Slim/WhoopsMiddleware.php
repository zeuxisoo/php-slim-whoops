<?php
namespace Zeuxisoo\Whoops\Provider\Slim;

use \Slim\Middleware;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class WhoopsMiddleware extends Middleware {
    public function call() {
        $app = $this->app;

        if ($app->config('debug') === true) {
            // Switch to custom error handler by disable debug
            $app->config('debug', false);

            //
            $app->container->singleton('whoopsPrettyPageHandler', function() {
                return new PrettyPageHandler();
            });

            $app->container->singleton('whoopsJsonResponseHandler', function() {
                $handler = new JsonResponseHandler();
                $handler->onlyForAjaxRequests(true);

                return $handler;
            });

            $app->whoopsSlimInfoHandler = $app->container->protect(function() use ($app) {
                try {
                    $request = $app->request();
                } catch (RuntimeException $e) {
                    return;
                }

                $current_route = $app->router()->getCurrentRoute();
                $route_details = array();

                if ($current_route !== null) {
                    $route_details = array(
                        'Route Name'       => $current_route->getName() ?: '<none>',
                        'Route Pattern'    => $current_route->getPattern() ?: '<none>',
                        'Route Middleware' => $current_route->getMiddleware() ?: '<none>',
                    );
                }

                $app->whoopsPrettyPageHandler->addDataTable('Slim Application', array_merge(array(
                    'Charset'          => $request->headers('ACCEPT_CHARSET'),
                    'Locale'           => $request->getContentCharset() ?: '<none>',
                    'Application Class'=> get_class($app)
                ), $route_details));

                $app->whoopsPrettyPageHandler->addDataTable('Slim Application (Request)', array(
                    'URI'         => $request->getRootUri(),
                    'Request URI' => $request->getResourceUri(),
                    'Path'        => $request->getPath(),
                    'Query String'=> $request->params() ?: '<none>',
                    'HTTP Method' => $request->getMethod(),
                    'Script Name' => $request->getScriptName(),
                    'Base URL'    => $request->getUrl(),
                    'Scheme'      => $request->getScheme(),
                    'Port'        => $request->getPort(),
                    'Host'        => $request->getHost(),
                ));
            });

            // Open with editor if editor is set
            $whoops_editor = $app->config('whoops.editor');

            if ($whoops_editor !== null) {
                $app->whoopsPrettyPageHandler->setEditor($whoops_editor);
            }

            $app->container->singleton('whoops', function() use ($app) {
                $run = new Run();
                $run->pushHandler($app->whoopsPrettyPageHandler);
                $run->pushHandler($app->whoopsJsonResponseHandler);
                $run->pushHandler($app->whoopsSlimInfoHandler);

                return $run;
            });

            $app->error(array($app->whoops, Run::EXCEPTION_HANDLER));
        }

        $this->next->call();
    }
}
