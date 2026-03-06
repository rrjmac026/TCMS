<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        return view('tenants.admin.subscription.upgrade');
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'subscription' => ['required', 'in:basic,standard,premium'],
        ]);

        $tenant = tenancy()->tenant;
        $plans  = ['basic', 'standard', 'premium'];

        $currentIndex = array_search($tenant->subscription, $plans);
        $newIndex     = array_search($request->subscription, $plans);

        // Prevent downgrading
        if ($newIndex <= $currentIndex) {
            return response()->json([
                'success' => false,
                'message' => 'You can only upgrade to a higher plan.',
            ], 422);
        }

        $expiresAt = match($request->subscription) {
            'basic'    => now()->addDays(30),
            'standard' => now()->addMonths(6),
            'premium'  => now()->addYear(),
        };

        $tenant->subscription = $request->subscription;
        $tenant->expires_at   = $expiresAt;
        $tenant->save();

        return response()->json([
            'success' => true,
            'plan'    => $request->subscription,
        ]);
    }
}