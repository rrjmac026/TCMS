<div class="up-plans">
    @foreach($plans as $plan)
        @php
            $planIndex      = array_search($plan->slug, $planSlugs);
            $isCurrent      = $plan->slug === $currentPlan;
            $isFeatured     = $plan->slug === $featuredSlug;
            $canUpgrade     = !$isCurrent && $planIndex > $currentIndex;
            $formattedPrice = number_format($plan->price, 0);

            $features = [];
            $features[] = [
                'label'  => $plan->max_trainees ? 'Up to ' . number_format($plan->max_trainees) . ' trainees' : 'Unlimited trainees',
                'locked' => false,
            ];
            $features[] = [
                'label'  => $plan->max_courses ? 'Up to ' . number_format($plan->max_courses) . ' courses' : 'Unlimited courses',
                'locked' => false,
            ];
            $features[] = ['label' => 'Course & enrollment management', 'locked' => false];
            $features[] = ['label' => 'Attendance tracking',            'locked' => false];
            $features[] = [
                'label'  => $plan->max_users ? ($plan->max_users === 1 ? '1 admin account' : 'Up to ' . $plan->max_users . ' user accounts') : 'Unlimited user accounts',
                'locked' => false,
            ];
            $features[] = ['label' => 'Trainer management',                    'locked' => !$plan->has_trainers];
            $features[] = ['label' => 'Assessments & training schedules',       'locked' => !$plan->has_assessments];

            $exportFormats = $plan->allowed_export_formats ?? [];
            if (count($exportFormats) === 0) {
                $features[] = ['label' => 'Data exports', 'locked' => true];
            } elseif ($plan->max_exports_monthly) {
                $features[] = ['label' => number_format($plan->max_exports_monthly) . ' exports/mo (' . strtoupper(implode(', ', $exportFormats)) . ')', 'locked' => false];
            } else {
                $features[] = ['label' => 'Unlimited exports (' . strtoupper(implode(', ', $exportFormats)) . ')', 'locked' => false];
            }

            $features[] = ['label' => 'Certifications & competency tracking', 'locked' => !$plan->has_certificates];
            $features[] = ['label' => 'Custom reports & analytics',           'locked' => !$plan->has_custom_reports];
            $features[] = ['label' => 'Custom branding',                      'locked' => !$plan->has_branding];
        @endphp

        <div class="up-card {{ $isFeatured && !$isCurrent ? 'featured' : '' }} {{ $isCurrent ? 'current-plan' : '' }}"
             onclick="{{ $canUpgrade ? "selectPlan('{$plan->slug}', '{$plan->name}', '₱{$formattedPrice}', '{$plan->duration_label}')" : '' }}">

            @if($isCurrent)
                <div class="up-current-badge"><i class="fas fa-check"></i> Current</div>
            @elseif($isFeatured && !$isCurrent)
                <div class="up-popular-badge">⭐ Most Popular</div>
            @endif

            <div class="up-card-inner">
                <div class="up-plan-icon">{{ $planIcons[$plan->slug] ?? '📦' }}</div>

                <div class="up-duration-badge">
                    <i class="fas fa-clock"></i> {{ $plan->duration_label }} access
                </div>

                <div class="up-plan-name">{{ $plan->name }}</div>

                <div class="up-plan-price">
                    <span class="up-price-amount">₱{{ $formattedPrice }}</span>
                    <span class="up-price-period">/plan</span>
                </div>

                @if($plan->description)
                    <div class="up-plan-desc">{{ $plan->description }}</div>
                @endif

                <div class="up-card-divider"></div>

                <ul class="up-features">
                    @foreach($features as $feat)
                        <li class="up-feat-item {{ $feat['locked'] ? 'locked' : '' }}">
                            <div class="up-feat-icon">
                                <i class="fas {{ $feat['locked'] ? 'fa-lock' : 'fa-check' }}"></i>
                            </div>
                            {{ $feat['label'] }}
                        </li>
                    @endforeach
                </ul>

                @if($isCurrent)
                    <button class="up-cta-btn current" disabled>
                        <i class="fas fa-check-circle"></i> Current Plan
                    </button>
                @elseif($canUpgrade)
                    <button class="up-cta-btn {{ $isFeatured ? 'on-dark' : 'primary' }}"
                            onclick="event.stopPropagation(); selectPlan('{{ $plan->slug }}', '{{ $plan->name }}', '₱{{ $formattedPrice }}', '{{ $plan->duration_label }}')">
                        <i class="fas fa-arrow-up"></i> Upgrade to {{ $plan->name }}
                    </button>
                @else
                    <button class="up-cta-btn {{ $isFeatured ? 'on-dark' : 'primary' }}" disabled style="opacity:0.4;cursor:not-allowed;">
                        <i class="fas fa-arrow-down"></i> Downgrade Not Allowed
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="up-guarantee">
    <i class="fas fa-shield-check" style="color:#22c55e;"></i>
    Simulation only — no payment required &nbsp;·&nbsp;
    <i class="fas fa-headset" style="color:#0057B8; margin-left:4px;"></i>
    &nbsp;Contact your system administrator for billing
</div>