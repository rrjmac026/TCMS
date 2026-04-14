<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:20px;">

    <div class="kpi">
        <div class="kpi-lbl">Total tenants</div>
        <div class="kpi-val" style="color:var(--c-ink);">{{ $totalTenants }}</div>
    </div>

    <div class="kpi">
        <div class="kpi-lbl">Approved</div>
        <div class="kpi-val" style="color:var(--c-green);">{{ $approvedTenants }}</div>
    </div>

    <div class="kpi">
        <div class="kpi-lbl">Pending</div>
        <div class="kpi-val" style="color:var(--c-amber);">{{ $pendingTenants }}</div>
    </div>

    <div class="kpi">
        <div class="kpi-lbl">Rejected</div>
        <div class="kpi-val" style="color:var(--c-red);">{{ $rejectedTenants }}</div>
    </div>

    <div class="kpi">
        <div class="kpi-lbl">Expired</div>
        <div class="kpi-val" style="color:var(--c-red);">{{ $expiredTenants }}</div>
    </div>

    <div class="kpi">
        <div class="kpi-lbl">Expiring soon</div>
        <div class="kpi-val" style="color:var(--c-amber);">{{ $expiringSoon->count() }}</div>
    </div>

</div>