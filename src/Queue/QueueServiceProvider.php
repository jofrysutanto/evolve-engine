<?php

namespace EvolveEngine\Queue;

use EvolveEngine\Core\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{

    /**
     * Commands made available by this provider
     *
     * @var array
     */
    protected $commands = [
        Commands\QueueListener::class,
        Commands\StopQueue::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('queue', function ($app) {
            return new QueueDriverManager($app);
        });
    }

}
