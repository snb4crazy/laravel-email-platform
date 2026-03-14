<?php

namespace App\Providers;

use App\Services\Mail\MailTemplateResolver;
use App\Services\Site\SiteResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MailTemplateResolver::class);
        $this->app->singleton(SiteResolver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
