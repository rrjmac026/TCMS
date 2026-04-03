@extends('layouts.app')

@section('title', 'Plan Management')

@section('content')

@include('superadmin.plans._styles')

<div class="space-y-6">

    @include('superadmin.plans._header')

    {{-- ── Tabs ── --}}
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

    <div id="tab-plans"     class="tab-content active">@include('superadmin.plans._tab_plans')</div>
    <div id="tab-discounts" class="tab-content">@include('superadmin.plans._tab_discounts')</div>
    <div id="tab-apply"     class="tab-content">@include('superadmin.plans._tab_apply')</div>

</div>

@include('superadmin.plans._modal_new')
@include('superadmin.plans._modal_edit')
@include('superadmin.plans._scripts')

@endsection