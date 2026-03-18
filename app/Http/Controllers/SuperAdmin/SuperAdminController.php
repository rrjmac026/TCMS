<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\TenantApprovalMail;
use App\Mail\TenantRejectionMail;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SuperAdminController extends Controller
{
    // -------------------------------------------------------------------------
    // Shared query helper
    // -------------------------------------------------------------------------

    private function tenantStats(): array
    {
        return [
            'tenants'          => Tenant::latest()->get(),
            'approvedTenants'  => Tenant::where('status', 'approved')->get(),
            'pendingTenants'   => Tenant::where('status', 'pending')->get(),
            'rejectedTenants'  => Tenant::where('status', 'rejected')->get(),
        ];
    }

    // -------------------------------------------------------------------------
    // Dashboard & Index
    // -------------------------------------------------------------------------

    public function dashboard()
    {
        return view('superadmin.dashboard', $this->tenantStats());
    }

    public function index()
    {
        return view('superadmin.tenants.index', $this->tenantStats());
    }

    // -------------------------------------------------------------------------
    // Create & Store
    // -------------------------------------------------------------------------

    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'admin_email'  => ['required', 'email', 'unique:tenants,admin_email'],
            'subdomain'    => ['required', 'string', 'alpha_dash', 'unique:tenants,subdomain'],
            'subscription' => ['required', 'in:basic,standard,premium'],
        ]);

        try {
            Tenant::create([
                'name'         => $request->name,
                'admin_email'  => $request->admin_email,
                'subdomain'    => strtolower($request->subdomain),
                'subscription' => $request->subscription,
                'status'       => 'pending',
                'expires_at'   => null,
            ]);

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant registration submitted and is pending approval.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating tenant: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(Tenant $tenant)
    {
        return view('superadmin.tenants.show', compact('tenant'));
    }

    // -------------------------------------------------------------------------
    // Approve
    // -------------------------------------------------------------------------

    public function approve(Tenant $tenant)
    {
        if ($tenant->status === 'approved') {
            return back()->with('error', 'Tenant is already approved.');
        }

        try {
            $password = 'TCM' . str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $domain   = $tenant->subdomain . '.tcm.com';

            // Central DB — create domain record
            $tenant->domains()->create(['domain' => $domain]);

            // Central DB — update tenant status
            $tenant->status     = 'approved';
            $tenant->expires_at = now()->addDays(30);
            $tenant->save();

            // Tenant DB — run migrations and seed admin user
            $tenant->run(function () use ($tenant, $password) {
                Artisan::call('migrate', [
                    '--path'  => 'database/migrations/tenant',
                    '--force' => true,
                ]);

                DB::connection('tenant')->table('users')->insert([
                    'name'       => $tenant->name,
                    'email'      => $tenant->admin_email,
                    'password'   => Hash::make($password),
                    'role'       => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            // Send credentials to tenant admin gamit email
            Mail::to($tenant->admin_email)->send(new TenantApprovalMail($tenant, $password));

            return redirect()->route('superadmin.tenants.index')
                ->with('success', "Tenant approved. Credentials sent to {$tenant->admin_email}.");

        } catch (\Exception $e) {
            // Manually rollback central DB changes
            $tenant->domains()->where('domain', $tenant->subdomain . '.tcm.com')->delete();
            $tenant->status     = 'pending';
            $tenant->expires_at = null;
            $tenant->save();

            return back()->with('error', 'Error approving tenant: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Reject
    // -------------------------------------------------------------------------

    public function reject(Tenant $tenant)
    {
        if ($tenant->status === 'rejected') {
            return back()->with('error', 'Tenant is already rejected.');
        }

        try {
            $tenant->status = 'rejected';
            $tenant->save();

            Mail::to($tenant->admin_email)->send(new TenantRejectionMail($tenant));

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant registration rejected.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting tenant: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Upgrade
    // -------------------------------------------------------------------------

    public function upgrade(Request $request, Tenant $tenant)
    {
        $request->validate([
            'subscription' => ['required', 'in:basic,standard,premium'],
        ]);

        $plans = ['basic', 'standard', 'premium'];
        $currentIndex = array_search($tenant->subscription, $plans);
        $newIndex = array_search($request->subscription, $plans);

        // Prevent downgrading
        if ($newIndex <= $currentIndex) {
            return back()->with('error', 'You can only upgrade to a higher plan.');
        }

        try {
            $expiresAt = match($request->subscription) {
                'basic'    => now()->addDays(30),
                'standard' => now()->addMonths(6),
                'premium'  => now()->addYear(),
            };

            $tenant->subscription = $request->subscription;
            $tenant->expires_at   = $expiresAt;
            $tenant->save();

            return redirect()->back()
                ->with('success', "Tenant upgraded to " . ucfirst($request->subscription) . " plan successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error upgrading tenant: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(Tenant $tenant)
    {
        try {
            // ma delete apil ang Database ug Files sa tenant
            $tenant->delete();

            return redirect()->route('superadmin.tenants.index')
                ->with('success', 'Tenant deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting tenant: ' . $e->getMessage());
        }
    }
}