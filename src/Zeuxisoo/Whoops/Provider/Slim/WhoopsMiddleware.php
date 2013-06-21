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

			$app->config('whoops.error_page_handler', new PrettyPageHandler);
			$app->config('whoops.error_json_handler', new JsonResponseHandler);
			$app->config('whoops.error_json_handler')->onlyForAjaxRequests(true);
			$app->config('whoops.slim_info_handler', function() use ($app) {
				try {
					$request = $app->request();
				} catch (RuntimeException $e) {
					return;
				}

				$app->config('whoops.error_page_handler')->addDataTable('Slim Application', array(
					'Charset'          => $request->headers('ACCEPT_CHARSET'),
					'Locale'           => $request->getContentCharset() ?: '<none>',
					'Route Name'       => $app->router()->getCurrentRoute()->getName() ?: '<none>',
					'Route Pattern'    => $app->router()->getCurrentRoute()->getPattern() ?: '<none>',
					'Route Middleware' => $app->router()->getCurrentRoute()->getMiddleware() ?: '<none>',
					'Application Class'=> get_class($app)
				));

				$app->config('whoops.error_page_handler')->addDataTable('Slim Application (Request)', array(
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
				$app->config('whoops.error_page_handler')->setEditor($whoops_editor);
			}

			$app->config('whoops', new Run);
			$app->config('whoops')->pushHandler($app->config('whoops.error_page_handler'));
			$app->config('whoops')->pushHandler($app->config('whoops.error_json_handler'));
			$app->config('whoops')->pushHandler($app->config('whoops.slim_info_handler'));
			$app->error(array($app->config('whoops'), Run::EXCEPTION_HANDLER));
		}

		$this->next->call();
	}
}
