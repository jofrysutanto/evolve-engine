<?php
/**
* Nav Picker Field v5
*
* @package ACF Nav Picker
*/

namespace EvolveEngine\Acf\Extensions;

/**
 * ACFTrueNavPicker5 Class
 * 5 stands for ACF5
 *
 * This class contains all the custom workings for the Nav Menu Field for ACF v5
 */
class NavPicker extends \acf_field {

    /**
     * Sets up some default values and delegats work to the parent constructor.
     */
    public function __construct() {
        $this->name     = 'nav_picker';
        $this->label    = __( 'Nav Picker' );
        $this->category = 'relational';
        $this->defaults = array(
            // 'save_format' => 'id',
            'allow_null'  => 0,
            // 'container'   => 'div',
        );

        parent::__construct();
    }

    /**
     * Renders the Nav Menu Field options seen when editing a Nav Menu Field.
     *
     * @param array $field The array representation of the current Nav Menu Field.
     */
    public function render_field_settings( $field ) {
        // Register the Return Value format setting
        acf_render_field_setting( $field, array(
            'label'        => __( 'Navigation' ),
            'instructions' => __( 'Select the source of nav item' ),
            'type'         => 'select',
            'name'         => 'nav_source',
            'choices'      => $this->getNavs(),
        ) );
    }

    private function getNavs()
    {
        $nav_menus  = $this->get_nav_menus();
        return $nav_menus;
    }

    /**
     * Renders the Nav Menu Field.
     *
     * @param array $field The array representation of the current Nav Menu Field.
     */
    public function render_field( $field ) {
        $nav_menus  = wp_get_nav_menu_items($field['nav_source']);
        $navs = [];
        foreach ((array) $nav_menus as $key => $menu_item) {
            if (intval($menu_item->menu_item_parent) !== 0) {
                continue;
            }
            $navs[$key] = $menu_item;
        }

        if ( empty( $navs ) ) {
            return;
        }
        ?>
        <select id="<?php esc_attr( $field['id'] ); ?>" class="<?php echo esc_attr( $field['class'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
        <?php foreach ( $navs as $key => $menu_item ): ?>
            <option value="<?php echo esc_attr( $menu_item->ID ); ?>" <?php selected( $field['value'], $menu_item->ID ); ?>>
                <?php echo esc_html( $menu_item->title ); ?>
            </option>
        <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Gets a list of Nav Menus indexed by their Nav Menu IDs.
     *
     * @param bool $allow_null If true, prepends the null option.
     *
     * @return array An array of Nav Menus indexed by their Nav Menu IDs.
     */
    private function get_nav_menus() {
        $navs = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

        $nav_menus = array();

        foreach ( $navs as $nav ) {
            $nav_menus[ $nav->term_id ] = $nav->name;
        }

        return $nav_menus;
    }

    /**
     * Renders the Nav Menu Field.
     *
     * @param int   $value   The Nav Menu ID selected for this Nav Menu Field.
     * @param int   $post_id The Post ID this $value is associated with.
     * @param array $field   The array representation of the current Nav Menu Field.
     *
     * @return mixed The Nav Menu ID, or the Nav Menu HTML, or the Nav Menu Object, or false.
     */
    public function format_value( $value, $post_id, $field ) {
        // bail early if no value
        if ( empty( $value ) ) {
            return false;
        }

        // Just return the Nav Menu ID
        return $value;
    }
}
