<?php

namespace EvolveEngine\Widget;

use EvolveEngine\Core\ServiceProvider;

class WidgetProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $factory = new WidgetFactory($app['config']->get('widgets.widgets'));

        $this->app->instance('widget', $factory);

        $this->app->action('widgets_init', 'widget@register', 1);       
    }

}
