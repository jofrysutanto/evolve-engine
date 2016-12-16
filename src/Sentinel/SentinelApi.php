<?php

namespace EvolveEngine\Sentinel;

class SentinelApi
{   
    /**
     * @var String
     */
    protected $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

}
