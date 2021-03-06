<?php

namespace EvolveEngine\Core;

abstract class WordpressBase
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Alias of current wordpress base
     *
     * @var string
     */
    protected $alias;

    /**
     * Create a new base instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app, $alias)
    {
        $this->app   = $app;
        $this->alias = $alias;

        if ($this->app->bound($alias)) {
            throw new \Exception("Given alias `$alias` is already bound to container.");
        }

        // Register self into service container
        $this->app->instance($this->alias, $this);
    }

    /**
     * Register all custom hooks and filters here
     * Use $this->filter() and $this->action() to register filters and actions.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Convenience shortcut to add filter
     *
     * @return void
     */
    public function filter($type, $action, $priority = null, $acceptedArgs = 1)
    {
        if (!is_array($action)) {
            $action = [$action];
        }
        foreach ($action as $actionMethod) {
            add_filter($type, $this->determineCallableInstance($actionMethod), $priority, $acceptedArgs);
        }
    }

    /**
     * Convenience shortcut to add action
     *
     * @return void
     */
    public function action($type, $action, $priority = null, $acceptedArgs = 1)
    {
        if (!is_array($action)) {
            $action = [$action];
        }
        foreach ($action as $actionMethod) {
            add_action($type, $this->determineCallableInstance($actionMethod), $priority, $acceptedArgs);
        }
    }

    /**
     * Convenience shortcut to add ajax hook
     *
     * @param  string  $name   
     * @param  string  $action 
     * @param  boolean $isPrivileged Must be logged in ('nopriv'). Defaults to false
     *
     * @return void
     */
    public function ajax($name, $action, $isPrivileged = false)
    {
        $prefixes = ['wp_ajax_'];
        
        if (!$isPrivileged) {
            $prefixes[] = 'wp_ajax_nopriv_';
        }

        foreach ($prefixes as $prefix) {
            $this->action($prefix . $name, $action);
        }
    }

    /**
     * Determine callable hooks/action method using '@' symbol.
     * If not available, current class's alias is inferred
     *
     * @param  string $action e.g. 'truelib@getCSS'
     *
     * @return array
     */
    protected function determineCallableInstance($action)
    {
        if (is_string($action) && !strpos($action, '@')) {
            $action = $this->alias . '@' . $action;
        }

        return $this->app->determineCallableInstance($action);
    }

}
