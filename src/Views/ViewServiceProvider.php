<?php

namespace EvolveEngine\Views;

use EvolveEngine\Core\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view-maker', function ($app)
        {
            return new ViewMaker($app->viewPath()); 
        });
    }

}
