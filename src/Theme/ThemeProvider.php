<?php

namespace EvolveEngine\Theme;

use EvolveEngine\Core\ServiceProvider;

class ThemeProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('theme', function ($app)
        {
            $themeManager = new ThemeManager($app['config']->get('theme'));

            $themeManager->boot();

            return $themeManager;
        });
    }

}
