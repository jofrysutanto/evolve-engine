<?php

namespace EvolveEngine\Utils;

use EvolveEngine\Core\ServiceProvider;

class UtilityProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('truelib', function ()
        {
            return new TrueLib; 
        });
        $this->app->singleton('adminstyles', function ()
        {
            return new AdminStyles; 
        });

        $this->app->filter('acf/format_value/type=wysiwyg', 'truelib@formatWysiwygFieldValue', 20, 3);


        if(is_admin()) {
            // ACF Hooks
            $this->app->filter('acf/render_field/type=message', 'adminstyles@customACFStyle', 8, 1);
            $this->app->filter('acf/render_field/type=image', 'adminstyles@customACFStyle', 8, 1);
            $this->app->filter('acf/load_field/type=image', 'adminstyles@renderSize');
            $this->app->action( 'admin_menu', 'adminstyles@removeMetaBoxes');
        }

        // Login
        $this->app->action('login_head', 'adminstyles@loginStyles');

        // Admin footer
        $this->app->filter('admin_footer_text', 'adminstyles@trueFooter');
    }

}
