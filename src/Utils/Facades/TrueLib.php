<?php
namespace EvolveEngine\Utils\Facades;

use Illuminate\Support\Facades\Facade;

class TrueLib extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'truelib';
    }
}
