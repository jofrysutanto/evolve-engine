<?php
namespace EvolveEngine\Router\Facades;

use Illuminate\Support\Facades\Facade;

class Route extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'router';
    }
}
