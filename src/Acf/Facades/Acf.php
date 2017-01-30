<?php
namespace EvolveEngine\Acf\Facades;

use Illuminate\Support\Facades\Facade;

class Acf extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'acf-helper';
    }
}
