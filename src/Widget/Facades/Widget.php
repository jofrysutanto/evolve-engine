<?php
namespace EvolveEngine\Widget\Facades;

use Illuminate\Support\Facades\Facade;

class Widget extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'widget';
    }
}
