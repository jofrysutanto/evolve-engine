<?php

namespace EvolveEngine\Assets;

use EvolveEngine\Core\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('assets', function ($app) {
            return new JsonManifest(config('assets.manifest'), config('assets.uri'));
        });
    }

}
