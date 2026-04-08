@extends('layouts.app')
@section('title', 'Plan & Discount Management')

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
    .tab-bar {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: var(--sa-surface);
        border: 1.5px solid var(--sa-border);
        border-radius: 12px;
        margin-bottom: 24px;
        width: fit-content;
    }
    .tab-btn {
        padding: 9px 22px;
        border-radius: 9px;
        border: none;
        background: transparent;
        color: var(--sa-muted);
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .tab-btn:hover { background: var(--sa-bg); color: var(--sa-text); }
    .tab-btn.active { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.25); }

    /* ── Plan cards grid ── */
    .plan-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 18px;
        margin-bottom: 8px;
    }
    .plan-card {
        background: var(--sa-bg);
        border: 2px solid var(--sa-border);
        border-radius: 18px;
        padding: 24px;
        transition: border-color .2s, box-shadow .2s;
        position: relative;
    }
    .plan-card:hover {
        border-color: var(--sa-accent);
        box-shadow: 0 8px 30px rgba(0,87,184,.12);
    }
    .plan-card.inactive { opacity: .65; }

    .plan-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .plan-icon { font-size: 28px; line-height: 1; }
    .plan-slug-badge {
        padding: 3px 12px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .6px;
    }
    .slug-basic    { background: rgba(90,122,170,.12);  color: var(--sa-muted); }
    .slug-standard { background: rgba(0,87,184,.12);    color: var(--sa-accent); }
    .slug-premium  { background: rgba(245,197,24,.15);  color: #a07800; }

    .plan-name { font-size: 18px; font-weight: 800; color: var(--sa-primary); margin-bottom: 2px; }
    .dark .plan-name { color: #dde8ff; }
    .plan-desc { font-size: 12px; color: var(--sa-muted); line-height: 1.5; margin-bottom: 14px; }

    .plan-price-row {
        display: flex;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 14px;
    }
    .plan-price { font-size: 30px; font-weight: 800; color: var(--sa-primary); }
    .dark .plan-price { color: #dde8ff; }
    .plan-duration { font-size: 12px; color: var(--sa-muted); }

    .plan-limits {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 14px;
        font-size: 12px;
    }
    .plan-limit-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 8px;
        border-radius: 7px;
        background: var(--sa-surface);
    }
    .plan-limit-key { color: var(--sa-muted); font-weight: 600; }
    .plan-limit-val { font-weight: 700; color: var(--sa-text); }

    .plan-flags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 16px;
    }
    .flag-pill {
        padding: 2px 10px;
        border-radius: 100px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .flag-on  { background: rgba(22,163,74,.10);  color: var(--sa-success); border: 1px solid rgba(22,163,74,.2); }
    .flag-off { background: rgba(90,122,170,.08); color: var(--sa-muted);   border: 1px solid rgba(90,122,170,.15); text-decoration: line-through; opacity: .7; }

    .plan-avail {
        font-size: 11px;
        color: var(--sa-muted);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .plan-card-actions {
        display: flex;
        gap: 8px;
        padding-top: 14px;
        border-top: 1px solid var(--sa-border);
    }

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
    .btn-success { background: rgba(22,163,74,.10); color: var(--sa-success); border: 1.5px solid rgba(22,163,74,.25); }
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
        width: 100%; max-width: 620px; max-height: 90vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,.25); transform: translateY(20px); transition: transform .2s;
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-header {
        padding: 22px 26px 18px; border-bottom: 2px solid var(--sa-border);
        display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: var(--sa-bg); z-index: 1;
    }
    .modal-title  { font-size: 17px; font-weight: 800; color: var(--sa-primary); }
    .modal-body   { padding: 24px 26px; }
    .modal-footer { padding: 16px 26px; border-top: 2px solid var(--sa-border); display: flex; gap: 10px; justify-content: flex-end; position: sticky; bottom: 0; background: var(--sa-bg); }

    /* ── Plan modal specific ── */
    .plan-form-section {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--sa-border);
    }
    .plan-form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .section-label {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .6px; color: var(--sa-muted);
        margin-bottom: 12px;
        display: flex; align-items: center; gap: 7px;
    }
    .section-label i { color: var(--sa-accent); }

    /* limit row */
    .limit-input-wrap { display: flex; align-items: center; gap: 8px; }
    .limit-input-wrap input { flex: 1; }
    .unlimited-toggle {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 600; color: var(--sa-muted);
        cursor: pointer; white-space: nowrap; user-select: none;
    }
    .unlimited-toggle input { accent-color: var(--sa-accent); width: 13px; height: 13px; cursor: pointer; }

    /* feature toggles */
    .feat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
    .feat-toggle { position: relative; }
    .feat-toggle input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .feat-toggle label {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 12px; border-radius: 9px; border: 1.5px solid var(--sa-border);
        background: var(--sa-surface); cursor: pointer; transition: all .15s;
        font-size: 12px; font-weight: 600; color: var(--sa-text); user-select: none;
    }
    .feat-check {
        flex-shrink: 0; width: 16px; height: 16px; border-radius: 4px;
        border: 1.5px solid var(--sa-border); background: var(--sa-bg);
        display: flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 700; color: transparent; transition: all .15s; line-height: 1;
    }
    .feat-toggle input:checked + label { border-color: var(--sa-accent); background: rgba(0,87,184,.07); }
    .feat-toggle input:checked + label .feat-check { background: var(--sa-accent); border-color: var(--sa-accent); color: #fff; }

    /* export format toggles */
    .export-group { display: flex; gap: 8px; flex-wrap: wrap; }
    .exp-toggle { position: relative; }
    .exp-toggle input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .exp-toggle label {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
        border-radius: 100px; border: 1.5px solid var(--sa-border); background: var(--sa-surface);
        cursor: pointer; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: var(--sa-muted); transition: all .15s; user-select: none;
    }
    .exp-toggle input:checked + label { border-color: var(--sa-success); background: rgba(22,163,74,.08); color: var(--sa-success); }

    /* slug radio */
    .slug-group { display: flex; gap: 8px; }
    .slug-pill { flex: 1; position: relative; }
    .slug-pill input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .slug-pill label {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        padding: 10px 8px; border-radius: 10px; border: 1.5px solid var(--sa-border);
        background: var(--sa-surface); cursor: pointer; transition: all .15s;
        text-align: center; user-select: none;
    }
    .slug-pill input:checked + label { border-color: var(--sa-accent); background: rgba(0,87,184,.07); }

    /* ── Type badge inside discount table ── */
    .type-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase;
    }
    .type-auto { background: rgba(22,163,74,.10); color: var(--sa-success); }
    .type-code { background: rgba(0,87,184,.10);  color: var(--sa-accent); }

    /* ── Plan scope pills ── */
    .plan-pill {
        display: inline-flex; align-items: center;
        padding: 1px 8px; border-radius: 20px; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .4px; margin: 1px 2px;
    }
    .pill-basic    { background: rgba(90,122,170,.12); color: var(--sa-muted); }
    .pill-standard { background: rgba(0,87,184,.12);   color: var(--sa-accent); }
    .pill-premium  { background: rgba(245,197,24,.15); color: #a07800; }
    .pill-all      { background: rgba(22,163,74,.10);  color: var(--sa-success); }

    /* ── Tenant pills in table ── */
    .tenant-pill {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 8px; border-radius: 20px; font-size: 10px; font-weight: 600;
        background: rgba(0,87,184,.08); color: var(--sa-accent); margin: 1px 2px;
        max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .tenant-pill-all {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 8px; border-radius: 20px; font-size: 10px; font-weight: 600;
        background: rgba(22,163,74,.10); color: var(--sa-success); margin: 1px 2px;
    }

    /* ── Stats ── */
    .stat-pill { display: flex; flex-direction: column; align-items: center; padding: 14px 20px; border-radius: 14px; background: var(--sa-surface); border: 1.5px solid var(--sa-border); gap: 4px; }
    .stat-pill-val { font-size: 22px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .stat-pill-lbl { font-size: 10px; font-weight: 600; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }

    /* Scrollbar for tenant list */
    #tenant-list::-webkit-scrollbar, #ed-tenant-list::-webkit-scrollbar,
    #pm-tenant-list::-webkit-scrollbar, #pm-ed-tenant-list::-webkit-scrollbar { width: 4px; }
    #tenant-list::-webkit-scrollbar-thumb, #ed-tenant-list::-webkit-scrollbar-thumb,
    #pm-tenant-list::-webkit-scrollbar-thumb, #pm-ed-tenant-list::-webkit-scrollbar-thumb {
        background: var(--sa-border); border-radius: 4px;
    }
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-layer-group mr-2" style="color:var(--sa-accent);"></i> Plan & Discount Management
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Edit subscription plan features, limits, and availability — or manage discounts and promo codes
            </p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
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

    {{-- ── Tab Bar ── --}}
    <div class="tab-bar">
        <button class="tab-btn active" id="tab-plans-btn" onclick="switchTab('plans')">
            <i class="fas fa-layer-group"></i> Subscription Plans
        </button>
        <button class="tab-btn" id="tab-discounts-btn" onclick="switchTab('discounts')">
            <i class="fas fa-percent"></i> Discounts & Promos
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Plans                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-plans">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <p class="text-sm font-semibold" style="color:var(--sa-text);">
                    These are the plans tenants see on their upgrade page.
                    Edit features, pricing, limits, and availability.
                </p>
            </div>
            <a href="{{ route('superadmin.plans.manage.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Plan
            </a>
        </div>

        {{-- Stats --}}
        <div class="flex flex-wrap gap-3 mb-6">
            <div class="stat-pill">
                <span class="stat-pill-val">{{ $plans->where('is_active', true)->count() }}</span>
                <span class="stat-pill-lbl">Active Plans</span>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-val" style="color:var(--sa-muted);">{{ $plans->where('is_active', false)->count() }}</span>
                <span class="stat-pill-lbl">Inactive Plans</span>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-val">{{ $plans->count() }}</span>
                <span class="stat-pill-lbl">Total Plans</span>
            </div>
        </div>

        @if($plans->count() > 0)
            <div class="plan-cards-grid">
                @foreach($plans as $plan)
                    @php
                        $icons = ['basic' => '🌱', 'standard' => '🚀', 'premium' => '💎'];
                        $icon  = $icons[$plan->slug] ?? '📦';
                        $today = today();
                        $isAvail = (!$plan->available_from || $plan->available_from <= $today)
                                && (!$plan->available_until || $plan->available_until >= $today);
                        $formats = $plan->allowed_export_formats ?? [];
                    @endphp
                    <div class="plan-card {{ !$plan->is_active ? 'inactive' : '' }}">

                        {{-- Active / inactive badge --}}
                        <div style="position:absolute;top:16px;right:16px;">
                            @if($plan->is_active && $isAvail)
                                <span class="status-badge sb-success">● Active</span>
                            @elseif($plan->is_active && !$isAvail)
                                <span class="status-badge sb-warning">● Scheduled</span>
                            @else
                                <span class="status-badge sb-muted">● Inactive</span>
                            @endif
                        </div>

                        <div class="plan-card-header">
                            <div class="plan-icon">{{ $icon }}</div>
                            <span class="plan-slug-badge slug-{{ $plan->slug }}">{{ $plan->slug }}</span>
                        </div>

                        <div class="plan-name">{{ $plan->name }}</div>
                        @if($plan->description)
                            <div class="plan-desc">{{ Str::limit($plan->description, 80) }}</div>
                        @endif

                        <div class="plan-price-row">
                            <span class="plan-price">₱{{ number_format($plan->price, 0) }}</span>
                            <span class="plan-duration">/ {{ $plan->duration_label }}</span>
                        </div>

                        {{-- Limits --}}
                        <div class="plan-limits">
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Trainees</span>
                                <span class="plan-limit-val">{{ $plan->max_trainees ? number_format($plan->max_trainees) : '∞ Unlimited' }}</span>
                            </div>
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Courses</span>
                                <span class="plan-limit-val">{{ $plan->max_courses ? number_format($plan->max_courses) : '∞ Unlimited' }}</span>
                            </div>
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Users</span>
                                <span class="plan-limit-val">{{ $plan->max_users ? number_format($plan->max_users) : '∞ Unlimited' }}</span>
                            </div>
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Trainers</span>
                                <span class="plan-limit-val">{{ $plan->max_trainers !== null ? ($plan->max_trainers == 0 ? 'None' : number_format($plan->max_trainers)) : '∞ Unlimited' }}</span>
                            </div>
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Exports/mo</span>
                                <span class="plan-limit-val">
                                    @if(count($formats) === 0) None
                                    @elseif($plan->max_exports_monthly === null) ∞ Unlimited
                                    @else {{ number_format($plan->max_exports_monthly) }}
                                    @endif
                                </span>
                            </div>
                            @if(count($formats) > 0)
                            <div class="plan-limit-row">
                                <span class="plan-limit-key">Formats</span>
                                <span class="plan-limit-val" style="font-size:11px;">{{ strtoupper(implode(', ', $formats)) }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Feature flags --}}
                        <div class="plan-flags">
                            <span class="flag-pill {{ $plan->has_trainers ? 'flag-on' : 'flag-off' }}">👨‍🏫 Trainers</span>
                            <span class="flag-pill {{ $plan->has_assessments ? 'flag-on' : 'flag-off' }}">📝 Assessments</span>
                            <span class="flag-pill {{ $plan->has_certificates ? 'flag-on' : 'flag-off' }}">🏅 Certificates</span>
                            <span class="flag-pill {{ $plan->has_custom_reports ? 'flag-on' : 'flag-off' }}">📊 Custom Reports</span>
                            <span class="flag-pill {{ $plan->has_branding ? 'flag-on' : 'flag-off' }}">🎨 Branding</span>
                        </div>

                        {{-- Availability --}}
                        @if($plan->available_from || $plan->available_until)
                        <div class="plan-avail">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $plan->available_from?->format('M d, Y') ?? 'Anytime' }}
                            → {{ $plan->available_until?->format('M d, Y') ?? 'No end date' }}
                        </div>
                        @endif

                        {{-- Sort order --}}
                        <div style="font-size:11px;color:var(--sa-muted);margin-bottom:14px;">
                            Sort order: <strong>{{ $plan->sort_order }}</strong>
                        </div>

                        <div class="plan-card-actions">
                            <a href="{{ route('superadmin.plans.manage.edit', $plan) }}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
                                <i class="fas fa-pencil-alt"></i> Edit Plan
                            </a>
                            <form action="{{ route('superadmin.plans.manage.destroy', $plan) }}" method="POST"
                                  onsubmit="return confirm('Delete the {{ addslashes($plan->name) }} plan? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border-2 p-14 text-center" style="background:var(--sa-bg);border-color:var(--sa-border);">
                <i class="fas fa-layer-group text-5xl mb-4" style="color:var(--sa-muted);opacity:.3;"></i>
                <p style="color:var(--sa-muted);" class="mb-3">No plans yet. Create your first plan.</p>
                <a href="{{ route('superadmin.plans.manage.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Plan
                </a>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Discounts                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-discounts" style="display:none;">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="flex gap-3">
                <div class="stat-pill">
                    <span class="stat-pill-val" style="color:var(--sa-success);">{{ $discounts->where('is_active', true)->count() }}</span>
                    <span class="stat-pill-lbl">Active</span>
                </div>
                <div class="stat-pill">
                    <span class="stat-pill-val">{{ $discounts->where('is_automatic', true)->count() }}</span>
                    <span class="stat-pill-lbl">Auto</span>
                </div>
                <div class="stat-pill">
                    <span class="stat-pill-val">{{ $discounts->where('is_automatic', false)->count() }}</span>
                    <span class="stat-pill-lbl">Codes</span>
                </div>
            </div>
            <button onclick="openModal('modal-new-discount')" class="btn btn-gold">
                <i class="fas fa-percent"></i> New Discount
            </button>
        </div>

        <div class="rounded-2xl border-2 overflow-hidden" style="background:var(--sa-bg);border-color:var(--sa-border);">
            @if($discounts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="disc-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Code / Label</th>
                                <th>Discount</th>
                                <th>Applies To</th>
                                <th>Tenants</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $d)
                                @php
                                    $statusClass   = match($d->status_label) {
                                        'Active'    => 'sb-success',
                                        'Scheduled' => 'sb-warning',
                                        default     => 'sb-danger',
                                    };
                                    $planSlugsJson = json_encode($d->plan_slugs ?? []);
                                    $tenantIdsJson = json_encode($d->tenant_ids ?? []);
                                    $tenantNames   = [];
                                    if (!empty($d->tenant_ids)) {
                                        $tenantNames = $tenants->whereIn('id', $d->tenant_ids)->pluck('name', 'id')->toArray();
                                    }
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
                                    <td>
                                        @if(empty($d->plan_slugs))
                                            <span class="plan-pill pill-all">All plans</span>
                                        @else
                                            @foreach($d->plan_slugs as $slug)
                                                <span class="plan-pill pill-{{ $slug }}">{{ ucfirst($slug) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->is_automatic)
                                            <span style="font-size:11px;color:var(--sa-muted);">—</span>
                                        @elseif(empty($d->tenant_ids))
                                            <span class="tenant-pill-all"><i class="fas fa-users" style="font-size:9px;"></i> Any</span>
                                        @else
                                            @foreach(array_slice($tenantNames, 0, 2) as $tName)
                                                <span class="tenant-pill" title="{{ $tName }}">{{ $tName }}</span>
                                            @endforeach
                                            @if(count($tenantNames) > 2)
                                                <span class="tenant-pill" style="background:rgba(90,122,170,.10);color:var(--sa-muted);">
                                                    +{{ count($tenantNames) - 2 }} more
                                                </span>
                                            @endif
                                        @endif
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
                                        <div id="disc-data-{{ $d->id }}" class="hidden"
                                             data-id="{{ $d->id }}"
                                             data-is-automatic="{{ $d->is_automatic ? '1' : '0' }}"
                                             data-code="{{ $d->code }}"
                                             data-label="{{ $d->label }}"
                                             data-type="{{ $d->type }}"
                                             data-value="{{ $d->value }}"
                                             data-plan-slugs="{{ $planSlugsJson }}"
                                             data-tenant-ids="{{ $tenantIdsJson }}"
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

</div>{{-- end space-y-6 --}}

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: New Discount                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-new-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> New Discount</span>
            <button onclick="closeModal('modal-new-discount')" style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.plans.discounts.store') }}" method="POST">
            @csrf
            <div class="modal-body space-y-4">
                @include('superadmin.plans._discount_fields', ['tenants' => $tenants])
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
            <span class="modal-title"><i class="fas fa-pencil-alt mr-2" style="color:var(--sa-accent);"></i> Edit Discount</span>
            <button onclick="closeModal('modal-edit-discount')" style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;"><i class="fas fa-times"></i></button>
        </div>
        <form id="edit-discount-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body space-y-4">
                @include('superadmin.plans._discount_fields', ['isEdit' => true, 'tenants' => $tenants])
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.getElementById('tab-plans').style.display    = tab === 'plans'    ? '' : 'none';
    document.getElementById('tab-discounts').style.display = tab === 'discounts' ? '' : 'none';
    document.getElementById('tab-plans-btn').classList.toggle('active',    tab === 'plans');
    document.getElementById('tab-discounts-btn').classList.toggle('active', tab === 'discounts');

    // Persist in session storage so refresh keeps the tab
    sessionStorage.setItem('planTab', tab);
}

