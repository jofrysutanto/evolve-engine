<?php

namespace EvolveEngine\Core\Bootstrap;

use Illuminate\Contracts\Container\Container;

class ConsumeRequest
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @return void
     */
    public function bootstrap(Container $app)
    {
        $request = \Illuminate\Http\Request::capture();
        $app->consumeRequest($request);
    }
}
