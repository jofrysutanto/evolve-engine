<?php
namespace EvolveEngine\Queue\Commands;

use EvolveEngine\Console\Command;

class StopQueue extends Command
{
    protected $signature = 'queue:stop';
    protected $description = 'Stop the queue worker';

    public function handle()
    {
        $stopFile = get_template_directory().'/restart-queue.txt';
        file_put_contents($stopFile, '1');
        $this->info("Stop signal sent.");
    }
}
