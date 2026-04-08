@extends('layouts.app')
@section('title', isset($plan) ? 'Edit Plan: ' . $plan->name : 'Create Subscription Plan')

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

    /* ── Layout ── */
    .pm-wrap {
        max-width: 860px;
        margin: 0 auto;
    }

    /* ── Section cards ── */
    .pm-card {
        background: var(--sa-bg);
        border: 1.5px solid var(--sa-border);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 20px;
    }
    .pm-card-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--sa-muted);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pm-card-title i {
        font-size: 14px;
        color: var(--sa-accent);
    }

    /* ── Form elements ── */
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
    }
    .fi {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .fi label {
        font-size: 11px;
        font-weight: 700;
        color: var(--sa-muted);
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .fi input[type="text"],
    .fi input[type="number"],
    .fi input[type="date"],
    .fi select,
    .fi textarea {
        padding: 9px 12px;
        border-radius: 10px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-bg);
        color: var(--sa-text);
        font-family: inherit;
        font-size: 13.5px;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        width: 100%;
    }
    .fi input:focus,
    .fi select:focus,
    .fi textarea:focus {
        border-color: var(--sa-accent);
        box-shadow: 0 0 0 3px rgba(0,87,184,.10);
    }
    .fi .hint {
        font-size: 11px;
        color: var(--sa-muted);
        margin-top: 1px;
    }
    .fi-full { grid-column: 1 / -1; }

    /* ── Slug pills ── */
    .slug-group {
        display: flex;
        gap: 10px;
    }
    .slug-pill {
        flex: 1;
        position: relative;
    }
    .slug-pill input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .slug-pill label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 14px 10px;
        border-radius: 12px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-surface);
        cursor: pointer;
        transition: all .15s;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        color: var(--sa-muted);
        user-select: none;
    }
    .slug-pill label .sp-icon { font-size: 20px; }
    .slug-pill label .sp-name { font-size: 12px; font-weight: 700; color: var(--sa-text); text-transform: uppercase; letter-spacing: .5px; }
    .slug-pill input:checked + label {
        border-color: var(--sa-accent);
        background: rgba(0,87,184,.07);
        color: var(--sa-accent);
    }
    .slug-pill input:checked + label .sp-name { color: var(--sa-accent); }

    /* ── Feature toggles ── */
    .feat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .feat-toggle {
        position: relative;
    }
    .feat-toggle input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .feat-toggle label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 10px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-surface);
        cursor: pointer;
        transition: all .15s;
        font-size: 13px;
        font-weight: 600;
        color: var(--sa-text);
        user-select: none;
    }
    .feat-check {
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: transparent;
        transition: all .15s;
        line-height: 1;
    }
    .feat-toggle input:checked + label {
        border-color: var(--sa-accent);
        background: rgba(0,87,184,.07);
    }
    .feat-toggle input:checked + label .feat-check {
        background: var(--sa-accent);
        border-color: var(--sa-accent);
        color: #fff;
    }

    /* ── Export format checkboxes ── */
    .export-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .exp-toggle {
        position: relative;
    }
    .exp-toggle input[type="checkbox"] {
        position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none;
    }
    .exp-toggle label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 16px;
        border-radius: 100px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-surface);
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--sa-muted);
        transition: all .15s;
        user-select: none;
    }
    .exp-toggle input:checked + label {
        border-color: var(--sa-success);
        background: rgba(22,163,74,.08);
        color: var(--sa-success);
    }

    /* ── Limit inputs with "unlimited" toggle ── */
    .limit-row {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .limit-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .limit-input-wrap input[type="number"] {
        flex: 1;
    }
    .limit-input-wrap input:disabled {
        opacity: .4;
        cursor: not-allowed;
    }
    .unlimited-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--sa-muted);
        cursor: pointer;
        white-space: nowrap;
        user-select: none;
    }
    .unlimited-toggle input[type="checkbox"] {
        accent-color: var(--sa-accent);
        width: 14px;
        height: 14px;
        cursor: pointer;
    }

    /* ── Availability date section ── */
    .date-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .always-available-wrap {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--sa-text);
        cursor: pointer;
        user-select: none;
        margin-bottom: 4px;
    }
    .always-available-wrap input[type="checkbox"] {
        accent-color: var(--sa-accent);
        width: 15px; height: 15px;
        cursor: pointer;
    }

    /* ── Active toggle ── */
    .active-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .switch {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .switch-track {
        position: absolute;
        inset: 0;
        background: var(--sa-border);
        border-radius: 100px;
        cursor: pointer;
        transition: background .2s;
    }
    .switch input:checked + .switch-track { background: var(--sa-accent); }
    .switch-track::after {
        content: '';
        position: absolute;
        width: 18px; height: 18px;
        border-radius: 50%;
        background: #fff;
        top: 3px; left: 3px;
        transition: transform .2s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .switch input:checked + .switch-track::after { transform: translateX(20px); }

    /* ── Price preview pill ── */
    .price-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: var(--sa-surface);
        border: 1.5px solid var(--sa-border);
        border-radius: 12px;
        margin-top: 14px;
        font-size: 13px;
        color: var(--sa-muted);
    }
    .price-preview .pp-val {
        font-size: 22px;
        font-weight: 800;
        color: var(--sa-primary);
    }
    .price-preview .pp-dur {
        font-size: 12px;
        color: var(--sa-muted);
    }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all .15s;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-outline  { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-danger   { background: rgba(206,17,38,.08); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.22); }
    .btn-lg { padding: 13px 28px; font-size: 14px; border-radius: 12px; }

    /* ── Divider ── */
    .pm-divider {
        height: 1px;
        background: var(--sa-border);
        margin: 18px 0;
    }

    /* ── Danger zone ── */
    .danger-zone {
        border-color: rgba(206,17,38,.30);
        background: rgba(206,17,38,.03);
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
        .slug-group { flex-direction: column; }
        .date-row { grid-template-columns: 1fr; }
        .pm-card { padding: 20px 18px; }
    }
</style>

<div class="pm-wrap space-y-0">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-{{ isset($plan) ? 'pencil-alt' : 'plus-circle' }} mr-2"
                   style="color:var(--sa-accent);"></i>
                {{ isset($plan) ? 'Edit Plan: ' . $plan->name : 'Create Subscription Plan' }}
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                {{ isset($plan) ? 'Update plan details, limits, and feature flags.' : 'Define a new plan available to tenant organizations.' }}
            </p>
        </div>
        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-xl border-2 p-4 mb-4"
             style="background:rgba(22,163,74,.05);border-color:var(--sa-success);">
            <p style="color:var(--sa-success);" class="font-semibold flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </p>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-2 p-4 mb-4"
             style="background:rgba(206,17,38,.05);border-color:var(--sa-danger);">
            <div style="color:var(--sa-danger);" class="font-semibold flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>@foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach</div>
            </div>
        </div>
    @endif

    <form
        action="{{ isset($plan) ? route('superadmin.plans.manage.update', $plan) : route('superadmin.plans.manage.store') }}"
        method="POST">
        @csrf
        @if(isset($plan)) @method('PATCH') @endif

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 1. Identity                                            --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-id-card"></i> Plan Identity
            </div>

            {{-- Slug pills --}}
            <div class="fi" style="margin-bottom:18px;">
                <label>Plan Tier *</label>
                <div class="slug-group">
                    @foreach([
                        'basic'    => ['🌱', 'Basic',    'Free starter plan'],
                        'standard' => ['🚀', 'Standard', 'Mid-tier plan'],
                        'premium'  => ['💎', 'Premium',  'Full-featured plan'],
                    ] as $slug => [$icon, $label, $desc])
                        <div class="slug-pill">
                            <input type="radio" name="slug" value="{{ $slug }}"
                                   id="slug-{{ $slug }}"
                                   {{ old('slug', $plan->slug ?? '') === $slug ? 'checked' : '' }}
                                   required>
                            <label for="slug-{{ $slug }}">
                                <span class="sp-icon">{{ $icon }}</span>
                                <span class="sp-name">{{ $label }}</span>
                                <span style="font-size:11px;font-weight:400;color:var(--sa-muted);">{{ $desc }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-grid-2">
                <div class="fi">
                    <label>Display Name *</label>
                    <input type="text" name="name"
                           value="{{ old('name', $plan->name ?? '') }}"
                           placeholder="e.g. Standard Plan"
                           required maxlength="100">
                </div>
                <div class="fi">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order"
                           value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                           min="0" max="99">
                    <span class="hint">Lower numbers appear first on plan cards.</span>
                </div>
                <div class="fi fi-full">
                    <label>Description</label>
                    <textarea name="description" rows="2"
                              placeholder="Brief description shown on the upgrade page…">{{ old('description', $plan->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 2. Pricing & Duration                                  --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-tag"></i> Pricing &amp; Duration
            </div>

            <div class="form-grid-2">
                <div class="fi">
                    <label>Price (₱) *</label>
                    <input type="number" name="price" id="inp-price"
                           value="{{ old('price', $plan->price ?? 0) }}"
                           min="0" step="0.01" required
                           oninput="updatePreview()">
                </div>
                <div class="fi">
                    <label>Duration (days) *</label>
                    <input type="number" name="duration_days" id="inp-duration"
                           value="{{ old('duration_days', $plan->duration_days ?? 30) }}"
                           min="1" required
                           oninput="updatePreview()">
                    <span class="hint">30 = 1 month · 180 = 6 months · 365 = 1 year</span>
                </div>
                <div class="fi">
                    <label>Currency</label>
                    <select name="currency">
                        <option value="PHP" {{ old('currency', $plan->currency ?? 'PHP') === 'PHP' ? 'selected' : '' }}>PHP — Philippine Peso</option>
                        <option value="USD" {{ old('currency', $plan->currency ?? '') === 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                    </select>
                </div>
            </div>

            {{-- Live preview --}}
            <div class="price-preview" id="price-preview">
                <i class="fas fa-receipt" style="color:var(--sa-accent);"></i>
                <div>
                    <div class="pp-val" id="preview-price">₱0</div>
                    <div class="pp-dur" id="preview-dur">for 30 days</div>
                </div>
                <div style="flex:1;"></div>
                <div id="preview-ppd" style="font-size:12px;color:var(--sa-muted);"></div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 3. Usage Limits                                        --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-sliders-h"></i> Usage Limits
                <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--sa-muted);">
                    — toggle "Unlimited" to remove the cap
                </span>
            </div>

            <div class="form-grid-2">
                @php
                    $limits = [
                        ['max_trainees',        'Max Trainees',         'Max simultaneous trainees enrolled'],
                        ['max_trainers',        'Max Trainers',         'Trainer accounts allowed'],
                        ['max_users',           'Max Admin Users',      'Admin user accounts'],
                        ['max_courses',         'Max Courses',          'Active course records'],
                        ['max_exports_monthly', 'Monthly Export Records','Records exported per calendar month (0 = no exports)'],
                    ];
                @endphp

                @foreach($limits as [$field, $label, $hint])
                    @php
                        $val = old($field, $plan->{$field} ?? null);
                        $isUnlimited = $val === null;
                    @endphp
                    <div class="limit-row">
                        <label style="font-size:11px;font-weight:700;color:var(--sa-muted);text-transform:uppercase;letter-spacing:.4px;">
                            {{ $label }}
                        </label>
                        <div class="limit-input-wrap">
                            <input type="number"
                                   name="{{ $field }}"
                                   id="inp-{{ $field }}"
                                   value="{{ $isUnlimited ? '' : $val }}"
                                   min="0"
                                   {{ $isUnlimited ? 'disabled' : '' }}
                                   placeholder="e.g. 100">
                            <label class="unlimited-toggle">
                                <input type="checkbox"
                                       id="ulim-{{ $field }}"
                                       {{ $isUnlimited ? 'checked' : '' }}
                                       onchange="toggleUnlimited('{{ $field }}', this.checked)">
                                ∞ Unlimited
                            </label>
                        </div>
                        <span class="hint">{{ $hint }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 4. Export Formats                                      --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-file-export"></i> Allowed Export Formats
            </div>

            @php
                $currentFormats = old('allowed_export_formats', $plan->allowed_export_formats ?? []);
            @endphp

            <div class="export-group">
                @foreach(['csv' => ['📄','CSV','Spreadsheet-compatible'], 'excel' => ['📊','Excel','.xlsx format'], 'pdf' => ['📑','PDF','Printable reports']] as $fmt => [$icon, $fmtLabel, $fmtDesc])
                    <div class="exp-toggle">
                        <input type="checkbox"
                               name="allowed_export_formats[]"
                               value="{{ $fmt }}"
                               id="fmt-{{ $fmt }}"
                               {{ in_array($fmt, $currentFormats) ? 'checked' : '' }}>
                        <label for="fmt-{{ $fmt }}">
                            <span>{{ $icon }}</span> {{ strtoupper($fmt) }}
                        </label>
                    </div>
                @endforeach
            </div>
            <p class="hint" style="margin-top:10px;">Leave all unchecked = no exports allowed on this plan.</p>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 5. Feature Flags                                       --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-toggle-on"></i> Feature Flags
            </div>

            <div class="feat-grid">
                @php
                    $flags = [
                        'has_trainers'       => ['👨‍🏫', 'Trainer Management',   'Allow trainer accounts & management'],
                        'has_assessments'    => ['📝',  'Assessments',          'Trainer-led competency assessments'],
                        'has_certificates'   => ['🏅',  'Certificates',         'Issue & download certificates'],
                        'has_custom_reports' => ['📊',  'Custom Reports',       'Custom report builder & analytics'],
                        'has_branding'       => ['🎨',  'Custom Branding',      'Custom logo, colors & tagline'],
                    ];
                @endphp

                @foreach($flags as $flag => [$icon, $flagLabel, $flagHint])
                    @php $isChecked = old($flag, $plan->{$flag} ?? false); @endphp
                    <div class="feat-toggle">
                        <input type="checkbox"
                               name="{{ $flag }}"
                               value="1"
                               id="flag-{{ $flag }}"
                               {{ $isChecked ? 'checked' : '' }}
                               onchange="syncFeatCheck(this)">
                        <label for="flag-{{ $flag }}" title="{{ $flagHint }}">
                            <span class="feat-check" id="check-{{ $flag }}">✓</span>
                            <span style="font-size:15px;line-height:1;flex-shrink:0;">{{ $icon }}</span>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--sa-text);">{{ $flagLabel }}</div>
                                <div style="font-size:11px;font-weight:400;color:var(--sa-muted);margin-top:1px;">{{ $flagHint }}</div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 6. Availability Window                                 --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-calendar-alt"></i> Availability Window
            </div>

            @php
                $from  = old('available_from',  $plan->available_from  ?? null);
                $until = old('available_until', $plan->available_until ?? null);
                $always = !$from && !$until;
            @endphp

            <div class="date-row">
                <label class="always-available-wrap">
                    <input type="checkbox" id="always-avail"
                           {{ $always ? 'checked' : '' }}
                           onchange="toggleAvailability(this.checked)">
                    Always available (no date restriction)
                </label>

                <div class="fi" id="wrap-from" style="{{ $always ? 'display:none' : '' }}">
                    <label>Available From</label>
                    <input type="date" name="available_from" id="inp-from"
                           value="{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '' }}">
                </div>

                <div class="fi" id="wrap-until" style="{{ $always ? 'display:none' : '' }}">
                    <label>Available Until</label>
                    <input type="date" name="available_until" id="inp-until"
                           value="{{ $until ? \Carbon\Carbon::parse($until)->format('Y-m-d') : '' }}">
                    <span class="hint">Leave blank = no end date.</span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- 7. Status                                              --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-power-off"></i> Plan Status
            </div>

            <div class="active-row">
                <label class="switch">
                    <input type="checkbox" name="is_active" value="1" id="sw-active"
                           {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
                    <span class="switch-track"></span>
                </label>
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--sa-text);" id="active-label">
                        {{ old('is_active', $plan->is_active ?? true) ? 'Active' : 'Inactive' }}
                    </div>
                    <div class="hint">Inactive plans are hidden from the tenant upgrade page.</div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- Actions                                               --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div class="pm-card" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-{{ isset($plan) ? 'save' : 'plus' }}"></i>
                {{ isset($plan) ? 'Save Changes' : 'Create Plan' }}
            </button>
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline btn-lg">
                Cancel
            </a>
        </div>
    </form>

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- Danger Zone (edit only)                               --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    @if(isset($plan))
        <div class="pm-card danger-zone mt-5">
            <div class="pm-card-title" style="color:var(--sa-danger);">
                <i class="fas fa-exclamation-triangle" style="color:var(--sa-danger);"></i> Danger Zone
            </div>
            <p class="text-sm mb-4" style="color:var(--sa-muted);">
                Deleting this plan is permanent. Any tenants currently on this plan
                will retain their <code>subscription</code> value but the plan record
                will no longer exist — use with caution.
            </p>
            <form action="{{ route('superadmin.plans.manage.destroy', $plan) }}"
                  method="POST"
                  onsubmit="return confirm('Permanently delete the {{ addslashes($plan->name) }} plan? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete This Plan
                </button>
            </form>
        </div>
    @endif

</div>

<script>
// ── Price preview ─────────────────────────────────────────────────────────────
function updatePreview() {
    const price    = parseFloat(document.getElementById('inp-price').value)    || 0;
    const duration = parseInt(document.getElementById('inp-duration').value)   || 30;

    document.getElementById('preview-price').textContent =
        '₱' + price.toLocaleString('en-PH', { minimumFractionDigits: 2 });

    let durLabel = duration + ' days';
    if (duration === 30)  durLabel = '1 month (30 days)';
    if (duration === 90)  durLabel = '3 months (90 days)';
    if (duration === 180) durLabel = '6 months (180 days)';
    if (duration === 365) durLabel = '1 year (365 days)';
    if (duration === 730) durLabel = '2 years (730 days)';
    document.getElementById('preview-dur').textContent = 'for ' + durLabel;

    const ppd = duration > 0 ? (price / duration) : 0;
    document.getElementById('preview-ppd').textContent =
        ppd > 0 ? '≈ ₱' + ppd.toFixed(2) + ' / day' : '';
}

// ── Unlimited toggle ──────────────────────────────────────────────────────────
function toggleUnlimited(field, isUnlimited) {
    const inp = document.getElementById('inp-' + field);
    inp.disabled = isUnlimited;
    if (isUnlimited) inp.value = '';
}

// ── Availability window ───────────────────────────────────────────────────────
function toggleAvailability(always) {
    ['wrap-from', 'wrap-until'].forEach(function(id) {
        document.getElementById(id).style.display = always ? 'none' : '';
    });
    if (always) {
        document.getElementById('inp-from').value  = '';
        document.getElementById('inp-until').value = '';
    }
}

// ── Active switch label ───────────────────────────────────────────────────────
document.getElementById('sw-active').addEventListener('change', function() {
    document.getElementById('active-label').textContent = this.checked ? 'Active' : 'Inactive';
});

// ── Feature check visual sync ────────────────────────────────────────────────
function syncFeatCheck(cb) {
    const check = document.getElementById('check-' + cb.id.replace('flag-', ''));
    if (!check) return;
    check.style.background  = cb.checked ? 'var(--sa-accent)' : 'var(--sa-bg)';
    check.style.borderColor = cb.checked ? 'var(--sa-accent)' : 'var(--sa-border)';
    check.style.color       = cb.checked ? '#fff' : 'transparent';
}

// ── Init on load ──────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();

    // Sync all feature checkboxes on load
    document.querySelectorAll('[id^="flag-"]').forEach(function(cb) {
        syncFeatCheck(cb);
    });

    // Sync unlimited states on load
    @php
        $limitFields = ['max_trainees','max_trainers','max_users','max_courses','max_exports_monthly'];
    @endphp
    @foreach($limitFields as $f)
        (function() {
            var cb = document.getElementById('ulim-{{ $f }}');
            if (cb) toggleUnlimited('{{ $f }}', cb.checked);
        })();
    @endforeach
});
</script>

@endsection