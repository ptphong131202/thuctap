<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup';

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
        $this->setup();
        return 0;
    }

    /**
     * Setup
     * @return void
     * @author ttdat
     * @version 1.0
     */
    private function setup()
    {
        // 1. Database
        if (!$this->canConnectDB()) {
            $this->error('Can not connect to database');
            return;
        }
        $this->info('Generate application key');
        Artisan::call('key:generate');

        $this->info('Running migration');
        Artisan::call('migrate');

        $this->info('Migration complete, seeding data');
        Artisan::call('db:seed --class=UsersTableSeeder');
        Artisan::call('db:seed --class=PermissionsTableSeeder');
        // Artisan::call('db:seed --class=HeDaoTaoSeeder');
        // Artisan::call('db:seed --class=KhoaDaoTaoSeeder');
        // Artisan::call('db:seed --class=NienkhoaSeeder');
        // Artisan::call('db:seed --class=LopHocSeeder');

        $this->info('Complete');
    }

    /**
     * Kiểm tra kết nối database
     * @return bool
     * @author ttdat
     * @version 1.0
     */
    private function canConnectDB()
    {
        try {
            \DB::connection()->getPdo();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
