<?php

namespace EvolveEngine\Widget;

class WidgetFactory
{
    /**
     * All resolvable custom post types
     *
     * @var array
     */
    protected $widgets = [];

    /**
     * Instance of each custom post type
     *
     * @var array
     */
    protected $resolvedTypes = [];

    /**
     * Alias of each resolved custom post type
     *
     * @var array
     */
    protected $resolvedAlias = [];

    public function __construct($widgets = [])
    {
        $this->widgets = $widgets;
    }

    /**
     * Register all available custom post types
     *
     * @return void
     */
    public function register()
    {
        foreach($this->widgets as $widget) {
            if (!class_exists($widget)) {
                trigger_error('Missing Widget Class: ' . $widget . '.');
                die;
            }

            register_widget($widget);
        }
    }
}
