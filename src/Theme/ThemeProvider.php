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
        $themeManager = new ThemeManager($this->app['config']->get('theme'));

        $themeManager->boot();

        $this->app->instance('theme', $themeManager);
    }

}
