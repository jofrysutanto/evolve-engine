<?php

namespace EvolveEngine\Acf;

use EvolveEngine\Core\ServiceProvider;
use EvolveEngine\Acf\Capsule\Manager;

class AcfServiceProvider extends ServiceProvider
{
    /**
     * All extensions to be registered
     *
     * @var array
     */
    protected $extensions = [
        Extensions\NavPicker::class,
        Extensions\NavMenu::class
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerExtensions();
        $this->registerAcfOptions($this->app['config']['acf.options']);

        $this->app->singleton('acf.capsule', function ($app) {
            return new Manager;
        });

        // Using our capsulated ACF
        $this->app->action('acf/init', 'acf.capsule@register');
    }

    /**
     * Register all acf extensions to wordpress
     *
     * @return void
     */
    protected function registerExtensions()
    {
        $extender = new Extender;
        $extensions = $this->app['config']['acf.extensions'];
        if (is_array($extensions)) {
            foreach ($extensions as $extend) {
                $extender->extendField($extend);
            }
        }

        $this->app->instance('acf-extensions', $extender);
        $this->app->action('acf/include_field_types', 'acf-extensions@register');
        $this->app->action('acf/input/admin_head', 'acf-extensions@seamlessContentFields');
        $this->app->action('wp_loaded', 'acf-extensions@addArchiveOptionsPage');

        // Aliasing acf helper
        $this->app->instance('acf-helper', AcfHelper::instance());
    }

    /**
     * Register all acf global options
     *
     * @param  array  $opts
     *
     * @return void
     */
    protected function registerAcfOptions($opts)
    {
        if (!function_exists('acf_add_options_page')) {
            return;
        }
        if (!$opts) {
            return;
        }

        foreach ($opts as $opt => $config) {
            if (isset($config['icon'])) {
                $iconUrl = $config['icon'];
            } elseif (isset($config['icon_url'])) {
                $iconUrl = $this->app['truelib']->getImageURL($config['icon_url']);
            } else {
                $iconUrl = 'dashicons-admin-generic';
            }

            acf_add_options_page([
                'page_title' => $opt,
                'icon_url'   => $iconUrl,
            ]);
        }
    }
}
