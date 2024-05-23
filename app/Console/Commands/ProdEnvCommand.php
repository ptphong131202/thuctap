<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProdEnvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change to production environment';

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
        $this->info('Switch to production environment');
        $this->line('-- Clear cache');
        Artisan::call('cache:clear');
        $this->line('-- Clear config');
        Artisan::call('config:cache');
        $this->line('-- Clear route');
        Artisan::call('route:cache');
        $this->line('-- Clear view');
        Artisan::call('view:cache');
        $this->info('Switch to production complete');
        return 0;
    }
}
