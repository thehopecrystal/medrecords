<?php

namespace App\Providers;

use App\Http\View\Composers\AppLayoutComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', AppLayoutComposer::class);

        // Force HTTPS in production so all generated URLs use https://.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Subdirectory installs (e.g. XAMPP http://localhost/medrecords/): the front
        // controller (public/index.php) strips the install folder from the request
        // path, so generated URLs must carry the folder again. Detected via the
        // executed script path — a no-op for domain-root deploys, `php artisan
        // serve` (/index.php) and CLI runs.
        if (str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/medrecords/public/')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Log slow queries (>500 ms) during local development.
        if ($this->app->isLocal()) {
            DB::listen(function ($query): void {
                if ($query->time > 500) {
                    logger()->warning("Slow query ({$query->time}ms): {$query->sql}");
                }
            });
        }

        // Per-user rate limiters (keyed on user ID so shared IPs don't bleed limits).
        RateLimiter::for('patient-writes', function (Request $request) {
            $user = $request->user();
            return $user
                ? Limit::perMinute(30)->by('user:' . $user->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('provider-writes', function (Request $request) {
            $user = $request->user();
            return $user
                ? Limit::perMinute(60)->by('user:' . $user->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('break-glass', function (Request $request) {
            $user = $request->user();
            return $user
                ? Limit::perHour(5)->by('user:' . $user->id)
                : Limit::perHour(1)->by($request->ip());
        });

        RateLimiter::for('consent-actions', function (Request $request) {
            $user = $request->user();
            return $user
                ? Limit::perMinute(20)->by('user:' . $user->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        // Global API limiter for the JWT REST API.
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            return $user
                ? Limit::perMinute(120)->by('api-user:' . $user->id)
                : Limit::perMinute(30)->by('api-ip:' . $request->ip());
        });
    }
}
