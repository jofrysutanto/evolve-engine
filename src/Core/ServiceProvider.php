<?php

namespace EvolveEngine\Core;

abstract class ServiceProvider
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Commands registered by service provider
     *
     * @var array
     */
    protected $commands =  [];

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Commands registered by service provider
     * 
     * @param ConsoleApplication $console
     *
     * @var array
     */
    public function registerCommands($console) 
    {
        if (!$this->commands) {
            return;   
        }
        foreach ($this->commands as $command) {
            $instance = $this->app->make($command);
            $console->add($instance);
        }
    }

}
