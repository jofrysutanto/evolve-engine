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
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('analytics', function ($app)
        {
            $config = $app['config']->get('analytics', []);
            return new AnalyticsManager($config);
        });
        $this->app->action('wp_head',   'analytics@injectHead');
        $this->app->action('wp_footer', 'analytics@injectFooter');
    }

}
