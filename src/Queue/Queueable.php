<?php
namespace EvolveEngine\Queue;

abstract class Queueable
{

    /**
     * Handle this task - which should call either `delete` on $job 
     * once the task is completed, or `release` to push the job back to the queue.
     *
     * @param Job    $job
     * @param array  $params Parameters to this task
     *
     * @return void
     */
    abstract public function handle($job, array $params = []);

}