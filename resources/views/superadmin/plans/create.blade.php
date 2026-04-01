@extends('layouts.app')

@section('title', 'Create Discount Code')

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
        border-radius: 20px;
        border: 2px solid var(--sa-border);
        background: var(--sa-bg);
        overflow: hidden;
    }
    .form-card-header {
        padding: 24px 28px 20px;
        border-bottom: 2px solid var(--sa-border);
        background: rgba(0,87,184,.03);
    }
    .form-card-body { padding: 28px; }
    .form-card-footer {
        padding: 18px 28px;
        border-top: 2px solid var(--sa-border);
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

    .fi { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
    .fi label {
        font-size: 11px; font-weight: 700;
        color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px;
    }
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
    .btn-gold     { background: linear-gradient(135deg,var(--sa-gold) 0%,#d4a800 100%); color: #001a4d; }

    .hint { font-size: 11px; color: var(--sa-muted); margin-top: 3px; }
    .required { color: var(--sa-danger); margin-left: 2px; }

    .sidebar-tip {
        border-radius: 16px; border: 2px solid rgba(0,87,184,.2);
        background: rgba(0,87,184,.04); padding: 20px 22px; margin-bottom: 16px;
    }
    .sidebar-tip h3 { font-size: 13px; font-weight: 800; color: var(--sa-primary); margin-bottom: 10px; }
    .sidebar-tip p  { font-size: 12px; color: var(--sa-muted); line-height: 1.6; margin-bottom: 6px; }
    .sidebar-tip p strong { color: var(--sa-text); }
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-plus-circle mr-2" style="color:var(--sa-accent);"></i> Create Discount Code
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Define a new promotional discount for plans and tenants
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Plans
            </a>
        </div>
    </div>

    {{-- ── Flash / Validation errors ── --}}
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
                    <div class="font-bold text-base" style="color:var(--sa-primary);">
                        <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i>
                        Discount Details
                    </div>
                </div>
                <form action="{{ route('superadmin.plans.discounts.store') }}" method="POST">
                    @csrf
                    <div class="form-card-body">

                        {{-- Basic Info --}}
                        <div class="section-divider">Basic Information</div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Discount Name <span class="required">*</span></label>
                                <input type="text" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="e.g. TESDA Anniversary Promo" required>
                            </div>
                            <div class="fi">
                                <label>Code (uppercase) <span class="required">*</span></label>
                                <input type="text" name="code"
                                       value="{{ old('code') }}"
                                       placeholder="TESDA2025" required
                                       style="text-transform:uppercase;font-family:monospace;letter-spacing:1px;"
                                       oninput="this.value=this.value.toUpperCase()">
                                <span class="hint">Alphanumeric, no spaces. Used during checkout.</span>
                            </div>
                        </div>

                        {{-- Value --}}
                        <div class="section-divider">Discount Value</div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Discount Type <span class="required">*</span></label>
                                <select name="type" required id="discount-type" onchange="updateValueHint()">
                                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed"      {{ old('type') === 'fixed'      ? 'selected' : '' }}>Fixed Amount (₱)</option>
                                </select>
                            </div>
                            <div class="fi">
                                <label>Discount Value <span class="required">*</span></label>
                                <input type="number" name="value"
                                       value="{{ old('value') }}"
                                       id="discount-value"
                                       placeholder="e.g. 20" min="0.01" step="0.01" required>
                                <span class="hint" id="value-hint">Enter percentage (e.g. 20 = 20% off)</span>
                            </div>
                        </div>

                        {{-- Applicability --}}
                        <div class="section-divider">Applicability</div>

                        <div class="fi">
                            <label>Applicable Plans <span class="hint" style="text-transform:none;font-weight:400;">(leave all unchecked = applies to all plans)</span></label>
                            <div class="check-group">
                                @foreach($plans as $p)
                                    <label class="check-item">
                                        <input type="checkbox" name="applicable_plans[]" value="{{ $p->slug }}"
                                               {{ in_array($p->slug, old('applicable_plans', [])) ? 'checked' : '' }}>
                                        {{ $p->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="fi">
                            <label>Applicable Actions <span class="hint" style="text-transform:none;font-weight:400;">(leave all unchecked = all actions)</span></label>
                            <div class="check-group">
                                @foreach([
                                    'approve'             => 'Approve (new tenant)',
                                    'upgrade_superadmin'  => 'SA Upgrade',
                                    'upgrade_admin'       => 'Admin Upgrade',
                                    'renewal'             => 'Renewal',
                                ] as $val => $lbl)
                                    <label class="check-item">
                                        <input type="checkbox" name="applicable_actions[]" value="{{ $val }}"
                                               {{ in_array($val, old('applicable_actions', [])) ? 'checked' : '' }}>
                                        {{ $lbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="fi">
                            <label>Restrict to Specific Tenant <span class="hint" style="text-transform:none;font-weight:400;">(optional)</span></label>
                            <select name="tenant_id">
                                <option value="">— Any tenant —</option>
                                @foreach($tenants as $t)
                                    <option value="{{ $t->id }}" {{ old('tenant_id') == $t->id ? 'selected' : '' }}>
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
                                <input type="date" name="valid_from" value="{{ old('valid_from') }}">
                            </div>
                            <div class="fi">
                                <label>Valid Until</label>
                                <input type="date" name="valid_until" value="{{ old('valid_until') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Max Total Uses</label>
                                <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                                       placeholder="Unlimited" min="1">
                                <span class="hint">Leave blank for unlimited uses</span>
                            </div>
                            <div class="fi">
                                <label>Minimum Plan Price (₱)</label>
                                <input type="number" name="minimum_price" value="{{ old('minimum_price') }}"
                                       placeholder="No minimum" min="0" step="0.01">
                                <span class="hint">Discount only applies if plan costs at least this amount</span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="section-divider">Status</div>

                        <div class="fi">
                            <label class="check-item" style="cursor:pointer;">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span>Active immediately</span>
                            </label>
                            <span class="hint" style="margin-left:21px;">Uncheck to save as draft</span>
                        </div>

                    </div>
                    <div class="form-card-footer">
                        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Discount
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Sidebar Tips ── --}}
        <div class="lg:col-span-1 space-y-4">

            <div class="sidebar-tip">
                <h3><i class="fas fa-lightbulb mr-2" style="color:var(--sa-gold);"></i> Tips</h3>
                <p>• <strong>Percentage</strong> discounts scale with the plan price — great for broad promotions.</p>
                <p>• <strong>Fixed (₱)</strong> discounts give a flat deduction — good for specific campaigns.</p>
                <p>• Restricting to a <strong>specific tenant</strong> creates a private one-time promo code.</p>
                <p>• Set both <strong>Valid From</strong> and <strong>Valid Until</strong> to schedule a flash sale.</p>
            </div>

            <div class="sidebar-tip" style="border-color:rgba(245,197,24,.4);background:rgba(245,197,24,.05);">
                <h3><i class="fas fa-tags mr-2" style="color:var(--sa-gold);"></i> Existing Plans</h3>
                @foreach($plans as $p)
                    <div class="flex justify-between items-center mb-2">
                        <span style="font-size:12px;font-weight:600;color:var(--sa-text);">{{ $p->name }}</span>
                        <span style="font-size:12px;font-weight:800;color:var(--sa-primary);">₱{{ number_format($p->price, 2) }}</span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<script>
    function updateValueHint() {
        const type  = document.getElementById('discount-type').value;
        const hint  = document.getElementById('value-hint');
        const input = document.getElementById('discount-value');
        if (type === 'percentage') {
            hint.textContent  = 'Enter percentage (e.g. 20 = 20% off). Max 100.';
            input.placeholder = 'e.g. 20';
            input.max         = '100';
        } else {
            hint.textContent  = 'Enter a flat amount in ₱ (e.g. 500).';
            input.placeholder = 'e.g. 500';
            input.removeAttribute('max');
        }
    }
    updateValueHint();
</script>
@endsection
