<?php

use Illuminate\Container\Container;
use Illuminate\Support\Str;

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $make
     * @param  array   $parameters
     * @return mixed|\Illuminate\Foundation\Application
     */
    function app($make = null, $parameters = [])
    {
        if (is_null($make)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($make, $parameters);
    }
}

if (! function_exists('config')) {
    /**
     * Return configuration value
     *
     * @param  string|null  $name
     * 
     * @return mixed
     */
    function config($name = '')
    {
        return app('config')->get($name);
    }
}

if (! function_exists('view')) {
    /**
     * Include view template
     *
     * @param  string|null  $name  
     * @param  array   $parameters
     * @return boolean $overrideRoot
     * 
     * @return mixed|ViewMaker
     */
    function view($name = null, $parameters = [], $overrideRoot = false)
    {
        $viewMaker = app('view-maker');
        if (is_null($name)) {
            return $viewMaker;
        }

        return $viewMaker->make($name, $parameters, $overrideRoot);
    }
}

if (! function_exists('asset')) {
    /**
     * Return asset link
     *
     * @param  string|null  $name
     * 
     * @return string
     */
    function asset($name = '')
    {
        return get_template_directory_uri() . '/assets/' . $name;
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('fluent')) {
    /**
     * Creates new fluent object
     *
     * @param  mixed
     * 
     * @return Illuminate\Support\Fluent
     */
    function fluent($object = [])
    {
        return new Illuminate\Support\Fluent($object);
    }
}