// Restore last active tab on page load
document.addEventListener('DOMContentLoaded', function () {
    const saved = sessionStorage.getItem('planTab');
    if (saved) switchTab(saved);
});

// ── Modal helpers ─────────────────────────────────────────────────────────────
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

// ── Populate edit discount modal ──────────────────────────────────────────────
function openEditDiscount(id) {
    const d = document.getElementById('disc-data-' + id).dataset;
    document.getElementById('edit-discount-form').action = d.updateUrl;

    const isAuto = d.isAutomatic === '1';
    document.getElementById('ed-radio-automatic').checked = isAuto;
    document.getElementById('ed-radio-code').checked      = !isAuto;
    document.getElementById('ed-radio-automatic').dispatchEvent(new Event('change'));

    document.getElementById('ed-code').value        = d.code;
    document.getElementById('ed-label').value       = d.label;
    document.getElementById('ed-type').value        = d.type;
    document.getElementById('ed-value').value       = d.value;
    document.getElementById('ed-valid-from').value  = d.validFrom;
    document.getElementById('ed-valid-until').value = d.validUntil;
    document.getElementById('ed-active').checked    = d.active === '1';

    let planSlugs = [];
    try { planSlugs = JSON.parse(d.planSlugs || '[]'); } catch(e) {}
    ['basic', 'standard', 'premium'].forEach(slug => {
        const cb = document.getElementById('ed-plan-' + slug);
        if (cb) {
            cb.checked = planSlugs.includes(slug);
            syncPlanRow('ed-', slug,
                { basic: '#5a7aaa', standard: '#0057B8', premium: '#a07800' }[slug],
                { basic: 'rgba(90,122,170', standard: 'rgba(0,87,184', premium: 'rgba(161,122,0' }[slug]
            );
        }
    });

    let tenantIds = [];
    try { tenantIds = JSON.parse(d.tenantIds || '[]'); } catch(e) {}
    const searchEl = document.getElementById('ed-tenant-search');
    if (searchEl) { searchEl.value = ''; filterTenants('ed-'); }
    document.querySelectorAll('#ed-tenant-list input[type="checkbox"]').forEach(cb => {
        cb.checked = tenantIds.includes(cb.value);
        syncTenantRow('ed-', cb.value);
    });

    openModal('modal-edit-discount');
}

// Switch to discounts tab if there are errors from a discount form
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        // Check if errors came from discount forms by looking at session
        @if(session('_old_input.label') || session('_old_input.code'))
            switchTab('discounts');
        @endif
    });
@endif
</script>

@endsection