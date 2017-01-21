<?php
namespace EvolveEngine\Core;

use Closure;
use EvolveEngine\Router\Router;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use October\Rain\Support\Str;

class Application extends Container
{
    /**
     * Directory to the root wordpres installation
     */
    protected $rootPath;

    /**
     * Directory to the active theme
     */
    protected $basePath;

    protected $bootstrappers = [
        \EvolveEngine\Core\Bootstrap\LoadConfiguration::class,
        \EvolveEngine\Core\Bootstrap\ConsumeRequest::class,
        \EvolveEngine\Core\Bootstrap\RegisterFacades::class,
        \EvolveEngine\Core\Bootstrap\RegisterProviders::class,
    ];

    public function __construct($rootPath, $basePath)
    {
        $this->basePath = $basePath;
        $this->rootPath = $rootPath;

        $this->registerBaseBindings();
    }

    /**
     * Get the root path of the Laravel installation.
     *
     * @return string
     */
    public function rootPath()
    {
        return $this->rootPath;
    }

    /**
     * Get the storage path of the Laravel installation.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->rootPath.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'uploads';
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Get the path to the application view files.
     *
     * @return string
     */
    public function viewPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'templates/';
    }

    /**
     * Start the engine
     *
     * @return void
     */
    public function start()
    {
        // Register all service providers
        foreach ($this->bootstrappers as $bootstrapper) {
            $bootstrapper = new $bootstrapper;
            $bootstrapper->bootstrap($this);
        }
    }

    /**
     * Register basic bindings
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance('Illuminate\Container\Container', $this);
    }

    /**
     * Detect the application's current environment.
     *
     * @param  \Closure  $callback
     * @return string
     */
    public function detectEnvironment(Closure $callback)
    {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        return $this['env'] = (new EnvironmentDetector())->detect($callback, $args);
    }

    /**
     * Get or check the current application environment.
     *
     * @param  mixed
     * @return string|bool
     */
    public function environment()
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $this['env'])) {
                    return true;
                }
            }

            return false;
        }

        return $this['env'];
    }

    /**
     * This is a dumb-down version of Laravel service provider registerer
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
        $providers = $this->config['app.providers'];

        foreach ($providers as $provider) {
            $providerService = new $provider($this);
            $providerService->register();
        }
    }

    /**
     * Convenience shortcut to add filter
     *
     * @return void
     */
    public function filter($type, $action, $priority = null, $acceptedArgs = 1)
    {
        return add_filter($type, $this->determineCallableInstance($action), $priority, $acceptedArgs);
    }

    /**
     * Convenience shortcut to add action
     *
     * @return void
     */
    public function action($type, $action)
    {
        return add_action($type, $this->determineCallableInstance($action));
    }

    /**
     * Consume given request
     *
     * @param  Request $request
     *
     * @return void
     */
    public function consumeRequest(Request $request)
    {
        $this->instance('request', $request);
        $this->alias('request', 'Illuminate\Http\Request');
    }

    /**
     * Determine callable hooks/action method using '@' symbol
     *
     * @param  string $action e.g. 'truelib@getCSS'
     *
     * @return array
     */
    protected function determineCallableInstance($action)
    {
        if ($pos = strpos($action, '@')) {
            list($instance, $method) = explode('@', $action);
        }

        // Determine action
        if (!$this->bound($instance)) {
            throw new \Exception("Unable to resolve $instance in Container");
        }

        $instance = $this->make($instance);
        return [$instance, $method];
    }

}