<?php
namespace EvolveEngine\Theme\Facades;

use Illuminate\Support\Facades\Facade;

class Theme extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'theme';
    }
}
