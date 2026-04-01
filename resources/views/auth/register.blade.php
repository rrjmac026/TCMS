<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register Your Training Center — {{ config('app.name', 'TCMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css','resources/css/auth/register.css', 'resources/js/app.js'])
    @endif


</head>
<body>

    <div class="stripe"></div>

    <!-- Page header -->
    <div class="page-header">
        <a href="{{ url('/') }}" class="header-brand">
            <div class="header-logo">
                <img src="{{ asset('assets/app_logo.PNG') }}" alt="TCMS Logo">
            </div>
            <div>
                <div class="header-brand-name">{{ config('app.name', 'TCMS') }}</div>
                <div class="header-brand-sub">TESDA Training Management</div>
            </div>
        </a>
        <a href="{{ route('login') }}" class="btn-back">
            <i class="fas fa-arrow-left" style="font-size:10px;"></i> Back to Login
        </a>
    </div>

    <!-- Main card -->
    <div class="card">

        <!-- Card top -->
        <div class="card-top">
            <div class="card-top-inner">
                <div class="icon-badge">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-headline">
                    <h1>Register Your Training Center</h1>
                    <p>Apply for a TCMS tenant account. Your registration will be reviewed and approved by a super admin before access is granted.</p>
                </div>
            </div>
            <div class="step-pills">
                <div class="step-pill active"><i class="fas fa-circle-dot"></i> Organization Info</div>
                <div class="step-pill active"><i class="fas fa-circle-dot"></i> Subscription Plan</div>
                <div class="step-pill active"><i class="fas fa-circle-dot"></i> Submit for Review</div>
            </div>
        </div>

        <!-- Card body -->
        <div class="card-body">

            {{-- Flash messages --}}
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-circle-check"></i>
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- ── Section 1: Organization Details ── -->
                <div class="section-header">
                    <div class="section-icon blue"><i class="fas fa-building-columns"></i></div>
                    <div>
                        <div class="section-label">Organization Details</div>
                        <div class="section-sub">Basic information about your training center</div>
                    </div>
                </div>

                <!-- Name + Email -->
                <div class="field-row-2">

                    <div class="field">
                        <label for="name">
                            <span class="required-dot"></span>
                            Organization / Center Name
                        </label>
                        <div class="input-wrap">
                            <i class="fas fa-building field-icon"></i>
                            <input
                                id="name" type="text" name="name"
                                value="{{ old('name') }}"
                                required autofocus autocomplete="organization"
                                placeholder="e.g. MACC Vocational Institute"
                                class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                            >
                        </div>
                        @if ($errors->has('name'))
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    <div class="field">
                        <label for="admin_email">
                            <span class="required-dot"></span>
                            Admin Email Address
                        </label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope field-icon"></i>
                            <input
                                id="admin_email" type="email" name="admin_email"
                                value="{{ old('admin_email') }}"
                                required autocomplete="email"
                                placeholder="admin@yourcenter.com"
                                class="{{ $errors->has('admin_email') ? 'is-invalid' : '' }}"
                            >
                        </div>
                        @if ($errors->has('admin_email'))
                            <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first('admin_email') }}</div>
                        @endif
                        <div class="field-hint"><i class="fas fa-info-circle"></i>Login credentials will be sent here upon approval.</div>
                    </div>

                </div>

                <!-- Subdomain -->
                <div class="field">
                    <label for="subdomain">
                        <span class="required-dot"></span>
                        Preferred Subdomain
                    </label>
                    <div class="input-wrap subdomain-wrap">
                        <i class="fas fa-globe field-icon"></i>
                        <input
                            id="subdomain" type="text" name="subdomain"
                            value="{{ old('subdomain') }}"
                            required autocomplete="off"
                            placeholder="yourcenter"
                            pattern="[a-z0-9\-_]+"
                            oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9\-_]/g, '')"
                            class="{{ $errors->has('subdomain') ? 'is-invalid' : '' }}"
                        >
                        <span class="subdomain-suffix">.tcm.com</span>
                    </div>
                    @if ($errors->has('subdomain'))
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first('subdomain') }}</div>
                    @endif
                    <div class="field-hint"><i class="fas fa-info-circle"></i>Lowercase letters, numbers, hyphens and underscores only. This will be your tenant URL.</div>
                </div>

                <div class="section-divider"></div>

                <!-- ── Section 2: Subscription Plan ── -->
                <div class="section-header">
                    <div class="section-icon gold"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <div class="section-label">Choose a Subscription Plan</div>
                        <div class="section-sub">Select the plan that fits your training center's needs</div>
                    </div>
                </div>

                {{-- Hidden select (keeps validation working) --}}
                <input type="hidden" name="subscription" id="subscription-value" value="{{ old('subscription', '') }}">

                <div class="plan-grid" id="plan-grid">

                    <!-- Basic -->
                    <div class="plan-card {{ old('subscription') === 'basic' ? 'selected' : '' }}" data-plan="basic" onclick="selectPlan('basic')">
                        <div class="plan-check"><i class="fas fa-check"></i></div>
                        <div class="plan-icon basic"><i class="fas fa-seedling"></i></div>
                        <div class="plan-name">Basic</div>
                        <div class="plan-price">Starts at <span>Free</span></div>
                        <ul class="plan-features">
                            <li><i class="fas fa-check"></i> Up to 50 trainees</li>
                            <li><i class="fas fa-check"></i> 5 courses</li>
                            <li><i class="fas fa-check"></i> Basic reports</li>
                            <li class="muted"><i class="fas fa-xmark"></i> PDF/Excel export</li>
                            <li class="muted"><i class="fas fa-xmark"></i> Priority support</li>
                        </ul>
                        <div style="margin-top:10px;">
                            <span style="font-size:10px;background:var(--light);color:var(--blue);padding:3px 8px;border-radius:5px;font-weight:700;">30-day trial</span>
                        </div>
                    </div>

                    <!-- Standard -->
                    <div class="plan-card {{ old('subscription') === 'standard' ? 'selected' : '' }}" data-plan="standard" onclick="selectPlan('standard')">
                        <div class="plan-badge popular">Popular</div>
                        <div class="plan-check"><i class="fas fa-check"></i></div>
                        <div class="plan-icon standard"><i class="fas fa-rocket"></i></div>
                        <div class="plan-name">Standard</div>
                        <div class="plan-price">Up to <span>200</span> trainees</div>
                        <ul class="plan-features">
                            <li><i class="fas fa-check"></i> Up to 200 trainees</li>
                            <li><i class="fas fa-check"></i> 20 courses</li>
                            <li><i class="fas fa-check"></i> Advanced reports</li>
                            <li><i class="fas fa-check"></i> CSV/Excel export</li>
                            <li class="muted"><i class="fas fa-xmark"></i> Priority support</li>
                        </ul>
                        <div style="margin-top:10px;">
                            <span style="font-size:10px;background:#fff0f2;color:var(--red);padding:3px 8px;border-radius:5px;font-weight:700;">6-month access</span>
                        </div>
                    </div>

                    <!-- Premium -->
                    <div class="plan-card {{ old('subscription') === 'premium' ? 'selected selected-premium' : '' }}" data-plan="premium" onclick="selectPlan('premium')">
                        <div class="plan-badge best">Best Value</div>
                        <div class="plan-check"><i class="fas fa-check"></i></div>
                        <div class="plan-icon premium"><i class="fas fa-crown"></i></div>
                        <div class="plan-name">Premium</div>
                        <div class="plan-price">Unlimited trainees</div>
                        <ul class="plan-features">
                            <li><i class="fas fa-check"></i> Unlimited trainees</li>
                            <li><i class="fas fa-check"></i> Unlimited courses</li>
                            <li><i class="fas fa-check"></i> Full analytics</li>
                            <li><i class="fas fa-check"></i> PDF/Excel/CSV export</li>
                            <li><i class="fas fa-check"></i> Priority support</li>
                        </ul>
                        <div style="margin-top:10px;">
                            <span style="font-size:10px;background:rgba(245,197,24,0.14);color:#b38a00;padding:3px 8px;border-radius:5px;font-weight:700;">1-year access</span>
                        </div>
                    </div>

                </div>

                @if ($errors->has('subscription'))
                    <div class="field-error" style="margin-top:8px;"><i class="fas fa-exclamation-circle"></i>{{ $errors->first('subscription') }}</div>
                @endif

                <div class="section-divider"></div>

                <!-- ── Pending approval notice ── -->
                <div class="notice-box">
                    <i class="fas fa-clock"></i>
                    <p>
                        <strong>Your application will be reviewed.</strong>
                        After submitting, a super admin will review and approve your registration. You'll receive your login credentials and tenant URL at your admin email once approved.
                    </p>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="submit-btn">
                    <i class="fas fa-paper-plane" style="font-size:13px;"></i>
                    Submit Application
                </button>

                <div class="login-row">
                    Already have an account?
                    <a href="{{ route('login') }}">Log in here</a>
                </div>

            </form>
        </div>
    </div>

    <div class="page-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'TCMS') }} &nbsp;·&nbsp; Powered by TESDA &nbsp;·&nbsp; All rights reserved.
    </div>

    <script>
        function selectPlan(plan) {
            // Update hidden input
            document.getElementById('subscription-value').value = plan;

            // Reset all cards
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('selected', 'selected-premium');
            });

            // Activate selected
            const selected = document.querySelector('[data-plan="' + plan + '"]');
            if (plan === 'premium') {
                selected.classList.add('selected', 'selected-premium');
            } else {
                selected.classList.add('selected');
            }
        }

        // Restore selection on page load (for validation errors)
        document.addEventListener('DOMContentLoaded', function () {
            const val = document.getElementById('subscription-value').value;
            if (val) selectPlan(val);

            // Subdomain preview feedback
            const subdomainInput = document.getElementById('subdomain');
            subdomainInput.addEventListener('input', function () {
                const suffix = this.closest('.subdomain-wrap').querySelector('.subdomain-suffix');
                if (this.value.length > 0) {
                    suffix.style.color = 'var(--blue)';
                    suffix.style.fontWeight = '700';
                } else {
                    suffix.style.color = '';
                    suffix.style.fontWeight = '';
                }
            });
        });
    </script>
    @if (session('status'))
    <div id="successModal" style="position:fixed;inset:0;z-index:9999;background:rgba(0,20,60,0.55);display:flex;align-items:center;justify-content:center;padding:24px;">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:420px;overflow:hidden;animation:modalIn 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;">
            <div style="height:4px;background:linear-gradient(90deg,#CE1126 0%,#CE1126 33%,#0057B8 33%,#0057B8 66%,#F5C518 66%,#F5C518 100%);"></div>
            <div style="padding:32px 28px 28px;text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:#e9f8ef;border:2px solid #6ee7a0;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fas fa-check" style="font-size:22px;color:#16a34a;"></i>
                </div>
                <h2 style="font-size:18px;font-weight:700;color:#001a4d;margin:0 0 8px;">Application submitted!</h2>
                <p style="font-size:13.5px;color:#5a7aaa;line-height:1.6;margin:0 0 20px;">
                    Your training center registration has been received. A super admin will review your application shortly.
                </p>
                <div style="background:#f0f5ff;border:1px solid #c5d8f5;border-radius:10px;padding:12px 14px;text-align:left;margin-bottom:22px;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#1a3a6b;margin-bottom:6px;">
                        <i class="fas fa-envelope" style="color:#0057B8;width:14px;"></i>
                        Credentials will be sent to your admin email
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#1a3a6b;margin-bottom:6px;">
                        <i class="fas fa-clock" style="color:#0057B8;width:14px;"></i>
                        Review typically takes 1–2 business days
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#1a3a6b;">
                        <i class="fas fa-layer-group" style="color:#0057B8;width:14px;"></i>
                        Your tenant URL will be confirmed upon approval
                    </div>
                </div>
                <a href="{{ url('/') }}" style="display:block;width:100%;padding:12px;border-radius:10px;border:none;font-size:14px;font-weight:700;color:#fff;background:linear-gradient(135deg,#003087 0%,#0057B8 100%);text-decoration:none;margin-bottom:10px;">
                    Back to home page
                </a>
                <a href="{{ route('login') }}" style="display:block;width:100%;padding:11px;border-radius:10px;border:1.5px solid #c5d8f5;font-size:13.5px;font-weight:600;color:#5a7aaa;text-decoration:none;">
                    Go to login
                </a>
            </div>
        </div>
    </div>
        <style>
            @keyframes modalIn {
                from { opacity:0; transform:scale(0.88) translateY(12px); }
                to   { opacity:1; transform:scale(1) translateY(0); }
            }
        </style>
    @endif

</body>
</html>