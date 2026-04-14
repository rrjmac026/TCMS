<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">

    {{-- Renewal Requests --}}
    <div class="ac">
        <div class="ac-title" style="justify-content:space-between;">
            <span><i class="fas fa-sync-alt" style="font-size:12px;color:var(--c-amber);"></i> Renewal requests</span>
            <a href="{{ route('superadmin.renewals.index') }}" style="font-size:11px;color:var(--c-blue);text-decoration:none;font-weight:500;">View all →</a>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <div class="pill">
                <span class="pill-v" style="color:var(--c-amber);">{{ $renewalStats['pending'] }}</span>
                <span class="pill-l">Pending</span>
            </div>
            <div class="pill">
                <span class="pill-v" style="color:var(--c-green);">{{ $renewalStats['approved'] }}</span>
                <span class="pill-l">Approved</span>
            </div>
            <div class="pill">
                <span class="pill-v" style="color:var(--c-red);">{{ $renewalStats['rejected'] }}</span>
                <span class="pill-l">Rejected</span>
            </div>
            <div class="pill">
                <span class="pill-v" style="color:var(--c-muted);">{{ $renewalStats['cancelled_by_upgrade'] }}</span>
                <span class="pill-l">Cancelled</span>
            </div>
        </div>

        @if($pendingRenewals->isEmpty())
            <p style="font-size:13px;color:var(--c-muted);text-align:center;padding:12px 0;">
                No pending renewal requests.
            </p>
        @else
            @foreach($pendingRenewals as $rr)
            <div class="brow">
                <div>
                    <span style="font-weight:500;">{{ $rr->tenant->name ?? '—' }}</span>
                    <span style="font-size:12px;color:var(--c-muted);margin-left:6px;">
                        {{ ucfirst($rr->plan_slug) }} · {{ $rr->duration_days }}d · ₱{{ number_format($rr->final_price, 2) }}
                    </span>
                </div>
                <a href="{{ route('superadmin.renewals.index') }}"
                   style="font-size:12px;padding:3px 10px;border-radius:6px;background:var(--c-blue);color:#fff;text-decoration:none;font-weight:500;">
                    Review
                </a>
            </div>
            @endforeach
        @endif
    </div>

    {{-- DB & File Storage --}}
    <div class="ac">
        <div class="ac-title">
            <i class="fas fa-database" style="font-size:12px;color:var(--c-blue);"></i>
            Storage
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <div class="pill">
                <span class="pill-v">{{ \App\Models\TenantUsageStat::formatBytes($storageAggregates['db_bytes']) }}</span>
                <span class="pill-l">DB total</span>
            </div>
            <div class="pill">
                <span class="pill-v">{{ \App\Models\TenantUsageStat::formatBytes($storageAggregates['file_bytes']) }}</span>
                <span class="pill-l">Files total</span>
            </div>
            <div class="pill">
                <span class="pill-v" style="color:var(--c-blue);">{{ \App\Models\TenantUsageStat::formatBytes($storageAggregates['total_bytes']) }}</span>
                <span class="pill-l">Combined</span>
            </div>
        </div>

        <div style="font-size:11px;color:var(--c-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Top consumers</div>
        @foreach($topDbStorage as $stat)
        @php $pct = $maxDbBytes > 0 ? ($stat->db_size_bytes / $maxDbBytes) * 100 : 0; @endphp
        <div class="bar-r">
            <div style="width:100px;font-size:12px;color:var(--c-ink2);flex-shrink:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                {{ $stat->tenant->name ?? '—' }}
            </div>
            <div class="bar-t">
                <div class="bar-f" style="width:{{ max($pct,2) }}%;"></div>
            </div>
            <div style="width:52px;font-size:11px;color:var(--c-muted);text-align:right;flex-shrink:0;">
                {{ $stat->formatted_db_size }}
            </div>
        </div>
        @endforeach
    </div>

</div>