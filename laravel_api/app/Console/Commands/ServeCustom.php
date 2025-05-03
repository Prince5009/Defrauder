<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeCustom extends Command
{
    protected $signature = 'serve:custom';
    protected $description = 'Start the Laravel development server on port 3000';

    public function handle()
    {
        $this->info('Starting Laravel development server on port 3000...');
        
        $process = new Process(['php', 'artisan', 'serve', '--port=3000']);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
    }
} 