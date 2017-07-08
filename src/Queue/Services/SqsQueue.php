<?php
/**
 * A wrapper for amazon SQS, using the AWS PHP SDK,
 * inspired from the link below.
 *
 * @link http://george.webb.uno/posts/aws-simple-queue-service-php-sdk
 */
namespace EvolveEngine\Queue\Services;

use Aws\Sqs\SqsClient;
use EvolveEngine\Queue\QueueJob;
use Exception;

class SqsQueue extends AbstractQueue
{
    
    /**
     * The url of the SQS queue
     *
     * @var string
     */
    private $url;

    /**
     * The array of credentials used to connect to the AWS API
     *
     * @var array
     */
    private $aws_credentials;

    /**
     * A SqsClient object from the AWS SDK, used to connect to the AWS SQS API
     *
     * @var SqsClient
     */
    private $sqs_client;

    /**
     * Constructs the wrapper using the url of the queue and the aws credentials
     *
     * @param $url
     * @param $aws_credentials
     */
    public function __construct($url, $config)
    {
        // Setup the connection to the queue
        $this->config = $config;
        $this->sqs_client = SqsClient::factory($config);
        // Get the queue URL
        $this->url = $url;
    }

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
        $messageBody = $this->buildJob($job, $params);

        try {
            // Send the message
            $this->sqs_client->sendMessage(array(
                'QueueUrl' => $this->url,
                'MessageBody' => $messageBody
            ));
            return true;
        } catch (Exception $e) {
            echo 'Error sending message to queue ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Receives a message from the queue and puts it into a Message object
     *
     * @return bool|Message  Message object built from the queue, or false if there is a problem receiving message
     */
    public function poll()
    {
        try {
            // Receive a message from the queue
            $result = $this->sqs_client->receiveMessage(array(
                'QueueUrl' => $this->url
            ));

            if ($result['Messages'] == null) {
                // No message to process
                return false;
            }

            // Get the message and return it
            $result_message = array_pop($result['Messages']);
            $jobConfig = $this->extractJob($result_message['Body']);

            $job = with(new QueueJob($this))
                ->setId($result_message['ReceiptHandle'])
                ->setParams($jobConfig['params'])
                ->setHandler($jobConfig['job']);

            return $job;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Deletes a message from the queue
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function delete(QueueJob $job)
    {
        try {
            // Delete the job
            $this->sqs_client->deleteMessage(array(
                'QueueUrl'      => $this->url,
                'ReceiptHandle' => $job->getId()
            ));
            return true;
        } catch (Exception $e) {
            echo 'Error deleting job from queue ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Releases a job back to the queue, making it visible again
     *
     * @param QueueJob $job
     * @return bool  returns true if successful, false otherwise
     */
    public function release(QueueJob $job)
    {
        try {
            // Set the visibility timeout to 0 to make the job visible in the queue again straight away
            $this->sqs_client->changeMessageVisibility(array(
                'QueueUrl'          => $this->url,
                'ReceiptHandle'     => $job->getId(),
                'VisibilityTimeout' => 0
            ));
            return true;
        } catch (Exception $e) {
            echo 'Error releasing job back to queue ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Extract job message
     *
     * @param  mixed $message
     *
     * @return array
     */
    public function extractJob($message)
    {
        return json_decode($message, $assoc = true);
    }
}