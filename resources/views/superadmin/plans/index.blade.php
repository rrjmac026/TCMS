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
    }
    .dark {
        --sa-bg:      #0a1628;
        --sa-surface: #0d1f3c;
        --sa-border:  #1e3a6b;
        --sa-text:    #dde8ff;
        --sa-muted:   #6b8abf;
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

    .plan-header {
        padding: 22px 24px 18px;
        border-bottom: 2px solid var(--sa-border);
    }
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

    .plan-actions { padding: 16px 24px; border-top: 2px solid var(--sa-border); }

    /* ── Edit form inside plan card ── */
    .plan-edit-form { display: none; padding: 0 24px 20px; }
    .plan-edit-form.open { display: block; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
    .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

    .fi { display: flex; flex-direction: column; gap: 5px; }
    .fi label { font-size: 11px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .4px; }
    .fi input, .fi select, .fi textarea {
        padding: 8px 10px; border-radius: 8px; border: 1.5px solid var(--sa-border);
        background: var(--sa-bg); color: var(--sa-text); font-family: inherit;
        font-size: 13px; outline: none; transition: border-color .15s;
    }
    .fi input:focus, .fi select:focus, .fi textarea:focus { border-color: var(--sa-accent); }
    .fi textarea { resize: vertical; min-height: 64px; }

    .check-group { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
    .check-item  { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--sa-text); cursor: pointer; }
    .check-item input { accent-color: var(--sa-accent); width: 15px; height: 15px; cursor: pointer; }

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
    .btn-primary  { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-success  { background: var(--sa-success); color: #fff; }
    .btn-danger   { background: rgba(206,17,38,.10); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.25); }
    .btn-outline  { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-gold     { background: linear-gradient(135deg,var(--sa-gold) 0%,#d4a800 100%); color: #001a4d; }
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
        box-shadow: 0 20px 60px rgba(0,0,0,.25); transform: translateY(20px);
        transition: transform .2s;
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }
    .modal-header {
        padding: 22px 26px 18px; border-bottom: 2px solid var(--sa-border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title { font-size: 17px; font-weight: 800; color: var(--sa-primary); }
    .modal-body  { padding: 24px 26px; }
    .modal-footer { padding: 16px 26px; border-top: 2px solid var(--sa-border); display: flex; gap: 10px; justify-content: flex-end; }

    /* ── Apply discount panel ── */
    .apply-panel {
        border-radius: 18px; border: 2px solid var(--sa-border);
        background: var(--sa-bg); padding: 24px 28px;
    }

    /* ── Validator pill ── */
    .validate-result {
        border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600;
        display: none; margin-top: 10px;
    }
    .validate-result.valid   { background: rgba(22,163,74,.08); border: 1.5px solid rgba(22,163,74,.3); color: var(--sa-success); display: block; }
    .validate-result.invalid { background: rgba(206,17,38,.08); border: 1.5px solid rgba(206,17,38,.3); color: var(--sa-danger);  display: block; }

    /* ── Stat pills ── */
    .stat-pill { display: flex; flex-direction: column; align-items: center; padding: 14px 20px; border-radius: 14px; background: var(--sa-surface); border: 1.5px solid var(--sa-border); gap: 4px; }
    .stat-pill-val { font-size: 22px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .stat-pill-lbl { font-size: 10px; font-weight: 600; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }
</style>

<div class="space-y-6">

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-tags mr-2" style="color:var(--sa-accent);"></i> Plan Management
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Configure subscription plans, manage discount codes, and apply pricing to tenants
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

    {{-- ── Flash messages ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="rounded-xl border-2 p-4" style="background:rgba(22,163,74,.05);border-color:var(--sa-success);">
            <div style="color:var(--sa-success);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border-2 p-4" style="background:rgba(206,17,38,.05);border-color:var(--sa-danger);">
            <div style="color:var(--sa-danger);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
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

    {{-- ── Stats Row ───────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3">
        <div class="stat-pill">
            <span class="stat-pill-val">{{ $plans->count() }}</span>
            <span class="stat-pill-lbl">Plans</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val" style="color:var(--sa-success);">{{ $discounts->where('is_active',true)->count() }}</span>
            <span class="stat-pill-lbl">Active Discounts</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val">{{ $totalUsages }}</span>
            <span class="stat-pill-lbl">Times Used</span>
        </div>
        <div class="stat-pill">
            <span class="stat-pill-val" style="color:var(--sa-success);">₱{{ number_format($totalSaved, 2) }}</span>
            <span class="stat-pill-lbl">Total Saved</span>
        </div>
    </div>

    {{-- ── Tabs ────────────────────────────────────────────────────────────── --}}
    <div class="tab-nav">
        <button class="tab-btn active" onclick="switchTab('plans', this)">
            <i class="fas fa-layer-group"></i> Subscription Plans
        </button>
        <button class="tab-btn" onclick="switchTab('discounts', this)">
            <i class="fas fa-percent"></i> Discount Codes
            @if($discounts->count())
                <span class="px-2 py-0.5 rounded-full text-xs" style="background:var(--sa-surface);color:var(--sa-muted);">{{ $discounts->count() }}</span>
            @endif
        </button>
        <button class="tab-btn" onclick="switchTab('apply', this)">
            <i class="fas fa-magic"></i> Apply to Tenant
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1 — PLANS                                                        --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
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
                @endphp
                <div class="plan-card">
                    {{-- Header --}}
                    <div class="plan-header" style="background:{{ $headerBg }};">
                        <div class="flex items-center justify-between mb-2">
                            <span class="plan-slug-badge {{ $badgeClass }}">
                                <i class="fas fa-circle" style="font-size:6px;"></i>
                                {{ strtoupper($plan->slug) }}
                            </span>
                            <span class="status-badge {{ $plan->is_active ? 'sb-success' : 'sb-muted' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="font-bold text-lg mb-1" style="color:var(--sa-primary);">{{ $plan->name }}</div>
                        <div class="plan-price">₱{{ number_format($plan->price, 2) }}<span>/ plan</span></div>
                        <div class="plan-duration"><i class="fas fa-clock mr-1"></i> {{ $plan->duration_label }} access</div>
                        @if($plan->description)
                            <p class="text-xs mt-2" style="color:var(--sa-muted);">{{ $plan->description }}</p>
                        @endif
                    </div>

                    {{-- Features summary --}}
                    <div class="plan-body">
                        <div class="feature-row">
                            <span class="feature-label">Trainees</span>
                            <span class="feature-val">{{ $plan->max_trainees ?? '∞' }}</span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Trainers</span>
                            <span class="feature-val">{{ $plan->max_trainers === 0 ? '—' : ($plan->max_trainers ?? '∞') }}</span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Users</span>
                            <span class="feature-val">{{ $plan->max_users ?? '∞' }}</span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Courses</span>
                            <span class="feature-val">{{ $plan->max_courses ?? '∞' }}</span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Exports / mo.</span>
                            <span class="feature-val">
                                @if($plan->max_exports_monthly === 0 || $plan->max_exports_monthly === null && count($plan->allowed_export_formats ?? []) === 0)
                                    None
                                @elseif($plan->max_exports_monthly === null)
                                    Unlimited
                                @else
                                    {{ number_format($plan->max_exports_monthly) }}
                                @endif
                            </span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Export formats</span>
                            <span class="feature-val text-xs">
                                @if(count($plan->allowed_export_formats ?? []) === 0)
                                    —
                                @else
                                    {{ strtoupper(implode(', ', $plan->allowed_export_formats)) }}
                                @endif
                            </span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Assessments</span>
                            <span class="feature-val {{ $plan->has_assessments ? 'feature-yes' : 'feature-no' }}">
                                <i class="fas {{ $plan->has_assessments ? 'fa-check' : 'fa-times' }}"></i>
                            </span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Certificates</span>
                            <span class="feature-val {{ $plan->has_certificates ? 'feature-yes' : 'feature-no' }}">
                                <i class="fas {{ $plan->has_certificates ? 'fa-check' : 'fa-times' }}"></i>
                            </span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Custom Reports</span>
                            <span class="feature-val {{ $plan->has_custom_reports ? 'feature-yes' : 'feature-no' }}">
                                <i class="fas {{ $plan->has_custom_reports ? 'fa-check' : 'fa-times' }}"></i>
                            </span>
                        </div>
                        <div class="feature-row">
                            <span class="feature-label">Branding</span>
                            <span class="feature-val {{ $plan->has_branding ? 'feature-yes' : 'feature-no' }}">
                                <i class="fas {{ $plan->has_branding ? 'fa-check' : 'fa-times' }}"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="plan-actions">
                        <button class="btn btn-outline w-full" style="justify-content:center;"
                                onclick="toggleEdit({{ $plan->id }})">
                            <i class="fas fa-pencil-alt"></i> Edit Plan
                        </button>
                    </div>

                    {{-- Inline edit form --}}
                    <div class="plan-edit-form" id="edit-form-{{ $plan->id }}">
                        <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="space-y-3">

                                <div class="form-row">
                                    <div class="fi">
                                        <label>Plan Name</label>
                                        <input type="text" name="name" value="{{ $plan->name }}" required>
                                    </div>
                                    <div class="fi">
                                        <label>Price (₱)</label>
                                        <input type="number" name="price" value="{{ $plan->price }}" min="0" step="0.01" required>
                                    </div>
                                </div>

                                <div class="fi">
                                    <label>Description</label>
                                    <textarea name="description">{{ $plan->description }}</textarea>
                                </div>

                                <div class="form-row">
                                    <div class="fi">
                                        <label>Duration (days)</label>
                                        <input type="number" name="duration_days" value="{{ $plan->duration_days }}" min="1" required>
                                    </div>
                                    <div class="fi">
                                        <label>Max Trainees (blank=∞)</label>
                                        <input type="number" name="max_trainees" value="{{ $plan->max_trainees }}" min="0" placeholder="Unlimited">
                                    </div>
                                </div>

                                <div class="form-row form-row-3">
                                    <div class="fi">
                                        <label>Max Trainers</label>
                                        <input type="number" name="max_trainers" value="{{ $plan->max_trainers }}" min="0" placeholder="Unlimited">
                                    </div>
                                    <div class="fi">
                                        <label>Max Users</label>
                                        <input type="number" name="max_users" value="{{ $plan->max_users }}" min="1" placeholder="Unlimited">
                                    </div>
                                    <div class="fi">
                                        <label>Max Courses</label>
                                        <input type="number" name="max_courses" value="{{ $plan->max_courses }}" min="0" placeholder="Unlimited">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="fi">
                                        <label>Max Exports / mo.</label>
                                        <input type="number" name="max_exports_monthly" value="{{ $plan->max_exports_monthly }}" min="0" placeholder="Unlimited">
                                    </div>
                                    <div class="fi">
                                        <label>Export Formats</label>
                                        <div class="check-group">
                                            @foreach(['csv','excel','pdf'] as $fmt)
                                                <label class="check-item">
                                                    <input type="checkbox" name="allowed_export_formats[]" value="{{ $fmt }}"
                                                           {{ in_array($fmt, $plan->allowed_export_formats ?? []) ? 'checked' : '' }}>
                                                    {{ strtoupper($fmt) }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="fi">
                                    <label>Feature Flags</label>
                                    <div class="check-group">
                                        @foreach([
                                            'has_assessments'    => 'Assessments',
                                            'has_certificates'   => 'Certificates',
                                            'has_custom_reports' => 'Custom Reports',
                                            'has_branding'       => 'Branding',
                                            'has_trainers'       => 'Trainers',
                                            'is_active'          => 'Active',
                                        ] as $field => $label)
                                            <label class="check-item">
                                                <input type="checkbox" name="{{ $field }}" value="1"
                                                       {{ $plan->$field ? 'checked' : '' }}>
                                                {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-outline" onclick="toggleEdit({{ $plan->id }})">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2 — DISCOUNTS                                                    --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-discounts" class="tab-content">
        <div class="rounded-2xl border-2 overflow-hidden" style="background:var(--sa-bg);border-color:var(--sa-border);">
            @if($discounts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="disc-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Discount</th>
                                <th>Applies To</th>
                                <th>Valid Period</th>
                                <th>Uses</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($discounts as $d)
                                <tr>
                                    {{-- Code --}}
                                    <td>
                                        <code class="px-2 py-1 rounded text-xs font-bold"
                                              style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                            {{ $d->code }}
                                        </code>
                                    </td>

                                    {{-- Name --}}
                                    <td>
                                        <div class="font-semibold">{{ $d->name }}</div>
                                        @if($d->tenant)
                                            <div class="text-xs" style="color:var(--sa-muted);">
                                                <i class="fas fa-building mr-1"></i>{{ $d->tenant->name }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Discount value --}}
                                    <td>
                                        <span class="font-bold text-base" style="color:var(--sa-success);">
                                            {{ $d->formatted_value }}
                                            @if($d->type === 'percentage') <span class="text-xs font-normal" style="color:var(--sa-muted);">off</span> @endif
                                        </span>
                                        <div class="text-xs" style="color:var(--sa-muted);">{{ ucfirst($d->type) }}</div>
                                    </td>

                                    {{-- Plans / actions --}}
                                    <td class="text-xs" style="color:var(--sa-muted);">
                                        @if($d->applicable_plans)
                                            <div>{{ implode(', ', array_map('ucfirst', $d->applicable_plans)) }}</div>
                                        @else
                                            <span style="color:var(--sa-muted);">All plans</span>
                                        @endif
                                        @if($d->applicable_actions)
                                            <div class="text-xs opacity-70">{{ implode(', ', $d->applicable_actions) }}</div>
                                        @endif
                                    </td>

                                    {{-- Valid period --}}
                                    <td class="text-xs" style="color:var(--sa-muted);">
                                        @if($d->valid_from || $d->valid_until)
                                            {{ $d->valid_from?->format('M d, Y') ?? '—' }}
                                            →
                                            {{ $d->valid_until?->format('M d, Y') ?? '—' }}
                                        @else
                                            <span style="color:var(--sa-muted);">No limit</span>
                                        @endif
                                    </td>

                                    {{-- Uses --}}
                                    <td>
                                        <span class="font-semibold">{{ $d->uses_count }}</span>
                                        @if($d->max_uses)
                                            <span class="text-xs" style="color:var(--sa-muted);"> / {{ $d->max_uses }}</span>
                                        @else
                                            <span class="text-xs" style="color:var(--sa-muted);"> / ∞</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @php
                                            $statusClass = match($d->status_label) {
                                                'Active'    => 'sb-success',
                                                'Scheduled' => 'sb-warning',
                                                default     => 'sb-danger',
                                            };
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">{{ $d->status_label }}</span>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openEditDiscount({{ $d->id }})"
                                                    class="btn btn-outline btn-sm">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <form action="{{ route('superadmin.plans.discounts.destroy', $d) }}" method="POST"
                                                  onsubmit="return confirm('Delete discount {{ $d->code }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        {{-- Hidden edit form data for JS --}}
                                        <div id="disc-data-{{ $d->id }}" class="hidden"
                                             data-id="{{ $d->id }}"
                                             data-name="{{ $d->name }}"
                                             data-code="{{ $d->code }}"
                                             data-type="{{ $d->type }}"
                                             data-value="{{ $d->value }}"
                                             data-plans="{{ json_encode($d->applicable_plans ?? []) }}"
                                             data-actions="{{ json_encode($d->applicable_actions ?? []) }}"
                                             data-tenant="{{ $d->tenant_id ?? '' }}"
                                             data-valid-from="{{ $d->valid_from?->format('Y-m-d') ?? '' }}"
                                             data-valid-until="{{ $d->valid_until?->format('Y-m-d') ?? '' }}"
                                             data-max-uses="{{ $d->max_uses ?? '' }}"
                                             data-min-price="{{ $d->minimum_price ?? '' }}"
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
                    <p style="color:var(--sa-muted);" class="mb-3">No discount codes yet.</p>
                    <button onclick="openModal('modal-new-discount')" class="btn btn-gold">
                        <i class="fas fa-plus"></i> Create First Discount
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3 — APPLY DISCOUNT TO TENANT                                     --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div id="tab-apply" class="tab-content">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Apply form --}}
            <div class="apply-panel">
                <h2 class="font-bold text-lg mb-5" style="color:var(--sa-primary);">
                    <i class="fas fa-magic mr-2" style="color:var(--sa-accent);"></i>
                    Apply Discount to Tenant
                </h2>
                <form action="{{ route('superadmin.plans.discounts.apply') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="fi">
                        <label>Select Tenant</label>
                        <select name="tenant_id" required>
                            <option value="">— Choose tenant —</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ ucfirst($t->subscription) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fi">
                        <label>Target Plan</label>
                        <select name="plan_slug" id="apply-plan-select" required onchange="liveValidate()">
                            <option value="">— Choose plan —</option>
                            @foreach($plans as $p)
                                <option value="{{ $p->slug }}">{{ $p->name }} (₱{{ number_format($p->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fi">
                        <label>Action</label>
                        <select name="action" required>
                            <option value="approve">Approve (new tenant)</option>
                            <option value="upgrade_superadmin">Upgrade (by SuperAdmin)</option>
                            <option value="renewal">Renewal / Extend</option>
                        </select>
                    </div>

                    <div class="fi">
                        <label>Discount Code</label>
                        <div class="flex gap-2">
                            <input type="text" name="discount_code" id="apply-code-input"
                                   placeholder="e.g. TESDA2025" style="text-transform:uppercase;flex:1;"
                                   oninput="this.value=this.value.toUpperCase();liveValidate()">
                            <button type="button" onclick="liveValidate()" class="btn btn-outline" style="white-space:nowrap;">
                                <i class="fas fa-check-circle"></i> Check
                            </button>
                        </div>
                        <div id="validate-result" class="validate-result"></div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-magic"></i> Apply Discount & Set Plan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick info panel --}}
            <div class="space-y-4">
                <div class="rounded-xl border-2 p-5" style="background:rgba(0,87,184,.04);border-color:rgba(0,87,184,.2);">
                    <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                        <i class="fas fa-info-circle mr-2"></i> How Discounts Work
                    </div>
                    <div class="space-y-2 text-sm" style="color:var(--sa-muted);">
                        <p>• <strong style="color:var(--sa-text);">Percentage</strong> — deducts a % from the plan's base price</p>
                        <p>• <strong style="color:var(--sa-text);">Fixed (₱)</strong> — deducts a flat amount from the base price</p>
                        <p>• Discounts can be restricted by plan, action, tenant, and date range</p>
                        <p>• Every usage is recorded in the discount history</p>
                        <p>• Applying a discount here also sets the tenant's plan and expiry</p>
                    </div>
                </div>

                {{-- Active discounts quick list --}}
                @php $activeDiscounts = $discounts->where('is_active', true)->take(6); @endphp
                @if($activeDiscounts->count())
                    <div class="rounded-xl border-2 p-5" style="background:var(--sa-bg);border-color:var(--sa-border);">
                        <div class="font-bold text-sm mb-3" style="color:var(--sa-primary);">
                            <i class="fas fa-bolt mr-2" style="color:var(--sa-gold);"></i> Active Codes
                        </div>
                        <div class="space-y-2">
                            @foreach($activeDiscounts as $d)
                                <div class="flex items-center justify-between text-sm">
                                    <code class="px-2 py-0.5 rounded text-xs font-bold"
                                          style="background:rgba(0,48,135,.08);color:var(--sa-accent);">
                                        {{ $d->code }}
                                    </code>
                                    <span class="font-semibold" style="color:var(--sa-success);">{{ $d->formatted_value }}</span>
                                    <span class="text-xs" style="color:var(--sa-muted);">{{ $d->uses_count }} use{{ $d->uses_count !== 1 ? 's' : '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL — New Discount                                                      --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-new-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> New Discount Code</span>
            <button onclick="closeModal('modal-new-discount')" style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('superadmin.plans.discounts.store') }}" method="POST">
            @csrf
            <div class="modal-body space-y-4">

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Name *</label>
                        <input type="text" name="name" placeholder="e.g. TESDA Anniversary Promo" required>
                    </div>
                    <div class="fi">
                        <label>Code (uppercase) *</label>
                        <input type="text" name="code" placeholder="TESDA2025" required
                               style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Type *</label>
                        <select name="type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₱)</option>
                        </select>
                    </div>
                    <div class="fi">
                        <label>Discount Value *</label>
                        <input type="number" name="value" placeholder="e.g. 20 or 500" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Plans (leave blank = all plans)</label>
                    <div class="check-group">
                        @foreach($plans as $p)
                            <label class="check-item">
                                <input type="checkbox" name="applicable_plans[]" value="{{ $p->slug }}">
                                {{ $p->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Actions (leave blank = all actions)</label>
                    <div class="check-group">
                        @foreach(['approve' => 'Approve (new)', 'upgrade_superadmin' => 'SA Upgrade', 'upgrade_admin' => 'Admin Upgrade', 'renewal' => 'Renewal'] as $val => $lbl)
                            <label class="check-item">
                                <input type="checkbox" name="applicable_actions[]" value="{{ $val }}">
                                {{ $lbl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Restrict to Specific Tenant (optional)</label>
                    <select name="tenant_id">
                        <option value="">— Any tenant —</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Valid From</label>
                        <input type="date" name="valid_from">
                    </div>
                    <div class="fi">
                        <label>Valid Until</label>
                        <input type="date" name="valid_until">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Max Total Uses (blank = unlimited)</label>
                        <input type="number" name="max_uses" placeholder="e.g. 50" min="1">
                    </div>
                    <div class="fi">
                        <label>Minimum Plan Price (₱) to qualify</label>
                        <input type="number" name="minimum_price" placeholder="Optional" min="0" step="0.01">
                    </div>
                </div>

                <div class="fi">
                    <label class="check-item">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Active immediately
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-new-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Create Discount</button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL — Edit Discount (populated via JS)                                  --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit-discount">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-pencil-alt mr-2" style="color:var(--sa-accent);"></i> Edit Discount Code</span>
            <button onclick="closeModal('modal-edit-discount')" style="background:none;border:none;cursor:pointer;color:var(--sa-muted);font-size:18px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="edit-discount-form" method="POST">
            @csrf @method('PATCH')
            <div class="modal-body space-y-4">

                <div class="form-row">
                    <div class="fi">
                        <label>Discount Name *</label>
                        <input type="text" name="name" id="ed-name" required>
                    </div>
                    <div class="fi">
                        <label>Code *</label>
                        <input type="text" name="code" id="ed-code" required
                               style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Type *</label>
                        <select name="type" id="ed-type" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₱)</option>
                        </select>
                    </div>
                    <div class="fi">
                        <label>Value *</label>
                        <input type="number" name="value" id="ed-value" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Plans</label>
                    <div class="check-group" id="ed-plans">
                        @foreach($plans as $p)
                            <label class="check-item">
                                <input type="checkbox" class="ed-plan-cb" name="applicable_plans[]" value="{{ $p->slug }}">
                                {{ $p->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Applicable Actions</label>
                    <div class="check-group">
                        @foreach(['approve' => 'Approve', 'upgrade_superadmin' => 'SA Upgrade', 'upgrade_admin' => 'Admin Upgrade', 'renewal' => 'Renewal'] as $val => $lbl)
                            <label class="check-item">
                                <input type="checkbox" class="ed-action-cb" name="applicable_actions[]" value="{{ $val }}">
                                {{ $lbl }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="fi">
                    <label>Restrict to Specific Tenant</label>
                    <select name="tenant_id" id="ed-tenant">
                        <option value="">— Any tenant —</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Valid From</label>
                        <input type="date" name="valid_from" id="ed-valid-from">
                    </div>
                    <div class="fi">
                        <label>Valid Until</label>
                        <input type="date" name="valid_until" id="ed-valid-until">
                    </div>
                </div>

                <div class="form-row">
                    <div class="fi">
                        <label>Max Uses</label>
                        <input type="number" name="max_uses" id="ed-max-uses" min="1" placeholder="Unlimited">
                    </div>
                    <div class="fi">
                        <label>Min Price (₱)</label>
                        <input type="number" name="minimum_price" id="ed-min-price" min="0" step="0.01">
                    </div>
                </div>

                <div class="fi">
                    <label class="check-item">
                        <input type="checkbox" name="is_active" id="ed-active" value="1">
                        Active
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit-discount')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // ── Tab switching ─────────────────────────────────────────────────────────
    function switchTab(name, btn) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    // ── Plan edit toggle ──────────────────────────────────────────────────────
    function toggleEdit(id) {
        const form = document.getElementById('edit-form-' + id);
        form.classList.toggle('open');
    }

    // ── Modal helpers ─────────────────────────────────────────────────────────
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ── Populate edit discount modal ──────────────────────────────────────────
    function openEditDiscount(id) {
        const d = document.getElementById('disc-data-' + id).dataset;
        document.getElementById('edit-discount-form').action = d.updateUrl;

        document.getElementById('ed-name').value       = d.name;
        document.getElementById('ed-code').value       = d.code;
        document.getElementById('ed-type').value       = d.type;
        document.getElementById('ed-value').value      = d.value;
        document.getElementById('ed-tenant').value     = d.tenant;
        document.getElementById('ed-valid-from').value = d.validFrom;
        document.getElementById('ed-valid-until').value= d.validUntil;
        document.getElementById('ed-max-uses').value   = d.maxUses;
        document.getElementById('ed-min-price').value  = d.minPrice;
        document.getElementById('ed-active').checked   = d.active === '1';

        const plans   = JSON.parse(d.plans   || '[]');
        const actions = JSON.parse(d.actions || '[]');

        document.querySelectorAll('.ed-plan-cb').forEach(cb => {
            cb.checked = plans.includes(cb.value);
        });
        document.querySelectorAll('.ed-action-cb').forEach(cb => {
            cb.checked = actions.includes(cb.value);
        });

        openModal('modal-edit-discount');
    }

    // ── Live discount code validator ──────────────────────────────────────────
    let validateTimeout;
    function liveValidate() {
        clearTimeout(validateTimeout);
        const code = document.getElementById('apply-code-input').value.trim();
        const plan = document.getElementById('apply-plan-select').value;
        const result = document.getElementById('validate-result');

        if (!code || !plan) { result.className = 'validate-result'; return; }

        validateTimeout = setTimeout(() => {
            fetch('{{ route('superadmin.plans.discounts.validate') }}', {
                method: 'POST',
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
                        `<i class="fas fa-check-circle mr-1"></i> ${data.message}<br>` +
                        `<span style="font-size:11px;">` +
                        `Original: ₱${Number(data.original_price).toFixed(2)} → ` +
                        `Discount: ₱${Number(data.discount_amount).toFixed(2)} → ` +
                        `<strong>Final: ₱${Number(data.final_price).toFixed(2)}</strong></span>`;
                } else {
                    result.className = 'validate-result invalid';
                    result.innerHTML = `<i class="fas fa-times-circle mr-1"></i> ${data.message}`;
                }
            })
            .catch(() => {
                result.className = 'validate-result invalid';
                result.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Validation failed.';
            });
        }, 400);
    }
</script>
@endsection