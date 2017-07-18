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
     *
     * 
     * @return
     */
    public function handle()
    {
        $queue = app('queue');
        $this->info("Queue has started..");
        $stopFile = get_template_directory().'/restart-queue.txt';

        while (true) {
            if (file_exists($stopFile)) {
                unlink($stopFile);
                $this->info("Queue has been stopped");
                exit(1);
            }

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
