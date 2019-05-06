<?php

namespace EvolveEngine\Analytics;

class AnalyticsManager
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var ViewMaker
     */
    protected $view;

    /**
     * Extendable analytics service
     *
     * @var array
     */
    protected $customCreator = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->view = app('blade');
    }

    /**
     * Render Google script by type
     *
     * @param string  $type
     *
     * @return void
     */
    public function render($type)
    {
        if (!$this->shouldUseAnalytics()) {
            return;
        }
        switch ($type) {
            case 'google-analytics':
                $this->renderGoogleAnalytics();
                break;
            case 'google-tag-manager':
                $this->renderGoogleTagManager();
                break;
            case 'google-tag-manager.noscript':
                $this->renderGoogleTagManager(false);
                break;
            default:
                $this->callCustomCreator($type);
                break;
        }
    }

    public function extend($key, $callable)
    {
        $this->customCreator[$key] = $callable;
        return $this;
    }

    public function callCustomCreator($type)
    {
        if (!isset($this->customCreator[$type])) {
            throw new \Exception("Service $type not registered");
        }

        $service = $this->customCreator[$type];
        $serviceConfig = array_get($this->config, 'services.' . $type);

        if (is_callable($service)) {
            echo call_user_func($service, $serviceConfig);
        } elseif (class_exists($service)) {
            echo with(new $service)->render($serviceConfig);
        }
        return;
    }

    /**
     * Inject script to head
     */
    public function injectHead()
    {
        foreach ($this->config['inject']['head'] as $config) {
            $this->render($config);
        }
    }

    /**
     * Inject script to footer
     */
    public function injectFooter()
    {
        foreach ($this->config['inject']['footer'] as $config) {
            $this->render($config);
        }
    }

    /**
     * Render Google Analytics
     */
    public function renderGoogleAnalytics()
    {
        foreach ($this->config['services']['google-analytics'] as $configValue) {
            if (!empty($configValue)) {
                echo $this->renderSingleGoogleAnalytics($configValue);
            }
        }
    }

    /**
     * Render Google Tag Manager
     *
     * @param string $isHead
     */
    public function renderGoogleTagManager($isHead = true)
    {
        if ($isHead) {
            foreach ($this->config['services']['google-tag-manager'] as $configValue) {
                if (!empty($configValue)) {
                    echo $this->renderSingleGoogleTagManager($configValue);
                }
            }
        } else {
            foreach ($this->config['services']['google-tag-manager'] as $configValue) {
                if (!empty($configValue)) {
                    echo $this->renderSingleGoogleTagManagerNoScript($configValue);
                }
            }
        }
    }

    /**
     * Render Single Google Analytics
     *
     * @param string $code
     *
     * @return string
     */
    public function renderSingleGoogleAnalytics($code)
    {
        if (!$this->view->exists('analytics/google-analytics')) {
            return;
        }
        return $this->view->render('analytics/google-analytics', ['code' => $code]);
    }

    /**
     * Render Single Google Tag Manager
     *
     * @param  string $code
     *
     * @return string
     */
    public function renderSingleGoogleTagManager($code)
    {
        if (!$this->view->exists('analytics/google-tag-manager')) {
            return;
        }
        return $this->view->render('analytics/google-tag-manager', ['code' => $code]);
    }

    /**
     * Render Single Google Tag Manager No Script
     *
     * @param  string $code
     *
     * @return string
     */
    public function renderSingleGoogleTagManagerNoScript($code)
    {
        if ($this->view->exists('analytics/google-tag-manager-no-script')) {
            return $this->view->make('analytics/google-tag-manager-no-script', ['code' => $code]);
        }

        return $this->view->makeWebRoot(__DIR__ . '/views/google-tag-manager-no-script', ['code' => $code], true);
    }

    /**
     * Check production environment
     *
     * @return boolean
     */
    protected function shouldUseAnalytics()
    {
        return app()->environment($this->config['environment']);
    }
}
