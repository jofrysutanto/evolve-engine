<?php

namespace EvolveEngine\Sentinel;

use Illuminate\Http\Request;

class SentinelAgent
{   
    /**
     * @var String
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $routes = [
        'status' => 'EvolveEngine\Sentinel\ApiController@status',
        'update' => 'EvolveEngine\Sentinel\ApiController@update',
    ];

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Activate routing, to accept incoming api request
     *
     * @return void
     */
    public function registerRouting($router, Request $request)
    {
        if (!$this->isSentinelRequest($request)) {
            return;
        }

        foreach ($this->routes as $url => $method) {
            $router->get($this->prependBaseUrl($url), $method);
        }
    }

    /**
     * Prepend base sentinel url endpoint
     *
     * @param  string $url
     *
     * @return string
     */
    protected function prependBaseUrl($url)
    {
        return $this->baseUrl . $url;
    }

    /**
     * Check if we should process current request as sentinel request
     *
     * @param  Request $request
     *
     * @return boolean
     */
    protected function isSentinelRequest(Request $request)
    {
        return true;
        if ($request->wantsJson() && $request->header('HQ_SENTINEL', null)) {
            return true;
        }

        return false;
    }

}
