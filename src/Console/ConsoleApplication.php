<?php
namespace EvolveEngine\Console;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\Console\Application as BaseConsole;

class ConsoleApplication extends BaseConsole
{

    /**
     * Our core application
     *
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        parent::__construct('Evolve Engine Console');
        $this->app = $app;
    }

    /**
     * Boot Console 
     *
     * @return $this
     */
    public function boot()
    {
        $this->app->registerProviderCommands($this);

        return $this;
    }

}