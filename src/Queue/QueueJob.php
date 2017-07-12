<?php
namespace EvolveEngine\Queue;

class QueueJob
{

    /**
     * @var string Queue Job identifier
     */
    protected $id;

    /**
     * @var mixed
     */
    protected $handler;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var Queue Driver current handling the job
     */
    protected $queue;

    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    public function handle()
    {
        if (is_string($this->handler)) {
            $this->handler = app()->make($this->handler);
        }

        $this->handler->handle($this, $this->params);
    }

    public function release()
    {
        $this->queue->release($this);
    }

    public function delete()
    {
        $this->queue->delete($this);
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

}