<?php

namespace EvolveEngine\Post;

use EvolveEngine\Core\ServiceProvider;

class CustomPostTypeProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $factory = new PostTypeFactory($app['config']->get('post-types.types'));

        $this->app->instance('post-type', $factory);

        $this->app->action('init', 'post-type@register', 1);
        $this->app->action('rest_api_init', 'post-type@registerRestApi');
        $this->app->filter('template_include', 'post-type@includeTemplate', 1);
    }

}
