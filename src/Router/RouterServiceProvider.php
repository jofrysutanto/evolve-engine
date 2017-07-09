<?php

namespace EvolveEngine\Router;

use EvolveEngine\Core\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{

    /**
     * Routes file location
     *
     * @var string
     */
    protected $routesFile = 'src/routes.php';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Make router available
        $this->app->instance('router', new Router($this->app, $this->app['request']));

        $this->registerRouteFile($this->routesFile);

        $this->app->action('init',       'router@onInit',   50);
        $this->app->action('wp_loaded',  'router@onWpLoad'. 50);
    }

    /**
     * Try to locate routes.php file and register them
     * 
     * @param String $path Relative path to routes file
     *
     * @return void
     */
    protected function registerRouteFile($path)
    {
        // Path is always relative to project
        $fullpath = base_path($path);
        if (!file_exists($fullpath)) {
            return;
        }

        require $fullpath;
    }

}
