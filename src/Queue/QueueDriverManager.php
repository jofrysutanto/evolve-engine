<?php
namespace EvolveEngine\Queue;

use EvolveEngine\Queue\Services\EmptyQueue;
use EvolveEngine\Queue\Services\SqsQueue;
use EvolveEngine\Queue\Services\SyncQueue;
use Illuminate\Support\Manager;

class QueueDriverManager extends Manager
{

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['queue.default'];
    }

    /**
     * Create new SQS Queue
     *
     * @return Queue
     */
    public function createSqsDriver()
    {
        $config = $this->app['config']['queue.connections.sqs'];
        $url = array_get($config, 'queue');
        $configurations = [
            'region'  => array_get($config, 'region'),
            'version' => '2012-11-05'
        ];

        $queue = new SqsQueue($url, $configurations);
        return $queue;
    }

    /**
     * Create new sync driver
     *
     * @return Queue
     */
    public function createSyncDriver()
    {
        return new SyncQueue();
    }

    /**
     * Create new empty Queue.
     * This queue immediately discards tasks pushed to it
     * without running any handler.
     *
     * @return Queue
     */
    public function createEmptyDriver()
    {
        return new EmptyQueue();
    }

}