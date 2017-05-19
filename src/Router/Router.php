<?php

namespace EvolveEngine\Router;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use October\Rain\Router\Router as OctoberRouter;

class Router
{

    /**
     * @var OctoberRouter
     */
    protected $matcher;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Array
     */
    protected $routes = [];

    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $app, Request $request)
    {
        $this->request   = $request;
        $this->container = $app;

        $this->matcher = new OctoberRouter;
    }

    /**
     * Get container instance
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Register 'GET' route
     *
     * @param  string $url    
     * @param  mixed  $config
     *
     * @return Route
     */
    public function get($url, $config)
    {
        return $this->registerRoute('get', $url, $config);
    }

    /**
     * Register 'POST' route
     *
     * @param  string $url    
     * @param  mixed  $config
     *
     * @return Route
     */
    public function post($url, $config)
    {
        return $this->registerRoute('post', $url, $config);
    }

    public function url($name, $params = [])
    {
        return $this->matcher->url($name, $params);
    }

    /**
     * Register new route
     *
     * @param  string $verb   'GET' or 'POST'
     * @param  string $url    
     * @param  array  $config
     *
     * @return Route
     */
    public function registerRoute($verb, $url, $config)
    {
        $route = new Route($this, $verb, $url, $config);

        array_unshift($this->routes, $route);

        return $route;
    }

    /**
     * Bind router into wordpress's action cycle,
     * which gives router the opportunity to intercept the timeline,
     * allowing custom action and returning custom response
     *
     * @param string      $type  Event type 
     * @param string|null $url   Url to respond to
     * @param string|null $verb  Verb
     *
     * @return mixed
     */
    public function runAvailableRoutesFor($type, $url = null, $verb = null)
    {
        if (is_null($url)) {
            $url = $this->request->path();
        }
        if (is_null($verb)) {
            $verb = $this->request->method();
        }

        if (!$this->matcher->match($url)) {
            return false;
        }

        $routeId = $this->matcher->matchedRoute();
        $runningRoute = null;
        foreach ($this->routes as $route) {
            if ($route->shouldRun($type, $verb, $routeId)) {
                $runningRoute = $route;
                break;
            }
        }

        if (!$runningRoute) {
            return false;
        }

        return $runningRoute;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function params()
    {
        return $this->matcher->getParameters();
    }

    /**
     * Get matcher instance
     *
     * @return OctoberRouter
     */
    public function getMatcher()
    {
        return $this->matcher;
    }

    /**
     * Wordpress action hook
     * Trigger registered routes at wordpress 'init' action
     *
     * @return void
     */
    public function onInit()
    {
        if ($route = $this->runAvailableRoutesFor('init')) {
            return $this->assessResponse($route->runAction());
        }
    }

    /**
     * Wordpress action hook
     * Trigger registered routes at wordpress 'wp_loaded' action
     *
     * @return void
     */
    public function onWpLoad()
    {
        if ($route = $this->runAvailableRoutesFor('wp_loaded')) {
            return $this->assessResponse($route->runAction());
        }
    }

    /**
     * Assess response from route
     *
     * @param  mixed $response
     *
     * @return mixed
     */
    protected function assessResponse($response)
    {
        if (!$response) {
            return;
        }

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            $response->send();
            exit;
        }
    }

}
