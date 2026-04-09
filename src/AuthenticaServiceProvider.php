<?php

namespace Authentica\LaravelAuthentica;

use Illuminate\Support\ServiceProvider;

class AuthenticaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/authentica.php' => config_path('authentica.php'),
            ], 'authentica-config');

            $this->publishes([
                __DIR__.'/../.env.example' => base_path('.env.example'),
            ], 'authentica-env');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/authentica.php', 'authentica');

        $this->app->singleton('authentica', function ($app) {
            return new AuthenticaClient();
        });

        $this->app->alias('authentica', AuthenticaClient::class);
    }
}