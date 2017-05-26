<?php
namespace EvolveEngine\Analytics\Facades;

use Illuminate\Support\Facades\Facade;

class Analytics extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'analytics';
    }
}
