<div style="background:var(--c-amber-lt);border:0.5px solid var(--c-amber);border-radius:12px;padding:16px 18px;margin-bottom:20px;">
    <div style="font-size:12px;font-weight:500;color:var(--c-amber);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">
        <i class="fas fa-exclamation-triangle" style="font-size:12px;"></i>
        {{ $expiringSoon->count() }} tenant{{ $expiringSoon->count() > 1 ? 's' : '' }} expiring within 7 days
    </div>
    @foreach($expiringSoon as $t)
    <div class="brow" style="border-color:rgba(217,119,6,.2);">
        <div>
            <span style="font-weight:500;color:var(--c-ink);">{{ $t->name }}</span>
            <span style="font-size:12px;color:var(--c-muted);margin-left:8px;">{{ $t->subdomain }}.tcm.com</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:12px;color:var(--c-amber);">{{ $t->expires_at->format('M d, Y') }}</span>
            <a href="{{ route('superadmin.tenants.show', $t) }}"
               style="font-size:12px;padding:4px 12px;border-radius:6px;background:var(--c-amber);color:#fff;text-decoration:none;font-weight:500;">
                Manage
            </a>
        </div>
    </div>
    @endforeach
</div>