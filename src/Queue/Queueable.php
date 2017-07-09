<?php
namespace EvolveEngine\Queue;

abstract class Queueable
{

    /**
     * Serialisable data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Handle this task
     *
     * @return void
     */
    abstract public function handle($job, array $params = []);

}