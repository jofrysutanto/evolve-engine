<?php

namespace EvolveEngine\Debug;

use EvolveEngine\Core\ServiceProvider;

class DebugProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
    
    public function boot()
    {
        if (!app()->environment('local')) {
            return;
        }
        if (!getenv('APP_DEBUG', false)) {
            return;
        }
        (new DebugServer)->enable();
    }
}
