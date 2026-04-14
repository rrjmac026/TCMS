<div class="ac" style="margin-bottom:20px;">
    <div class="ac-title" style="justify-content:space-between;">
        <span><i class="fas fa-layer-group" style="font-size:12px;color:var(--c-gold);"></i> Subscription plans</span>
        <span style="font-size:12px;color:var(--c-muted);">
            {{ $allPlans->count() }} defined · {{ $allPlans->where('is_active', true)->count() }} active ·
            <a href="{{ route('superadmin.plans.index') }}" style="color:var(--c-blue);text-decoration:none;font-weight:500;">Manage →</a>
        </span>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:10px;">
        @foreach($allPlans as $plan)
        @php $count = $tenantCountByPlan[$plan->slug] ?? 0; @endphp
        <div class="pcard" style="{{ $plan->is_active ? 'border-color:var(--c-gold);' : 'opacity:.7;' }}">

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <span style="font-size:14px;font-weight:500;color:var(--c-ink);">
                    {{ $plan->icon }} {{ $plan->name }}
                </span>
                <span class="badge" style="{{ $plan->is_active
                    ? 'background:var(--c-green-lt);color:var(--c-green);'
                    : 'background:var(--color-background-secondary);color:var(--c-muted);' }}">
                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div style="font-size:20px;font-weight:500;color:var(--c-blue);margin-bottom:4px;">
                {{ $plan->formatted_price }}
            </div>

            <div style="font-size:12px;color:var(--c-muted);margin-bottom:8px;">
                {{ $plan->duration_label }} ·
                trainees: {{ $plan->max_trainees ?? '∞' }} ·
                trainers: {{ $plan->max_trainers ?? '∞' }}
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px;">
                @if($plan->has_assessments)
                    <span class="badge" style="background:var(--c-blue-lt);color:var(--c-blue);">Assessments</span>
                @endif
                @if($plan->has_certificates)
                    <span class="badge" style="background:var(--c-gold-lt);color:var(--c-gold);">Certificates</span>
                @endif
                @if($plan->has_branding)
                    <span class="badge" style="background:var(--c-green-lt);color:var(--c-green);">Branding</span>
                @endif
                @if($plan->has_custom_reports)
                    <span class="badge" style="background:var(--c-red-lt);color:var(--c-red);">Custom reports</span>
                @endif
                @if($plan->has_trainers)
                    <span class="badge" style="background:var(--color-background-secondary);color:var(--c-muted);">Trainers</span>
                @endif
            </div>

            <div style="border-top:0.5px solid var(--c-line);padding-top:8px;font-size:12px;color:var(--c-muted);">
                <span style="font-size:16px;font-weight:500;color:var(--c-ink);">{{ $count }}</span>
                tenant{{ $count !== 1 ? 's' : '' }} on this plan
            </div>

        </div>
        @endforeach
    </div>
</div>