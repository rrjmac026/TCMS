<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Reports\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminReportController extends Controller
{
    // ── Report definitions ────────────────────────────────────────────────────
    // Each entry describes what we collect per approved tenant.

    protected ReportExportService $exporter;

    public function __construct(ReportExportService $exporter)
    {
        $this->exporter = $exporter;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Index — show the reports dashboard
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $totalTenants    = Tenant::count();
        $approvedTenants = Tenant::where('status', 'approved')->count();
        $pendingTenants  = Tenant::where('status', 'pending')->count();
        $rejectedTenants = Tenant::where('status', 'rejected')->count();

        // Subscription breakdown
        $subscriptionBreakdown = Tenant::where('status', 'approved')
            ->selectRaw('subscription, COUNT(*) as count')
            ->groupBy('subscription')
            ->pluck('count', 'subscription')
            ->toArray();

        $subscriptionBreakdown = array_merge(
            ['basic' => 0, 'standard' => 0, 'premium' => 0],
            $subscriptionBreakdown
        );

        // Expiring soon
        $expiringSoon = Tenant::where('status', 'approved')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(30)])
            ->count();

        $expiredCount = Tenant::where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        // Monthly registrations (last 12 months)
        $monthlyRegistrations = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRegistrations[] = [
                'label' => $month->format('M Y'),
                'count' => Tenant::whereYear('created_at', $month->year)
                               ->whereMonth('created_at', $month->month)
                               ->count(),
            ];
        }

        return view('superadmin.reports.index', compact(
            'totalTenants',
            'approvedTenants',
            'pendingTenants',
            'rejectedTenants',
            'subscriptionBreakdown',
            'expiringSoon',
            'expiredCount',
            'monthlyRegistrations'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Export: Tenant Overview (all tenants)
    // ─────────────────────────────────────────────────────────────────────────

    public function exportTenants(Request $request)
    {
        $format = $request->input('format', 'excel');

        $tenants = Tenant::orderBy('created_at', 'desc')->get();

        $data = $tenants->map(fn($t) => [
            'ID'           => $t->id,
            'Organization' => $t->name,
            'Admin Email'  => $t->admin_email,
            'Subdomain'    => $t->subdomain . '.tcm.com',
            'Plan'         => ucfirst($t->subscription),
            'Status'       => ucfirst($t->status),
            'Expires At'   => $t->expires_at ? $t->expires_at->format('Y-m-d') : '—',
            'Registered'   => $t->created_at->format('Y-m-d'),
        ])->toArray();

        return $this->exporter->export(
            data:     $data,
            filename: 'tenants-report-' . now()->format('Ymd'),
            format:   $format,
            title:    'Tenant Overview Report',
            plan:     'premium'   // SuperAdmin always gets premium export
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Export: Subscription Summary
    // ─────────────────────────────────────────────────────────────────────────

    public function exportSubscriptions(Request $request)
    {
        $format = $request->input('format', 'excel');

        $tenants = Tenant::where('status', 'approved')
            ->orderBy('subscription')
            ->orderBy('expires_at')
            ->get();

        $data = $tenants->map(fn($t) => [
            'Organization'  => $t->name,
            'Admin Email'   => $t->admin_email,
            'Subdomain'     => $t->subdomain . '.tcm.com',
            'Plan'          => ucfirst($t->subscription),
            'Expires At'    => $t->expires_at ? $t->expires_at->format('Y-m-d') : '—',
            'Days Left'     => $t->expires_at
                                    ? max(0, (int) now()->diffInDays($t->expires_at, false))
                                    : '—',
            'Status'        => $t->expires_at && $t->expires_at->isPast() ? 'Expired' : 'Active',
        ])->toArray();

        return $this->exporter->export(
            data:     $data,
            filename: 'subscriptions-report-' . now()->format('Ymd'),
            format:   $format,
            title:    'Subscription Status Report',
            plan:     'premium'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Export: Per-Tenant Activity (cross-DB stats)
    // ─────────────────────────────────────────────────────────────────────────

    public function exportActivity(Request $request)
    {
        $format = $request->input('format', 'excel');

        $approvedTenants = Tenant::where('status', 'approved')->get();

        $data = [];
        foreach ($approvedTenants as $tenant) {
            try {
                $stats = $tenant->run(function () {
                    return [
                        'trainers'     => DB::table('users')->where('role', 'trainer')->count(),
                        'trainees'     => DB::table('users')->where('role', 'trainee')->count(),
                        'courses'      => DB::table('courses')->count(),
                        'enrollments'  => DB::table('enrollments')->count(),
                        'assessments'  => DB::table('assessments')->count(),
                        'certificates' => DB::table('certificates')->count(),
                    ];
                });
            } catch (\Throwable) {
                $stats = [
                    'trainers' => 0, 'trainees' => 0, 'courses' => 0,
                    'enrollments' => 0, 'assessments' => 0, 'certificates' => 0,
                ];
            }

            $data[] = [
                'Organization'  => $tenant->name,
                'Subdomain'     => $tenant->subdomain . '.tcm.com',
                'Plan'          => ucfirst($tenant->subscription),
                'Trainers'      => $stats['trainers'],
                'Trainees'      => $stats['trainees'],
                'Courses'       => $stats['courses'],
                'Enrollments'   => $stats['enrollments'],
                'Assessments'   => $stats['assessments'],
                'Certificates'  => $stats['certificates'],
                'Expires At'    => $tenant->expires_at ? $tenant->expires_at->format('Y-m-d') : '—',
            ];
        }

        return $this->exporter->export(
            data:     $data,
            filename: 'tenant-activity-' . now()->format('Ymd'),
            format:   $format,
            title:    'Tenant Activity Report',
            plan:     'premium'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Export: Monthly Registrations
    // ─────────────────────────────────────────────────────────────────────────

    public function exportRegistrations(Request $request)
    {
        $format = $request->input('format', 'excel');
        $months = (int) $request->input('months', 12);
        $months = min(max($months, 1), 24); // clamp to 1–24

        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month   = now()->subMonths($i);
            $tenants = Tenant::whereYear('created_at', $month->year)
                             ->whereMonth('created_at', $month->month)
                             ->get();

            foreach ($tenants as $t) {
                $data[] = [
                    'Month'        => $month->format('F Y'),
                    'Organization' => $t->name,
                    'Admin Email'  => $t->admin_email,
                    'Plan'         => ucfirst($t->subscription),
                    'Status'       => ucfirst($t->status),
                    'Registered'   => $t->created_at->format('Y-m-d H:i'),
                ];
            }
        }

        if (empty($data)) {
            $data = [['Month' => 'No registrations found in selected range', 'Organization' => '', 'Admin Email' => '', 'Plan' => '', 'Status' => '', 'Registered' => '']];
        }

        return $this->exporter->export(
            data:     $data,
            filename: 'registrations-' . now()->format('Ymd'),
            format:   $format,
            title:    "Tenant Registrations (Last {$months} Months)",
            plan:     'premium'
        );
    }
}