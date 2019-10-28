<?php

namespace EvolveEngine\Sentinel;

use EvolveEngine\Core\ServiceProvider;

class SentinelServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        // Bail early if we're not using sentinel
        if (!$this->app['config']->get('sentinel.use-sentinel')) {
            return;
        }
        $this->app->bind('sentinel.api', function ($app) {
            $api = new SentinelApi($app['config']->get('sentinel.hq-url'));
            $api->registerRouting($app['router'], $app['request']);
            return $api;
        });

        $agent = new SentinelAgent($app['config']->get('sentinel.local-url-base'));
        $agent->registerRouting($app['router'], $app['request']);
        $this->app->instance('sentinel.agent', $agent);
    }
}
