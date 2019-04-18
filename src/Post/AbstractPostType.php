<?php

namespace EvolveEngine\Post;

abstract class AbstractPostType
{
    /**
     * Create new post type, by extending from AbstractPostType
     * and register them as part of config/post-types
     */

    /**
     * @var string  The unique key of the custom post type
     */
    public $id = 'true_cpt';

    /**
     * @var string  Singular form
     */
    protected $singleName = 'CPT';

    /**
     * @var string  Plural form
     */
    protected $pluralName = 'CPTs';

    /**
     * @var string  URL friendly name of the post type
     */
    protected $slug = 'cpt';

    /**
     * @var string  Name of the template used to render single post type.
     *              The file must be located within page-templates/*.php
     */
    protected $singleTemplateName = 'single-cpt';

    /**
     * @var string  Name of the template used to render post type archive.
     *              The file must be located within page-templates/*.php
     */
    protected $archiveTemplateName = 'archive-cpt';

    /**
     * @var string  Icon used in backend. Usually uses dashicons set, e.g. `dashicons-format-status`
     *
     * @see https://developer.wordpress.org/resource/dashicons/
     */
    protected $menuIcon = null;

    /**
     * @var string  Icon image used in admin backend. This overrides $menuIcon
     */
    protected $menuImage = 'trueKeylockWPIcons';

    /**
     * @var array  List of post type supported Wordpress feature.
     *
     * @see https://codex.wordpress.org/Function_Reference/add_post_type_support
     */
    protected $supports = ['title'];

    /**
     * Add ACF Tax options, will be accessible using `cpt_{post_type}`
     * e.g. get_field('banner_image', 'cpt_true_post_type')
     *
     * @var boolean
     */
    public $hasAcfArchive = true;

    /**
     * @var array  List of taxonomy
     */
    protected $tax = [
        // 'true_cpt_tax' => [
        //     'slug'   => 'cpts',
        //     'single' => 'Category',
        //     'plural' => 'Categories',
        // ]
    ];

    /**
     * Allow REST API hook to this post type
     *
     * @var boolean
     */
    public $hasApi = false;

    /**
     * ACF fields to be exposed via REST API
     *
     * @var array
     */
    protected $acfApi = [
    ];

