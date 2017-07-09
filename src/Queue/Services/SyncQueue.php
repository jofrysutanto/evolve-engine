<?php
namespace EvolveEngine\Queue\Services;

use EvolveEngine\Queue\QueueJob;
use Exception;

class SyncQueue extends AbstractQueue
{

    /**
     * Push into queue
     *
     * @param  string $job
     * @param  array  $params
     *
     * @return void
     */
    public function push($job, $params = [])
    {
        $job = with(new QueueJob($this))
            ->setId(microtime())
            ->setParams($params)
            ->setHandler($job);

        return $job->handle();
    }

    /**
     * Receives a message from the queue and puts it into a Message object
     *
     * @return bool|Message  Message object built from the queue, or false if there is a problem receiving message
     */
    public function poll()
    {
    }

    /**
     * Deletes a message from the queue
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function delete(QueueJob $job)
    {
    }

    /**
     * Releases a job back to the queue, making it visible again
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function release(QueueJob $job)
    {
    }
}