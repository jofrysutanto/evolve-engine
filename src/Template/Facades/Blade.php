<?php
namespace EvolveEngine\Template\Facades;

use Illuminate\Support\Facades\Facade;

class Blade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blade';
    }
}
