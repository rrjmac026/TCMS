@extends('layouts.app')
@section('title', 'Discount Management')

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
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> Discount Management
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Manage discounts and promo codes for subscription plans
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

    {{-- ── Discounts Table ── --}}
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
                                $planSlugsJson = json_encode($d->plan_slugs ?? []);
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
                                {{-- Applies-to column: multi-plan pills --}}
                                <td>
                                    @if(empty($d->plan_slugs))
                                        <span class="plan-pill pill-all">All plans</span>
                                    @else
                                        @foreach($d->plan_slugs as $slug)
                                            <span class="plan-pill pill-{{ $slug }}">{{ ucfirst($slug) }}</span>
                                        @endforeach
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

                                    {{-- Hidden data for JS --}}
                                    <div id="disc-data-{{ $d->id }}" class="hidden"
                                         data-id="{{ $d->id }}"
                                         data-is-automatic="{{ $d->is_automatic ? '1' : '0' }}"
                                         data-code="{{ $d->code }}"
                                         data-label="{{ $d->label }}"
                                         data-type="{{ $d->type }}"
                                         data-value="{{ $d->value }}"
                                         data-plan-slugs="{{ $planSlugsJson }}"
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

        // ── Set automatic vs code radio ────────────────────────────────────
        const isAuto = d.isAutomatic === '1';
        document.getElementById('ed-radio-automatic').checked = isAuto;
        document.getElementById('ed-radio-code').checked      = !isAuto;

        // Trigger the visual toggle update (defined in _discount_fields.blade.php)
        document.getElementById('ed-radio-automatic').dispatchEvent(new Event('change'));

        // ── Populate simple fields ─────────────────────────────────────────
        document.getElementById('ed-code').value        = d.code;
        document.getElementById('ed-label').value       = d.label;
        document.getElementById('ed-type').value        = d.type;
        document.getElementById('ed-value').value       = d.value;
        document.getElementById('ed-valid-from').value  = d.validFrom;
        document.getElementById('ed-valid-until').value = d.validUntil;
        document.getElementById('ed-active').checked    = d.active === '1';

        // ── Populate multi-plan checkboxes ─────────────────────────────────
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

        openModal('modal-edit-discount');
    }
</script>

@endsection