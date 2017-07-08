<?php
namespace EvolveEngine\Queue\Services;

use Exception;

abstract class AbstractQueue
{
    /**
     * Push into queue
     *
     * @param  string $job
     * @param  array  $params
     *
     * @return void
     */
    abstract public function push($job, $params = []);

    /**
     * Receives a message from the queue and puts it into a Message object
     *
     * @return bool|QueueJob
     */
    abstract public function poll();

    /**
     * Build job parameters to be send to the Queue
     *
     * @return $params
     */
    protected function buildJob($job, $params)
    {
        return json_encode([
            'job'    => $job,
            'params' => $params
        ]);
    }
}