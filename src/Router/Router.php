<?php

namespace EvolveEngine\Router;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = [];

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

    /**
     * Get the URL
     *
     * @param  String $name  
     * @param  array  $params
     *
     * @return String 
     */
    public function url($name, $params = [])
    {
        return $this->container['config']->get('app.url') . $this->matcher->url($name, $params);
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param  string  $uri
     * @return string
     */
    protected function prefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  array     $attributes
     * @param  \Closure  $callback
     * @return void
     */
    public function group(array $attributes, Closure $callback)
    {
        $this->updateGroupStack($attributes);

        // Once we have updated the group stack, we will execute the user Closure and
        // merge in the groups attributes when the route is created. After we have
        // run the callback, we will pop the attributes off of this group stack.
        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes)
    {
        if (! empty($this->groupStack)) {
            $attributes = $this->mergeGroup($attributes, end($this->groupStack));
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     * @return array
     */
    public function mergeWithLastGroup($new)
    {
        return $this->mergeGroup($new, end($this->groupStack));
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return array
     */
    public static function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);

        $new['prefix'] = static::formatGroupPrefix($new, $old);

        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        $new['where'] = array_merge(
            isset($old['where']) ? $old['where'] : [],
            isset($new['where']) ? $new['where'] : []
        );

        if (isset($old['as'])) {
            $new['as'] = $old['as'].(isset($new['as']) ? $new['as'] : '');
        }

        return array_merge_recursive(Arr::except($old, ['namespace', 'prefix', 'where', 'as']), $new);
    }

    /**
     * Format the uses prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatUsesPrefix($new, $old)
    {
        if (isset($new['namespace'])) {
            return isset($old['namespace'])
                    ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                    : trim($new['namespace'], '\\');
        }

        return isset($old['namespace']) ? $old['namespace'] : null;
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     * @return string|null
     */
    protected static function formatGroupPrefix($new, $old)
    {
        $oldPrefix = isset($old['prefix']) ? $old['prefix'] : null;

        if (isset($new['prefix'])) {
            return trim($oldPrefix, '/').'/'.trim($new['prefix'], '/');
        }

        return $oldPrefix;
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix()
    {
        if (! empty($this->groupStack)) {
            $last = end($this->groupStack);

            return isset($last['prefix']) ? $last['prefix'] : '';
        }

        return '';
    }

    /**
     * Determine if the router currently has a group stack.
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return ! empty($this->groupStack);
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
    public function registerRoute($verb, $url, $action)
    {
        $url = $this->prefix($url);

        // If the route is routing to a controller we will parse the route action into
        // an acceptable array format before registering it and creating this route
        // instance itself. We need to build the Closure that will call this out.
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }

        $route = new Route($this, $verb, $url, $action);

        array_unshift($this->routes, $route);

        return $route;
    }

    /**
     * Prepend the last group uses onto the use clause.
     *
     * @param  string  $uses
     * @return string
     */
    protected function prependGroupUses($uses)
    {
        $group = end($this->groupStack);

        return isset($group['namespace']) && strpos($uses, '\\') !== 0 ? $group['namespace'].'\\'.$uses : $uses;
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
     * Determine if the action is routing to a controller.
     *
     * @param  array  $action
     * @return bool
     */
    protected function actionReferencesController($action)
    {
        if ($action instanceof Closure) {
            return false;
        }

        return is_string($action) || is_string(isset($action['uses']) ? $action['uses'] : null);
    }

    /**
     * Add a controller based route action to the action array.
     *
     * @param  array|string  $action
     * @return array
     */
    protected function convertToControllerAction($action)
    {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }

        // Here we'll merge any group "uses" statement if necessary so that the action
        // has the proper clause for this property. Then we can simply set the name
        // of the controller on the action and return the action array for usage.
        if (! empty($this->groupStack)) {
            $action['uses'] = $this->prependGroupUses($action['uses']);
        }

        // Here we will set this controller name on the action array just so we always
        // have a copy of it for reference if we need it. This can be used while we
        // search for a controller name or do some other type of fetch operation.
        $action['controller'] = $action['uses'];

        return $action;
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
     * Get all registered routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
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
