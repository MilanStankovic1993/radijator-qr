<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\QrLabel;
use App\Observers\QrLabelObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production') && request()->isSecure()) {
            URL::forceScheme('https');
        }

        QrLabel::observe(QrLabelObserver::class);
    }
}