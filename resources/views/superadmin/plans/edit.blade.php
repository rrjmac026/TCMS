@extends('layouts.app')

@section('title', 'Edit Discount: ' . $discount->code)

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

    .form-card {
        border-radius: 20px; border: 2px solid var(--sa-border);
        background: var(--sa-bg); overflow: hidden;
    }
    .form-card-header {
        padding: 24px 28px 20px;
        border-bottom: 2px solid var(--sa-border);
        background: rgba(0,87,184,.03);
        display: flex; align-items: center; justify-content: space-between;
    }
    .form-card-body  { padding: 28px; }
    .form-card-footer {
        padding: 18px 28px; border-top: 2px solid var(--sa-border);
        display: flex; gap: 10px; justify-content: flex-end;
    }

    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

    .fi { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
    .fi label { font-size: 11px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }
    .fi input, .fi select, .fi textarea {
        padding: 9px 12px; border-radius: 9px;
        border: 1.5px solid var(--sa-border);
        background: var(--sa-bg); color: var(--sa-text);
        font-family: inherit; font-size: 13px;
        outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .fi input:focus, .fi select:focus, .fi textarea:focus {
        border-color: var(--sa-accent);
        box-shadow: 0 0 0 3px rgba(0,87,184,.08);
    }
    .fi textarea { resize: vertical; min-height: 64px; }

    .check-group { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px; }
    .check-item  {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 600; color: var(--sa-text); cursor: pointer;
    }
    .check-item input { accent-color: var(--sa-accent); width: 15px; height: 15px; cursor: pointer; }

    .section-divider {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .8px; color: var(--sa-muted);
        border-bottom: 1.5px solid var(--sa-border);
        padding-bottom: 8px; margin: 20px 0 14px;
    }

    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px; font-size: 12px;
        font-weight: 700; border: none; cursor: pointer;
        font-family: inherit; text-decoration: none; transition: all .15s;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: var(--sa-accent); color: #fff; box-shadow: 0 2px 8px rgba(0,87,184,.22); }
    .btn-outline  { background: var(--sa-surface); color: var(--sa-text); border: 1.5px solid var(--sa-border); }
    .btn-danger   { background: rgba(206,17,38,.08); color: var(--sa-danger); border: 1.5px solid rgba(206,17,38,.2); }

    .hint { font-size: 11px; color: var(--sa-muted); margin-top: 3px; }
    .required { color: var(--sa-danger); margin-left: 2px; }

    /* ── Change indicator — highlights fields that differ from original ── */
    .fi input.changed, .fi select.changed, .fi textarea.changed {
        border-color: var(--sa-warning) !important;
        background: rgba(179,138,0,.04);
    }

    /* ── Current value badge ── */
    .current-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 12px;
        background: rgba(0,87,184,.07); border: 1.5px solid rgba(0,87,184,.2);
    }
    .current-badge .code { font-family: monospace; font-size: 18px; font-weight: 900; letter-spacing: 2px; color: var(--sa-accent); }
    .current-badge .val  { font-size: 22px; font-weight: 900; color: var(--sa-success); }

    /* ── Danger zone ── */
    .danger-zone {
        border-radius: 16px; border: 2px solid rgba(206,17,38,.2);
        background: rgba(206,17,38,.03); padding: 20px 22px;
    }
    .danger-zone h3 { font-size: 13px; font-weight: 800; color: var(--sa-danger); margin-bottom: 8px; }
    .danger-zone p  { font-size: 12px; color: var(--sa-muted); margin-bottom: 12px; line-height: 1.5; }

    /* ── Stat mini ── */
    .stat-mini {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; border-radius: 12px;
        background: var(--sa-surface); border: 1.5px solid var(--sa-border);
        margin-bottom: 8px;
    }
    .stat-mini .lbl { font-size: 11px; font-weight: 600; color: var(--sa-muted); }
    .stat-mini .val { font-size: 14px; font-weight: 800; color: var(--sa-primary); }

    /* ── Status badge ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .sb-success { background: rgba(22,163,74,.10);  color: var(--sa-success); }
    .sb-warning { background: rgba(179,138,0,.10);  color: var(--sa-warning); }
    .sb-danger  { background: rgba(206,17,38,.10);  color: var(--sa-danger);  }
    .sb-muted   { background: rgba(90,122,170,.10); color: var(--sa-muted);   }
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-pencil-alt mr-2" style="color:var(--sa-accent);"></i> Edit Discount
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Modifying <strong style="color:var(--sa-text);">{{ $discount->name }}</strong>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.plans.discounts.show', $discount) }}" class="btn btn-outline">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- ── Flash / Errors ── --}}
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Main Form ── --}}
        <div class="lg:col-span-2">
            <div class="form-card">
                <div class="form-card-header">
                    <div>
                        <div class="font-bold text-base mb-1" style="color:var(--sa-primary);">
                            <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i>
                            Edit Discount Details
                        </div>
                        <div class="current-badge">
                            <span class="code">{{ $discount->code }}</span>
                            <span class="val">{{ $discount->formatted_value }}</span>
                            @php
                                $statusClass = match($discount->status_label) {
                                    'Active'    => 'sb-success',
                                    'Scheduled' => 'sb-warning',
                                    default     => 'sb-danger',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}" style="font-size:10px;">{{ $discount->status_label }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('superadmin.plans.discounts.update', $discount) }}" method="POST" id="edit-form">
                    @csrf @method('PATCH')
                    <div class="form-card-body">

                        {{-- Basic Info --}}
                        <div class="section-divider">Basic Information</div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Discount Name <span class="required">*</span></label>
                                <input type="text" name="name"
                                       value="{{ old('name', $discount->name) }}"
                                       data-original="{{ $discount->name }}"
                                       required oninput="markChanged(this)">
                            </div>
                            <div class="fi">
                                <label>Code (uppercase) <span class="required">*</span></label>
                                <input type="text" name="code"
                                       value="{{ old('code', $discount->code) }}"
                                       data-original="{{ $discount->code }}"
                                       required
                                       style="text-transform:uppercase;font-family:monospace;letter-spacing:1px;"
                                       oninput="this.value=this.value.toUpperCase();markChanged(this)">
                                <span class="hint">Changing the code will not affect historical usages.</span>
                            </div>
                        </div>

                        {{-- Value --}}
                        <div class="section-divider">Discount Value</div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Discount Type <span class="required">*</span></label>
                                <select name="type" required id="discount-type"
                                        data-original="{{ $discount->type }}"
                                        onchange="updateValueHint();markChanged(this)">
                                    <option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed"      {{ old('type', $discount->type) === 'fixed'      ? 'selected' : '' }}>Fixed Amount (₱)</option>
                                </select>
                            </div>
                            <div class="fi">
                                <label>Discount Value <span class="required">*</span></label>
                                <input type="number" name="value"
                                       value="{{ old('value', $discount->value) }}"
                                       data-original="{{ $discount->value }}"
                                       id="discount-value"
                                       min="0.01" step="0.01" required
                                       oninput="markChanged(this)">
                                <span class="hint" id="value-hint"></span>
                            </div>
                        </div>

                        {{-- Applicability --}}
                        <div class="section-divider">Applicability</div>

                        <div class="fi">
                            <label>Applicable Plans <span class="hint" style="text-transform:none;font-weight:400;">(leave all unchecked = all plans)</span></label>
                            <div class="check-group">
                                @foreach($plans as $p)
                                    <label class="check-item">
                                        <input type="checkbox" name="applicable_plans[]" value="{{ $p->slug }}"
                                               {{ in_array($p->slug, old('applicable_plans', $discount->applicable_plans ?? [])) ? 'checked' : '' }}>
                                        {{ $p->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="fi">
                            <label>Applicable Actions <span class="hint" style="text-transform:none;font-weight:400;">(leave all unchecked = all actions)</span></label>
                            <div class="check-group">
                                @foreach([
                                    'approve'            => 'Approve (new tenant)',
                                    'upgrade_superadmin' => 'SA Upgrade',
                                    'upgrade_admin'      => 'Admin Upgrade',
                                    'renewal'            => 'Renewal',
                                ] as $val => $lbl)
                                    <label class="check-item">
                                        <input type="checkbox" name="applicable_actions[]" value="{{ $val }}"
                                               {{ in_array($val, old('applicable_actions', $discount->applicable_actions ?? [])) ? 'checked' : '' }}>
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="fi">
                            <label>Restrict to Specific Tenant</label>
                            <select name="tenant_id"
                                    data-original="{{ $discount->tenant_id ?? '' }}"
                                    onchange="markChanged(this)">
                                <option value="">— Any tenant —</option>
                                @foreach($tenants as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('tenant_id', $discount->tenant_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Validity & Limits --}}
                        <div class="section-divider">Validity & Limits</div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Valid From</label>
                                <input type="date" name="valid_from"
                                       value="{{ old('valid_from', $discount->valid_from?->format('Y-m-d')) }}"
                                       data-original="{{ $discount->valid_from?->format('Y-m-d') ?? '' }}"
                                       oninput="markChanged(this)">
                            </div>
                            <div class="fi">
                                <label>Valid Until</label>
                                <input type="date" name="valid_until"
                                       value="{{ old('valid_until', $discount->valid_until?->format('Y-m-d')) }}"
                                       data-original="{{ $discount->valid_until?->format('Y-m-d') ?? '' }}"
                                       oninput="markChanged(this)">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Max Total Uses</label>
                                <input type="number" name="max_uses"
                                       value="{{ old('max_uses', $discount->max_uses) }}"
                                       data-original="{{ $discount->max_uses ?? '' }}"
                                       placeholder="Unlimited" min="1"
                                       oninput="markChanged(this)">
                                <span class="hint">
                                    Currently used {{ $discount->uses_count }} time{{ $discount->uses_count !== 1 ? 's' : '' }}.
                                    @if($discount->max_uses)
                                        {{ $discount->max_uses - $discount->uses_count }} remaining.
                                    @endif
                                </span>
                            </div>
                            <div class="fi">
                                <label>Minimum Plan Price (₱)</label>
                                <input type="number" name="minimum_price"
                                       value="{{ old('minimum_price', $discount->minimum_price) }}"
                                       data-original="{{ $discount->minimum_price ?? '' }}"
                                       placeholder="No minimum" min="0" step="0.01"
                                       oninput="markChanged(this)">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="section-divider">Status</div>

                        <div class="fi">
                            <label class="check-item" style="cursor:pointer;">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $discount->is_active) ? 'checked' : '' }}>
                                <span>Active</span>
                            </label>
                            <span class="hint" style="margin-left:21px;">Uncheck to deactivate without deleting</span>
                        </div>

                    </div>
                    <div class="form-card-footer">
                        <a href="{{ route('superadmin.plans.discounts.show', $discount) }}" class="btn btn-outline">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Right sidebar ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Current stats ── --}}
            <div class="form-card" style="padding:20px 22px;">
                <div class="font-bold text-sm mb-4" style="color:var(--sa-primary);">
                    <i class="fas fa-chart-bar mr-2" style="color:var(--sa-accent);"></i> Current Stats
                </div>
                <div class="stat-mini">
                    <span class="lbl">Times Used</span>
                    <span class="val">{{ $discount->uses_count }}</span>
                </div>
                <div class="stat-mini">
                    <span class="lbl">Total Saved</span>
                    <span class="val" style="color:var(--sa-success);">₱{{ number_format($discount->total_saved ?? 0, 2) }}</span>
                </div>
                <div class="stat-mini">
                    <span class="lbl">Status</span>
                    <span class="status-badge {{ $statusClass }}" style="font-size:10px;">{{ $discount->status_label }}</span>
                </div>
                <div class="stat-mini">
                    <span class="lbl">Created</span>
                    <span class="val" style="font-size:11px;font-weight:600;">{{ $discount->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            {{-- Plan reference --}}
            <div class="form-card" style="padding:20px 22px;">
                <div class="font-bold text-sm mb-4" style="color:var(--sa-primary);">
                    <i class="fas fa-tags mr-2" style="color:var(--sa-gold);"></i> Plan Prices
                </div>
                @foreach($plans as $p)
                    <div class="flex justify-between items-center mb-2 text-sm">
                        <span style="font-weight:600;color:var(--sa-text);">{{ $p->name }}</span>
                        <span style="font-weight:800;color:var(--sa-primary);">₱{{ number_format($p->price, 2) }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Danger zone --}}
            <div class="danger-zone">
                <h3><i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone</h3>
                <p>Deleting this discount is permanent. All usage history records will also be removed.</p>
                <form action="{{ route('superadmin.plans.discounts.destroy', $discount) }}"
                      method="POST"
                      onsubmit="return confirm('Permanently delete discount {{ $discount->code }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-full" style="justify-content:center;">
                        <i class="fas fa-trash"></i> Delete This Discount
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    // ── Highlight fields that changed ──────────────────────────────────────
    function markChanged(el) {
        const original = el.dataset.original ?? '';
        if (el.value !== original) {
            el.classList.add('changed');
        } else {
            el.classList.remove('changed');
        }
    }

    // ── Value hint update ──────────────────────────────────────────────────
    function updateValueHint() {
        const type  = document.getElementById('discount-type').value;
        const hint  = document.getElementById('value-hint');
        const input = document.getElementById('discount-value');
        if (type === 'percentage') {
            hint.textContent  = 'Enter a percentage (e.g. 20 = 20% off). Max 100.';
            input.max         = '100';
        } else {
            hint.textContent  = 'Enter a flat amount in ₱ (e.g. 500).';
            input.removeAttribute('max');
        }
    }
    updateValueHint();

    // ── Warn if leaving with unsaved changes ───────────────────────────────
    let formDirty = false;
    document.getElementById('edit-form').addEventListener('input', () => formDirty = true);
    document.getElementById('edit-form').addEventListener('change', () => formDirty = true);
    document.getElementById('edit-form').addEventListener('submit', () => formDirty = false);
    window.addEventListener('beforeunload', e => {
        if (formDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
@endsection
