<?php
namespace EvolveEngine\Queue\Facades;

use Illuminate\Support\Facades\Facade;

class Queue extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queue';
    }
}
