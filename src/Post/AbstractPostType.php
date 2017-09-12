<?php

namespace EvolveEngine\Post;

abstract class AbstractPostType
{
    
    /* -------------------------------------------------- */
    /* Edit these variables
    /* -------------------------------------------------- */
    public $id                     = 'true_cpt';

    protected $singleName          = 'CPT';
    protected $pluralName          = 'CPTs';
    protected $slug                = 'cpt';
    protected $singleTemplateName  = 'single-cpt';
    protected $archiveTemplateName = 'archive-cpt';
    protected $menuImage           = 'trueKeylockWPIcons';
    protected $menuIcon            = null;

    protected $supports = ['title'];

    protected $tax = [
        'true_cpt_tax' => [
            'slug'   => 'cpts',
            'single' => 'Category',
            'plural' => 'Categories',
        ]  
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
                'name' => __( $this->pluralName, 'TRUE'),
                'singular_name' => __( $this->singleName, 'TRUE'),
                'add_new_item'  => __( 'New ' . $this->singleName, 'TRUE'),
                'add_new'             => __( 'New ' . $this->singleName, 'TRUE'),
                'edit_item'           => __( 'Edit ' . $this->singleName, 'TRUE'),
                'update_item'         => __( 'Update ' . $this->singleName, 'TRUE'),
                'search_items'        => __( 'Search ' . $this->pluralName, 'TRUE'),
                'not_found'           => __( 'No ' . $this->pluralName . ' found', 'TRUE'),
                'not_found_in_trash'  => __( 'No ' . $this->pluralName . ' found in Trash', 'TRUE'),
            ],
            'public'              => true,
            'has_archive'         => true,
            'show_ui'             => true,
            'menu_icon'           => $this->getIcon(),
            'supports'            => $this->supports,
            'exclude_from_search' => false
        ];
            
        if(trim($this->slug) != '') {
            $args['rewrite'] = ['slug' => $this->slug, 'with_front' => false];
        } else {
            $args['rewrite'] = false;
        }

        $args = $this->extendConfigurations($args);
        
        register_post_type( $this->id, $args);

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
        $labels = array(
            'name'                       => _x( $this->singleName . ' '.$tax['plural'].'', 'Taxonomy General Name', 'text_domain' ),
            'singular_name'              => _x( $this->singleName . ' '.$tax['single'].'', 'Taxonomy Singular Name', 'text_domain' ),
        
            'menu_name'                  => __( $tax['plural'].'', 'text_domain' ),
        
            'all_items'                  => __( $this->singleName . ' '.$tax['plural'].'', 'text_domain' ),
            'parent_item'                => __( 'Parent ' . $this->singleName . ' '.$tax['single'].'', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent ' . $this->singleName . ' '.$tax['single'].':', 'text_domain' ),
            'new_item_name'              => __( 'New ' . $this->singleName . ' '.$tax['single'].'', 'text_domain' ),
            'add_new_item'               => __( 'Add New ' . $this->singleName . ' '.$tax['single'].'', 'text_domain' ),
            'edit_item'                  => __( 'Edit ' . $this->singleName . ' '.$tax['single'].'', 'text_domain' ),
            'update_item'                => __( 'Update ' . $this->singleName . ' '.$tax['single'].'', 'text_domain' ),
        
            'separate_items_with_commas' => __( 'Separate ' . $this->singleName . ' '.$tax['plural'].' with commas', 'text_domain' ),
        
            'search_items'               => __( 'Search ' . $this->singleName . ' '.$tax['plural'].'', 'text_domain' ),
        
            'add_or_remove_items'        => __( 'Add or remove ' . $this->singleName . ' '.$tax['plural'].'', 'text_domain' ),
        
            'choose_from_most_used'      => __( 'Choose from the most used ' . $this->singleName . ' '.$tax['plural'].'', 'text_domain' ),
        );
        
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'rewrite'                    => array('with_front'=> false, 'slug' => $tax['slug']),
            'show_tagcloud'              => false,
        );

        $args = $this->extendTaxConfigurations($taxID, $args);
        
        register_taxonomy($taxID, $this->id, $args );
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

        $path = 'page-templates/' . $path;
        if (!ends_with($path, '.php')) {
            $path .= '.php';
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
        if($this->menuIcon != null) {
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
            'post__in' => $ids
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
            'status' => 'publish'
        ]);
    }
    
}