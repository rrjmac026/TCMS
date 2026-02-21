<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password — {{ config('app.name', 'TCMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
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
            background: #f0f5ff;
            overflow-x: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 15% 10%,  rgba(0,87,184,0.10)  0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 90%,  rgba(206,17,38,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 50% 50%,  rgba(0,48,135,0.04)  0%, transparent 70%);
        }

        /* Tricolor stripe */
        .stripe {
            position: fixed; top: 0; left: 0; right: 0; height: 4px; z-index: 100;
            background: linear-gradient(90deg,
                #CE1126 0%, #CE1126 33%,
                #0057B8 33%, #0057B8 66%,
                #F5C518 66%, #F5C518 100%);
        }

        /* Card */
        .card {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 40px rgba(0,48,135,0.10), 0 2px 8px rgba(0,48,135,0.06);
            overflow: hidden;
        }

        /* Card top */
        .card-top {
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            padding: 32px 36px 28px;
            position: relative; overflow: hidden;
        }
        .card-top::before {
            content: ''; position: absolute; top: -30px; right: -30px;
            width: 140px; height: 140px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .card-top::after {
            content: ''; position: absolute; bottom: -40px; left: -20px;
            width: 120px; height: 120px; border-radius: 50%;
            background: rgba(245,197,24,0.08);
        }

        .card-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; margin-bottom: 20px;
            position: relative; z-index: 1;
        }
        .card-logo {
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.20);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; flex-shrink: 0;
        }
        .card-logo img { width: 30px; height: 30px; object-fit: contain; filter: brightness(0) invert(1); }

        .card-brand-text { line-height: 1.15; }
        .card-brand-name { font-size: 15px; font-weight: 800; color: #fff; letter-spacing: 0.3px; }
        .card-brand-sub  { font-size: 9.5px; font-weight: 500; color: rgba(255,255,255,0.60); letter-spacing: 1.2px; text-transform: uppercase; }

        /* Icon badge */
        .icon-badge {
            width: 52px; height: 52px; border-radius: 14px;
            background: rgba(255,255,255,0.13);
            border: 1px solid rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--gold);
            margin-bottom: 16px;
            position: relative; z-index: 1;
        }

        .card-headline { position: relative; z-index: 1; }
        .card-headline h1 { font-size: 22px; font-weight: 800; color: #fff; line-height: 1.2; }
        .card-headline p  { font-size: 13px; color: rgba(255,255,255,0.62); margin-top: 4px; line-height: 1.55; }

        /* Form body */
        .card-body { padding: 32px 36px 36px; }

        /* Info box */
        .info-box {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 13px 15px; border-radius: 10px; margin-bottom: 24px;
            background: var(--light); border: 1px solid var(--border);
            font-size: 12.5px; color: var(--muted); line-height: 1.55;
        }
        .info-box i { color: var(--blue); font-size: 13px; margin-top: 1px; flex-shrink: 0; }

        /* Session status */
        .alert-status {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-radius: 10px; margin-bottom: 20px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            font-size: 13px; color: #16a34a; font-weight: 500;
        }

        /* Field */
        .field { margin-bottom: 20px; }

        .field label {
            display: block; font-size: 12.5px; font-weight: 700;
            color: var(--text); margin-bottom: 6px; letter-spacing: 0.2px;
        }

        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            font-size: 13px; color: var(--muted); pointer-events: none;
        }

        .field input[type="email"] {
            width: 100%; padding: 11px 14px 11px 38px;
            border-radius: 10px; border: 1.5px solid var(--border);
            font-family: inherit; font-size: 13.5px; color: var(--text);
            background: #fff; outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .field input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0,87,184,0.10);
        }
        .field input.is-invalid { border-color: var(--red); }
        .field input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(206,17,38,0.10); }

        /* Error */
        .field-error {
            display: flex; align-items: center; gap: 5px;
            margin-top: 5px; font-size: 11.5px; color: var(--red); font-weight: 500;
        }
        .field-error i { font-size: 10px; }

        /* Submit */
        .btn-submit {
            width: 100%; padding: 13px;
            border-radius: 10px; border: none; cursor: pointer;
            font-family: inherit; font-size: 15px; font-weight: 700;
            color: #fff; letter-spacing: 0.2px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
            box-shadow: 0 3px 12px rgba(0,87,184,0.25);
            transition: all 0.18s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 20px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,87,184,0.35); }
        .btn-submit:active { transform: translateY(0); }

        /* Back to login */
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            font-size: 13px; color: var(--muted); text-decoration: none;
            transition: color 0.15s;
        }
        .back-link i { font-size: 11px; }
        .back-link span { color: var(--blue); font-weight: 700; }
        .back-link:hover span { color: var(--navy); text-decoration: underline; }

        /* Footer */
        .page-footer {
            margin-top: 20px; font-size: 11px; color: #8aa4cc;
            text-align: center; position: relative; z-index: 1;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            body { background: #060d1f; }
            .card { background: #0d1f3c; border-color: #1e3a6b; box-shadow: 0 8px 40px rgba(0,0,0,0.45); }
            .field label { color: #adc4f0; }
            .field input[type="email"] { background: #0a1628; border-color: #1e3a6b; color: #dde8ff; }
            .field input:focus { border-color: #5b9cf6; box-shadow: 0 0 0 3px rgba(91,156,246,0.12); }
            .input-wrap i { color: #3a5a8a; }
            .info-box { background: #0a1628; border-color: #1e3a6b; color: #6b8abf; }
            .info-box i { color: #5b9cf6; }
            .back-link { color: #4a6a9f; }
            .back-link span { color: #5b9cf6; }
            .page-footer { color: #3a5a8a; }
        }
    </style>
</head>
<body>

    <div class="stripe"></div>

    <div class="card">

        <!-- Card top -->
        <div class="card-top">
            <a href="{{ url('/') }}" class="card-brand">
                <div class="card-logo">
                    <img src="{{ asset('assets/app_logo.PNG') }}" alt="TCMS Logo">
                </div>
                <div class="card-brand-text">
                    <div class="card-brand-name">{{ config('app.name', 'TCMS') }}</div>
                    <div class="card-brand-sub">TESDA Training Management</div>
                </div>
            </a>
            <div class="icon-badge">
                <i class="fas fa-key"></i>
            </div>
            <div class="card-headline">
                <h1>Forgot your password?</h1>
                <p>No worries — we'll send you a reset link right away.</p>
            </div>
        </div>

        <!-- Card body -->
        <div class="card-body">

            <!-- Info message -->
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Enter your registered email address and we will send you a password reset link to choose a new one.
            </div>

            <!-- Session status -->
            @if (session('status'))
                <div class="alert-status">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email -->
                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="you@example.com"
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        >
                    </div>
                    @if ($errors->has('email'))
                        <div class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $errors->first('email') }}
                        </div>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane" style="font-size:13px;"></i>
                    Email Password Reset Link
                </button>

                <!-- Back to login -->
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to <span>Log In</span>
                    </a>
                @endif

            </form>
        </div>
    </div>

    <div class="page-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'TCMS') }} &nbsp;·&nbsp; Powered by TESDA
    </div>

</body>
</html>