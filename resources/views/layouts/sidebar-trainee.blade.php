{{-- sidebar-trainee.blade.php --}}
<style>
    /* ══════════════════════════════════════════
       SIDEBAR DESIGN TOKENS — TESDA Theme
    ══════════════════════════════════════════ */
    :root {
        --sb-surface:      #ffffff;
        --sb-surface2:     #f0f5ff;
        --sb-border:       #c5d8f5;
        --sb-text:         #001a4d;
        --sb-text-sec:     #1a3a6b;
        --sb-muted:        #5a7aaa;
        --sb-shadow:       0 1px 3px rgba(0,48,135,0.08), 0 1px 2px rgba(0,48,135,0.04);
        --sb-accent:       #0057B8;
        --sb-accent-bg:    #e8f0fb;
        --sb-accent-hover: #dde8f8;
        --sb-accent-dark:  #5b9cf6;
        --sb-red:          #CE1126;
        --sb-red-bg:       #fff0f2;
    }
    .dark {
        --sb-surface:      #0a1628;
        --sb-surface2:     #0d1f3c;
        --sb-border:       #1e3a6b;
        --sb-text:         #dde8ff;
        --sb-text-sec:     #adc4f0;
        --sb-muted:        #6b8abf;
        --sb-accent-bg:    rgba(0,87,184,0.15);
        --sb-accent-hover: rgba(0,87,184,0.10);
        --sb-red-bg:       rgba(206,17,38,0.12);
    }

    /* ── Section label ── */
    .sb-section-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--sb-muted);
        padding: 0 16px;
        margin-bottom: 6px;
        margin-top: 16px;
        display: block;
    }

    /* ── Nav link base ── */
    .sb-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--sb-muted);
        text-decoration: none;
        transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        position: relative;
        overflow: hidden;
    }

    /* icon wrapper */
    .sb-link .sb-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
        background: transparent;
        transition: background 0.18s ease, color 0.18s ease;
    }

    /* hover */
    .sb-link:hover {
        background: var(--sb-surface2);
        color: var(--sb-text-sec);
        box-shadow: var(--sb-shadow);
    }
    .sb-link:hover .sb-icon {
        background: var(--sb-border);
        color: var(--sb-text);
    }

    /* active — TESDA blue accent */
    .sb-link.active {
        background: var(--sb-accent-bg);
        color: var(--sb-accent);
        font-weight: 600;
        box-shadow: 0 1px 4px rgba(0,87,184,0.12);
    }
    .dark .sb-link.active {
        color: var(--sb-accent-dark);
        background: var(--sb-accent-bg);
        box-shadow: 0 1px 6px rgba(0,87,184,0.20);
    }
    .sb-link.active .sb-icon {
        background: rgba(0,87,184,0.12);
        color: var(--sb-accent);
    }
    .dark .sb-link.active .sb-icon {
        background: rgba(0,87,184,0.22);
        color: var(--sb-accent-dark);
    }

    /* active left bar — TESDA blue */
    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%; bottom: 20%;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: var(--sb-accent);
    }
    .dark .sb-link.active::before {
        background: var(--sb-accent-dark);
    }

    /* ── Divider ── */
    .sb-divider {
        height: 1px;
        background: var(--sb-border);
        margin: 12px 16px;
    }
</style>

<nav class="space-y-1 p-3">

    <span class="sb-section-label">Main</span>

    <a href="{{ route('trainee.dashboard') }}"
       class="sb-link {{ request()->routeIs('trainee.dashboard') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-chart-line"></i></span>
        Dashboard
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Courses</span>

    <a href="{{ route('trainee.courses.index') }}"
       class="sb-link {{ request()->routeIs('trainee.courses*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-book-open"></i></span>
        Browse Courses
    </a>

    <a href="{{ route('trainee.enrollments.index') }}"
       class="sb-link {{ request()->routeIs('trainee.enrollments*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-file-signature"></i></span>
        My Enrollments
    </a>

    <a href="{{ route('trainee.schedules.index') }}"
       class="sb-link {{ request()->routeIs('trainee.schedules*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-calendar-alt"></i></span>
        My Schedules
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Progress</span>

    <a href="{{ route('trainee.assessments.index') }}"
       class="sb-link {{ request()->routeIs('trainee.assessments*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-clipboard-check"></i></span>
        Assessments
    </a>

    <a href="{{ route('trainee.certificates.index') }}"
       class="sb-link {{ request()->routeIs('trainee.certificates*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-certificate"></i></span>
        Certificates
    </a>

    <div class="sb-divider"></div>
    <span class="sb-section-label">Account</span>

    <a href="{{ route('profile.edit') }}"
       class="sb-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
        <span class="sb-icon"><i class="fas fa-user-cog"></i></span>
        My Profile
    </a>

</nav>