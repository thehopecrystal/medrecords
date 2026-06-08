<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS in production so all generated URLs use https://.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Log slow queries (>500 ms) during local development.
        if ($this->app->isLocal()) {
            DB::listen(function ($query): void {
                if ($query->time > 500) {
                    logger()->warning("Slow query ({$query->time}ms): {$query->sql}");
                }
            });
        }
    }
}
