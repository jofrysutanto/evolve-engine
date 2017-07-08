<?php
namespace EvolveEngine\Queue;

use EvolveEngine\Queue\Services\SqsQueue;
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

}