<?php

namespace EvolveEngine\Social;

use EvolveEngine\Core\ServiceProvider;

class SocialServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('social.share', function ($app) {
            $shareBuilder = new ShareBuilder(config('social.share'));
            return $shareBuilder;
        });
    }

}
