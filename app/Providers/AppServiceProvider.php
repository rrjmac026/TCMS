<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::directive('plan', function ($feature) {
            return "<?php if(tenancy()->tenant && \\App\\Helpers\\SubscriptionHelper::canAccess(tenancy()->tenant->subscription, {$feature})): ?>";
        });

        Blade::directive('endplan', function () {
            return "<?php endif; ?>";
        });
    }
}
