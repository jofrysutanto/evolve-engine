<?php

namespace EvolveEngine\Core\Bootstrap;

use Exception;
use ErrorException;
use Illuminate\Contracts\Container\Container;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

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

        $run     = new \Whoops\Run;
        $handler = new PrettyPageHandler;

        // Set the title of the error page:
        $handler->setPageTitle("Whoops! There was a problem.");
        $run->pushHandler($handler);

        // Add a special handler to deal with AJAX requests with an
        // equally-informative JSON response. Since this handler is
        // first in the stack, it will be executed before the error
        // page handler, and will have a chance to decide if anything
        // needs to be done.
        if (\Whoops\Util\Misc::isAjaxRequest()) {
          $run->pushHandler(new JsonResponseHandler);
        }

        // Register the handler with PHP, and you're set!
        $run->register();
    }
}
