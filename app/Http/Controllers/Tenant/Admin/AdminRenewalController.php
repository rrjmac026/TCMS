<?php
// app/Http/Controllers/Tenant/Admin/AdminRenewalController.php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\RenewalRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminRenewalController extends Controller
{
    // ── Expired wall page ─────────────────────────────────────────────────

    public function expired()
    {
        $tenant = tenancy()->tenant;
        $plans  = SubscriptionPlan::active()->orderBy('sort_order')->get();

        // Check if a pending renewal request already exists
        $pendingRequest = RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('tenants.admin.subscription.expired', compact(
            'tenant', 'plans', 'pendingRequest'
        ));
    }

    // ── Submit a renewal request ──────────────────────────────────────────

    public function request(Request $request)
    {
        $data = $request->validate([
            'plan_slug'     => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = tenancy()->tenant;
        $planModel = SubscriptionPlan::where('slug', $data['plan_slug'])->firstOrFail();
        $basePrice = (float) $planModel->price;
        $discount  = null;
        $discountAmount = 0;
        $finalPrice = $basePrice;

        // Prevent duplicate pending requests
        $existing = RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending renewal request.',
            ], 422);
        }

        // Resolve promo code if provided
        if (! empty($data['discount_code'])) {
            $discount = Discount::findValidCode(
                $data['discount_code'], $data['plan_slug'], $tenant->id
            );
            if ($discount) {
                $discountAmount = $discount->discountAmount($basePrice);
                $finalPrice     = $discount->applyTo($basePrice);
            }
        }

        // Also try automatic discount if no code
        if (! $discount) {
            $autoDiscount = Discount::bestAutomaticFor($data['plan_slug']);
            if ($autoDiscount) {
                $discountAmount = $autoDiscount->discountAmount($basePrice);
                $finalPrice     = $autoDiscount->applyTo($basePrice);
            }
        }

        RenewalRequest::create([
            'tenant_id'       => $tenant->id,
            'plan_slug'       => $data['plan_slug'],
            'discount_code'   => $data['discount_code'] ?? null,
            'original_price'  => $basePrice,
            'discount_amount' => $discountAmount,
            'final_price'     => $finalPrice,
            'status'          => 'pending',
        ]);

        // Create an in-app bell notification for the tenant admin
        $tenant->run(function () use ($data) {
            $admin = \App\Models\User::where('role', 'admin')->first();
            if ($admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Renewal Request Submitted',
                    'message' => 'Your renewal request for the ' . ucfirst($data['plan_slug'])
                               . ' Plan has been submitted. Please wait for super admin approval.',
                    'is_read' => false,
                    'link'    => '/admin/subscription',
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Renewal request submitted. Awaiting super admin approval.',
        ]);
    }

    // ── Cancel a pending renewal request ─────────────────────────────────

    public function cancel()
    {
        $tenant = tenancy()->tenant;

        RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Renewal request cancelled.');
    }
}