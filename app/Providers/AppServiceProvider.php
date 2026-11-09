<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // AdminLTE 3 memakai Bootstrap 4, jadi tautan paginasi harus ikut Bootstrap 4
        Paginator::useBootstrapFour();

        // Tanggal tampil dalam bahasa Indonesia (mis. "9 Nov 2026")
        Carbon::setLocale(config('app.locale'));
    }
}
