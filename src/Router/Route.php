<?php

namespace EvolveEngine\Router;

use EvolveEngine\Router\Traits\RouteDependencyResolverTrait;
use Illuminate\Http\Request;
use October\Rain\Router\Router as OctoberRouter;

class Route
{
    use RouteDependencyResolverTrait;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Corresponding url to listen to
     *
     * @var string
     */
    protected $url;

    /**
     * Unique name of the identifier
     *
     * @var string
     */
    protected $name;

    /**
     * Verb of the route, e.g. 'GET', 'POST'
     *
     * @var string
     */
    protected $verb;

    /**
     * Instantiable controller name
     *
     * @var string
     */
    protected $controller;

    /**
     * Method to call
     *
     * @var string
     */
    protected $method;

    /**
     * Event to trigger this route e.g. 'init', 'wp_loaded'
     *
     * @var string
     */
    protected $event;

    public function __construct($router, $verb, $url, $config)
    {
        $this->router    = $router;
        $this->container = $router->getContainer();
        $this->url    = $url;
        $this->verb   = $verb;

        $this->parseConfig($config);
    }

    /**
     * Name this route
     *
     * @param  string $name
     *
     * @return $this
     */
    public function name($name)
    {
        // Update route reference
        $rule = $this->router->getMatcher()->getRouteMap()[$this->name];
        $rule->name($name);
        $this->name = $name;

        return $this;
    }

    /**
     * Check if given name matches this route
     *
     * @param  string $name
     *
     * @return boolean
     */
    public function is($name)
    {
        return $this->name === $name;
    }

    /**
     * Which event level 
     *
     * @param  string $name
     *
     * @return $this
     */
    public function on($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Check if route should run based on event and identifier
     *
     * @param  string $event
     * @param  string $name
     *
     * @return $this
     */
    public function shouldRun($event, $verb, $name)
    {
        return $this->event === $event 
            && strtolower($this->verb) === strtolower($verb)
            && $this->name === $name;
    }

    /**
     * Execute route's actions
     *
     * @return mixed
     */
    public function runAction()
    {
        $instance = $this->container->make($this->controller);

        $parameters = $this->resolveClassMethodDependencies(
            $this->router->params(), $instance, $this->method
        );

        return $instance->callAction($this->method, $parameters);
    }

    /**
     * Parse given configuration and set route properties
     *
     * @param  mixed $config
     *
     * @return void
     */
    protected function parseConfig($config)
    {
        if (is_string($config) && strpos($config, '@')) {
            $this->setControllerMethod($config);
        } else if (is_array($config)) {
            foreach ($config as $key => $value) {
                switch ($key) {
                    case 'uses':
                        $this->setControllerMethod($value);
                        break;
                    case 'as':
                        $this->name($value);
                        break;
                    case 'on':
                        $this->on($value);
                        break;
                    default:
                        break;
                }
            }
        }

        if (!$this->name) {
            $this->name = $this->verb . '.' . $this->controller . '.' . $this->method;
        }
        if (!$this->event) {
            $this->event = 'init';
        }

        $this->router->getMatcher()->route($this->name, $this->url);
    }

    /**
     * Parse string into controller and method
     *
     * @param string $useString e.g. 'IndexController@create'
     *
     * @return $this
     */
    protected function setControllerMethod($useString)
    {
        list($controller, $method) = explode('@', $useString);
        $this->controller = $controller;
        $this->method = $method;
        return $this;
    }

}
