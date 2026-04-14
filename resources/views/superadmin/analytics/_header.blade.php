<div style="display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:24px;">
    <div>
        <p style="font-size:12px;color:var(--c-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.6px;">
            Super Admin
        </p>
        <h1 style="font-size:24px;font-weight:500;color:var(--c-ink);margin:0;">
            Platform analytics
        </h1>
        <p style="font-size:13px;color:var(--c-muted);margin-top:4px;">
            as of {{ now()->format('M d, Y · H:i') }}
        </p>
    </div>
    <a href="{{ route('superadmin.dashboard') }}"
       style="font-size:13px;padding:7px 14px;border-radius:8px;border:0.5px solid var(--c-line);color:var(--c-muted);text-decoration:none;">
        ← Dashboard
    </a>
</div>