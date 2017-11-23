<?php

namespace EvolveEngine\Post;

class PostTypeFactory
{

    /**
     * Post types namespace for the application
     *
     * @var string
     */
    protected $namespace = 'App\\PostTypes\\';

    /**
     * All resolvable custom post types
     *
     * @var array
     */
    protected $types = [];

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

    public function __construct($types = [])
    {
        $this->types = $types;
    }

    /**
     * Get resolved custom post type
     *
     * @param  string $id
     *
     * @return mixed
     */
    public function get($id)
    {
        if (isset($this->resolvedTypes[$id])) {
            return $this->resolvedTypes[$id];
        }

        $namespacedId = $this->applyNamespace($id);
        if (isset($this->resolvedAlias[$namespacedId])) {
            return $this->resolvedAlias[$namespacedId];
        }

        return null;
    }

    /**
     * Register all available custom post types
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->types as $type) {
            if (!class_exists($type)) {
                trigger_error('Missing post type class: ' . $type . '.');
                continue;
            }
            $typeInstance = with(new $type)->init();
            $this->resolvedAlias[$type] = $this->resolvedTypes[$typeInstance->id] = $typeInstance;
        }
    }

    /**
     * Alter where template is included if currently dealing with CPT
     *
     * @param  string $path
     *
     * @return string
     */
    public function includeTemplate($template_path)
    {
        if (is_search() || is_404()) {
            return $template_path;
        }

        $postType = get_post_type();
        // get_post_type() can sometimes be unreliable
        // another attempt is to look for in get_query_var
        if (!is_string($postType)) {
            $postType = get_query_var('post_type');
        }
        
        if (!in_array($postType, array_keys($this->resolvedTypes))) {
            return $template_path;
        }
        
        $post = $this->get($postType);
        $templateType = null;
        if (is_single()) {
            $templateType = 'single';
        }
        if (is_archive()) {
            $templateType = 'archive';
        }
        if ( $path = $post->getTemplatePath($templateType) ) {
            $template_path = $path;
        }
        return $template_path;
    }

    /**
     * Apply namespace to given post type id to shorten post type usage
     *
     * @param  string $id e.g. 'Product', which will be translated ot 'App\PostTypes\Product'
     *
     * @return string
     */
    protected function applyNamespace($id)
    {
        if (class_exists($id)) {
            return $id;
        }

        return $this->namespace . $id;
    }

}
