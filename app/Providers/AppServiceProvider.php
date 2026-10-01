<?php

namespace App\Providers;

use Filament\Tables\Table;
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
        // Filament v4 made deferFilters() default to true, which requires users to
        // press a button before table filters apply. The ten resources in this
        // panel were built against v3, where filters applied immediately, so the
        // previous behaviour is restored here instead of in each resource.
        Table::configureUsing(fn (Table $table) => $table->deferFilters(false));
    }
}
