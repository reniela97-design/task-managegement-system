<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Idugang ni

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // I-force ang HTTPS kung naa sa production (parehas sa Railway)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
