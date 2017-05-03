<?php
namespace EvolveEngine\Social;

class ShareBuilder
{   

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array Cached share widget, so it's only run once per request
     */
    protected $cache;

    /**
     * @var ViewMaker
     */
    protected $viewMaker;

    public function __construct($config = [])
    {
        $this->config    = $config;
        $this->viewMaker = app('view-maker');
    }

    /**
     * Render share widget for given key
     *
     * @param  string $name 
     *
     * @return string
     */
    public function render($name = 'default')
    {
        $shareConfig = array_get($this->config, $name);
        if (!$shareConfig) {
            return '';
        }

        if (!isset($this->cache[$name])) {
            $services = array_get($shareConfig, 'services', []);
            $urlResolver = array_get($shareConfig, 'custom_resolver');

            $this->cache[$name] = $this->buildShareServices($services, $urlResolver);
        }

        $shareable = $this->cache[$name];
        $template  = array_get($shareConfig, 'template');

        return $this->renderTemplate($template, $shareable);
    }

    /**
     * Creates collection of share object
     *
     * @param  array  $services
     * @param  string|null  $urlResolver URL customiser 
     *
     * @return Collection
     */
    protected function buildShareServices($services, $urlResolver = null)
    {
        $shareable = [];
        foreach ($services as $service) {
            $shareService = new \Illuminate\Support\Fluent([
                'service' => $service,
                'url'     => $this->resolveServiceUrl($service, $urlResolver)
            ]);
            $shareable[] = $shareService;
        }

        return $shareable;
    }

    /**
     * Returns share url for given service
     *
     * @param  string       $serviceName Key name of service
     * @param  string|null  $urlResolver URL customiser 
     *
     * @return string
     */
    protected function resolveServiceUrl($serviceName, $urlResolver = null)
    {
        if (is_null($urlResolver)) {
            return app('request')->fullUrl();
        }
        if (is_string($urlResolver)) {
            return $urlResolver;
        }
    }

    /**
     * Render share widget to given template
     *
     * @param  string|null $template  The template used to render share widget. If null, a default tempalte will be rendered.
     * @param  Collection  $shareable Shareable Collection object
     *
     * @return string
     */
    protected function renderTemplate($template, $shareable)
    {
        $viewVars = compact('shareable');

        if (is_string($template) && $this->viewMaker->exists($template)) {
            return $this->viewMaker->make($template, $viewVars);
        }

        return $this->viewMaker->make($this->getDefaultTemplate(), $viewVars, true);
    }

    /**
     * Path to default template for share widget
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'share';
    }

}
