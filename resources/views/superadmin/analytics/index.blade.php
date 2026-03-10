@extends('layouts.app')

@section('title', 'Platform Analytics')

@section('content')

{{-- ── Inline Styles ───────────────────────────────────────────────────────── --}}
<style>
    :root {
        --sa-primary: #003087;
        --sa-accent:  #0057B8;
        --sa-success: #16a34a;
        --sa-warning: #b38a00;
        --sa-danger:  #CE1126;
        --sa-gold:    #F5C518;
        --sa-border:  #c5d8f5;
        --sa-text:    #001a4d;
        --sa-muted:   #5a7aaa;
        --sa-bg:      #ffffff;
        --sa-surface: #f4f8ff;
    }
    .dark {
        --sa-bg:      #0a1628;
        --sa-surface: #0d1f3c;
        --sa-border:  #1e3a6b;
        --sa-text:    #dde8ff;
        --sa-muted:   #6b8abf;
    }

    /* ── Stat Cards ── */
    .stat-card {
        border-radius: 16px;
        border: 2px solid var(--sa-border);
        background: var(--sa-bg);
        padding: 22px 24px;
        transition: transform .18s, box-shadow .18s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,48,135,.10); }

    .stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }

    /* ── Section Cards ── */
    .section-card {
        border-radius: 18px;
        border: 2px solid var(--sa-border);
        background: var(--sa-bg);
        padding: 26px 28px;
    }

    .section-title {
        font-size: 15px; font-weight: 700;
        color: var(--sa-primary); margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── Bar Chart ── */
    .bar-group { display: flex; flex-direction: column; gap: 10px; }

    .bar-row { display: flex; align-items: center; gap: 10px; }

    .bar-label {
        width: 64px; font-size: 11px; font-weight: 600;
        color: var(--sa-muted); text-align: right; flex-shrink: 0;
    }

    .bar-track {
        flex: 1; height: 22px; border-radius: 6px;
        background: var(--sa-surface); overflow: hidden; position: relative;
    }

    .bar-fill {
        height: 100%; border-radius: 6px; min-width: 4px;
        display: flex; align-items: center; justify-content: flex-end;
        padding-right: 8px;
        transition: width .6s cubic-bezier(.4,0,.2,1);
    }

    .bar-val { font-size: 11px; font-weight: 700; color: #fff; white-space: nowrap; }

    /* ── Donut ── */
    .donut-wrap { position: relative; width: 130px; height: 130px; flex-shrink: 0; }
    .donut-wrap svg { width: 100%; height: 100%; }
    .donut-center {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        pointer-events: none;
    }
    .donut-center-val  { font-size: 22px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .donut-center-lbl  { font-size: 10px; font-weight: 600; color: var(--sa-muted); letter-spacing: .5px; text-transform: uppercase; }

    .legend-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: var(--sa-text);
    }
    .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

    /* ── Tenant Table ── */
    .tenant-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .tenant-table th {
        padding: 10px 14px; text-align: left; font-weight: 700;
        font-size: 11px; letter-spacing: .4px; text-transform: uppercase;
        color: var(--sa-muted);
        border-bottom: 2px solid var(--sa-border);
        background: var(--sa-surface);
    }
    .tenant-table td {
        padding: 11px 14px; color: var(--sa-text);
        border-bottom: 1px solid var(--sa-border);
    }
    .tenant-table tr:last-child td { border-bottom: none; }
    .tenant-table tr:hover td { background: var(--sa-surface); }

    .plan-badge {
        display: inline-block; padding: 2px 10px;
        border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .plan-basic    { background: rgba(90,122,170,.12);  color: var(--sa-muted); }
    .plan-standard { background: rgba(0,87,184,.12);    color: var(--sa-accent); }
    .plan-premium  { background: rgba(245,197,24,.15);  color: #a07800; }

    /* ── Alert strip ── */
    .expiry-strip {
        background: rgba(179,138,0,.08);
        border: 2px solid rgba(179,138,0,.25);
        border-radius: 14px; padding: 16px 20px;
    }
    .expiry-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 8px 0; border-bottom: 1px solid rgba(179,138,0,.12);
        font-size: 13px;
    }
    .expiry-row:last-child { border-bottom: none; }

    /* ── Platform aggregate pills ── */
    .agg-pill {
        display: flex; flex-direction: column; align-items: center;
        padding: 14px 18px; border-radius: 14px;
        background: var(--sa-surface); border: 1.5px solid var(--sa-border);
        gap: 4px; flex: 1; min-width: 90px;
    }
    .agg-pill-val { font-size: 20px; font-weight: 800; color: var(--sa-primary); }
    .agg-pill-lbl { font-size: 10px; font-weight: 600; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }
</style>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Page Header                                                                --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-chart-line mr-2" style="color:var(--sa-accent);"></i>
                Platform Analytics
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Consolidated insights across all tenants · as of {{ now()->format('M d, Y H:i') }}
            </p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}"
           class="text-sm px-4 py-2 rounded-lg font-semibold transition"
           style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
            <i class="fas fa-arrow-left mr-1"></i> Dashboard
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Row 1 — Tenant KPI Cards                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Total --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold" style="color:var(--sa-muted);">Total Tenants</span>
            <div class="stat-icon" style="background:rgba(0,87,184,.10);color:var(--sa-accent);">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>
        <div class="text-3xl font-bold" style="color:var(--sa-primary);">{{ $totalTenants }}</div>
        <p class="text-xs mt-1" style="color:var(--sa-muted);">registered organizations</p>
    </div>

    {{-- Approved --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold" style="color:var(--sa-muted);">Approved</span>
            <div class="stat-icon" style="background:rgba(22,163,74,.10);color:var(--sa-success);">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="text-3xl font-bold" style="color:var(--sa-success);">{{ $approvedTenants }}</div>
        <p class="text-xs mt-1" style="color:var(--sa-muted);">active tenants</p>
    </div>

    {{-- Pending --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold" style="color:var(--sa-muted);">Pending</span>
            <div class="stat-icon" style="background:rgba(179,138,0,.10);color:var(--sa-warning);">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
        <div class="text-3xl font-bold" style="color:var(--sa-warning);">{{ $pendingTenants }}</div>
        <p class="text-xs mt-1" style="color:var(--sa-muted);">awaiting approval</p>
    </div>

    {{-- Rejected --}}
    <div class="stat-card">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold" style="color:var(--sa-muted);">Rejected</span>
            <div class="stat-icon" style="background:rgba(206,17,38,.10);color:var(--sa-danger);">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="text-3xl font-bold" style="color:var(--sa-danger);">{{ $rejectedTenants }}</div>
        <p class="text-xs mt-1" style="color:var(--sa-muted);">rejected requests</p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Row 2 — Platform Aggregates                                                 --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="section-card mb-6">
    <div class="section-title">
        <i class="fas fa-globe" style="color:var(--sa-accent);"></i>
        Platform-Wide Totals
        <span class="ml-auto text-xs font-normal" style="color:var(--sa-muted);">across all approved tenants</span>
    </div>
    <div class="flex flex-wrap gap-3">
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['trainees']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-user-graduate mr-1"></i>Trainees</span>
        </div>
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['trainers']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-chalkboard-teacher mr-1"></i>Trainers</span>
        </div>
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['courses']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-book mr-1"></i>Courses</span>
        </div>
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['enrollments']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-clipboard-list mr-1"></i>Enrollments</span>
        </div>
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['assessments']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-clipboard-check mr-1"></i>Assessments</span>
        </div>
        <div class="agg-pill">
            <span class="agg-pill-val">{{ number_format($platformTotals['certificates']) }}</span>
            <span class="agg-pill-lbl"><i class="fas fa-certificate mr-1"></i>Certificates</span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Row 3 — Charts Row                                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Monthly Registrations Bar Chart --}}
    <div class="section-card lg:col-span-2">
        <div class="section-title">
            <i class="fas fa-calendar-alt" style="color:var(--sa-accent);"></i>
            Monthly Registrations
        </div>

        @php
            $counts = array_column($monthlyRegistrations, 'count');
            $maxReg = count($counts) ? max(max($counts), 1) : 1;
        @endphp

        <div class="bar-group">
            @foreach($monthlyRegistrations as $month)
                @php $pct = ($month['count'] / $maxReg) * 100; @endphp
                <div class="bar-row">
                    <div class="bar-label">{{ $month['label'] }}</div>
                    <div class="bar-track">
                        <div class="bar-fill"
                             style="width:{{ max($pct, 3) }}%; background: linear-gradient(90deg, var(--sa-accent), var(--sa-primary));">
                            @if($month['count'] > 0)
                                <span class="bar-val">{{ $month['count'] }}</span>
                            @endif
                        </div>
                    </div>
                    @if($month['count'] === 0)
                        <span style="font-size:11px;color:var(--sa-muted);">0</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Subscription Donut --}}
    <div class="section-card flex flex-col">
        <div class="section-title">
            <i class="fas fa-credit-card" style="color:var(--sa-accent);"></i>
            Subscription Mix
        </div>

        @php
            $basic    = $subscriptionBreakdown['basic']    ?? 0;
            $standard = $subscriptionBreakdown['standard'] ?? 0;
            $premium  = $subscriptionBreakdown['premium']  ?? 0;
            $subTotal = max($basic + $standard + $premium, 1);

            // SVG donut math (r=50, circumference≈314)
            $cx = 65; $cy = 65; $r = 50; $circ = 2 * pi() * $r;
            $offsets = [];
            $pctB = $basic    / $subTotal;
            $pctS = $standard / $subTotal;
            $pctP = $premium  / $subTotal;
            // dash arrays
            $dB = $circ * $pctB;
            $dS = $circ * $pctS;
            $dP = $circ * $pctP;
            // start offsets (rotate so first seg starts at top: offset = -circ/4)
            $oB = -$circ / 4;
            $oS = $oB - $dB;
            $oP = $oS - $dS;
        @endphp

        <div class="flex items-center gap-6 flex-1">
            <div class="donut-wrap">
                <svg viewBox="0 0 130 130">
                    <circle cx="65" cy="65" r="50" fill="none" stroke="var(--sa-surface)" stroke-width="18"/>
                    @if($basic > 0)
                    <circle cx="65" cy="65" r="50" fill="none" stroke="#7fa8d4" stroke-width="18"
                            stroke-dasharray="{{ $dB }} {{ $circ - $dB }}"
                            stroke-dashoffset="{{ $oB }}" stroke-linecap="round"/>
                    @endif
                    @if($standard > 0)
                    <circle cx="65" cy="65" r="50" fill="none" stroke="var(--sa-accent)" stroke-width="18"
                            stroke-dasharray="{{ $dS }} {{ $circ - $dS }}"
                            stroke-dashoffset="{{ $oS }}" stroke-linecap="round"/>
                    @endif
                    @if($premium > 0)
                    <circle cx="65" cy="65" r="50" fill="none" stroke="#d4a800" stroke-width="18"
                            stroke-dasharray="{{ $dP }} {{ $circ - $dP }}"
                            stroke-dashoffset="{{ $oP }}" stroke-linecap="round"/>
                    @endif
                </svg>
                <div class="donut-center">
                    <span class="donut-center-val">{{ $approvedTenants }}</span>
                    <span class="donut-center-lbl">active</span>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <div class="legend-item">
                    <div class="legend-dot" style="background:#7fa8d4;"></div>
                    <div>
                        <span class="font-semibold" style="color:var(--sa-text);">Basic</span>
                        <span class="ml-2 text-xs" style="color:var(--sa-muted);">{{ $basic }}</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:var(--sa-accent);"></div>
                    <div>
                        <span class="font-semibold" style="color:var(--sa-text);">Standard</span>
                        <span class="ml-2 text-xs" style="color:var(--sa-muted);">{{ $standard }}</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background:#d4a800;"></div>
                    <div>
                        <span class="font-semibold" style="color:var(--sa-text);">Premium</span>
                        <span class="ml-2 text-xs" style="color:var(--sa-muted);">{{ $premium }}</span>
                    </div>
                </div>
                @if($expiredTenants > 0)
                <div class="legend-item mt-2 pt-2" style="border-top:1px solid var(--sa-border);">
                    <div class="legend-dot" style="background:var(--sa-danger);"></div>
                    <div>
                        <span class="font-semibold" style="color:var(--sa-danger);">Expired</span>
                        <span class="ml-2 text-xs" style="color:var(--sa-muted);">{{ $expiredTenants }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Row 4 — Expiring Soon Alert                                                 --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
@if($expiringSoon->count() > 0)
<div class="expiry-strip mb-6">
    <div class="flex items-center gap-2 mb-3">
        <i class="fas fa-exclamation-triangle" style="color:var(--sa-warning);"></i>
        <span class="font-bold text-sm" style="color:var(--sa-warning);">
            {{ $expiringSoon->count() }} tenant{{ $expiringSoon->count() > 1 ? 's' : '' }} expiring within 7 days
        </span>
    </div>
    @foreach($expiringSoon as $t)
        <div class="expiry-row">
            <div>
                <span class="font-semibold" style="color:var(--sa-text);">{{ $t->name }}</span>
                <span class="text-xs ml-2" style="color:var(--sa-muted);">{{ $t->subdomain }}.tcm.com</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold" style="color:var(--sa-warning);">
                    expires {{ $t->expires_at->format('M d, Y') }}
                </span>
                <a href="{{ route('superadmin.tenants.show', $t) }}"
                   class="text-xs px-3 py-1 rounded-lg font-semibold transition"
                   style="background:var(--sa-accent);color:#fff;">
                    Manage
                </a>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- Row 5 — Per-Tenant Data Table                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-title">
        <i class="fas fa-table" style="color:var(--sa-accent);"></i>
        Tenant Activity Breakdown
        <span class="ml-auto text-xs font-normal" style="color:var(--sa-muted);">{{ count($tenantStats) }} approved tenant(s)</span>
    </div>

    @if(count($tenantStats) > 0)
        <div class="overflow-x-auto">
            <table class="tenant-table">
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Plan</th>
                        <th>Trainers</th>
                        <th>Trainees</th>
                        <th>Courses</th>
                        <th>Enrollments</th>
                        <th>Assessments</th>
                        <th>Certificates</th>
                        <th>Expires</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenantStats as $row)
                        @php $t = $row['tenant']; @endphp
                        <tr>
                            <td>
                                <div class="font-semibold" style="color:var(--sa-text);">{{ $t->name }}</div>
                                <div class="text-xs" style="color:var(--sa-muted);">{{ $t->subdomain }}.tcm.com</div>
                            </td>
                            <td>
                                <span class="plan-badge plan-{{ $t->subscription }}">
                                    {{ ucfirst($t->subscription) }}
                                </span>
                            </td>
                            <td class="text-center font-semibold">{{ $row['trainers'] }}</td>
                            <td class="text-center font-semibold">{{ $row['trainees'] }}</td>
                            <td class="text-center font-semibold">{{ $row['courses'] }}</td>
                            <td class="text-center font-semibold">{{ $row['enrollments'] }}</td>
                            <td class="text-center font-semibold">{{ $row['assessments'] }}</td>
                            <td class="text-center font-semibold">{{ $row['certificates'] }}</td>
                            <td class="text-xs" style="color:{{ $t->expires_at && $t->expires_at->isPast() ? 'var(--sa-danger)' : 'var(--sa-muted)' }};">
                                {{ $t->expires_at ? $t->expires_at->format('M d, Y') : '—' }}
                                @if($t->expires_at && $t->expires_at->isPast())
                                    <span class="ml-1 font-bold">(expired)</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('superadmin.tenants.show', $t) }}"
                                   class="text-xs px-3 py-1 rounded-lg font-semibold transition"
                                   style="background:rgba(0,87,184,.10);color:var(--sa-accent);">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <i class="fas fa-inbox text-4xl mb-3" style="color:var(--sa-muted);opacity:.4;"></i>
            <p style="color:var(--sa-muted);">No approved tenants yet.</p>
        </div>
    @endif
</div>

@endsection