<?php

namespace EvolveEngine\Template;

use EvolveEngine\Core\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('blade', function ($app) {
            $cachePath = config('view.compiled');
            if (!file_exists($cachePath)) {
                wp_mkdir_p($cachePath);
            }
            (new BladeProvider($app))->register();
            return new Blade($app['view']);
        });

        /**
         * Create @asset() Blade directive
         */
        $this->app['blade']
            ->compiler()
            ->directive('asset', function ($asset) {
                return "<?= asset_path({$asset}); ?>";
            });
    }

}
