<?php

namespace EvolveEngine\Theme;

use EvolveEngine\Core\ServiceProvider;

class ThemeProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $themeOptions = $this->app['config']->get('theme');

        $this->registerAcfOptions(array_get($themeOptions, 'acf-options'));

        $this->addImageSizes(array_get($themeOptions, 'image-sizes'));
    }

    /**
     * Register all acf global options
     *
     * @param  array  $opts
     *
     * @return void
     */
    public function registerAcfOptions($opts)
    {
        if(!function_exists('acf_add_options_page')) {
            return;
        }

        foreach ($opts as $opt => $config) {
            acf_add_options_page(array(
                'page_title' => $opt,
                'icon_url'   => $this->app['truelib']->getImageURL($config['icon_url']),
            ));
        }
    }

    /**
     * Register wordpress image sizes
     *
     * @param array  $sizes
     */
    public function addImageSizes($sizes)
    {
        foreach ($sizes as $sizeKey => $size) {
            $name   = $sizeKey;
            $width  = isset($size[0]) ? $size[0] : 0;
            $height = isset($size[1]) ? $size[1] : 0;
            $crop   = isset($size[2]) ? $size[2] : false;
            add_image_size($name, $width, $height, $crop);
        }
    }

}
