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

    .pm-wrap { max-width: 860px; margin: 0 auto; }

    .pm-card {
        background: var(--sa-bg);
        border: 1.5px solid var(--sa-border);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 20px;
    }
    .pm-card-title {
        font-size: 13px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .7px; color: var(--sa-muted);
        margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
    }
    .pm-card-title i { font-size: 14px; color: var(--sa-accent); }

    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    .fi { display: flex; flex-direction: column; gap: 6px; }
    .fi label { font-size: 11px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .4px; }
    .fi input[type="text"],
    .fi input[type="number"],
    .fi input[type="date"],
    .fi select,
    .fi textarea {
        padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--sa-border);
        background: var(--sa-bg); color: var(--sa-text); font-family: inherit;
        font-size: 13.5px; outline: none; transition: border-color .15s, box-shadow .15s; width: 100%;
    }
    .fi input:focus, .fi select:focus, .fi textarea:focus {
        border-color: var(--sa-accent); box-shadow: 0 0 0 3px rgba(0,87,184,.10);
    }
    .fi .hint { font-size: 11px; color: var(--sa-muted); margin-top: 1px; }
    .fi-full { grid-column: 1 / -1; }

    /* Slug preview badge */
    .slug-preview {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 100px;
        font-size: 12px; font-weight: 700; letter-spacing: .5px;
        background: rgba(0,87,184,.10); color: var(--sa-accent);
        border: 1.5px solid rgba(0,87,184,.20);
        margin-top: 6px; transition: all .2s;
    }

    /* Feature toggles */
    .feat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
    .feat-toggle { position: relative; }
    .feat-toggle input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .feat-toggle label {
        display: flex; align-items: center; gap: 10px; padding: 11px 14px;
        border-radius: 10px; border: 1.5px solid var(--sa-border); background: var(--sa-surface);
        cursor: pointer; transition: all .15s; font-size: 13px; font-weight: 600; color: var(--sa-text); user-select: none;
    }
    .feat-check {
        flex-shrink: 0; width: 18px; height: 18px; border-radius: 5px;
        border: 1.5px solid var(--sa-border); background: var(--sa-bg);
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: transparent; transition: all .15s; line-height: 1;
    }
    .feat-toggle input:checked + label { border-color: var(--sa-accent); background: rgba(0,87,184,.07); }
    .feat-toggle input:checked + label .feat-check { background: var(--sa-accent); border-color: var(--sa-accent); color: #fff; }

    /* Export format toggles */
    .export-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .exp-toggle { position: relative; }
    .exp-toggle input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .exp-toggle label {
        display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px;
        border-radius: 100px; border: 1.5px solid var(--sa-border); background: var(--sa-surface);
        cursor: pointer; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: var(--sa-muted); transition: all .15s; user-select: none;
    }
    .exp-toggle input:checked + label { border-color: var(--sa-success); background: rgba(22,163,74,.08); color: var(--sa-success); }

    /* Limit rows */
    .limit-row { display: flex; flex-direction: column; gap: 6px; }
    .limit-input-wrap { position: relative; display: flex; align-items: center; gap: 8px; }
    .limit-input-wrap input[type="number"] { flex: 1; }
    .limit-input-wrap input:disabled { opacity: .4; cursor: not-allowed; }
    .unlimited-toggle {
        display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
        color: var(--sa-muted); cursor: pointer; white-space: nowrap; user-select: none;
    }
    .unlimited-toggle input[type="checkbox"] { accent-color: var(--sa-accent); width: 14px; height: 14px; cursor: pointer; }

    /* Availability date */
    .date-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .always-available-wrap {
        grid-column: 1 / -1; display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 600; color: var(--sa-text); cursor: pointer;
        user-select: none; margin-bottom: 4px;
    }
    .always-available-wrap input[type="checkbox"] { accent-color: var(--sa-accent); width: 15px; height: 15px; cursor: pointer; }

    /* Active toggle */
    .active-row { display: flex; align-items: center; gap: 12px; }
    .switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .switch-track {
        position: absolute; inset: 0; background: var(--sa-border);
        border-radius: 100px; cursor: pointer; transition: background .2s;
    }
    .switch input:checked + .switch-track { background: var(--sa-accent); }
    .switch-track::after {
        content: ''; position: absolute; width: 18px; height: 18px; border-radius: 50%;
        background: #fff; top: 3px; left: 3px; transition: transform .2s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .switch input:checked + .switch-track::after { transform: translateX(20px); }

    /* Price preview */
    .price-preview {
        display: flex; align-items: center; gap: 10px; padding: 10px 16px;
        background: var(--sa-surface); border: 1.5px solid var(--sa-border);
        border-radius: 12px; margin-top: 14px; font-size: 13px; color: var(--sa-muted);
    }
    .price-preview .pp-val { font-size: 22px; font-weight: 800; color: var(--sa-primary); }
    .price-preview .pp-dur { font-size: 12px; color: var(--sa-muted); }

    /* Icon picker */
    .icon-picker { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
    .icon-opt { position: relative; }
    .icon-opt input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
    .icon-opt label {
        display: flex; align-items: center; justify-content: center;
        width: 42px; height: 42px; border-radius: 10px; font-size: 20px;
        border: 1.5px solid var(--sa-border); background: var(--sa-surface);
        cursor: pointer; transition: all .15s; user-select: none;
    }
    .icon-opt input:checked + label { border-color: var(--sa-accent); background: rgba(0,87,184,.08); }

    /* Buttons */
    .btn {
        display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px;
        border-radius: 10px; font-size: 13px; font-weight: 700; border: none;
        cursor: pointer; font-family: inherit; text-decoration: none; transition: all .15s;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-outline  { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-danger   { background: rgba(206,17,38,.08); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.22); }
    .btn-lg { padding: 13px 28px; font-size: 14px; border-radius: 12px; }

    .danger-zone { border-color: rgba(206,17,38,.30); background: rgba(206,17,38,.03); }

    @media (max-width: 640px) {
        .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
        .date-row { grid-template-columns: 1fr; }
        .pm-card { padding: 20px 18px; }
    }
</style>

<div class="pm-wrap space-y-0">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-{{ isset($plan) ? 'pencil-alt' : 'plus-circle' }} mr-2" style="color:var(--sa-accent);"></i>
                {{ isset($plan) ? 'Edit Plan: ' . $plan->name : 'Create Subscription Plan' }}
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                {{ isset($plan) ? 'Update plan details, limits, availability, and feature flags.' : 'Define a new plan available to tenant organizations.' }}
            </p>
        </div>
        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Plans
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rounded-xl border-2 p-4 mb-4" style="background:rgba(22,163,74,.05);border-color:var(--sa-success);">
            <p style="color:var(--sa-success);" class="font-semibold flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </p>
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-2 p-4 mb-4" style="background:rgba(206,17,38,.05);border-color:var(--sa-danger);">
            <div style="color:var(--sa-danger);" class="font-semibold flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>@foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach</div>
            </div>
        </div>
    @endif

    <form action="{{ isset($plan) ? route('superadmin.plans.manage.update', $plan) : route('superadmin.plans.manage.store') }}"
          method="POST">
        @csrf
        @if(isset($plan)) @method('PATCH') @endif

        {{-- 1. Identity --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-id-card"></i> Plan Identity</div>

            <div class="form-grid-2" style="margin-bottom:18px;">
                {{-- Display Name --}}
                <div class="fi">
                    <label>Display Name *</label>
                    <input type="text" name="name" id="inp-name"
                           value="{{ old('name', $plan->name ?? '') }}"
                           placeholder="e.g. Gold Plan" required maxlength="100"
                           oninput="autoSlug()">
                </div>

                {{-- Plan Icon --}}
                <div class="fi">
                    <label>Plan Icon</label>
                    <div class="icon-picker" id="icon-picker">
                        @php
                            $iconOptions = ['🌱','🚀','💎','⭐','🔥','👑','🏆','🎯','💡','🛡️','⚡','🌟'];
                            $currentIcon = old('icon', $plan->icon ?? '🌱');
                        @endphp
                        @foreach($iconOptions as $ico)
                            <div class="icon-opt">
                                <input type="radio" name="icon" value="{{ $ico }}"
                                       id="icon-{{ $loop->index }}"
                                       {{ $currentIcon === $ico ? 'checked' : '' }}>
                                <label for="icon-{{ $loop->index }}">{{ $ico }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Slug --}}
                <div class="fi">
                    <label>Plan Slug (ID) *</label>
                    <input type="text" name="slug" id="inp-slug"
                           value="{{ old('slug', $plan->slug ?? '') }}"
                           placeholder="e.g. gold-plan"
                           {{ isset($plan) ? '' : '' }}
                           oninput="sanitizeSlug(this)"
                           maxlength="50" required>
                    <span class="hint">
                        Unique identifier — lowercase letters, numbers, hyphens only.
                        @if(isset($plan))
                            ⚠️ Changing the slug will break existing tenant subscriptions on this plan.
                        @endif
                    </span>
                    <div id="slug-preview" class="slug-preview" style="{{ old('slug', $plan->slug ?? '') ? '' : 'display:none' }}">
                        <i class="fas fa-tag" style="font-size:10px;"></i>
                        <span id="slug-preview-text">{{ old('slug', $plan->slug ?? '') }}</span>
                    </div>
                </div>

                {{-- Sort Order --}}
                <div class="fi">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order"
                           value="{{ old('sort_order', $plan->sort_order ?? 0) }}" min="0" max="99">
                    <span class="hint">Lower numbers appear first on plan cards (0, 1, 2…).</span>
                </div>

                {{-- Description --}}
                <div class="fi fi-full">
                    <label>Description</label>
                    <textarea name="description" rows="2"
                              placeholder="Brief description shown on the upgrade page…">{{ old('description', $plan->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Pricing & Duration --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-tag"></i> Pricing &amp; Duration</div>

            <div class="form-grid-2">
                <div class="fi">
                    <label>Price (₱) *</label>
                    <input type="number" name="price" id="inp-price"
                           value="{{ old('price', $plan->price ?? 0) }}"
                           min="0" step="0.01" required oninput="updatePreview()">
                    <span class="hint">Set to 0 for a free plan.</span>
                </div>
                <div class="fi">
                    <label>Duration (days) *</label>
                    <input type="number" name="duration_days" id="inp-duration"
                           value="{{ old('duration_days', $plan->duration_days ?? 30) }}"
                           min="1" required oninput="updatePreview()">
                    <span class="hint">30 = 1 month · 180 = 6 months · 365 = 1 year</span>
                </div>
            </div>

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

        {{-- 3. Usage Limits --}}
        <div class="pm-card">
            <div class="pm-card-title">
                <i class="fas fa-sliders-h"></i> Usage Limits
                <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--sa-muted);">
                    — toggle "Unlimited" to remove the cap (null = unlimited, 0 = not allowed)
                </span>
            </div>

            <div class="form-grid-2">
                @php
                    $limits = [
                        ['max_trainees',        'Max Trainees',          'Max simultaneous trainees enrolled'],
                        ['max_trainers',        'Max Trainers',          'Trainer accounts (0 = no trainers on this plan)'],
                        ['max_users',           'Max Admin Users',       'Admin user accounts'],
                        ['max_courses',         'Max Courses',           'Active course records'],
                        ['max_exports_monthly', 'Monthly Export Records', 'Records exported per calendar month (0 = no exports)'],
                    ];
                @endphp

                @foreach($limits as [$field, $label, $hint])
                    @php
                        $val         = old($field, $plan->{$field} ?? null);
                        $isUnlimited = $val === null;
                    @endphp
                    <div class="limit-row">
                        <label style="font-size:11px;font-weight:700;color:var(--sa-muted);text-transform:uppercase;letter-spacing:.4px;">
                            {{ $label }}
                        </label>
                        <div class="limit-input-wrap">
                            <input type="number" name="{{ $field }}" id="inp-{{ $field }}"
                                   value="{{ $isUnlimited ? '' : $val }}" min="0"
                                   {{ $isUnlimited ? 'disabled' : '' }} placeholder="e.g. 100">
                            <label class="unlimited-toggle">
                                <input type="checkbox" id="ulim-{{ $field }}"
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

        {{-- 4. Export Formats --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-file-export"></i> Allowed Export Formats</div>

            @php $currentFormats = old('allowed_export_formats', $plan->allowed_export_formats ?? []); @endphp

            <div class="export-group">
                @foreach(['csv' => ['📄','CSV','Spreadsheet-compatible'], 'excel' => ['📊','Excel','.xlsx format'], 'pdf' => ['📑','PDF','Printable reports']] as $fmt => [$fmtIcon, $fmtLabel, $fmtDesc])
                    <div class="exp-toggle">
                        <input type="checkbox" name="allowed_export_formats[]" value="{{ $fmt }}"
                               id="fmt-{{ $fmt }}" {{ in_array($fmt, $currentFormats) ? 'checked' : '' }}>
                        <label for="fmt-{{ $fmt }}">
                            <span>{{ $fmtIcon }}</span> {{ strtoupper($fmt) }}
                            <span style="font-weight:400;font-size:10px;text-transform:none;letter-spacing:0;">— {{ $fmtDesc }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            <p class="hint" style="margin-top:10px;">Leave all unchecked = no exports allowed on this plan.</p>
        </div>

        {{-- 5. Feature Flags --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-toggle-on"></i> Feature Flags</div>

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

                @foreach($flags as $flag => [$ficon, $flagLabel, $flagHint])
                    @php $isChecked = (bool) old($flag, $plan->{$flag} ?? false); @endphp
                    <div class="feat-toggle">
                        <input type="checkbox" name="{{ $flag }}" value="1"
                               id="flag-{{ $flag }}" {{ $isChecked ? 'checked' : '' }}
                               onchange="syncFeatCheck(this)">
                        <label for="flag-{{ $flag }}" title="{{ $flagHint }}">
                            <span class="feat-check" id="check-{{ $flag }}">✓</span>
                            <span style="font-size:15px;line-height:1;flex-shrink:0;">{{ $ficon }}</span>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--sa-text);">{{ $flagLabel }}</div>
                                <div style="font-size:11px;font-weight:400;color:var(--sa-muted);margin-top:1px;">{{ $flagHint }}</div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 6. Availability Window --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-calendar-alt"></i> Availability Window</div>

            @php
                $from  = old('available_from',  isset($plan->available_from)  ? $plan->available_from  : null);
                $until = old('available_until', isset($plan->available_until) ? $plan->available_until : null);
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
                    <span class="hint">Plan appears on upgrade page starting this date.</span>
                </div>

                <div class="fi" id="wrap-until" style="{{ $always ? 'display:none' : '' }}">
                    <label>Available Until</label>
                    <input type="date" name="available_until" id="inp-until"
                           value="{{ $until ? \Carbon\Carbon::parse($until)->format('Y-m-d') : '' }}">
                    <span class="hint">Plan is hidden after this date. Leave blank = no end date.</span>
                </div>
            </div>
        </div>

        {{-- 7. Status --}}
        <div class="pm-card">
            <div class="pm-card-title"><i class="fas fa-power-off"></i> Plan Status</div>

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

        {{-- Actions --}}
        <div class="pm-card" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-{{ isset($plan) ? 'save' : 'plus' }}"></i>
                {{ isset($plan) ? 'Save Changes' : 'Create Plan' }}
            </button>
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline btn-lg">
                Cancel
            </a>
            @if(isset($plan))
                <div style="flex:1;"></div>
                <span style="font-size:12px;color:var(--sa-muted);">
                    Last updated: {{ $plan->updated_at->format('M d, Y h:i A') }}
                </span>
            @endif
        </div>
    </form>

    {{-- Danger Zone (edit only) --}}
    @if(isset($plan))
        <div class="pm-card danger-zone mt-5">
            <div class="pm-card-title" style="color:var(--sa-danger);">
                <i class="fas fa-exclamation-triangle" style="color:var(--sa-danger);"></i> Danger Zone
            </div>
            <p class="text-sm mb-4" style="color:var(--sa-muted);">
                Deleting this plan is permanent. Tenants currently on
                <strong>{{ $plan->name }}</strong> will keep their <code>subscription</code>
                value in the database, but the plan record itself will no longer exist.
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
// ── Slug auto-generate from name ──────────────────────────────────────────────
let slugManuallyEdited = {{ isset($plan) ? 'true' : 'false' }};

function autoSlug() {
    if (slugManuallyEdited) return;
    const name = document.getElementById('inp-name').value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    document.getElementById('inp-slug').value = slug;
    updateSlugPreview(slug);
}

function sanitizeSlug(input) {
    slugManuallyEdited = true;
    let val = input.value.toLowerCase()
        .replace(/[^a-z0-9-]/g, '')
        .replace(/-+/g, '-');
    input.value = val;
    updateSlugPreview(val);
}

function updateSlugPreview(slug) {
    const preview = document.getElementById('slug-preview');
    const text    = document.getElementById('slug-preview-text');
    if (slug) {
        text.textContent     = slug;
        preview.style.display = 'inline-flex';
    } else {
        preview.style.display = 'none';
    }
}

// ── Price preview ─────────────────────────────────────────────────────────────
function updatePreview() {
    const price    = parseFloat(document.getElementById('inp-price').value)  || 0;
    const duration = parseInt(document.getElementById('inp-duration').value)  || 30;

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

// ── Limit toggles ─────────────────────────────────────────────────────────────
function toggleUnlimited(field, isUnlimited) {
    const inp = document.getElementById('inp-' + field);
    inp.disabled = isUnlimited;
    if (isUnlimited) inp.value = '';
}

// ── Availability ──────────────────────────────────────────────────────────────
function toggleAvailability(always) {
    ['wrap-from', 'wrap-until'].forEach(function(id) {
        document.getElementById(id).style.display = always ? 'none' : '';
    });
    if (always) {
        document.getElementById('inp-from').value  = '';
        document.getElementById('inp-until').value = '';
    }
}

// ── Active switch ─────────────────────────────────────────────────────────────
document.getElementById('sw-active').addEventListener('change', function() {
    document.getElementById('active-label').textContent = this.checked ? 'Active' : 'Inactive';
});

// ── Feature check sync ────────────────────────────────────────────────────────
function syncFeatCheck(cb) {
    const check = document.getElementById('check-' + cb.id.replace('flag-', ''));
    if (!check) return;
    check.style.background  = cb.checked ? 'var(--sa-accent)' : 'var(--sa-bg)';
    check.style.borderColor = cb.checked ? 'var(--sa-accent)' : 'var(--sa-border)';
    check.style.color       = cb.checked ? '#fff' : 'transparent';
}

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    updateSlugPreview(document.getElementById('inp-slug').value);

    document.querySelectorAll('[id^="flag-"]').forEach(function(cb) {
        syncFeatCheck(cb);
    });

    @php $limitFields = ['max_trainees','max_trainers','max_users','max_courses','max_exports_monthly']; @endphp
    @foreach($limitFields as $f)
        (function() {
            var cb = document.getElementById('ulim-{{ $f }}');
            if (cb) toggleUnlimited('{{ $f }}', cb.checked);
        })();
    @endforeach
});
</script>

@endsection