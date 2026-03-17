<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

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

        // Share notifications with navigation layout
        View::composer('layouts.navigation', function ($view) {
            $notifications = collect();
            if (Auth::check()) {
                $notifications = Auth::user()->notifications()->latest()->get();
            }
            $view->with('notifications', $notifications);
        });
    }
}
