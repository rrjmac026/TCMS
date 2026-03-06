<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TCMS') }} — Training Center Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:    #003087;
            --blue:    #0057B8;
            --blue-lt: #1a6fd4;
            --red:     #CE1126;
            --red-dk:  #A50E1E;
            --gold:    #F5C518;
            --gold-dk: #d4a800;
            --white:   #ffffff;
            --text:    #001a4d;
            --muted:   #5a7aaa;
            --border:  #c5d8f5;
            --light:   #f0f5ff;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Figtree', sans-serif;
            background: #f0f5ff;
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── Tricolor stripe ── */
        .stripe {
            position: fixed; top: 0; left: 0; right: 0; height: 4px; z-index: 200;
            background: linear-gradient(90deg,
                #CE1126 0%   33.33%,
                #0057B8      33.33% 66.66%,
                #F5C518      66.66% 100%);
        }

        /* ── Background mesh ── */
        .bg-mesh {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 90% 70% at 10% 5%,   rgba(0,87,184,0.09) 0%, transparent 55%),
                radial-gradient(ellipse 70% 60% at 90% 95%,  rgba(206,17,38,0.07) 0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 50% 50%,  rgba(0,48,135,0.04) 0%, transparent 70%),
                radial-gradient(ellipse 40% 30% at 80% 10%,  rgba(245,197,24,0.05) 0%, transparent 50%);
        }

        /* ── NAV ── */
        nav {
            position: fixed; top: 4px; left: 0; right: 0; z-index: 100;
            padding: 0 5%;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(240,245,255,0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(197,216,245,0.60);
        }

        .nav-brand {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .nav-logo {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--navy);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }
        .nav-logo img { width: 24px; height: 24px; object-fit: contain; filter: brightness(0) invert(1); }
        .nav-brand-name { font-size: 16px; font-weight: 800; color: var(--navy); letter-spacing: 0.2px; }
        .nav-brand-sub  { font-size: 9px; font-weight: 600; color: var(--muted); letter-spacing: 1.5px; text-transform: uppercase; }

        .nav-links { display: flex; align-items: center; gap: 8px; }

        .btn-nav-ghost {
            padding: 8px 18px; border-radius: 8px; border: 1.5px solid var(--border);
            font-family: inherit; font-size: 13px; font-weight: 700; color: var(--navy);
            background: transparent; cursor: pointer; text-decoration: none;
            transition: all 0.18s;
        }
        .btn-nav-ghost:hover { border-color: var(--blue); color: var(--blue); background: rgba(0,87,184,0.05); }

        .btn-nav-solid {
            padding: 8px 20px; border-radius: 8px; border: none;
            font-family: inherit; font-size: 13px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, var(--red) 0%, var(--red-dk) 100%);
            cursor: pointer; text-decoration: none;
            box-shadow: 0 2px 10px rgba(206,17,38,0.25);
            transition: all 0.18s;
        }
        .btn-nav-solid:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(206,17,38,0.35); }

        /* ── HERO ── */
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            padding: 120px 5% 80px;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(0,48,135,0.08);
            border: 1px solid rgba(0,87,184,0.20);
            color: var(--blue);
            font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            padding: 5px 14px; border-radius: 20px;
            margin-bottom: 28px;
            opacity: 0; animation: fadeUp 0.6s 0.1s ease forwards;
        }

        .hero-title {
            font-size: clamp(38px, 6vw, 72px);
            font-weight: 900;
            color: var(--navy);
            line-height: 1.08;
            letter-spacing: -1.5px;
            max-width: 820px;
            margin-bottom: 24px;
            opacity: 0; animation: fadeUp 0.6s 0.2s ease forwards;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--red) 0%, var(--blue) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: clamp(15px, 2vw, 18px);
            color: var(--muted);
            line-height: 1.7;
            max-width: 560px;
            margin-bottom: 44px;
            opacity: 0; animation: fadeUp 0.6s 0.3s ease forwards;
        }

        .hero-actions {
            display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap;
            margin-bottom: 72px;
            opacity: 0; animation: fadeUp 0.6s 0.4s ease forwards;
        }

        .btn-hero-primary {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 15px 32px; border-radius: 12px; border: none;
            font-family: inherit; font-size: 15px; font-weight: 800; color: #fff;
            background: linear-gradient(135deg, var(--red) 0%, var(--red-dk) 100%);
            box-shadow: 0 4px 20px rgba(206,17,38,0.30);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(206,17,38,0.42); }
        .btn-hero-primary:active { transform: translateY(-1px); }

        .btn-hero-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 15px 28px; border-radius: 12px;
            border: 2px solid var(--border);
            font-family: inherit; font-size: 15px; font-weight: 700; color: var(--navy);
            background: rgba(255,255,255,0.80);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-hero-secondary:hover { border-color: var(--blue); color: var(--blue); background: rgba(255,255,255,1); transform: translateY(-2px); }

        /* Stats strip */
        .hero-stats {
            display: flex; align-items: center; justify-content: center; gap: 40px; flex-wrap: wrap;
            opacity: 0; animation: fadeUp 0.6s 0.5s ease forwards;
        }

        .stat-item { text-align: center; }
        .stat-num  { font-size: 28px; font-weight: 900; color: var(--navy); letter-spacing: -1px; line-height: 1; }
        .stat-num span { color: var(--red); }
        .stat-label { font-size: 11px; font-weight: 600; color: var(--muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }

        .stat-divider { width: 1px; height: 40px; background: var(--border); }

        /* ── FEATURES ── */
        .features {
            position: relative; z-index: 1;
            padding: 80px 5%;
            background: #fff;
        }

        .section-label {
            text-align: center;
            font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
            color: var(--blue); margin-bottom: 12px;
        }
        .section-title {
            text-align: center;
            font-size: clamp(26px, 4vw, 40px); font-weight: 900;
            color: var(--navy); letter-spacing: -0.8px;
            margin-bottom: 14px;
        }
        .section-sub {
            text-align: center; font-size: 15px; color: var(--muted); line-height: 1.7;
            max-width: 500px; margin: 0 auto 56px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            max-width: 1100px; margin: 0 auto;
        }

        .feature-card {
            background: var(--light);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 28px 32px;
            transition: all 0.22s;
            cursor: default;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,48,135,0.10);
            border-color: rgba(0,87,184,0.30);
            background: #fff;
        }

        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 18px;
        }
        .icon-blue   { background: rgba(0,87,184,0.10); color: var(--blue); }
        .icon-red    { background: rgba(206,17,38,0.10); color: var(--red); }
        .icon-gold   { background: rgba(245,197,24,0.15); color: var(--gold-dk); }
        .icon-navy   { background: rgba(0,48,135,0.10); color: var(--navy); }
        .icon-green  { background: rgba(22,163,74,0.10); color: #16a34a; }
        .icon-purple { background: rgba(124,58,237,0.10); color: #7c3aed; }

        .feature-title { font-size: 16px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }
        .feature-desc  { font-size: 13.5px; color: var(--muted); line-height: 1.65; }

        /* ── ROLES ── */
        .roles {
            position: relative; z-index: 1;
            padding: 80px 5%;
            background: var(--light);
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            max-width: 900px; margin: 0 auto;
        }

        .role-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px 28px;
            text-align: center;
            transition: all 0.22s;
        }
        .role-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,48,135,0.10); }

        .role-avatar {
            width: 64px; height: 64px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin: 0 auto 16px;
        }
        .avatar-admin   { background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%); color: #fff; }
        .avatar-trainer { background: linear-gradient(135deg, var(--red) 0%, #ff6b6b 100%); color: #fff; }
        .avatar-trainee { background: linear-gradient(135deg, var(--gold-dk) 0%, var(--gold) 100%); color: var(--navy); }

        .role-name  { font-size: 17px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }
        .role-desc  { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 20px; }

        .role-perms { list-style: none; text-align: left; }
        .role-perms li {
            font-size: 12.5px; color: var(--text);
            padding: 5px 0; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .role-perms li:last-child { border-bottom: none; }
        .role-perms li i { color: #16a34a; font-size: 11px; flex-shrink: 0; }

        /* ── CTA ── */
        .cta {
            position: relative; z-index: 1;
            padding: 80px 5%;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            text-align: center;
            overflow: hidden;
        }

        .cta::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 300px; height: 300px; border-radius: 50%;
            background: rgba(245,197,24,0.07);
        }
        .cta::after {
            content: ''; position: absolute; bottom: -80px; left: -40px;
            width: 250px; height: 250px; border-radius: 50%;
            background: rgba(206,17,38,0.08);
        }

        .cta-title {
            font-size: clamp(26px, 4vw, 42px); font-weight: 900;
            color: #fff; letter-spacing: -0.8px;
            margin-bottom: 14px; position: relative; z-index: 1;
        }
        .cta-sub {
            font-size: 16px; color: rgba(255,255,255,0.65);
            margin-bottom: 36px; line-height: 1.7;
            position: relative; z-index: 1;
        }
        .cta-actions {
            display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap;
            position: relative; z-index: 1;
        }

        .btn-cta-gold {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 14px 32px; border-radius: 12px; border: none;
            font-family: inherit; font-size: 15px; font-weight: 800;
            color: var(--navy);
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dk) 100%);
            box-shadow: 0 4px 20px rgba(245,197,24,0.35);
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-cta-gold:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(245,197,24,0.50); }

        .btn-cta-outline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 28px; border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.30);
            font-family: inherit; font-size: 15px; font-weight: 700; color: #fff;
            background: transparent;
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
        }
        .btn-cta-outline:hover { border-color: rgba(255,255,255,0.70); background: rgba(255,255,255,0.08); }

        /* ── FOOTER ── */
        footer {
            position: relative; z-index: 1;
            background: var(--navy);
            padding: 32px 5%;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        }

        .footer-brand { display: flex; align-items: center; gap: 10px; }
        .footer-logo {
            width: 30px; height: 30px; border-radius: 6px;
            background: rgba(255,255,255,0.10);
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .footer-logo img { width: 20px; height: 20px; object-fit: contain; filter: brightness(0) invert(1); }
        .footer-name { font-size: 13px; font-weight: 800; color: #fff; }
        .footer-sub  { font-size: 10px; color: rgba(255,255,255,0.40); letter-spacing: 1px; text-transform: uppercase; }

        .footer-copy { font-size: 12px; color: rgba(255,255,255,0.35); }

        .footer-links { display: flex; gap: 20px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.40); text-decoration: none; transition: color 0.15s; }
        .footer-links a:hover { color: rgba(255,255,255,0.80); }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .reveal {
            opacity: 0; transform: translateY(24px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .hero-stats { gap: 20px; }
            .stat-divider { display: none; }
            nav { padding: 0 4%; }
            .btn-nav-ghost { display: none; }
        }
    </style>
</head>
<body>

<div class="bg-mesh"></div>
<div class="stripe"></div>

<!-- NAV -->
<nav>
    <a href="{{ url('/') }}" class="nav-brand">
        <div class="nav-logo">
            <img src="{{ asset('assets/app_logo.PNG') }}" alt="Logo">
        </div>
        <div>
            <div class="nav-brand-name">{{ config('app.name', 'TCMS') }}</div>
            <div class="nav-brand-sub">Training Center</div>
        </div>
    </a>
    <div class="nav-links">
        @auth
            <a href="{{ url('/') }}" class="btn-nav-ghost">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn-nav-ghost">Log In</a>
            <a href="{{ route('login') }}" class="btn-nav-solid">
                <i class="fas fa-sign-in-alt" style="font-size:11px;"></i>
                Get Started
            </a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">
        <i class="fas fa-shield-halved"></i>
        TESDA Accredited Training Management
    </div>

    <h1 class="hero-title">
        Your Training Center,<br>
        <span>Fully Managed.</span>
    </h1>

    <p class="hero-sub">
        A complete platform for TESDA-accredited training centers. Manage trainers, trainees, courses, schedules, assessments, and certificates — all in one place.
    </p>

    <div class="hero-actions">
        @auth
            <a href="{{ url('/') }}" class="btn-hero-primary">
                <i class="fas fa-tachometer-alt"></i>
                Go to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-hero-primary">
                <i class="fas fa-sign-in-alt"></i>
                Sign In to Portal
            </a>
            <a href="#features" class="btn-hero-secondary">
                <i class="fas fa-info-circle"></i>
                Learn More
            </a>
        @endauth
    </div>

    <div class="hero-stats">
        <div class="stat-item">
            <div class="stat-num">3<span>+</span></div>
            <div class="stat-label">User Roles</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-num">360<span>°</span></div>
            <div class="stat-label">Management</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-num">100<span>%</span></div>
            <div class="stat-label">TESDA Aligned</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <div class="stat-num">24<span>/7</span></div>
            <div class="stat-label">Online Access</div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features" id="features">
    <p class="section-label reveal">What's Inside</p>
    <h2 class="section-title reveal">Everything your training center needs</h2>
    <p class="section-sub reveal">Built specifically for TESDA-accredited centers with all the tools to run your operations smoothly.</p>

    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="feature-title">Trainer & Trainee Management</div>
            <div class="feature-desc">Onboard staff and students easily. Manage profiles, assignments, and track progress from a central dashboard.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon icon-red"><i class="fas fa-book-open"></i></div>
            <div class="feature-title">Course Management</div>
            <div class="feature-desc">Create and manage TESDA-aligned courses. Handle enrollments, set prerequisites, and track completion rates.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon icon-gold"><i class="fas fa-calendar-alt"></i></div>
            <div class="feature-title">Training Schedules</div>
            <div class="feature-desc">Plan and publish training schedules. Trainers and trainees can view their upcoming sessions at any time.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon icon-green"><i class="fas fa-clipboard-check"></i></div>
            <div class="feature-title">Attendance Tracking</div>
            <div class="feature-desc">Record and monitor attendance per session. Generate attendance reports for compliance and audits.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon icon-purple"><i class="fas fa-tasks"></i></div>
            <div class="feature-title">Assessments</div>
            <div class="feature-desc">Create assessments, record scores, and track trainee competency levels aligned with TESDA standards.</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon icon-navy"><i class="fas fa-certificate"></i></div>
            <div class="feature-title">Certificate Management</div>
            <div class="feature-desc">Generate, preview, and issue certificates of completion. Trainees can download their certificates directly.</div>
        </div>
    </div>
</section>

<!-- ROLES -->
<section class="roles">
    <p class="section-label reveal">Who Uses It</p>
    <h2 class="section-title reveal">Three roles, one platform</h2>
    <p class="section-sub reveal">Each user gets a tailored experience with access to exactly what they need.</p>

    <div class="roles-grid">
        <div class="role-card reveal">
            <div class="role-avatar avatar-admin"><i class="fas fa-user-shield"></i></div>
            <div class="role-name">Admin</div>
            <div class="role-desc">Full control over the training center's operations, staff, and data.</div>
            <ul class="role-perms">
                <li><i class="fas fa-check"></i> Manage trainers & trainees</li>
                <li><i class="fas fa-check"></i> Create courses & schedules</li>
                <li><i class="fas fa-check"></i> Issue certificates</li>
                <li><i class="fas fa-check"></i> View all reports</li>
            </ul>
        </div>
        <div class="role-card reveal">
            <div class="role-avatar avatar-trainer"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="role-name">Trainer</div>
            <div class="role-desc">Handle day-to-day training, attendance, and assessments for assigned sessions.</div>
            <ul class="role-perms">
                <li><i class="fas fa-check"></i> View assigned schedules</li>
                <li><i class="fas fa-check"></i> Record attendance</li>
                <li><i class="fas fa-check"></i> Manage assessments</li>
                <li><i class="fas fa-check"></i> View trainee progress</li>
            </ul>
        </div>
        <div class="role-card reveal">
            <div class="role-avatar avatar-trainee"><i class="fas fa-user-graduate"></i></div>
            <div class="role-name">Trainee</div>
            <div class="role-desc">Access personal training journey — from enrollment all the way to certification.</div>
            <ul class="role-perms">
                <li><i class="fas fa-check"></i> Enroll in courses</li>
                <li><i class="fas fa-check"></i> View schedules</li>
                <li><i class="fas fa-check"></i> Check assessment results</li>
                <li><i class="fas fa-check"></i> Download certificates</li>
            </ul>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <h2 class="cta-title">Ready to get started?</h2>
    <p class="cta-sub">Log in to your training center portal and manage everything from one place.</p>
    <div class="cta-actions">
        @auth
            <a href="{{ url('/') }}" class="btn-cta-gold">
                <i class="fas fa-tachometer-alt"></i>
                Go to Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="btn-cta-gold">
                <i class="fas fa-sign-in-alt"></i>
                Sign In Now
            </a>
            <a href="#features" class="btn-cta-outline">
                <i class="fas fa-arrow-up"></i>
                Back to Top
            </a>
        @endauth
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-brand">
        <div class="footer-logo">
            <img src="{{ asset('assets/app_logo.PNG') }}" alt="Logo">
        </div>
        <div>
            <div class="footer-name">{{ config('app.name', 'TCMS') }}</div>
            <div class="footer-sub">Powered by TESDA</div>
        </div>
    </div>
    <div class="footer-copy">&copy; {{ date('Y') }} All rights reserved.</div>
    <div class="footer-links">
        <a href="{{ route('login') }}">Log In</a>
    </div>
</footer>

<script>
    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => entry.target.classList.add('visible'), i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>

</body>
</html>