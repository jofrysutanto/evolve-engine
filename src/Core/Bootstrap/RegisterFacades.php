<?php

namespace EvolveEngine\Core\Bootstrap;

use Illuminate\Support\Facades\Facade;
use EvolveEngine\Core\AliasLoader;
use Illuminate\Contracts\Container\Container;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Container $app)
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance($app->make('config')->get('app.aliases'))->register();
    }
}
