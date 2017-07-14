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

    /**
     * Process the job
     *
     * @return void
     */
    public function handle()
    {
        if (is_string($this->handler)) {
            $this->handler = app()->make($this->handler);
        }

        $this->handler->handle($this, $this->params);
    }

    /**
     * Release the job back to the queue, to be processed later
     *
     * @return void
     */
    public function release()
    {
        $this->queue->release($this);
    }

    /**
     * Delete the job, marking the job as completed
     *
     * @return void
     */
    public function delete()
    {
        $this->queue->delete($this);
    }

    /**
     * Set the identifier of this job
     *
     * @param string $id
     * 
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the identifier of this job
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set handler used by the queued Job
     *
     * @param Queueable $handler
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * Get handler used by the queued Job
     *
     * @return Queueable
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set parameters for the handler
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get parameters for the handler
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}