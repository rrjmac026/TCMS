<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Notification;
use App\Models\RenewalRequest;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SuperAdminRenewalController extends Controller
{
    // ── Renewals dashboard ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $renewals = RenewalRequest::with(['tenant', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        $counts = RenewalRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $expiringSoon = Tenant::where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays(10))
            ->whereNotIn('id', RenewalRequest::where('status', 'pending')->pluck('tenant_id'))
            ->get();

        return view('superadmin.renewals.index', compact(
            'renewals', 'counts', 'status', 'expiringSoon'
        ));
    }

    // ── Approve renewal ───────────────────────────────────────────────────

    public function approve(Request $request, RenewalRequest $renewal)
    {
        if (! $renewal->isPending()) {
            return back()->withErrors(['error' => 'This request has already been processed.']);
        }

        $tenant = Tenant::findOrFail($renewal->tenant_id);

        // ── KEY FIX: extend from current expiry, never reset to now ──────
        $expiresAt = $renewal->calculateNewExpiry($tenant);

        DB::transaction(function () use ($renewal, $tenant, $expiresAt) {
            $discountUsageId = null;

            if ($renewal->discount_amount > 0 && $renewal->discount_code) {
                $discount = Discount::findValidCode(
                    $renewal->discount_code, $renewal->plan_slug, $tenant->id
                );
                if ($discount) {
                    $usage = DiscountUsage::create([
                        'discount_id'     => $discount->id,
                        'tenant_id'       => $tenant->id,
                        'action'          => 'renewal',
                        'plan_slug'       => $renewal->plan_slug,
                        'original_price'  => $renewal->original_price,
                        'discount_amount' => $renewal->discount_amount,
                        'final_price'     => $renewal->final_price,
                        'applied_by'      => auth()->id(),
                    ]);
                    $discountUsageId = $usage->id;
                }
            }

            TenantSubscription::create([
                'tenant_id'         => $tenant->id,
                'plan_slug'         => $renewal->plan_slug,
                'discount_usage_id' => $discountUsageId,
                'amount_paid'       => $renewal->final_price,
                'action'            => 'renewal',
                'starts_at'         => now(),
                'expires_at'        => $expiresAt,
                'applied_by'        => auth()->id(),
            ]);

            // Update the tenant — extend expiry, activate
            $tenant->subscription = $renewal->plan_slug;
            $tenant->expires_at   = $expiresAt;   // ← the actual fix
            $tenant->status       = 'approved';
            $tenant->is_active    = true;
            $tenant->save();

            $renewal->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        $this->notifyTenantAdmin($tenant, 'approved', $renewal->plan_slug, $expiresAt);

        try {
            Mail::to($tenant->admin_email)
                ->send(new \App\Mail\RenewalApprovedMail($tenant, $renewal));
        } catch (\Throwable) {}

        return back()->with('success', "Renewal approved for {$tenant->name}. Expires: {$expiresAt->format('M d, Y')}.");
    }

    // ── Reject renewal ────────────────────────────────────────────────────

    public function reject(Request $request, RenewalRequest $renewal)
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $renewal->isPending()) {
            return back()->withErrors(['error' => 'This request has already been processed.']);
        }

        $renewal->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'notes'       => $request->input('notes'),
        ]);

        $tenant = Tenant::findOrFail($renewal->tenant_id);
        $this->notifyTenantAdmin($tenant, 'rejected', $renewal->plan_slug);

        return back()->with('success', "Renewal rejected for {$tenant->name}.");
    }

    // ── Notify tenant admin in-app ────────────────────────────────────────

    private function notifyTenantAdmin(Tenant $tenant, string $outcome, string $planSlug, $expiresAt = null): void
    {
        try {
            $tenant->run(function () use ($outcome, $planSlug, $expiresAt) {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if (! $admin) return;

                if ($outcome === 'approved') {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => '✅ Subscription Renewed!',
                        'message' => 'Your ' . ucfirst($planSlug) . ' Plan has been renewed successfully.'
                                   . ($expiresAt ? ' New expiry: ' . $expiresAt->format('F d, Y') . '.' : ''),
                        'is_read' => false,
                        'link'    => '/admin/subscription',
                    ]);
                } else {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => '❌ Renewal Request Rejected',
                        'message' => 'Your renewal request for the ' . ucfirst($planSlug)
                                   . ' Plan was not approved. Please contact support.',
                        'is_read' => false,
                        'link'    => '/admin/subscription',
                    ]);
                }
            });
        } catch (\Throwable) {}
    }
}