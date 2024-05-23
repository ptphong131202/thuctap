<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DevEnvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change to development environment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Switch to development environment');
        $this->line('-- Clear cache');
        Artisan::call('cache:clear');
        $this->line('-- Cache config');
        Artisan::call('config:clear');
        $this->line('-- Cache route');
        Artisan::call('route:clear');
        $this->line('-- Cache view');
        Artisan::call('view:clear');
        $this->info('Switch to development complete');
        return 0;
    }
}
