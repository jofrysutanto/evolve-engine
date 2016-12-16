<?php

namespace EvolveEngine\Router;

use EvolveEngine\Core\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Make router available
        $this->app->instance('router', new Router($this->app, $this->app['request']));

        $this->app->action('init',       'router@onInit');
        $this->app->action('wp_loaded',  'router@onWpLoad');
    }

}
