<?php

namespace App\Console\Commands;

use App\Jobs\ProcessClientDataJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ProcessClientData extends Command
{
    protected $signature = 'excel:process {file}';
    protected $description = 'Process Excel file in the background';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error('File not found.');
            return;
        }

        // Dispatch the job to handle the Excel file processing
        Bus::dispatch(new ProcessClientDataJob($file));

        $this->info('Excel file processing job has been dispatched.');
    }
}
