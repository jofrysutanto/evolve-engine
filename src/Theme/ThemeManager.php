<?php

namespace EvolveEngine\Theme;

class ThemeManager
{
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Boot theme manager
     *
     * @return void
     */
    public function boot()
    {
        $this->addImageSizes(array_get($this->config, 'image-sizes'));
    }

    /**
     * Register wordpress image sizes
     *
     * @param array  $sizes
     */
    protected function addImageSizes($sizes)
    {
        foreach ($sizes as $sizeKey => $size) {
            $name   = $sizeKey;
            $width  = isset($size[0]) ? $size[0] : 0;
            $height = isset($size[1]) ? $size[1] : 0;
            $crop   = isset($size[2]) ? $size[2] : false;
            add_image_size($name, $width, $height, $crop);
        }
    }

    /**
     * Load deferred scripts, this outputs required scripts for deferring
     *
     * @todo Work on this
     * !DO NOT USE, WORK IN PROGRESS!
     */
    public function deferredScripts()
    {
        $scripts = array_get($this->config, 'deferred-scripts');
        if (count($scripts) <= 0) {
            return;
        }

        $scripts = $this->resolveAssetsPath($scripts);
        $viewMaker = app('view-maker');

        // Give chance for developers to override their deferred scripts loader
        if ($viewMaker->exists('deferred-scripts')) {
            echo $viewMaker->make('deferred-scripts', compact('scripts'));
            return;
        }

        // Use built-in deferrer
        $this->loadDeferrerTemplate($scripts);
    }

    /**
     * Render <script> tags which defers given scripts
     *
     * @param array $scripts
     *
     * @return void
     */
    protected function loadDeferrerTemplate($scripts)
    {
        ob_start();
        ?>
        <script>
            window._deferred_scripts = <?= json_encode($scripts) ?>;
            function downloadJSAtOnload() {
                for (var i = _deferred_scripts.length - 1; i >= 0; i--) {
                    var script = _deferred_scripts[i];
                    var element = document.createElement("script");
                    element.src = script;
                    document.body.appendChild(element);
                }
            }
            if (window.addEventListener)
            window.addEventListener("load", downloadJSAtOnload, false);
            else if (window.attachEvent)
            window.attachEvent("onload", downloadJSAtOnload);
            else window.onload = downloadJSAtOnload;
        </script>
        <?php
        $html = ob_get_clean();
        echo $html;
    }

    /**
     * Resolve assets path
     *
     * @param  array  $assets
     *
     * @return void
     */
    public function resolveAssetsPath($assets = [])
    {
        $results = [];
        foreach ($assets as $asset) {
            if (starts_with($asset, '@')) {
                $asset = asset(ltrim($asset, '@'));
            }

            $results[] = $asset;
        }

        return $results;
    }

}
