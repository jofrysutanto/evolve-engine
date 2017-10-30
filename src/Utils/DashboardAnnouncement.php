<?php

namespace EvolveEngine\Utils;

class DashboardAnnouncement
{

    protected $cachePath;

    protected $url;

    public function __construct($pathToCache, $url)
    {
        $this->cachePath = $pathToCache;
        $this->url = $url;
    }

    public function getAnnouncement()
    {
        $content = $this->query($this->url);
        $html = array_get($content, 'html');
        if (!$html) {
            $html = $this->fallbackHtml();
        }
        return $html;
    }

    protected function query($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $content = json_decode($result, true);
            return $content;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function fallbackHtml()
    {
        $logoUrl = 'https://trueagency.com.au/wp-content/themes/true/assets/svg/true-logo.svg';
        return '<div class="true_widget_detail" style="font-family:helvetica;"><strong>Welcome to your Dashboard</strong><br/><br/>For support, please contact us by emailing <a href="mailto:support@trueagency.com.au">support@trueagency.com.au</a> or calling 03 9529 1850. Please note that support for any plugins/extensions/code not implemented by True Agency will be quoted separately.<br/><br/><a href="http://www.trueagency.com.au">True Agency</a> specialise in websites, ecommerce and mobile apps.</div>'; 
    }

}
