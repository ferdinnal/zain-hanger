<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan alias middleware 'admin'
        Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
	Schema::defaultStringLength(191);
    Route::aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);
    }
}
