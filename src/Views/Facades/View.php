<?php
namespace EvolveEngine\Views\Facades;

use Illuminate\Support\Facades\Facade;

class View extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'view-maker';
    }
}
