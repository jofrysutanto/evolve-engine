<?php
namespace EvolveEngine\Queue\Services;

use EvolveEngine\Queue\QueueJob;
use Exception;

class EmptyQueue extends AbstractQueue
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
        return;
    }

    /**
     * Receives a message from the queue and puts it into a Message object
     *
     * @return bool|Message  Message object built from the queue, or false if there is a problem receiving message
     */
    public function poll()
    {
        return null;
    }

    /**
     * Deletes a message from the queue
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function delete(QueueJob $job)
    {
        return true;
    }

    /**
     * Releases a job back to the queue, making it visible again
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function release(QueueJob $job)
    {
        return true;
    }
}