@extends('layouts.app')

@section('title', 'Discount: ' . $discount->code)

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

    /* ── Detail card ── */
    .detail-card {
        border-radius: 20px; border: 2px solid var(--sa-border);
        background: var(--sa-bg); overflow: hidden;
    }
    .detail-card-header {
        padding: 26px 30px 22px;
        border-bottom: 2px solid var(--sa-border);
        background: rgba(0,87,184,.03);
        display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
    }
    .detail-card-body { padding: 28px 30px; }

    /* ── Stat pills ── */
    .stat-row { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
    .stat-pill {
        flex: 1; min-width: 130px;
        display: flex; flex-direction: column; align-items: center;
        padding: 16px 20px; border-radius: 14px;
        background: var(--sa-surface); border: 1.5px solid var(--sa-border); gap: 4px;
    }
    .stat-pill-val { font-size: 24px; font-weight: 800; color: var(--sa-primary); line-height: 1; }
    .stat-pill-lbl { font-size: 10px; font-weight: 700; color: var(--sa-muted); text-transform: uppercase; letter-spacing: .5px; }

    /* ── Info grid ── */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-item .lbl {
        font-size: 10px; font-weight: 700; color: var(--sa-muted);
        text-transform: uppercase; letter-spacing: .5px;
    }
    .info-item .val { font-size: 14px; font-weight: 700; color: var(--sa-text); }
    .info-item .val.muted { font-weight: 500; color: var(--sa-muted); font-style: italic; }

    /* ── Section titles ── */
    .section-title {
        font-size: 11px; font-weight: 800; text-transform: uppercase;
        letter-spacing: .8px; color: var(--sa-muted);
        border-bottom: 1.5px solid var(--sa-border);
        padding-bottom: 8px; margin: 24px 0 16px;
    }

    /* ── Status badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .sb-success { background: rgba(22,163,74,.10);  color: var(--sa-success); }
    .sb-warning { background: rgba(179,138,0,.10);  color: var(--sa-warning); }
    .sb-danger  { background: rgba(206,17,38,.10);  color: var(--sa-danger);  }
    .sb-muted   { background: rgba(90,122,170,.10); color: var(--sa-muted);   }

    /* ── Tag pills ── */
    .tag-pill {
        display: inline-flex; align-items: center;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
        background: rgba(0,87,184,.08); color: var(--sa-accent);
        border: 1px solid rgba(0,87,184,.15);
    }

    /* ── Usage history table ── */
    .usage-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .usage-table th {
        padding: 10px 14px; text-align: left; font-size: 11px;
        font-weight: 700; letter-spacing: .4px; text-transform: uppercase;
        color: var(--sa-muted); border-bottom: 2px solid var(--sa-border);
        background: var(--sa-surface);
    }
    .usage-table td { padding: 11px 14px; color: var(--sa-text); border-bottom: 1px solid var(--sa-border); }
    .usage-table tr:last-child td { border-bottom: none; }
    .usage-table tr:hover td { background: var(--sa-surface); }

    /* ── Big code display ── */
    .code-display {
        font-family: 'Courier New', monospace;
        font-size: 26px; font-weight: 900;
        letter-spacing: 4px;
        color: var(--sa-accent);
        background: rgba(0,87,184,.07);
        border: 2px dashed rgba(0,87,184,.25);
        border-radius: 12px; padding: 14px 22px;
        display: inline-block; margin-bottom: 6px;
    }

    /* ── Value badge ── */
    .value-badge {
        font-size: 36px; font-weight: 900; color: var(--sa-success); line-height: 1;
    }
    .value-badge .unit { font-size: 16px; color: var(--sa-muted); font-weight: 500; }

    /* ── Progress bar ── */
    .usage-bar-wrap { background: var(--sa-border); border-radius: 99px; height: 8px; overflow: hidden; }
    .usage-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--sa-accent), var(--sa-success)); transition: width .4s; }

    /* ── Buttons ── */
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
    .btn-sm { padding: 5px 11px; font-size: 11px; border-radius: 7px; }
</style>

