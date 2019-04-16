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

        $compiler = $this->app['blade']->compiler();

        /**
         * Create @asset() Blade directive
         */
        $compiler->directive('asset', function ($asset) {
            return "<?= asset_path({$asset}); ?>";
        });

        /**
         * Create @svg() Blade directive
         * A simple 'include' to inline SVG
         */
        $compiler->directive('svg', function ($asset) {
            return "<?php include base_path('resources/assets/' . {$asset}); ?>";
        });

        /**
         * Create @fluent() Blade directive
         * Create fluent instance of given object
         */
        $compiler->directive('fluent', function ($obj) {
            return "<?php {$obj} = fluent({$obj}); ?>";
        });

        /**
         * Create @collect() Blade directive
         * Create collection instance of given object
         */
        $compiler->directive('collect', function ($obj) {
            return "<?php {$obj} = collect({$obj}); ?>";
        });

        /**
         * Create @target() Blade directive
         * A shorcut to conditionally `target="_blank"`
         * ```
         * $linkTarget = '_blank';
         * @target($linkTarget)
         * // Outputs 'target="_blank"'
         * @target($empty)
         * // Outputs nothing
         * ```
         */
        $compiler->directive('target', function ($obj) {
            return "<?php echo empty({$obj}) ? '' : 'target=\"' . {$obj} . '\"' ; ?>";
        });
    }
}