    /**
     * Register post type
     *
     * @return $this
     */
    public function init()
    {
        $args = [
            'labels' => [
                'name'                => __($this->pluralName, 'TRUE'),
                'singular_name'       => __($this->singleName, 'TRUE'),
                'add_new_item'        => __('New ' . $this->singleName, 'TRUE'),
                'add_new'             => __('New ' . $this->singleName, 'TRUE'),
                'edit_item'           => __('Edit ' . $this->singleName, 'TRUE'),
                'update_item'         => __('Update ' . $this->singleName, 'TRUE'),
                'search_items'        => __('Search ' . $this->pluralName, 'TRUE'),
                'not_found'           => __('No ' . $this->pluralName . ' found', 'TRUE'),
                'not_found_in_trash'  => __('No ' . $this->pluralName . ' found in Trash', 'TRUE'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'show_ui'             => true,
            'menu_icon'           => $this->getIcon(),
            'supports'            => $this->supports,
            'exclude_from_search' => false,
            'show_in_rest'        => $this->hasApi
        ];

        if (trim($this->slug) != '') {
            $args['rewrite'] = ['slug' => $this->slug, 'with_front' => false];
        } else {
            $args['rewrite'] = false;
        }

        $args = $this->extendConfigurations($args);

        register_post_type($this->id, $args);

        $this->createTaxonomy();

        return $this;
    }

    protected function createTaxonomy()
    {
        if (count($this->tax) <= 0) {
            return;
        }

        foreach ($this->tax as $taxId => $tax) {
            $this->registerTax($taxId, $tax);
        }
    }

    /**
     * Register single taxonomy
     *
     * @param  string $id  Unique taxonomy identifier
     * @param  array  $tax Taxonomy configuration
     *
     * @return void
     */
    protected function registerTax($taxID, $tax)
    {
        // Register Custom Taxonomy
        $labels = [
            'name'                       => _x($this->singleName . ' ' . $tax['plural'] . '', 'Taxonomy General Name', 'text_domain'),
            'singular_name'              => _x($this->singleName . ' ' . $tax['single'] . '', 'Taxonomy Singular Name', 'text_domain'),

            'menu_name'                  => __($tax['plural'] . '', 'text_domain'),

            'all_items'                  => __($this->singleName . ' ' . $tax['plural'] . '', 'text_domain'),
            'parent_item'                => __('Parent ' . $this->singleName . ' ' . $tax['single'] . '', 'text_domain'),
            'parent_item_colon'          => __('Parent ' . $this->singleName . ' ' . $tax['single'] . ':', 'text_domain'),
            'new_item_name'              => __('New ' . $this->singleName . ' ' . $tax['single'] . '', 'text_domain'),
            'add_new_item'               => __('Add New ' . $this->singleName . ' ' . $tax['single'] . '', 'text_domain'),
            'edit_item'                  => __('Edit ' . $this->singleName . ' ' . $tax['single'] . '', 'text_domain'),
            'update_item'                => __('Update ' . $this->singleName . ' ' . $tax['single'] . '', 'text_domain'),

            'separate_items_with_commas' => __('Separate ' . $this->singleName . ' ' . $tax['plural'] . ' with commas', 'text_domain'),

            'search_items'               => __('Search ' . $this->singleName . ' ' . $tax['plural'] . '', 'text_domain'),

            'add_or_remove_items'        => __('Add or remove ' . $this->singleName . ' ' . $tax['plural'] . '', 'text_domain'),

            'choose_from_most_used'      => __('Choose from the most used ' . $this->singleName . ' ' . $tax['plural'] . '', 'text_domain'),
        ];

        $args = [
            'labels'             => $labels,
            'hierarchical'       => true,
            'public'             => true,
            'show_ui'            => true,
            'show_admin_column'  => true,
            'show_in_nav_menus'  => true,
            'rewrite'            => ['with_front'=> false, 'slug' => $tax['slug']],
            'show_tagcloud'      => false,
            'show_in_rest'       => $this->hasApi
        ];

        $args = $this->extendTaxConfigurations($taxID, $args);

        register_taxonomy($taxID, $this->id, $args);
    }

    /**
     * Return template path
     *
     * @param  string $type
     *
     * @return string
     */
    public function getTemplatePath($type = 'single')
    {
        $path = false;
        switch ($type) {
            case 'archive':
                $path = $this->archiveTemplateName;
                break;
            case 'single':
            default:
                $path = $this->singleTemplateName;
                break;
        }

        if (!$path) {
            return false;
        }

        $path = 'views/' . $path;
        if (!ends_with($path, '.blade.php')) {
            $path .= '.blade.php';
        }

        $filePath = locate_template([$path]);

        if (!$filePath) {
            return false;
        }

        return $filePath;
    }

    /**
     * Retrieve path to post type backend icon
     *
     * @param  string $name
     *
     * @return string
     */
    protected function getIcon()
    {
        if ($this->menuIcon != null) {
            $icon = $this->menuIcon;
        } else {
            $icon = asset('img/true-icons/' . $this->menuImage . '.png');
        }

        return $icon;
    }

    /**
     * Allow extension to configurations when registering custom post type
     *
     * @param  array  $config  Existing configuration
     *
     * @return array
     */
    protected function extendConfigurations($config)
    {
        // Modify configuration here and return $config
        return $config;
    }

    /**
     * Allow extension to configurations when registering custom post type
     *
     * @param  array  $config  Existing configuration
     *
     * @return array
     */
    protected function extendTaxConfigurations($tax, $config)
    {
        // Modify configuration here and return $config
        return $config;
    }

    //
    // Queries
    //

    public function whereIn($ids = [])
    {
        return get_posts([
            'post_type' => $this->id,
            'post__in'  => $ids
        ]);
    }

    /**
     * Get Posts
     *
     * @return Array
     */
    public function get()
    {
        return get_posts([
            'post_type' => $this->id,
            'status'    => 'publish'
        ]);
    }

    //
    // REST API
    //

    /**
     * Retrieve and merge values to be returned via API calls
     *
     * @param mixed $object
     * @param string $field_name
     * @param object $request
     * @return void
     */
    public function retrieveApiMeta($object, $field_name, $request)
    {
        $values = [];
        foreach ($this->acfApi as $key) {
            $values[$key] = get_field($key, $object['id']);
        }
        return $this->apiGet($values, $object, $request);
    }

    /**
     * Retrieve additional field for given post,
     * typically used to supply additional value to return post object
     * such as ACF fields
     *
     * @param array $values
     * @param array $object
     * @param object $request
     * @return mixed
     */
    public function apiGet($values, $object, $request)
    {
        return $values;
    }
}
