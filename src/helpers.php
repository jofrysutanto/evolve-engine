<?php

use Illuminate\Container\Container;
use Illuminate\Support\Str;

if (!function_exists('app')) {
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

if (!function_exists('root_path')) {
    /**
     * Get application root path
     *
     * @param  string $path
     *
     * @return string
     */
    function root_path($path = '')
    {
        return app()->rootPath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get application storage path
     *
     * @param  string $path
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return app()->storagePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get application base path
     *
     * @param  string $path
     *
     * @return string
     */
    function base_path($path = '')
    {
        return app()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config')) {
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

if (!function_exists('view')) {
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

if (!function_exists('route')) {
    /**
     * Route
     *
     * @param  string|null  $name
     * @param  array   $parameters
     *
     * @return String
     */
    function route($name = null, $parameters = [])
    {
        return app('router')->url($name, $parameters);
    }
}

if (!function_exists('asset')) {
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

if (!function_exists('asset_version')) {
    /**
     * Return asset version from manifest file
     *
     * @param string $path Override version manifest file path. If not supplied, default path will be used.
     *
     * @return string
     */
    function asset_version($path = null)
    {
        static $version = false;

        if ($version === false) {
            if (is_null($path)) {
                $path = base_path('assets/version.json');
            }

            if (!file_exists($path)) {
                $version = [];
            } else {
                $version = json_decode(file_get_contents($path), true);
            }
        }

        return isset($version['version'])
            ? $version['version']
            : '';
    }
}

if (!function_exists('get_cpt')) {
    /**
     * Convenient method to create post type instance
     *
     * @param  string  $id  Post type identifier
     * @return mixed
     */
    function get_cpt($id = 'post')
    {
        return app('post-type')->get($id);
    }
}

if (!function_exists('getcpt')) {
    /**
     * Alias to get_cpt()
     *
     * @param  string  $id  Post type identifier
     * @return mixed
     */
    function getcpt($id = 'post')
    {
        return get_cpt($id);
    }
}

if (!function_exists('env')) {
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

if (!function_exists('fluent')) {
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

if (!function_exists('strip_wp_slashes')) {
    function strip_wp_slashes($array)
    {
        $newArray = $array;

        foreach ($newArray as &$item) {
            if (is_string($item)) {
                $item = stripslashes($item);
            }
        }

        return $newArray;
    }
}

if (!function_exists('get_blog_url')) {
    function get_blog_url()
    {
        return get_permalink(get_option('page_for_posts'));
    }
}

if (!function_exists('make_image')) {
    function make_image($img, $size = 'thumbnail', $classes = [], $attributes = [])
    {
        if (is_array($img)) {
            $img = array_get($img, 'ID');
        }
        $attributes = array_merge($attributes, [
            'class' => implode(' ', $classes)
        ]);
        $imgTag = wp_get_attachment_image($img, $size, false, $attributes);
        if (empty($imgTag) && is_string($img) && !empty($img)) {
            $htmlAttributes = str_replace('=', '="', http_build_query($attributes, null, '" ', PHP_QUERY_RFC3986)) . '"';
            $htmlAttributes = str_replace('%20', ' ', $htmlAttributes);
            $imgTag = sprintf('<img src="%s" %s>', $img, $htmlAttributes);
        }
        return $imgTag;
    }
}
