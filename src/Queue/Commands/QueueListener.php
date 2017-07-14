<?php
namespace EvolveEngine\Queue\Commands;

use EvolveEngine\Console\Command;

class QueueListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the queue worker';

    /**
     * Run the listener
     * 
     * @return void
     */
    public function handle()
    {
        $queue = app('queue');
        $this->info("Queue has started..");
        while (true) {
            if ($job = $queue->poll()) {
                 $this->info("Processing message");
                try {
                    $job->handle();
                    $this->success(get_class($job) . " : Queue processed");
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    $job->release();
                }
            }
            sleep(2);
        }
    }

}