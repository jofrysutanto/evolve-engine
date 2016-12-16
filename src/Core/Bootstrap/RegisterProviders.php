<?php

namespace EvolveEngine\Core\Bootstrap;

use Illuminate\Contracts\Container\Container;

class RegisterProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @return void
     */
    public function bootstrap(Container $app)
    {
        $app->registerConfiguredProviders();
    }
}
