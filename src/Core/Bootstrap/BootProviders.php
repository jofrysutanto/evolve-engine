<?php

namespace EvolveEngine\Core\Bootstrap;

use Illuminate\Contracts\Container\Container;

class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Container $app)
    {
        $app->boot();
    }
}