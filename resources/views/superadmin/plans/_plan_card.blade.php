@php
    $icons = ['basic' => '🌱', 'standard' => '🚀', 'premium' => '💎'];
    $icon  = $icons[$plan->slug] ?? '📦';
    $today = today();
    $isAvail = (!$plan->available_from || $plan->available_from <= $today)
            && (!$plan->available_until || $plan->available_until >= $today);
    $formats = $plan->allowed_export_formats ?? [];
@endphp
<div class="plan-card {{ !$plan->is_active ? 'inactive' : '' }}">
    <div style="position:absolute;top:16px;right:16px;">
        @if($plan->is_active && $isAvail)
            <span class="status-badge sb-success">● Active</span>
        @elseif($plan->is_active && !$isAvail)
            <span class="status-badge sb-warning">● Scheduled</span>
        @else
            <span class="status-badge sb-muted">● Inactive</span>
        @endif
    </div>

    <div class="plan-card-header">
        <div class="plan-icon">{{ $icon }}</div>
        <span class="plan-slug-badge slug-{{ $plan->slug }}">{{ $plan->slug }}</span>
    </div>

    <div class="plan-name">{{ $plan->name }}</div>
    @if($plan->description)
        <div class="plan-desc">{{ Str::limit($plan->description, 80) }}</div>
    @endif

    <div class="plan-price-row">
        <span class="plan-price">₱{{ number_format($plan->price, 0) }}</span>
        <span class="plan-duration">/ {{ $plan->duration_label }}</span>
    </div>

    <div class="plan-limits">
        <div class="plan-limit-row">
            <span class="plan-limit-key">Trainees</span>
            <span class="plan-limit-val">{{ $plan->max_trainees ? number_format($plan->max_trainees) : '∞ Unlimited' }}</span>
        </div>
        <div class="plan-limit-row">
            <span class="plan-limit-key">Courses</span>
            <span class="plan-limit-val">{{ $plan->max_courses ? number_format($plan->max_courses) : '∞ Unlimited' }}</span>
        </div>
        <div class="plan-limit-row">
            <span class="plan-limit-key">Users</span>
            <span class="plan-limit-val">{{ $plan->max_users ? number_format($plan->max_users) : '∞ Unlimited' }}</span>
        </div>
        <div class="plan-limit-row">
            <span class="plan-limit-key">Trainers</span>
            <span class="plan-limit-val">{{ $plan->max_trainers !== null ? ($plan->max_trainers == 0 ? 'None' : number_format($plan->max_trainers)) : '∞ Unlimited' }}</span>
        </div>
        <div class="plan-limit-row">
            <span class="plan-limit-key">Exports/mo</span>
            <span class="plan-limit-val">
                @if(count($formats) === 0) None
                @elseif($plan->max_exports_monthly === null) ∞ Unlimited
                @else {{ number_format($plan->max_exports_monthly) }}
                @endif
            </span>
        </div>
        @if(count($formats) > 0)
        <div class="plan-limit-row">
            <span class="plan-limit-key">Formats</span>
            <span class="plan-limit-val" style="font-size:11px;">{{ strtoupper(implode(', ', $formats)) }}</span>
        </div>
        @endif
    </div>

    <div class="plan-flags">
        <span class="flag-pill {{ $plan->has_trainers ? 'flag-on' : 'flag-off' }}">👨‍🏫 Trainers</span>
        <span class="flag-pill {{ $plan->has_assessments ? 'flag-on' : 'flag-off' }}">📝 Assessments</span>
        <span class="flag-pill {{ $plan->has_certificates ? 'flag-on' : 'flag-off' }}">🏅 Certificates</span>
        <span class="flag-pill {{ $plan->has_custom_reports ? 'flag-on' : 'flag-off' }}">📊 Custom Reports</span>
        <span class="flag-pill {{ $plan->has_branding ? 'flag-on' : 'flag-off' }}">🎨 Branding</span>
    </div>

    @if($plan->available_from || $plan->available_until)
    <div class="plan-avail">
        <i class="fas fa-calendar-alt"></i>
        {{ $plan->available_from?->format('M d, Y') ?? 'Anytime' }}
        → {{ $plan->available_until?->format('M d, Y') ?? 'No end date' }}
    </div>
    @endif

    <div style="font-size:11px;color:var(--sa-muted);margin-bottom:14px;">
        Sort order: <strong>{{ $plan->sort_order }}</strong>
    </div>

    <div class="plan-card-actions">
        <a href="{{ route('superadmin.plans.manage.edit', $plan) }}" class="btn btn-outline btn-sm" style="flex:1;justify-content:center;">
            <i class="fas fa-pencil-alt"></i> Edit Plan
        </a>
        <form action="{{ route('superadmin.plans.manage.destroy', $plan) }}" method="POST"
              onsubmit="return confirm('Delete the {{ addslashes($plan->name) }} plan? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</div>
