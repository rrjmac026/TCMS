{{-- ── Page Header ── --}}
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

{{-- ── Flash Messages ── --}}
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

{{-- ── Stats Row ── --}}
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
