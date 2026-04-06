@extends('layouts.app')
@section('title', 'Plan Management')

@section('content')

<style>
    :root {
        --sa-primary:  #003087;
        --sa-accent:   #0057B8;
        --sa-success:  #16a34a;
        --sa-warning:  #b38a00;
        --sa-danger:   #CE1126;
        --sa-gold:     #F5C518;
        --sa-border:   #c5d8f5;
        --sa-text:     #001a4d;
        --sa-muted:    #5a7aaa;
        --sa-bg:       #ffffff;
        --sa-surface:  #f4f8ff;

        --sa-cb-bg:             #ffffff;
        --sa-cb-border:         #c5d8f5;
        --sa-cb-checked-bg:     rgba(0,87,184,.12);
        --sa-cb-checked-border: #0057B8;
        --sa-cb-checked-text:   #003087;
        --sa-cb-hover-bg:       rgba(0,87,184,.06);
        --sa-cb-hover-border:   #0057B8;
        --sa-cb-hover-text:     #0057B8;
    }
    .dark {
        --sa-bg:      #0a1628;
        --sa-surface: #0d1f3c;
        --sa-border:  #1e3a6b;
        --sa-text:    #dde8ff;
        --sa-muted:   #6b8abf;

        --sa-cb-bg:             #0d1f3c;
        --sa-cb-border:         #2a4a7f;
        --sa-cb-checked-bg:     rgba(0,120,255,.18);
        --sa-cb-checked-border: #4d9fff;
        --sa-cb-checked-text:   #a8d0ff;
        --sa-cb-hover-bg:       rgba(0,120,255,.10);
        --sa-cb-hover-border:   #4d9fff;
        --sa-cb-hover-text:     #7ab8ff;
    }

    /* ── Tabs ── */
    .tab-nav { display: flex; gap: 0; border-bottom: 2px solid var(--sa-border); margin-bottom: 24px; }
    .tab-btn {
        padding: 11px 22px; font-size: 13px; font-weight: 700;
        color: var(--sa-muted); border: none; background: none;
        border-bottom: 3px solid transparent; margin-bottom: -2px;
        cursor: pointer; font-family: inherit; transition: all .15s;
        display: flex; align-items: center; gap: 7px;
    }
    .tab-btn.active { color: var(--sa-accent); border-bottom-color: var(--sa-accent); }
    .tab-btn:hover:not(.active) { color: var(--sa-text); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── Plan Cards ── */
    .plan-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap: 20px; }
    .plan-card {
        border-radius: 18px; border: 2px solid var(--sa-border);
        background: var(--sa-bg); overflow: hidden;
        transition: box-shadow .18s, transform .18s;
    }
    .plan-card:hover { box-shadow: 0 8px 30px rgba(0,48,135,.10); transform: translateY(-2px); }
    .plan-header { padding: 22px 24px 18px; border-bottom: 2px solid var(--sa-border); }
    .plan-slug-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
        margin-bottom: 10px;
    }
    .badge-basic    { background: rgba(90,122,170,.12); color: var(--sa-muted); }
    .badge-standard { background: rgba(0,87,184,.12);   color: var(--sa-accent); }
    .badge-premium  { background: rgba(245,197,24,.15); color: #a07800; }
    .plan-price { font-size: 28px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .plan-price span { font-size: 14px; font-weight: 500; color: var(--sa-muted); margin-left: 2px; }
    .plan-duration { font-size: 12px; color: var(--sa-muted); margin-top: 4px; }
    .plan-body { padding: 18px 24px; }
    .feature-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 7px 0; border-bottom: 1px solid var(--sa-border); font-size: 13px;
    }
    .feature-row:last-child { border-bottom: none; }
    .feature-label { color: var(--sa-muted); font-weight: 500; }
    .feature-val   { font-weight: 700; color: var(--sa-text); }
    .feature-yes   { color: var(--sa-success); }
    .feature-no    { color: var(--sa-danger); opacity: .6; }

    /* ── Form elements ── */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
    .fi { display: flex; flex-direction: column; gap: 5px; }
    .fi label { font-size: 11px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .4px; }
    .fi input, .fi select, .fi textarea {
        padding: 8px 10px; border-radius: 8px; border: 1.5px solid var(--sa-border);
        background: var(--sa-bg); color: var(--sa-text); font-family: inherit;
        font-size: 13px; outline: none; transition: border-color .15s;
    }
    .fi input:focus, .fi select:focus, .fi textarea:focus { border-color: var(--sa-accent); }

    /* ── Checkbox pill toggles ── */
    .check-group { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .check-item input[type="checkbox"] {
        position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;
    }
    .check-item {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600; color: var(--sa-muted);
        cursor: pointer; padding: 6px 12px; border-radius: 8px;
        border: 1.5px solid var(--sa-cb-border); background: var(--sa-cb-bg);
        transition: background .15s, border-color .15s, color .15s;
        user-select: none; position: relative;
    }
    .check-item::before {
        content: ''; display: inline-flex; flex-shrink: 0;
        width: 14px; height: 14px; border-radius: 4px;
        border: 1.5px solid var(--sa-cb-border); background: var(--sa-cb-bg);
        transition: background .15s, border-color .15s;
    }
    .check-item:hover { border-color: var(--sa-cb-hover-border); color: var(--sa-cb-hover-text); background: var(--sa-cb-hover-bg); }
    .check-item:hover::before { border-color: var(--sa-cb-hover-border); }
    .check-item:has(input:checked) { background: var(--sa-cb-checked-bg); border-color: var(--sa-cb-checked-border); color: var(--sa-cb-checked-text); }
    .check-item:has(input:checked)::before {
        background: var(--sa-cb-checked-border); border-color: var(--sa-cb-checked-border);
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 10 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 4l3 3 5-6' stroke='%23fff' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: center; background-size: 10px 8px;
    }

    /* ── Discount table ── */
    .disc-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .disc-table th {
        padding: 10px 14px; text-align: left; font-weight: 700;
        font-size: 11px; letter-spacing: .4px; text-transform: uppercase;
        color: var(--sa-muted); border-bottom: 2px solid var(--sa-border);
        background: var(--sa-surface);
    }
    .disc-table td { padding: 11px 14px; color: var(--sa-text); border-bottom: 1px solid var(--sa-border); }
    .disc-table tr:last-child td { border-bottom: none; }
    .disc-table tr:hover td { background: var(--sa-surface); }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
    }
    .sb-success { background: rgba(22,163,74,.10);  color: var(--sa-success); }
    .sb-warning { background: rgba(179,138,0,.10);  color: var(--sa-warning); }
    .sb-danger  { background: rgba(206,17,38,.10);  color: var(--sa-danger);  }
    .sb-muted   { background: rgba(90,122,170,.10); color: var(--sa-muted);   }

    /* ── Buttons ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; font-family: inherit; text-decoration: none; transition: all .15s; }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-danger  { background: rgba(206,17,38,.10); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.25); }
    .btn-outline { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-gold    { background: linear-gradient(135deg,var(--sa-gold) 0%,#d4a800 100%); color: #001a4d; }
    .btn-sm { padding: 5px 10px; font-size: 11px; border-radius: 7px; }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        z-index: 1000; display: flex; align-items: center; justify-content: center;
        padding: 24px; opacity: 0; pointer-events: none; transition: opacity .2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: var(--sa-bg); border-radius: 20px; border: 2px solid var(--sa-border);
        width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,.25); transform: translateY(20px); transition: transform .2s;
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-header {
        padding: 22px 26px 18px; border-bottom: 2px solid var(--sa-border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title  { font-size: 17px; font-weight: 800; color: var(--sa-primary); }
    .modal-body   { padding: 24px 26px; }
    .modal-footer { padding: 16px 26px; border-top: 2px solid var(--sa-border); display: flex; gap: 10px; justify-content: flex-end; }

    /* ── Apply panel ── */
    .apply-panel { border-radius: 18px; border: 2px solid var(--sa-border); background: var(--sa-bg); padding: 24px 28px; }

    /* ── Validator pill ── */
    .validate-result { border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; display: none; margin-top: 10px; }
    .validate-result.valid   { background: rgba(22,163,74,.08); border: 1.5px solid rgba(22,163,74,.3); color: var(--sa-success); display: block; }
    .validate-result.invalid { background: rgba(206,17,38,.08); border: 1.5px solid rgba(206,17,38,.3); color: var(--sa-danger); display: block; }

    /* ── Stat pills ── */
    .stat-pill { display: flex; flex-direction: column; align-items: center; padding: 14px 20px; border-radius: 14px; background: var(--sa-surface); border: 1.5px solid var(--sa-border); gap: 4px; }
    .stat-pill-val { font-size: 22px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .stat-pill-lbl { font-size: 10px; font-weight: 600; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }

    /* ── Type badge inside discount table ── */
    .type-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase;
    }
    .type-auto { background: rgba(22,163,74,.10); color: var(--sa-success); }
    .type-code { background: rgba(0,87,184,.10);  color: var(--sa-accent); }
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-tags mr-2" style="color:var(--sa-accent);"></i> Plan Management
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Configure subscription plans, manage discounts, and apply pricing to tenants
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('modal-new-discount')" class="btn btn-gold">
                <i class="fas fa-percent"></i> New Discount
            </button>
            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="rounded-xl border-2 p-4" style="background:rgba(22,163,74,.05);border-color:var(--sa-success);">
            <div style="color:var(--sa-success);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-2 p-4" style="background:rgba(206,17,38,.05);border-color:var(--sa-danger);">
            <div style="color:var(--sa-danger);" class="font-semibold flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
            </div>
        </div>
    @endif

    {{-- ── Stats Row ── --}}
    <div class="flex flex-wrap gap-3">
        <div class="stat-pill">
            <span class="stat-pill-val">{{ $plans->count() }}</span>
            <span class="stat-pill-lbl">Plans</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val" style="color:var(--sa-success);">{{ $discounts->where('is_active', true)->count() }}</span>
            <span class="stat-pill-lbl">Active Discounts</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val">{{ $discounts->where('is_automatic', true)->count() }}</span>
            <span class="stat-pill-lbl">Auto Discounts</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val">{{ $discounts->where('is_automatic', false)->count() }}</span>
            <span class="stat-pill-lbl">Promo Codes</span>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('plans', this)">
            <i class="fas fa-layer-group"></i> Subscription Plans
        </button>
        <button class="tab-btn" onclick="switchTab('discounts', this)">
            <i class="fas fa-percent"></i> Discounts
            @if($discounts->count())
                <span class="px-2 py-0.5 rounded-full text-xs" style="background:var(--sa-surface);color:var(--sa-muted);">{{ $discounts->count() }}</span>
            @endif
        </button>
        <button class="tab-btn" onclick="switchTab('apply', this)">
            <i class="fas fa-magic"></i> Apply to Tenant
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Plans                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-plans" class="tab-content active">
        <div class="plan-grid">
            @foreach($plans as $plan)
                @php
                    $badgeClass = match($plan->slug) {
                        'standard' => 'badge-standard',
                        'premium'  => 'badge-premium',
                        default    => 'badge-basic',
                    };
                    $headerBg = match($plan->slug) {
                        'standard' => 'rgba(0,87,184,.04)',
                        'premium'  => 'rgba(245,197,24,.06)',
                        default    => 'rgba(90,122,170,.04)',
                    };

                    // Build feature rows from model fields
                    $features = [
                        'Trainees'        => $plan->max_trainees  ? number_format($plan->max_trainees)  : 'Unlimited',
                        'Trainers'        => $plan->max_trainers  ? number_format($plan->max_trainers)  : ($plan->has_trainers ? 'Unlimited' : '—'),
                        'Users'           => $plan->max_users     ? number_format($plan->max_users)     : 'Unlimited',
                        'Courses'         => $plan->max_courses   ? number_format($plan->max_courses)   : 'Unlimited',
                        'Trainer Mgmt'    => $plan->has_trainers,
                        'Assessments'     => $plan->has_assessments,
                        'Certificates'    => $plan->has_certificates,
                        'Custom Reports'  => $plan->has_custom_reports,
                        'Custom Branding' => $plan->has_branding,
                        'Exports/month'   => $plan->max_exports_monthly === null
                            ? 'Unlimited'
                            : ($plan->max_exports_monthly === 0 ? '—' : number_format($plan->max_exports_monthly)),
                        'Export Formats'  => count($plan->allowed_export_formats ?? [])
                            ? strtoupper(implode(', ', $plan->allowed_export_formats))
                            : '—',
                    ];
                @endphp

                <div class="plan-card">
                    <div class="plan-header" style="background:{{ $headerBg }};">
                        <span class="plan-slug-badge {{ $badgeClass }}">
                            <i class="fas fa-circle" style="font-size:6px;"></i>
                            {{ strtoupper($plan->slug) }}
                        </span>
                        <div class="font-bold text-lg mb-1" style="color:var(--sa-primary);">{{ $plan->name }}</div>
                        <div class="plan-price">
                            @if((float)$plan->price === 0.0) Free
                            @else ₱{{ number_format($plan->price, 0) }}
                            @endif
                            <span>/ plan</span>
                        </div>
                        <div class="plan-duration">
                            <i class="fas fa-clock mr-1"></i> {{ $plan->duration_label }}
                        </div>
                    </div>

                    <div class="plan-body">
                        @foreach($features as $label => $val)
                            <div class="feature-row">
                                <span class="feature-label">{{ $label }}</span>
                                <span class="feature-val">
                                    @if(is_bool($val))
                                        @if($val)
                                            <span class="feature-yes"><i class="fas fa-check"></i></span>
                                        @else
                                            <span class="feature-no"><i class="fas fa-times"></i></span>
                                        @endif
                                    @else
                                        {{ $val }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Discounts                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-discounts" class="tab-content">
        <div class="rounded-2xl border-2 overflow-hidden" style="background:var(--sa-bg);border-color:var(--sa-border);">
            @if($discounts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="disc-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Code / Label</th>
                                <th>Discount</th>
                                <th>Plan</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $d)
                                @php
                                    $statusClass = match($d->status_label) {
                                        'Active'    => 'sb-success',
                                        'Scheduled' => 'sb-warning',
                                        default     => 'sb-danger',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        @if($d->is_automatic)
                                            <span class="type-badge type-auto">🗓 Auto</span>
                                        @else
                                            <span class="type-badge type-code">🔑 Code</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->is_automatic)
                                            <span class="font-semibold">{{ $d->label }}</span>
                                            <div class="text-xs mt-0.5" style="color:var(--sa-muted);">No code needed</div>
                                        @else
                                            <code class="px-2 py-1 rounded text-xs font-bold"
                                                  style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                                {{ $d->code }}
                                            </code>
                                            <div class="text-xs mt-0.5" style="color:var(--sa-muted);">{{ $d->label }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-bold text-base" style="color:var(--sa-success);">
                                            {{ $d->formatted_value }}
                                            @if($d->type === 'percentage')
                                                <span class="text-xs font-normal" style="color:var(--sa-muted);">off</span>
                                            @endif
                                        </span>
                                        <div class="text-xs" style="color:var(--sa-muted);">{{ ucfirst($d->type) }}</div>
                                    </td>
                                    <td class="text-xs" style="color:var(--sa-muted);">
                                        {{ $d->plan_slug ? ucfirst($d->plan_slug) : 'All plans' }}
                                    </td>
                                    <td class="text-xs" style="color:var(--sa-muted);">
                                        @if($d->valid_from || $d->valid_until)
                                            {{ $d->valid_from?->format('M d, Y') ?? '—' }} → {{ $d->valid_until?->format('M d, Y') ?? '—' }}
                                        @else
                                            No limit
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $statusClass }}">{{ $d->status_label }}</span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openEditDiscount({{ $d->id }})" class="btn btn-outline btn-sm">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('superadmin.plans.discounts.destroy', $d) }}" method="POST"
                                                  onsubmit="return confirm('Delete this discount?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Hidden data for JS --}}
                                        <div id="disc-data-{{ $d->id }}" class="hidden"
                                             data-id="{{ $d->id }}"
                                             data-is-automatic="{{ $d->is_automatic ? '1' : '0' }}"
                                             data-code="{{ $d->code }}"
                                             data-label="{{ $d->label }}"
                                             data-type="{{ $d->type }}"
                                             data-value="{{ $d->value }}"
                                             data-plan-slug="{{ $d->plan_slug ?? '' }}"
                                             data-valid-from="{{ $d->valid_from?->format('Y-m-d') ?? '' }}"
                                             data-valid-until="{{ $d->valid_until?->format('Y-m-d') ?? '' }}"
                                             data-active="{{ $d->is_active ? '1' : '0' }}"
                                             data-update-url="{{ route('superadmin.plans.discounts.update', $d) }}">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-14 text-center">
                    <i class="fas fa-percent text-5xl mb-4" style="color:var(--sa-muted);opacity:.3;"></i>
                    <p style="color:var(--sa-muted);" class="mb-3">No discounts yet.</p>
                    <button onclick="openModal('modal-new-discount')" class="btn btn-gold">
                        <i class="fas fa-plus"></i> Create First Discount
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Apply to Tenant                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-apply" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="apply-panel">
                <h2 class="font-bold text-lg mb-5" style="color:var(--sa-primary);">
                    <i class="fas fa-magic mr-2" style="color:var(--sa-accent);"></i>
                    Apply Plan to Tenant
                </h2>
                <p class="text-sm mb-5" style="color:var(--sa-muted);">
                    This <strong style="color:var(--sa-text);">changes the tenant's active plan</strong>.
                    An optional promo code only adjusts the recorded price — it does not affect the plan assigned.
                </p>

                <form action="{{ route('superadmin.plans.apply') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="fi">
                        <label>Select Tenant</label>
                        <select name="tenant_id" required>
                            <option value="">— Choose tenant —</option>
                            @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} — currently on {{ ucfirst($t->subscription ?? 'basic') }}
                                    ({{ $t->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fi">
                        <label>Target Plan</label>
                        <select name="plan_slug" id="apply-plan-select" required onchange="liveValidate()">
                            <option value="">— Choose plan —</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->slug }}">
                                    {{ $plan->name }} (₱{{ number_format($plan->price, 0) }} / {{ $plan->duration_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fi">
                        <label>
                            Promo Code
                            <span style="color:var(--sa-muted);font-weight:400;text-transform:none;">(optional — affects recorded price only)</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="discount_code" id="apply-code-input"
                                   placeholder="e.g. SAVE20"
                                   style="text-transform:uppercase;flex:1;"
                                   oninput="this.value=this.value.toUpperCase();liveValidate()">
                            <button type="button" onclick="liveValidate()" class="btn btn-outline" style="white-space:nowrap;">
                                <i class="fas fa-check-circle"></i> Check
                            </button>
                        </div>
                        <div id="validate-result" class="validate-result"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-magic"></i> Apply & Set Plan
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-xl border-2 p-5" style="background:rgba(0,87,184,.04);border-color:rgba(0,87,184,.2);">
                    <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                        <i class="fas fa-info-circle mr-2"></i> How it works
                    </div>
                    <div class="space-y-2 text-sm" style="color:var(--sa-muted);">
                        <p>• <strong style="color:var(--sa-text);">Plan assignment</strong> changes the tenant's subscription immediately</p>
                        <p>• <strong style="color:var(--sa-text);">Promo codes</strong> here only reduce the amount recorded in billing history</p>
                        <p>• <strong style="color:var(--sa-text);">Automatic discounts</strong> are shown directly on tenant plan cards — no code needed</p>
                        <p>• Tenants can also self-upgrade from their subscription page</p>
                    </div>
                </div>

                @php $activeCodes = $discounts->where('is_active', true)->where('is_automatic', false)->take(5); @endphp
                @if($activeCodes->count())
                    <div class="rounded-xl border-2 p-5" style="background:var(--sa-bg);border-color:var(--sa-border);">
                        <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                            <i class="fas fa-bolt mr-2" style="color:var(--sa-gold);"></i> Active Promo Codes
                        </div>
                        <div class="space-y-2">
                            @foreach($activeCodes as $d)
                                <div class="flex items-center justify-between text-sm">
                                    <code class="px-2 py-0.5 rounded text-xs font-bold"
                                          style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                        {{ $d->code }}
                                    </code>
                                    <span class="font-semibold" style="color:var(--sa-success);">{{ $d->formatted_value }}</span>
                                    <span class="text-xs" style="color:var(--sa-muted);">
                                        {{ $d->plan_slug ? ucfirst($d->plan_slug) : 'All plans' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: New Discount                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-new-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> New Discount
            </span>
            <button onclick="closeModal('modal-new-discount')"
                    style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('superadmin.plans.discounts.store') }}" method="POST">
            @csrf
            <div class="modal-body space-y-4">
                @include('superadmin.plans._discount_fields')
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-new-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Edit Discount                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">
                <i class="fas fa-pencil-alt mr-2" style="color:var(--sa-accent);"></i> Edit Discount
            </span>
            <button onclick="closeModal('modal-edit-discount')"
                    style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="edit-discount-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body space-y-4">
                @include('superadmin.plans._discount_fields', ['isEdit' => true])
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Scripts ── --}}
<script>
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    function openEditDiscount(id) {
        const d = document.getElementById('disc-data-' + id).dataset;

        document.getElementById('edit-discount-form').action = d.updateUrl;

        // Set automatic vs code radio
        const isAuto = d.isAutomatic === '1';
        document.getElementById('ed-radio-automatic').checked = isAuto;
        document.getElementById('ed-radio-code').checked      = !isAuto;
        toggleCodeField('ed-');

        document.getElementById('ed-code').value        = d.code;
        document.getElementById('ed-label').value       = d.label;
        document.getElementById('ed-type').value        = d.type;
        document.getElementById('ed-value').value       = d.value;
        document.getElementById('ed-plan-slug').value   = d.planSlug;
        document.getElementById('ed-valid-from').value  = d.validFrom;
        document.getElementById('ed-valid-until').value = d.validUntil;
        document.getElementById('ed-active').checked    = d.active === '1';

        openModal('modal-edit-discount');
    }

    let validateTimeout;
    function liveValidate() {
        clearTimeout(validateTimeout);

        const code   = document.getElementById('apply-code-input').value.trim();
        const plan   = document.getElementById('apply-plan-select').value;
        const result = document.getElementById('validate-result');

        if (!code || !plan) { result.className = 'validate-result'; return; }

        validateTimeout = setTimeout(() => {
            fetch('{{ route('superadmin.plans.discounts.validate') }}', {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code, plan_slug: plan })
            })
            .then(r => r.json())
            .then(data => {
                if (data.valid) {
                    result.className = 'validate-result valid';
                    result.innerHTML =
                        `<i class="fas fa-check-circle mr-1"></i> Code valid! Saves ${data.formatted_value}<br>` +
                        `<span style="font-size:11px;">` +
                        `Original: ₱${Number(data.original_price).toFixed(2)} → ` +
                        `Discount: −₱${Number(data.discount_amount).toFixed(2)} → ` +
                        `<strong>Final: ₱${Number(data.final_price).toFixed(2)}</strong></span>`;
                } else {
                    result.className = 'validate-result invalid';
                    result.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
                }
            });
        }, 400);
    }
</script>

@endsection