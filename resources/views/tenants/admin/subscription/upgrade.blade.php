@extends('layouts.app')

@section('title', 'Upgrade Your Plan')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; }

    .up-page {
        font-family: 'Plus Jakarta Sans', sans-serif;
        min-height: 100vh;
        padding: 48px 24px 80px;
        position: relative;
        overflow: hidden;
    }

    /* ── Background mesh ── */
    .up-bg {
        position: fixed; inset: 0; z-index: 0; pointer-events: none;
    }
    .up-bg::before {
        content: '';
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 80% 60% at 10% 10%, rgba(0,87,184,0.09) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 20%, rgba(206,17,38,0.07) 0%, transparent 55%),
            radial-gradient(ellipse 50% 60% at 50% 90%, rgba(0,48,135,0.06) 0%, transparent 55%);
    }
    .dark .up-bg::before {
        background:
            radial-gradient(ellipse 80% 60% at 10% 10%, rgba(0,87,184,0.18) 0%, transparent 60%),
            radial-gradient(ellipse 60% 50% at 90% 20%, rgba(206,17,38,0.12) 0%, transparent 55%),
            radial-gradient(ellipse 50% 60% at 50% 90%, rgba(0,48,135,0.14) 0%, transparent 55%);
    }

    .up-inner { position: relative; z-index: 1; max-width: 1100px; margin: 0 auto; }

    /* ── Header ── */
    .up-header { text-align: center; margin-bottom: 56px; }
    .up-badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 16px; border-radius: 100px;
        font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase;
        background: rgba(0,87,184,0.10); color: #0057B8;
        border: 1px solid rgba(0,87,184,0.20);
        margin-bottom: 20px;
    }
    .dark .up-badge { background: rgba(91,156,246,0.12); color: #5b9cf6; border-color: rgba(91,156,246,0.25); }

    .up-title {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: clamp(36px, 5vw, 58px);
        font-weight: 400;
        line-height: 1.12;
        color: #001a4d;
        margin-bottom: 16px;
    }
    .dark .up-title { color: #dde8ff; }
    .up-title em { font-style: italic; color: #0057B8; }
    .dark .up-title em { color: #5b9cf6; }

    .up-subtitle {
        font-size: 17px; color: #5a7aaa; max-width: 500px; margin: 0 auto;
        line-height: 1.65;
    }
    .dark .up-subtitle { color: #6b8abf; }

    /* ── Current plan pill ── */
    .up-current-pill {
        display: inline-flex; align-items: center; gap: 8px;
        margin-top: 20px; padding: 8px 18px; border-radius: 100px;
        font-size: 13px; font-weight: 600;
        border: 1.5px dashed rgba(0,87,184,0.30);
        color: #1a3a6b;
        background: rgba(255,255,255,0.7);
    }
    .dark .up-current-pill {
        border-color: rgba(91,156,246,0.30);
        color: #adc4f0;
        background: rgba(13,31,60,0.7);
    }
    .up-current-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34,197,94,0.20);
    }

    /* ── Plans grid ── */
    .up-plans {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        align-items: start;
    }

    /* ── Card ── */
    .up-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        cursor: pointer;
    }
    .up-card:hover { transform: translateY(-6px); }

    /* Card surfaces */
    .up-card-inner {
        background: #fff;
        border: 1.5px solid #c5d8f5;
        border-radius: 20px;
        padding: 32px 28px 28px;
        height: 100%;
    }
    .dark .up-card-inner {
        background: #0d1f3c;
        border-color: #1e3a6b;
    }
    .up-card:hover .up-card-inner {
        box-shadow: 0 20px 60px rgba(0,48,135,0.14);
        border-color: #0057B8;
    }
    .dark .up-card:hover .up-card-inner {
        box-shadow: 0 20px 60px rgba(0,0,0,0.40);
        border-color: #5b9cf6;
    }

    /* Featured (Standard) card */
    .up-card.featured .up-card-inner {
        background: linear-gradient(145deg, #003087 0%, #0057B8 60%, #0070e0 100%);
        border-color: transparent;
        box-shadow: 0 20px 60px rgba(0,87,184,0.35);
    }
    .up-card.featured:hover { transform: translateY(-10px); }
    .up-card.featured:hover .up-card-inner {
        box-shadow: 0 30px 80px rgba(0,87,184,0.45);
        border-color: transparent;
    }

    /* Current plan card */
    .up-card.current-plan .up-card-inner {
        border-color: #22c55e;
        background: rgba(240,253,244,0.8);
    }
    .dark .up-card.current-plan .up-card-inner {
        border-color: rgba(74,222,128,0.40);
        background: rgba(5,46,22,0.25);
    }

    /* ── Popular badge ── */
    .up-popular-badge {
        position: absolute; top: 20px; right: 20px;
        background: #F5C518; color: #1a1a00;
        font-size: 10px; font-weight: 800;
        letter-spacing: 0.8px; text-transform: uppercase;
        padding: 4px 12px; border-radius: 100px;
        box-shadow: 0 2px 10px rgba(245,197,24,0.40);
    }

    /* Current badge */
    .up-current-badge {
        position: absolute; top: 20px; right: 20px;
        background: #22c55e; color: #fff;
        font-size: 10px; font-weight: 800;
        letter-spacing: 0.8px; text-transform: uppercase;
        padding: 4px 12px; border-radius: 100px;
        display: flex; align-items: center; gap: 5px;
    }

    /* ── Plan header ── */
    .up-plan-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-bottom: 16px;
        background: rgba(0,87,184,0.10);
    }
    .up-card.featured .up-plan-icon {
        background: rgba(255,255,255,0.15);
    }

    .up-plan-name {
        font-size: 13px; font-weight: 700; letter-spacing: 1px;
        text-transform: uppercase; color: #5a7aaa;
        margin-bottom: 4px;
    }
    .up-card.featured .up-plan-name { color: rgba(255,255,255,0.70); }

    .up-plan-price {
        display: flex; align-items: baseline; gap: 4px;
        margin-bottom: 4px;
    }
    .up-price-amount {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: 52px; line-height: 1; color: #001a4d;
    }
    .dark .up-price-amount { color: #dde8ff; }
    .up-card.featured .up-price-amount { color: #fff; }

    .up-price-period {
        font-size: 14px; color: #5a7aaa; padding-bottom: 6px;
    }
    .up-card.featured .up-price-period { color: rgba(255,255,255,0.65); }

    .up-plan-desc {
        font-size: 13.5px; color: #5a7aaa; line-height: 1.6;
        margin-bottom: 24px;
        min-height: 42px;
    }
    .dark .up-plan-desc { color: #6b8abf; }
    .up-card.featured .up-plan-desc { color: rgba(255,255,255,0.72); }

    /* ── Divider ── */
    .up-card-divider {
        height: 1px; background: #c5d8f5; margin: 0 0 22px;
    }
    .dark .up-card-divider { background: #1e3a6b; }
    .up-card.featured .up-card-divider { background: rgba(255,255,255,0.18); }

    /* ── Features list ── */
    .up-features { list-style: none; padding: 0; margin: 0 0 28px; space-y: 10px; }
    .up-feat-item {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 5px 0; font-size: 13.5px; color: #1a3a6b;
    }
    .dark .up-feat-item { color: #adc4f0; }
    .up-card.featured .up-feat-item { color: rgba(255,255,255,0.90); }

    .up-feat-icon {
        width: 18px; height: 18px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 9px; flex-shrink: 0; margin-top: 1px;
        background: rgba(34,197,94,0.15); color: #16a34a;
    }
    .up-card.featured .up-feat-icon { background: rgba(255,255,255,0.20); color: #fff; }

    .up-feat-item.locked .up-feat-icon {
        background: rgba(90,122,170,0.12); color: #5a7aaa;
    }
    .up-feat-item.locked { opacity: 0.45; }

    /* ── CTA Button ── */
    .up-cta-btn {
        width: 100%; padding: 14px 20px; border-radius: 12px;
        font-size: 14px; font-weight: 700; letter-spacing: 0.3px;
        border: none; cursor: pointer;
        transition: all 0.22s ease;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .up-cta-btn.primary {
        background: linear-gradient(135deg, #003087 0%, #0057B8 100%);
        color: #fff;
        box-shadow: 0 4px 20px rgba(0,87,184,0.30);
    }
    .up-cta-btn.primary:hover {
        background: linear-gradient(135deg, #0057B8 0%, #0070e0 100%);
        box-shadow: 0 6px 28px rgba(0,87,184,0.45);
        transform: scale(1.02);
    }
    .up-cta-btn.on-dark {
        background: rgba(255,255,255,0.92);
        color: #003087;
        box-shadow: 0 4px 20px rgba(0,0,0,0.20);
    }
    .up-cta-btn.on-dark:hover {
        background: #fff;
        box-shadow: 0 6px 28px rgba(0,0,0,0.30);
        transform: scale(1.02);
    }
    .up-cta-btn.current {
        background: rgba(34,197,94,0.10);
        color: #16a34a;
        border: 1.5px solid rgba(34,197,94,0.30);
        cursor: default;
    }
    .dark .up-cta-btn.current {
        background: rgba(74,222,128,0.10);
        color: #4ade80;
        border-color: rgba(74,222,128,0.25);
    }

    /* ── Guarantee note ── */
    .up-guarantee {
        text-align: center; margin-top: 48px;
        font-size: 13px; color: #5a7aaa;
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .dark .up-guarantee { color: #6b8abf; }

    /* ── Modal ── */
    .up-modal-backdrop {
        position: fixed; inset: 0; z-index: 100;
        background: rgba(0,26,77,0.55);
        backdrop-filter: blur(6px);
        display: flex; align-items: center; justify-content: center;
        padding: 24px;
    }
    .up-modal {
        background: #fff;
        border-radius: 24px;
        padding: 40px;
        max-width: 460px; width: 100%;
        box-shadow: 0 40px 100px rgba(0,48,135,0.25);
        text-align: center;
        animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .dark .up-modal {
        background: #0d1f3c;
        box-shadow: 0 40px 100px rgba(0,0,0,0.60);
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.85) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .up-modal-icon {
        width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 20px;
        display: flex; align-items: center; justify-content: center; font-size: 30px;
    }
    .up-modal-title {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: 28px; color: #001a4d; margin-bottom: 10px;
    }
    .dark .up-modal-title { color: #dde8ff; }
    .up-modal-sub {
        font-size: 14px; color: #5a7aaa; line-height: 1.65; margin-bottom: 28px;
    }
    .dark .up-modal-sub { color: #6b8abf; }
    .up-modal-plan-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 20px; border-radius: 100px; margin-bottom: 28px;
        font-size: 15px; font-weight: 700;
        background: linear-gradient(135deg, #003087 0%, #0057B8 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,87,184,0.30);
    }
    .up-modal-actions { display: flex; gap: 12px; flex-direction: column; }
    .up-modal-confirm {
        padding: 14px; border-radius: 12px; border: none; cursor: pointer;
        font-size: 14px; font-weight: 700;
        background: linear-gradient(135deg, #003087 0%, #0057B8 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,87,184,0.30);
        transition: all 0.2s;
    }
    .up-modal-confirm:hover { transform: scale(1.02); box-shadow: 0 6px 24px rgba(0,87,184,0.40); }
    .up-modal-cancel {
        padding: 12px; border-radius: 12px; border: 1.5px solid #c5d8f5;
        cursor: pointer; font-size: 14px; font-weight: 600;
        background: transparent; color: #5a7aaa;
        transition: all 0.2s;
    }
    .dark .up-modal-cancel { border-color: #1e3a6b; color: #6b8abf; }
    .up-modal-cancel:hover { background: rgba(0,87,184,0.06); color: #1a3a6b; }

    /* ── Success state ── */
    .up-success { animation: successPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }
    @keyframes successPop {
        from { transform: scale(0.5); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    /* ── Comparison table ── */
    .up-compare { margin-top: 64px; }
    .up-compare-title {
        text-align: center; font-family: 'Instrument Serif', Georgia, serif;
        font-size: 32px; color: #001a4d; margin-bottom: 32px;
    }
    .dark .up-compare-title { color: #dde8ff; }
    .up-table-wrap { overflow-x: auto; border-radius: 16px; border: 1.5px solid #c5d8f5; }
    .dark .up-table-wrap { border-color: #1e3a6b; }
    .up-table {
        width: 100%; border-collapse: collapse;
        font-size: 13.5px;
        background: #fff;
    }
    .dark .up-table { background: #0d1f3c; }
    .up-table th {
        padding: 16px 20px; font-weight: 700; text-align: left;
        background: #f0f5ff; color: #1a3a6b; border-bottom: 1.5px solid #c5d8f5;
    }
    .dark .up-table th { background: #0a1628; color: #adc4f0; border-color: #1e3a6b; }
    .up-table th:not(:first-child) { text-align: center; }
    .up-table td {
        padding: 13px 20px; border-bottom: 1px solid #e8f0fb; color: #1a3a6b;
    }
    .dark .up-table td { border-color: #1e3a6b; color: #adc4f0; }
    .up-table tr:last-child td { border-bottom: none; }
    .up-table td:not(:first-child) { text-align: center; }
    .up-check { color: #22c55e; font-size: 16px; }
    .up-cross { color: #d1d5db; font-size: 16px; }
    .up-table tbody tr:hover td { background: rgba(0,87,184,0.03); }
    .dark .up-table tbody tr:hover td { background: rgba(91,156,246,0.05); }

    /* Highlight current plan column */
    .up-table th.highlight { background: #e8f0fb; color: #0057B8; }
    .dark .up-table th.highlight { background: rgba(0,87,184,0.15); color: #5b9cf6; }
    .up-table td.highlight { background: rgba(0,87,184,0.03); }
    .dark .up-table td.highlight { background: rgba(0,87,184,0.06); }

    /* Animations */
    .up-card { animation: cardFadeIn 0.5s ease both; }
    .up-card:nth-child(1) { animation-delay: 0.05s; }
    .up-card:nth-child(2) { animation-delay: 0.15s; }
    .up-card:nth-child(3) { animation-delay: 0.25s; }
    @keyframes cardFadeIn {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
@php
    $tenant = tenancy()->tenant;
    $currentPlan = $tenant->subscription ?? 'basic';
    $plans = ['basic', 'standard', 'premium'];
    $currentIndex = array_search($currentPlan, $plans);
@endphp

<div class="up-page" :class="$store.darkMode.on ? 'dark' : ''">
    <div class="up-bg"></div>

    <div class="up-inner">

        {{-- Header --}}
        <div class="up-header">
            <div class="up-badge">
                <i class="fas fa-rocket"></i>
                Subscription Plans
            </div>
            <h1 class="up-title">
                Choose the plan that<br><em>fits your center</em>
            </h1>
            <p class="up-subtitle">
                Unlock more features as your training center grows. Upgrade anytime.
            </p>
            <div class="up-current-pill">
                <div class="up-current-dot"></div>
                Currently on <strong style="margin-left:4px; text-transform: capitalize;">{{ $currentPlan }} Plan</strong>
            </div>
        </div>

        {{-- Plan Cards --}}
        <div class="up-plans">

            {{-- BASIC --}}
            <div class="up-card {{ $currentPlan === 'basic' ? 'current-plan' : '' }}" onclick="selectPlan('basic', 'Basic', '₱0')">
                @if($currentPlan === 'basic')
                    <div class="up-current-badge"><i class="fas fa-check"></i> Current</div>
                @endif
                <div class="up-card-inner">
                    <div class="up-plan-icon">🌱</div>
                    <div class="up-plan-name">Basic</div>
                    <div class="up-plan-price">
                        <span class="up-price-amount">₱0</span>
                        <span class="up-price-period">/month</span>
                    </div>
                    <div class="up-plan-desc">Get started with core training management tools.</div>
                    <div class="up-card-divider"></div>
                    <ul class="up-features">
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Up to 100 trainees</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Course & enrollment management</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Attendance tracking</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> 1 admin account</li>
                        <li class="up-feat-item locked"><div class="up-feat-icon"><i class="fas fa-lock"></i></div> Trainer management</li>
                        <li class="up-feat-item locked"><div class="up-feat-icon"><i class="fas fa-lock"></i></div> Assessments & reports</li>
                        <li class="up-feat-item locked"><div class="up-feat-icon"><i class="fas fa-lock"></i></div> Certifications</li>
                    </ul>
                    @if($currentPlan === 'basic')
                        <button class="up-cta-btn current" disabled>
                            <i class="fas fa-check-circle"></i> Current Plan
                        </button>
                    @else
                        <button class="up-cta-btn primary" disabled style="opacity:0.4;cursor:not-allowed;">
                            <i class="fas fa-arrow-down"></i> Downgrade Not Allowed
                        </button>
                    @endif
                </div>
            </div>

            {{-- STANDARD (featured) --}}
            <div class="up-card featured {{ $currentPlan === 'standard' ? 'current-plan' : '' }}" onclick="{{ $currentPlan !== 'standard' && $currentIndex < 1 ? 'selectPlan(\'standard\', \'Standard\', \'₱1,499\')' : '' }}">
                @if($currentPlan === 'standard')
                    <div class="up-current-badge"><i class="fas fa-check"></i> Current</div>
                @else
                    <div class="up-popular-badge">⭐ Most Popular</div>
                @endif
                <div class="up-card-inner">
                    <div class="up-plan-icon">🚀</div>
                    <div class="up-plan-name">Standard</div>
                    <div class="up-plan-price">
                        <span class="up-price-amount">₱1,499</span>
                        <span class="up-price-period">/month</span>
                    </div>
                    <div class="up-plan-desc">Everything your growing training center needs.</div>
                    <div class="up-card-divider"></div>
                    <ul class="up-features">
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Up to 500 trainees</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Full training management</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Trainer & assessment tools</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Basic analytics & reports</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> CSV export (3,000 records/mo)</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Up to 5 users</li>
                        <li class="up-feat-item locked"><div class="up-feat-icon"><i class="fas fa-lock"></i></div> Certifications</li>
                    </ul>
                    @if($currentPlan === 'standard')
                        <button class="up-cta-btn current" disabled>
                            <i class="fas fa-check-circle"></i> Current Plan
                        </button>
                    @elseif($currentIndex < 1)
                        <button class="up-cta-btn on-dark" onclick="event.stopPropagation(); selectPlan('standard', 'Standard', '₱1,499')">
                            <i class="fas fa-arrow-up"></i> Upgrade to Standard
                        </button>
                    @else
                        <button class="up-cta-btn on-dark" disabled style="opacity:0.4;cursor:not-allowed;">
                            <i class="fas fa-arrow-down"></i> Downgrade Not Allowed
                        </button>
                    @endif
                </div>
            </div>

            {{-- PREMIUM --}}
            <div class="up-card {{ $currentPlan === 'premium' ? 'current-plan' : '' }}" onclick="{{ $currentIndex < 2 ? 'selectPlan(\'premium\', \'Premium\', \'₱3,999\')' : '' }}">
                @if($currentPlan === 'premium')
                    <div class="up-current-badge"><i class="fas fa-check"></i> Current</div>
                @endif
                <div class="up-card-inner">
                    <div class="up-plan-icon">💎</div>
                    <div class="up-plan-name">Premium</div>
                    <div class="up-plan-price">
                        <span class="up-price-amount">₱3,999</span>
                        <span class="up-price-period">/month</span>
                    </div>
                    <div class="up-plan-desc">Full power for large or multi-branch training centers.</div>
                    <div class="up-card-divider"></div>
                    <ul class="up-features">
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Unlimited trainees & courses</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Advanced analytics dashboard</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Certification & competency tracking</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Unlimited exports (CSV, Excel, PDF)</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Unlimited users</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Custom branding & API access</li>
                        <li class="up-feat-item"><div class="up-feat-icon"><i class="fas fa-check"></i></div> Priority support</li>
                    </ul>
                    @if($currentPlan === 'premium')
                        <button class="up-cta-btn current" disabled>
                            <i class="fas fa-check-circle"></i> Current Plan
                        </button>
                    @else
                        <button class="up-cta-btn primary" onclick="event.stopPropagation(); selectPlan('premium', 'Premium', '₱3,999')">
                            <i class="fas fa-crown"></i> Upgrade to Premium
                        </button>
                    @endif
                </div>
            </div>

        </div>

        {{-- Guarantee note --}}
        <div class="up-guarantee">
            <i class="fas fa-shield-check" style="color: #22c55e;"></i>
            Simulation only — no payment required &nbsp;·&nbsp;
            <i class="fas fa-headset" style="color: #0057B8; margin-left: 4px;"></i>
            &nbsp;Contact your system administrator for billing
        </div>

        {{-- Comparison Table --}}
        <div class="up-compare">
            <h2 class="up-compare-title">Compare all features</h2>
            <div class="up-table-wrap">
                <table class="up-table">
                    <thead>
                        <tr>
                            <th style="width: 40%">Feature</th>
                            <th class="{{ $currentPlan === 'basic' ? 'highlight' : '' }}">Basic</th>
                            <th class="{{ $currentPlan === 'standard' ? 'highlight' : '' }}">Standard</th>
                            <th class="{{ $currentPlan === 'premium' ? 'highlight' : '' }}">Premium</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $rows = [
                            ['Trainees',                  '100',            '500',               'Unlimited'],
                            ['Courses & Enrollments',     '✅',             '✅',                '✅'],
                            ['Attendance Tracking',       '✅',             '✅',                '✅'],
                            ['Trainer Management',        '❌',             '✅',                '✅'],
                            ['Assessments',               '❌',             '✅',                '✅'],
                            ['Training Schedules',        '❌',             '✅',                '✅'],
                            ['User Accounts',             '1 (Admin)',      'Up to 5',           'Unlimited'],
                            ['Analytics & Reports',       '❌',             'Basic',             'Advanced'],
                            ['CSV Export',                '❌',             '3,000 rec/mo',      'Unlimited'],
                            ['PDF & Excel Export',        '❌',             '❌',                '✅'],
                            ['Certifications',            '❌',             '❌',                '✅'],
                            ['Custom Branding',           '❌',             '❌',                '✅'],
                            ['API Access',                '❌',             '❌',                '✅'],
                            ['Email Notifications',       '❌',             '✅',                '✅'],
                            ['Priority Support',          '❌',             '❌',                '✅'],
                        ];
                        @endphp
                        @foreach($rows as $row)
                        <tr>
                            <td style="font-weight: 500;">{{ $row[0] }}</td>
                            <td class="{{ $currentPlan === 'basic' ? 'highlight' : '' }}">
                                @if($row[1] === '✅') <i class="fas fa-check up-check"></i>
                                @elseif($row[1] === '❌') <i class="fas fa-times up-cross"></i>
                                @else <span style="font-size:13px;">{{ $row[1] }}</span>
                                @endif
                            </td>
                            <td class="{{ $currentPlan === 'standard' ? 'highlight' : '' }}">
                                @if($row[2] === '✅') <i class="fas fa-check up-check"></i>
                                @elseif($row[2] === '❌') <i class="fas fa-times up-cross"></i>
                                @else <span style="font-size:13px;">{{ $row[2] }}</span>
                                @endif
                            </td>
                            <td class="{{ $currentPlan === 'premium' ? 'highlight' : '' }}">
                                @if($row[3] === '✅') <i class="fas fa-check up-check"></i>
                                @elseif($row[3] === '❌') <i class="fas fa-times up-cross"></i>
                                @else <span style="font-size:13px;">{{ $row[3] }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Upgrade Confirmation Modal --}}
<div id="upgradeModal" class="up-modal-backdrop" style="display:none;" onclick="closeModal(event)">
    <div class="up-modal" id="modalBox" onclick="event.stopPropagation()">
        <div id="confirmView">
            <div class="up-modal-icon" style="background: linear-gradient(135deg,#e8f0fb,#c5d8f5);">
                🚀
            </div>
            <h3 class="up-modal-title">Confirm Upgrade</h3>
            <p class="up-modal-sub">You're about to upgrade your plan to:</p>
            <div class="up-modal-plan-pill" id="planPill">
                <i class="fas fa-crown"></i> <span id="planName">Standard</span> — <span id="planPrice">₱1,499</span>/mo
            </div>
            <p class="up-modal-sub" style="margin-bottom:0">
                This is a <strong>simulation</strong>. No payment will be charged. Your features will be upgraded immediately.
            </p>
            <div class="up-modal-actions" style="margin-top: 24px;">
                <button class="up-modal-confirm" id="confirmBtn" onclick="confirmUpgrade()">
                    <i class="fas fa-check"></i> Yes, Upgrade Now
                </button>
                <button class="up-modal-cancel" onclick="document.getElementById('upgradeModal').style.display='none'">
                    Maybe Later
                </button>
            </div>
        </div>
        <div id="successView" style="display:none;">
            <div class="up-success">
                <div class="up-modal-icon" style="background: rgba(34,197,94,0.15); font-size:36px; margin: 0 auto 16px;">✅</div>
                <h3 class="up-modal-title">Plan Upgraded!</h3>
                <p class="up-modal-sub">
                    Your plan has been successfully upgraded to <strong id="successPlanName">Standard</strong>. New features are now active.
                </p>
                <button class="up-modal-confirm" onclick="window.location.href='{{ route('admin.dashboard') }}'">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedPlanKey = null;

function selectPlan(key, name, price) {
    const currentPlan = '{{ $currentPlan }}';
    const plans = ['basic', 'standard', 'premium'];
    const currentIdx = plans.indexOf(currentPlan);
    const newIdx = plans.indexOf(key);

    if (newIdx <= currentIdx) return; // No downgrade

    selectedPlanKey = key;
    document.getElementById('planName').textContent = name;
    document.getElementById('planPrice').textContent = price;
    document.getElementById('successPlanName').textContent = name;
    document.getElementById('confirmView').style.display = 'block';
    document.getElementById('successView').style.display = 'none';
    document.getElementById('upgradeModal').style.display = 'flex';
}

function closeModal(event) {
    document.getElementById('upgradeModal').style.display = 'none';
}

function confirmUpgrade() {
    if (!selectedPlanKey) return;
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upgrading...';

    fetch('{{ route("admin.subscription.upgrade") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ subscription: selectedPlanKey })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('confirmView').style.display = 'none';
            document.getElementById('successView').style.display = 'block';
        } else {
            alert(data.message || 'Upgrade failed. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
        }
    })
    .catch(() => {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Yes, Upgrade Now';
    });
}
</script>
@endpush