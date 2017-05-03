<?php
namespace EvolveEngine\Social\Facades;

use Illuminate\Support\Facades\Facade;

class Share extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'social.share';
    }
}
