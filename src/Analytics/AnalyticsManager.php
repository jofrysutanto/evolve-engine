<?php

namespace EvolveEngine\Analytics;

class AnalyticsManager
{

    protected $config;

    protected $view;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->view   = app('view-maker');
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
                break;
        }
    }

    /**
     * Render Google Analytics
     */
    public function renderGoogleAnalytics() {
        foreach ($this->config['services']['google-analytics'] as $configValue) {
            echo $this->renderSingleGoogleAnalytics($configValue);
        }
    }

    /**
     * Render Google Tag Manager
     *
     * @param string $isHead
     */
    public function renderGoogleTagManager($isHead = true) {
        if ($isHead) {
            foreach ($this->config['services']['google-tag-manager'] as $configValue) {
                echo $this->renderSingleGoogleTagManager($configValue);
            }
        }
        else {
            foreach ($this->config['services']['google-tag-manager'] as $configValue) {
                echo $this->renderSingleGoogleTagManagerNoScript($configValue);
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
        if ($this->view->exists('analytics/google-analytics')) {
            return $this->view->make('analytics/google-analytics', ['code' => $code]);
        }

        return $this->view->make(__DIR__ . '/views/google-analytics', ['code' => $code], true);
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
        if ($this->view->exists('analytics/google-tag-manager')) {
            return $this->view->make('analytics/google-tag-manager', ['code' => $code]);
        }

        return $this->view->make(__DIR__ . '/views/google-tag-manager', ['code' => $code], true);
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

        return $this->view->make(__DIR__ . '/views/google-tag-manager-no-script', ['code' => $code], true);
    }
}
