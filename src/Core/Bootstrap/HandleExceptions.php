<?php

namespace EvolveEngine\Core\Bootstrap;

use Exception;
use Illuminate\Contracts\Container\Container;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;

class HandleExceptions
{
    /**
     * The application instance.
     *
     * @var Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Container  $app
     * @return void
     */
    public function bootstrap(Container $app)
    {
        $this->app = $app;

        if (!$app['config']->get('app.debug')) {
            return;
        }

        error_reporting(E_ERROR & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);

        // On Debug mode we buffer the entire output
        // and clean it up in case of exception
        ob_start();

        $run = new \Whoops\Run;
        $handler = new PrettyPageHandler;

        $run->pushHandler(function () {
            // Clear all output and only show PrettyPageHandler output
            ob_clean();
        });

        // Set the title of the error page:
        $handler->setPageTitle('Whoops! There was a problem.');
        $run->pushHandler($handler);

        // Handle ajax calls error
        if (\Whoops\Util\Misc::isAjaxRequest()) {
            $run->pushHandler(new JsonResponseHandler);
        }

        // Handle cli calls error
        if (\Whoops\Util\Misc::isCommandLine()) {
            $run->pushHandler(new PlainTextHandler);
        }

        // Register the handler with PHP, and you're set!
        $run->register();
    }
}
