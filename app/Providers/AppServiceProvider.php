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
        $this->app->bind(
        \App\Services\Payments\PaymentGateway::class,
        fn () => config('payments.driver') === 'wave'
            ? new \App\Services\Payments\WaveGateway()
            : new \App\Services\Payments\SimulationGateway()
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
