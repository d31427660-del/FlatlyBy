<?php

namespace App\Providers;

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
        app()->setLocale('ru');
        \Carbon\Carbon::setLocale('ru');

        view()->composer(['layouts.app', 'listings.index'], function ($view) {
            try {
                $activeCities = \App\Models\Listing::where('status', 'active')
                    ->whereNotNull('city')
                    ->where('city', '!=', '')
                    ->select('city')
                    ->distinct()
                    ->orderBy('city')
                    ->pluck('city')
                    ->toArray();
            } catch (\Exception $e) {
                $activeCities = [];
            }
            $view->with('activeCities', $activeCities);
        });
    }
}
