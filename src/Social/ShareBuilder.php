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
        $this->viewMaker = app('blade');
    }

    /**
     * Render share widget for given key
     *
     * @param  string $name
     * @param  string|null $title  (Optional) Can be inferred using resolver
     * @param  string|null $url    (Optional) Can be inferred using resolver
     *
     * @return string
     */
    public function render($name = 'default', $title = null, $url = null)
    {
        $shareConfig = array_get($this->config, $name);
        if (!$shareConfig) {
            return '';
        }

        if (!isset($this->cache[$name])) {
            $services = array_get($shareConfig, 'services', []);
            $customResolver = array_get($shareConfig, 'custom_resolver');

            $this->cache[$name] = $this->buildShareServices($services, $customResolver, $title, $url);
        }

        $shareable = $this->cache[$name];
        $template  = array_get($shareConfig, 'template');

        return $this->renderTemplate($template, $shareable);
    }

    /**
     * Creates collection of share object
     *
     * @param  array  $services
     * @param  array|null  $customResolver Custom resolver
     * @param  string|null $title  (Optional) Can be inferred using resolver
     * @param  string|null $url    (Optional) Can be inferred using resolver
     *
     * @return Collection
     */
    protected function buildShareServices($services, $customResolver = null, $title = null, $url = null)
    {
        $shareable = [];
        foreach ($services as $service) {
            $shareService = new \Illuminate\Support\Fluent([
                'service' => $service,
                'url'     => $this->resolveServiceUrl($service, $customResolver, $title, $url)
            ]);
            $shareable[] = $shareService;
        }

        return $shareable;
    }

    /**
     * Returns share url for given service
     *
     * @param  string     $serviceName Key name of service
     * @param  array|null $customResolver Custom resolver
     * @param  string|null $title  (Optional) Can be inferred using resolver
     * @param  string|null $url    (Optional) Can be inferred using resolver
     *
     * @return string
     */
    protected function resolveServiceUrl($serviceName, $customResolver = null, $title = null, $url = null)
    {
        if (!is_null($title) && !is_null($url)) {
            $shareUrl   = $url;
            $shareTitle = $title;
        } elseif (is_null($customResolver)) {
            $shareUrl   = app('request')->fullUrl();
            $shareTitle = function_exists('get_the_title');
        } elseif (is_array($customResolver)) {
            $shareTitle = array_get($customResolver, 'title');
            $shareUrl = array_get($customResolver, 'url');
        } elseif (is_string($customResolver) && strpos($customResolver, '@') !== false) {
            list($class, $method) = explode('@', $customResolver);
            $result = app($class)->{$method}();
            $shareTitle = array_get($result, 'title');
            $shareUrl = array_get($result, 'url');
        }

        /**
         * @see https://simplesharebuttons.com/html-share-buttons/
         */
        switch ($serviceName) {
            case 'facebook':
                $serviceUrl = "http://www.facebook.com/sharer.php?u=%url%";
                break;
            case 'twitter':
                $serviceUrl = "https://twitter.com/share?url=%url%&amp;text=%title%";
                break;
            case 'linkedin':
                $serviceUrl = "http://www.linkedin.com/shareArticle?mini=true&amp;url=%url%";
                break;
            case 'googleplus':
                $serviceUrl = "https://plus.google.com/share?url=%url%";
                break;
            case 'email':
                $serviceUrl = "mailto:?Subject=%title%&amp;Body=%url%";
                break;
            default:
                break;
        }

        $serviceUrl = str_replace('%url%', $shareUrl, $serviceUrl);
        $serviceUrl = str_replace('%title%', $shareTitle, $serviceUrl);

        return $serviceUrl;
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
            return $this->viewMaker->render($template, $viewVars);
        }

        return $this->viewMaker->render($this->getDefaultTemplate(), $viewVars);
    }

    /**
     * Path to default template for share widget
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'share.blade.php';
    }
}
