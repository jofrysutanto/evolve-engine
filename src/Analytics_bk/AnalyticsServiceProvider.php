<?php

namespace EvolveEngine\Analytics;

use EvolveEngine\Core\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('analytics', function ($app)
        {
            $config = $app['config']->get('analytics', []);
            return new AnalyticsManager($config); 
        });
    }

}
