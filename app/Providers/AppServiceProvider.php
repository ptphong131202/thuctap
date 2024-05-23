<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
             $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
             $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->helperBoot();

        $this->macroBoot();
    }

    public function macroBoot()
    {
        Builder::macro('select2Paginate', function ($perPage) {
            $paginate = $this->paginate($perPage);
            $results = $paginate->map(function ($value) {
                    return [
                        'id' => $value->mh_id,
                        'text' => $value->mh_ten,
                    ];
                });
            return [
                'items' => $results,
                'total_count' => $paginate->total(),
            ];
        });
    }

    /**
     * Load helper
     * @author ttdat
     * @status [status]
     * @return [type]   [description]
     */
    private function helperBoot()
    {
        require_once __DIR__ . '/../helper.php';
    }
}