<div class="space-y-6">

    {{-- ── Page Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold" style="color:var(--sa-primary);">
                <i class="fas fa-percent mr-2" style="color:var(--sa-accent);"></i> Discount Details
            </h1>
            <p class="text-sm mt-1" style="color:var(--sa-muted);">
                Viewing discount code <strong style="color:var(--sa-text);">{{ $discount->code }}</strong>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('superadmin.plans.discounts.edit', $discount) }}" class="btn btn-primary">
                <i class="fas fa-pencil-alt"></i> Edit
            </a>
            <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="rounded-xl border-2 p-4" style="background:rgba(22,163,74,.05);border-color:var(--sa-success);">
            <div style="color:var(--sa-success);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left: Main detail card ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Overview card --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <div>
                        <div class="code-display">{{ $discount->code }}</div>
                        <div class="text-base font-bold mt-1" style="color:var(--sa-primary);">{{ $discount->name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="value-badge">
                            {{ $discount->type === 'percentage'
                                ? number_format($discount->value, 0) . '%'
                                : '₱' . number_format($discount->value, 2) }}
                            <span class="unit">off</span>
                        </div>
                        <div class="mt-2">
                            @php
                                $statusClass = match($discount->status_label) {
                                    'Active'    => 'sb-success',
                                    'Scheduled' => 'sb-warning',
                                    default     => 'sb-danger',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $discount->status_label }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-card-body">

                    {{-- Stat row --}}
                    <div class="stat-row">
                        <div class="stat-pill">
                            <span class="stat-pill-val">{{ $discount->uses_count }}</span>
                            <span class="stat-pill-lbl">Times Used</span>
                        </div>
                        <div class="stat-pill">
                            <span class="stat-pill-val" style="color:var(--sa-success);">
                                ₱{{ number_format($discount->total_saved ?? 0, 2) }}
                            </span>
                            <span class="stat-pill-lbl">Total Saved</span>
                        </div>
                        <div class="stat-pill">
                            <span class="stat-pill-val">
                                @if($discount->max_uses)
                                    {{ $discount->max_uses - $discount->uses_count }}
                                @else
                                    ∞
                                @endif
                            </span>
                            <span class="stat-pill-lbl">Remaining Uses</span>
                        </div>
                    </div>

                    {{-- Usage bar (only if max_uses set) --}}
                    @if($discount->max_uses)
                        @php $pct = min(100, ($discount->uses_count / $discount->max_uses) * 100); @endphp
                        <div class="mb-5">
                            <div class="flex justify-between text-xs mb-1" style="color:var(--sa-muted);">
                                <span>Usage</span>
                                <span>{{ $discount->uses_count }} / {{ $discount->max_uses }}</span>
                            </div>
                            <div class="usage-bar-wrap">
                                <div class="usage-bar-fill" style="width:{{ $pct }}%;"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Info grid --}}
                    <div class="section-title">Configuration</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="lbl">Type</span>
                            <span class="val">{{ ucfirst($discount->type) }}</span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Value</span>
                            <span class="val" style="color:var(--sa-success);">{{ $discount->formatted_value }}</span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Valid From</span>
                            <span class="val {{ !$discount->valid_from ? 'muted' : '' }}">
                                {{ $discount->valid_from?->format('M d, Y') ?? 'No start limit' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Valid Until</span>
                            <span class="val {{ !$discount->valid_until ? 'muted' : '' }}">
                                {{ $discount->valid_until?->format('M d, Y') ?? 'No end limit' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Max Uses</span>
                            <span class="val {{ !$discount->max_uses ? 'muted' : '' }}">
                                {{ $discount->max_uses ? number_format($discount->max_uses) : 'Unlimited' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Minimum Plan Price</span>
                            <span class="val {{ !$discount->minimum_price ? 'muted' : '' }}">
                                {{ $discount->minimum_price ? '₱' . number_format($discount->minimum_price, 2) : 'None' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Restricted Tenant</span>
                            <span class="val {{ !$discount->tenant ? 'muted' : '' }}">
                                {{ $discount->tenant?->name ?? 'Any tenant' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="lbl">Status</span>
                            <span class="status-badge {{ $statusClass }}" style="font-size:11px;">{{ $discount->status_label }}</span>
                        </div>
                    </div>

                    {{-- Applicable plans --}}
                    <div class="section-title">Applies To</div>
                    <div class="mb-3">
                        <div class="text-xs font-700 mb-2" style="color:var(--sa-muted);font-weight:700;">PLANS</div>
                        <div class="flex flex-wrap gap-2">
                            @if(count($discount->applicable_plans ?? []) === 0)
                                <span class="tag-pill" style="background:rgba(22,163,74,.08);color:var(--sa-success);border-color:rgba(22,163,74,.2);">
                                    All Plans
                                </span>
                            @else
                                @foreach($discount->applicable_plans as $slug)
                                    <span class="tag-pill">{{ ucfirst($slug) }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-700 mb-2" style="color:var(--sa-muted);font-weight:700;">ACTIONS</div>
                        <div class="flex flex-wrap gap-2">
                            @if(count($discount->applicable_actions ?? []) === 0)
                                <span class="tag-pill" style="background:rgba(22,163,74,.08);color:var(--sa-success);border-color:rgba(22,163,74,.2);">
                                    All Actions
                                </span>
                            @else
                                @php
                                    $actionLabels = [
                                        'approve'            => 'Approve (new)',
                                        'upgrade_superadmin' => 'SA Upgrade',
                                        'upgrade_admin'      => 'Admin Upgrade',
                                        'renewal'            => 'Renewal',
                                    ];
                                @endphp
                                @foreach($discount->applicable_actions as $action)
                                    <span class="tag-pill">{{ $actionLabels[$action] ?? $action }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Usage History ── --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <div class="font-bold" style="color:var(--sa-primary);">
                        <i class="fas fa-history mr-2" style="color:var(--sa-accent);"></i> Usage History
                    </div>
                    <span class="text-sm" style="color:var(--sa-muted);">{{ $discount->uses_count }} total use{{ $discount->uses_count !== 1 ? 's' : '' }}</span>
                </div>
                <div class="detail-card-body" style="padding:0;">
                    @if(isset($usages) && $usages->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="usage-table">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Plan</th>
                                        <th>Action</th>
                                        <th>Original</th>
                                        <th>Saved</th>
                                        <th>Final</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usages as $u)
                                        <tr>
                                            <td>
                                                <div class="font-semibold">{{ $u->tenant->name ?? '—' }}</div>
                                            </td>
                                            <td>
                                                <span class="tag-pill">{{ ucfirst($u->plan_slug ?? '—') }}</span>
                                            </td>
                                            <td class="text-xs" style="color:var(--sa-muted);">{{ $u->action ?? '—' }}</td>
                                            <td>₱{{ number_format($u->original_price ?? 0, 2) }}</td>
                                            <td style="color:var(--sa-success);font-weight:700;">
                                                -₱{{ number_format($u->discount_amount ?? 0, 2) }}
                                            </td>
                                            <td style="font-weight:800;color:var(--sa-primary);">
                                                ₱{{ number_format($u->final_price ?? 0, 2) }}
                                            </td>
                                            <td class="text-xs" style="color:var(--sa-muted);">
                                                {{ $u->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <i class="fas fa-history text-4xl mb-3" style="color:var(--sa-muted);opacity:.3;"></i>
                            <p style="color:var(--sa-muted);">This discount has not been used yet.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Right sidebar ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Quick Actions --}}
            <div class="detail-card" style="padding:20px 22px;">
                <div class="font-bold text-sm mb-4" style="color:var(--sa-primary);">
                    <i class="fas fa-bolt mr-2" style="color:var(--sa-gold);"></i> Quick Actions
                </div>
                <div class="space-y-2">
                    <a href="{{ route('superadmin.plans.discounts.edit', $discount) }}"
                       class="btn btn-primary w-full" style="justify-content:center;">
                        <i class="fas fa-pencil-alt"></i> Edit This Discount
                    </a>
                    <a href="{{ route('superadmin.plans.index') }}?tab=apply"
                       class="btn btn-outline w-full" style="justify-content:center;">
                        <i class="fas fa-magic"></i> Apply to Tenant
                    </a>
                    <form action="{{ route('superadmin.plans.discounts.destroy', $discount) }}"
                          method="POST"
                          onsubmit="return confirm('Permanently delete discount {{ $discount->code }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-full" style="justify-content:center;">
                            <i class="fas fa-trash"></i> Delete Discount
                        </button>
                    </form>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="detail-card" style="padding:20px 22px;">
                <div class="font-bold text-sm mb-4" style="color:var(--sa-primary);">
                    <i class="fas fa-info-circle mr-2" style="color:var(--sa-accent);"></i> Metadata
                </div>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="text-xs font-700 mb-1" style="color:var(--sa-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Created</div>
                        <div style="color:var(--sa-text);font-weight:600;">{{ $discount->created_at->format('M d, Y · h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-700 mb-1" style="color:var(--sa-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Last Updated</div>
                        <div style="color:var(--sa-text);font-weight:600;">{{ $discount->updated_at->format('M d, Y · h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-700 mb-1" style="color:var(--sa-muted);font-weight:700;text-transform:uppercase;letter-spacing:.4px;">ID</div>
                        <div style="color:var(--sa-muted);font-family:monospace;">#{{ $discount->id }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
