<?php

namespace EvolveEngine\Core;

use EvolveEngine\Core\ServiceProvider;

class WordpressServiceProvider extends ServiceProvider
{
    /**
     * These are all custom logic classes we will write
     * to customise and extend our Wordpress site
     *
     * @var array  Assoc array of container alias and class name
     */
    protected $logicClasses = [
        // 'main' => \App\Main::class  
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->bindHooksFilters();
    }

    /**
     * Bind hooks filters of registered classes
     *
     * @return void
     */
    protected function bindHooksFilters()
    {
        foreach ($this->logicClasses as $key => $value) {
            if (!class_exists($value)) {
                throw new \Exception("Class $value not found");
            }

            $instance = new $value($this->app, $key);
            $instance->init();
        }
    }

}
