<div class="plan-grid">
    @foreach($plans as $plan)
        @php
            $badgeClass = match($plan->slug) {
                'standard' => 'badge-standard',
                'premium'  => 'badge-premium',
                default    => 'badge-basic',
            };
            $headerBg = match($plan->slug) {
                'standard' => 'rgba(0,87,184,.04)',
                'premium'  => 'rgba(245,197,24,.06)',
                default    => 'rgba(90,122,170,.04)',
            };
        @endphp

        <div class="plan-card">

            {{-- ── Card Header ── --}}
            <div class="plan-header" style="background:{{ $headerBg }};">
                <div class="flex items-center justify-between mb-2">
                    <span class="plan-slug-badge {{ $badgeClass }}">
                        <i class="fas fa-circle" style="font-size:6px;"></i>
                        {{ strtoupper($plan->slug) }}
                    </span>
                    <span class="status-badge {{ $plan->is_active ? 'sb-success' : 'sb-muted' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="font-bold text-lg mb-1" style="color:var(--sa-primary);">{{ $plan->name }}</div>
                <div class="plan-price">₱{{ number_format($plan->price, 2) }}<span>/ plan</span></div>
                <div class="plan-duration"><i class="fas fa-clock mr-1"></i> {{ $plan->duration_label }} access</div>
                @if($plan->description)
                    <p class="text-xs mt-2" style="color:var(--sa-muted);">{{ $plan->description }}</p>
                @endif
            </div>

            {{-- ── Feature Summary ── --}}
            <div class="plan-body">
                @php
                    $featureRows = [
                        'Trainees'       => $plan->max_trainees ?? '∞',
                        'Trainers'       => $plan->max_trainers === 0 ? '—' : ($plan->max_trainers ?? '∞'),
                        'Users'          => $plan->max_users ?? '∞',
                        'Courses'        => $plan->max_courses ?? '∞',
                    ];
                @endphp

                @foreach($featureRows as $label => $value)
                    <div class="feature-row">
                        <span class="feature-label">{{ $label }}</span>
                        <span class="feature-val">{{ $value }}</span>
                    </div>
                @endforeach

                <div class="feature-row">
                    <span class="feature-label">Exports / mo.</span>
                    <span class="feature-val">
                        @if($plan->max_exports_monthly === 0 || ($plan->max_exports_monthly === null && count($plan->allowed_export_formats ?? []) === 0))
                            None
                        @elseif($plan->max_exports_monthly === null)
                            Unlimited
                        @else
                            {{ number_format($plan->max_exports_monthly) }}
                        @endif
                    </span>
                </div>

                <div class="feature-row">
                    <span class="feature-label">Export formats</span>
                    <span class="feature-val text-xs">
                        {{ count($plan->allowed_export_formats ?? []) === 0 ? '—' : strtoupper(implode(', ', $plan->allowed_export_formats)) }}
                    </span>
                </div>

                @foreach([
                    'Assessments'    => $plan->has_assessments,
                    'Certificates'   => $plan->has_certificates,
                    'Custom Reports' => $plan->has_custom_reports,
                    'Branding'       => $plan->has_branding,
                ] as $label => $enabled)
                    <div class="feature-row">
                        <span class="feature-label">{{ $label }}</span>
                        <span class="feature-val {{ $enabled ? 'feature-yes' : 'feature-no' }}">
                            <i class="fas {{ $enabled ? 'fa-check' : 'fa-times' }}"></i>
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- ── Edit Button ── --}}
            <div class="plan-actions">
                <button class="btn btn-outline w-full" style="justify-content:center;"
                        onclick="toggleEdit({{ $plan->id }})">
                    <i class="fas fa-pencil-alt"></i> Edit Plan
                </button>
            </div>

            {{-- ── Inline Edit Form ── --}}
            <div class="plan-edit-form" id="edit-form-{{ $plan->id }}">
                <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="space-y-3">

                        <div class="form-row">
                            <div class="fi">
                                <label>Plan Name</label>
                                <input type="text" name="name" value="{{ $plan->name }}" required>
                            </div>
                            <div class="fi">
                                <label>Price (₱)</label>
                                <input type="number" name="price" value="{{ $plan->price }}" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="fi">
                            <label>Description</label>
                            <textarea name="description">{{ $plan->description }}</textarea>
                        </div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Duration (days)</label>
                                <input type="number" name="duration_days" value="{{ $plan->duration_days }}" min="1" required>
                            </div>
                            <div class="fi">
                                <label>Max Trainees (blank=∞)</label>
                                <input type="number" name="max_trainees" value="{{ $plan->max_trainees }}" min="0" placeholder="Unlimited">
                            </div>
                        </div>

                        <div class="form-row form-row-3">
                            <div class="fi">
                                <label>Max Trainers</label>
                                <input type="number" name="max_trainers" value="{{ $plan->max_trainers }}" min="0" placeholder="Unlimited">
                            </div>
                            <div class="fi">
                                <label>Max Users</label>
                                <input type="number" name="max_users" value="{{ $plan->max_users }}" min="1" placeholder="Unlimited">
                            </div>
                            <div class="fi">
                                <label>Max Courses</label>
                                <input type="number" name="max_courses" value="{{ $plan->max_courses }}" min="0" placeholder="Unlimited">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="fi">
                                <label>Max Exports / mo.</label>
                                <input type="number" name="max_exports_monthly" value="{{ $plan->max_exports_monthly }}" min="0" placeholder="Unlimited">
                            </div>
                            <div class="fi">
                                <label>Export Formats</label>
                                <div class="check-group">
                                    @foreach(['csv', 'excel', 'pdf'] as $fmt)
                                        <label class="check-item">
                                            <input type="checkbox" name="allowed_export_formats[]" value="{{ $fmt }}"
                                                   {{ in_array($fmt, $plan->allowed_export_formats ?? []) ? 'checked' : '' }}>
                                            {{ strtoupper($fmt) }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="fi">
                            <label>Feature Flags</label>
                            <div class="check-group">
                                @foreach([
                                    'has_assessments'    => 'Assessments',
                                    'has_certificates'   => 'Certificates',
                                    'has_custom_reports' => 'Custom Reports',
                                    'has_branding'       => 'Branding',
                                    'has_trainers'       => 'Trainers',
                                    'is_active'          => 'Active',
                                ] as $field => $label)
                                    <label class="check-item">
                                        <input type="checkbox" name="{{ $field }}" value="1"
                                               {{ $plan->$field ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-outline" onclick="toggleEdit({{ $plan->id }})">
                                Cancel
                            </button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    @endforeach
</div>
