<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In — {{ config('app.name', 'TCMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=playfair-display:700,800i&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #003087;
            --blue:   #0057B8;
            --red:    #CE1126;
            --red-dk: #A50E1E;
            --gold:   #F5C518;
            --light:  #e8f0fb;
            --border: #c5d8f5;
            --text:   #001a4d;
            --muted:  #5a7aaa;
        }

        html, body {
            min-height: 100vh;
            font-family: 'Figtree', sans-serif;
            background: #eef3fb;
            overflow-x: hidden;
        }

        body {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 52px 20px 36px;
            position: relative;
        }

        /* Blobs */
        .bg-blobs { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
        .bg-blobs span { position: absolute; border-radius: 50%; animation: blobFloat 20s ease-in-out infinite; }
        .bg-blobs span:nth-child(1) { width: 700px; height: 700px; top: -200px; left: -150px; background: radial-gradient(circle, rgba(0,87,184,0.09) 0%, transparent 70%); }
        .bg-blobs span:nth-child(2) { width: 550px; height: 550px; bottom: -120px; right: -100px; background: radial-gradient(circle, rgba(206,17,38,0.06) 0%, transparent 70%); animation-delay: -7s; }
        .bg-blobs span:nth-child(3) { width: 350px; height: 350px; top: 40%; left: 35%; background: radial-gradient(circle, rgba(245,197,24,0.05) 0%, transparent 70%); animation-delay: -13s; }
        @keyframes blobFloat {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%       { transform: translate(20px,-30px) scale(1.05); }
            66%       { transform: translate(-15px,22px) scale(0.96); }
        }

        /* Stripe */
        .stripe { position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 200;
            background: linear-gradient(90deg, #CE1126 0%, #CE1126 33%, #0057B8 33%, #0057B8 66%, #F5C518 66%, #F5C518 100%); }

        /* Card */
        .login-card {
            position: relative; z-index: 1;
            width: 100%; max-width: 860px;
            display: grid; grid-template-columns: 5fr 6fr;
            border-radius: 24px; overflow: hidden;
            box-shadow: 0 30px 90px rgba(0,48,135,0.16), 0 4px 20px rgba(0,48,135,0.08);
            animation: cardIn 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes cardIn { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
        @media (max-width: 620px) { .login-card { grid-template-columns: 1fr; } .hero-side { display: none; } }

        /* ── LEFT HERO ── */
        .hero-side {
            background: linear-gradient(155deg, #001a5c 0%, #003087 30%, #0057B8 70%, #006fd6 100%);
            padding: 44px 38px;
            display: flex; flex-direction: column; justify-content: space-between;
            position: relative; overflow: hidden;
        }
        .hero-side::before {
            content: ''; position: absolute; inset: 0;
            background: repeating-linear-gradient(-45deg, transparent, transparent 50px, rgba(255,255,255,0.012) 50px, rgba(255,255,255,0.012) 51px);
        }
        .hero-arc { position: absolute; bottom: -90px; right: -90px; width: 320px; height: 320px; border-radius: 50%; border: 2px solid rgba(245,197,24,0.15); box-shadow: 0 0 0 50px rgba(245,197,24,0.04); pointer-events: none; }
        .hero-dot { position: absolute; top: -50px; left: -50px; width: 180px; height: 180px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,0.06); pointer-events: none; }

        .hero-top { position: relative; z-index: 1; }

        .hero-brand { display: flex; align-items: center; gap: 11px; text-decoration: none; margin-bottom: 44px; }
        .hero-brand-logo { width: 44px; height: 44px; border-radius: 11px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
        .hero-brand-logo img { width: 28px; height: 28px; object-fit: contain; filter: brightness(0) invert(1); }
        .hero-brand-name { font-size: 14px; font-weight: 800; color: #fff; line-height: 1.1; }
        .hero-brand-sub  { font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.5); letter-spacing: 1.4px; text-transform: uppercase; }

        .hero-heading { font-family: 'Playfair Display', Georgia, serif; font-size: 30px; font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 14px; }
        .hero-heading em { font-style: italic; color: var(--gold); }
        .hero-sub { font-size: 12.5px; color: rgba(255,255,255,0.58); line-height: 1.75; margin-bottom: 32px; }

        .hero-perks { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .hero-perk  { display: flex; align-items: center; gap: 10px; font-size: 12px; color: rgba(255,255,255,0.72); font-weight: 500; }
        .perk-pip   { width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 9px; }
        .perk-pip.g { background: rgba(245,197,24,0.18); color: var(--gold); }
        .perk-pip.w { background: rgba(255,255,255,0.10); color: rgba(255,255,255,0.7); }

        .hero-bottom { position: relative; z-index: 1; }
        .tenant-tag  { display: inline-flex; align-items: center; gap: 7px; padding: 6px 14px; border-radius: 20px; background: rgba(245,197,24,0.12); border: 1px solid rgba(245,197,24,0.22); font-size: 11px; font-weight: 700; color: var(--gold); }
        .tenant-tag i { font-size: 9px; }

        /* ── RIGHT FORM ── */
        .form-side { background: #fff; padding: 44px 44px 38px; display: flex; flex-direction: column; }

        .form-eyebrow { font-size: 10px; font-weight: 800; letter-spacing: 2.5px; text-transform: uppercase; color: var(--blue); display: flex; align-items: center; gap: 7px; margin-bottom: 6px; }
        .form-eyebrow::before { content: ''; width: 18px; height: 2px; background: var(--blue); border-radius: 1px; }
        .form-title { font-size: 26px; font-weight: 900; color: var(--text); line-height: 1.18; letter-spacing: -0.4px; margin-bottom: 4px; }
        .form-title span { color: var(--blue); }
        .form-sub   { font-size: 12.5px; color: var(--muted); line-height: 1.6; margin-bottom: 28px; }

        /* Flash */
        .alert-ok { display: flex; align-items: center; gap: 8px; padding: 11px 14px; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 13px; color: #166534; font-weight: 500; margin-bottom: 20px; }

        /* Floating label fields */
        .field { margin-bottom: 14px; }
        .field-float { position: relative; }
        .field-float input {
            width: 100%; padding: 22px 44px 8px 44px;
            border-radius: 12px; border: 1.5px solid var(--border);
            font-family: inherit; font-size: 14px; font-weight: 500;
            color: var(--text); background: #f7faff; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }
        .field-float input::placeholder { color: transparent; }
        .field-float input:focus { border-color: var(--blue); background: #fff; box-shadow: 0 0 0 3.5px rgba(0,87,184,0.10); }
        .field-float input.is-invalid { border-color: var(--red); background: #fff7f7; }
        .field-float input.is-invalid:focus { box-shadow: 0 0 0 3.5px rgba(206,17,38,0.09); }
        .field-float label { position: absolute; left: 44px; top: 50%; transform: translateY(-50%); font-size: 13.5px; font-weight: 600; color: var(--muted); pointer-events: none; transition: all 0.18s cubic-bezier(0.4,0,0.2,1); white-space: nowrap; }
        .field-float input:focus ~ label,
        .field-float input:not(:placeholder-shown) ~ label { top: 11px; transform: none; font-size: 9.5px; font-weight: 800; color: var(--blue); letter-spacing: 0.8px; text-transform: uppercase; }
        .field-float input.is-invalid ~ label,
        .field-float input.is-invalid:focus ~ label { color: var(--red); }
        .fld-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #b0c4e8; pointer-events: none; transition: color 0.18s; }
        .field-float:focus-within .fld-icon { color: var(--blue); }
        .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #b0c4e8; font-size: 13px; padding: 4px; transition: color 0.15s; }
        .toggle-pw:hover { color: var(--blue); }
        .field-error { display: flex; align-items: center; gap: 5px; margin-top: 5px; font-size: 11.5px; color: var(--red); font-weight: 600; padding-left: 4px; animation: errFade 0.2s ease both; }
        @keyframes errFade { from { opacity:0; transform:translateY(-3px); } to { opacity:1; transform:translateY(0); } }
        .field-error i { font-size: 10px; }

        /* Remember row */
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
        .remember-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--blue); border-radius: 4px; cursor: pointer; flex-shrink: 0; }
        .remember-row label { font-size: 12.5px; color: var(--muted); cursor: pointer; user-select: none; }

        /* Submit */
        .btn-login {
            width: 100%; padding: 15px 20px;
            border-radius: 12px; border: none; cursor: pointer;
            font-family: inherit; font-size: 15px; font-weight: 800;
            color: #fff; letter-spacing: 0.2px;
            background: linear-gradient(135deg, #0060cc 0%, #003087 100%);
            box-shadow: 0 5px 22px rgba(0,87,184,0.35), inset 0 1px 0 rgba(255,255,255,0.14);
            transition: all 0.22s cubic-bezier(0.4,0,0.2,1);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-top: 20px; margin-bottom: 16px;
            position: relative; overflow: hidden;
        }
        .btn-login::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 55%); pointer-events: none; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(0,87,184,0.45), inset 0 1px 0 rgba(255,255,255,0.14); }
        .btn-login:active { transform: translateY(0); }
        .btn-login .arrow-box { width: 24px; height: 24px; border-radius: 7px; background: rgba(255,255,255,0.14); display: flex; align-items: center; justify-content: center; font-size: 10px; transition: transform 0.2s; }
        .btn-login:hover .arrow-box { transform: translateX(4px); }

        /* Register link */
        .register-row { text-align: center; font-size: 13px; color: var(--muted); margin-top: 2px; }
        .register-row a { color: var(--blue); font-weight: 700; text-decoration: none; border-bottom: 1.5px solid transparent; transition: color 0.15s, border-color 0.15s; padding-bottom: 1px; }
        .register-row a:hover { color: var(--navy); border-bottom-color: var(--navy); }

        .form-note { margin-top: 18px; padding-top: 14px; border-top: 1px solid #eef3fb; font-size: 10.5px; color: #adc4f0; text-align: center; line-height: 1.65; }

        .page-footer { position: relative; z-index: 1; text-align: center; font-size: 11px; color: #8aa4cc; padding: 18px 0 6px; }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body { background: #04091a; }
            .login-card { box-shadow: 0 30px 90px rgba(0,0,0,0.65); }
            .form-side { background: #08122a; }
            .form-eyebrow { color: #5b9cf6; }
            .form-eyebrow::before { background: #5b9cf6; }
            .form-title { color: #dde8ff; }
            .form-title span { color: #5b9cf6; }
            .form-sub { color: #4a6a9f; }
            .field-float input { background: #0d1f3c; border-color: #1a3060; color: #dde8ff; }
            .field-float input:focus { background: #122550; border-color: #5b9cf6; box-shadow: 0 0 0 3.5px rgba(91,156,246,0.12); }
            .field-float input.is-invalid { background: rgba(206,17,38,0.07); border-color: var(--red); }
            .field-float input:focus ~ label,
            .field-float input:not(:placeholder-shown) ~ label { color: #5b9cf6; }
            .fld-icon { color: #2a4a7a; }
            .field-float:focus-within .fld-icon { color: #5b9cf6; }
            .toggle-pw { color: #2a4a7a; }
            .remember-row label { color: #4a6a9f; }
            .register-row { color: #4a6a9f; }
            .register-row a { color: #5b9cf6; }
            .register-row a:hover { color: #adc4f0; border-bottom-color: #adc4f0; }
            .form-note { border-color: #1a3060; color: #2a4a7a; }
            .page-footer { color: #2a4a7a; }
        }
    </style>
</head>
<body>

    <div class="stripe"></div>
    <div class="bg-blobs"><span></span><span></span><span></span></div>

    @php $tenant = tenancy()->tenant ?? null; @endphp

    <div class="login-card">

        {{-- LEFT HERO --}}
        <div class="hero-side">
            <div class="hero-dot"></div>
            <div class="hero-arc"></div>

            <div class="hero-top">
                <a href="{{ url('/') }}" class="hero-brand">
                    <div class="hero-brand-logo">
                        @if ($tenant?->brand_logo)
                            <img src="{{ asset('storage/' . $tenant->brand_logo) }}" alt="Logo">
                        @else
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="Logo">
                        @endif
                    </div>
                    <div>
                        <div class="hero-brand-name">{{ $tenant?->brand_name ?? $tenant?->name ?? config('app.name', 'TCMS') }}</div>
                        <div class="hero-brand-sub">TESDA Training Management</div>
                    </div>
                </a>

                <div class="hero-heading">
                    Welcome<br>
                    <em>back.</em>
                </div>

                <p class="hero-sub">
                    Sign in to your training portal to continue your courses, check your assessments, and download your certificates.
                </p>

                <ul class="hero-perks">
                    <li class="hero-perk"><div class="perk-pip g"><i class="fas fa-graduation-cap"></i></div> View your enrolled courses</li>
                    <li class="hero-perk"><div class="perk-pip g"><i class="fas fa-clipboard-check"></i></div> Check assessment results</li>
                    <li class="hero-perk"><div class="perk-pip g"><i class="fas fa-certificate"></i></div> Download certificates</li>
                    <li class="hero-perk"><div class="perk-pip w"><i class="fas fa-calendar-alt"></i></div> View attendance records</li>
                </ul>
            </div>

            <div class="hero-bottom">
                @if ($tenant)
                    <div class="tenant-tag">
                        <i class="fas fa-building"></i>
                        {{ $tenant->brand_name ?? $tenant->name }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT FORM --}}
        <div class="form-side">

            <div class="form-eyebrow">Trainee Portal</div>
            <div class="form-title">Sign in to your <span>account</span></div>
            <p class="form-sub">Enter your credentials to access your training dashboard.</p>

            @if (session('status'))
                <div class="alert-ok">
                    <i class="fas fa-circle-check"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="field">
                    <div class="field-float">
                        <i class="fas fa-envelope fld-icon"></i>
                        <input
                            id="email" type="email" name="email"
                            value="{{ old('email') }}"
                            placeholder=" "
                            required autofocus autocomplete="username"
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        >
                        <label for="email">Email Address</label>
                    </div>
                    @error('email')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field">
                    <div class="field-float">
                        <i class="fas fa-lock fld-icon"></i>
                        <input
                            id="password" type="password" name="password"
                            placeholder=" "
                            required autocomplete="current-password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        >
                        <label for="password">Password</label>
                        <button type="button" class="toggle-pw" onclick="togglePw('password','pw-icon')">
                            <i class="fas fa-eye" id="pw-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="remember-row" style="margin-top:4px;">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Keep me signed in</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt" style="font-size:13px;"></i>
                    Sign In
                    <span class="arrow-box"><i class="fas fa-arrow-right"></i></span>
                </button>

                <div style="display:flex;align-items:center;gap:10px;margin:16px 0;">
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                    <span style="font-size:10px;font-weight:800;color:var(--muted);letter-spacing:0.8px;text-transform:uppercase;">or continue with</span>
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                </div>

                <a href="{{ route('auth.google') }}"
                style="
                    display:flex;align-items:center;justify-content:center;gap:10px;
                    width:100%;padding:13px 20px;border-radius:12px;
                    border:1.5px solid var(--border);background:#fff;
                    font-family:inherit;font-size:14px;font-weight:700;color:var(--text);
                    text-decoration:none;cursor:pointer;
                    transition:all 0.22s cubic-bezier(0.4,0,0.2,1);
                    box-shadow:0 2px 8px rgba(0,48,135,0.07);
                "
                onmouseover="this.style.borderColor='#4285F4';this.style.boxShadow='0 4px 16px rgba(66,133,244,0.18)';"
                onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='0 2px 8px rgba(0,48,135,0.07)';"
                >
                    <svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M45.12 24.5c0-1.57-.14-3.08-.4-4.54H24v8.58h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.2z"/>
                        <path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>
                        <path fill="#FBBC05" d="M11.69 28.18C11.25 26.86 11 25.45 11 24s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24c0 3.55.85 6.91 2.34 9.88l7.35-5.7z"/>
                        <path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/>
                    </svg>
                    Sign in with Google
                </a>

                <div class="register-row">
                    Don't have an account?
                    <a href="/register">Create one here</a>
                </div>

            </form>

            <div class="form-note">
                This portal is for
                <strong style="color:#7a9abf;">{{ $tenant?->brand_name ?? $tenant?->name ?? config('app.name') }}</strong>
                trainees only. If you're an admin or trainer, contact your training center administrator.
            </div>

        </div>
    </div>

    <div class="page-footer">
        &copy; {{ date('Y') }} {{ $tenant?->brand_name ?? $tenant?->name ?? config('app.name', 'TCMS') }}
        &nbsp;·&nbsp; Powered by TESDA
    </div>

    <script>
        function togglePw(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            input.type  = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>

</body>
</html>