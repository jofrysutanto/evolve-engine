<?php
namespace EvolveEngine\Post\Facades;

use Illuminate\Support\Facades\Facade;

class PostType extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'post-type';
    }
}
