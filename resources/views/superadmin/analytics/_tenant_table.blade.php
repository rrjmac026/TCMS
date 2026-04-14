<div class="ac">
    <div class="ac-title" style="justify-content:space-between;">
        <span><i class="fas fa-table" style="font-size:12px;color:var(--c-blue);"></i> Tenant activity</span>
        <span style="font-size:12px;color:var(--c-muted);">{{ count($tenantStats) }} approved tenant(s)</span>
    </div>

    @if(count($tenantStats) > 0)
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:0.5px solid var(--c-line);">
                    @foreach(['Organization','Plan','Trainers','Trainees','Courses','Enrollments','Assessments','Certs','Expires',''] as $h)
                    <th style="padding:8px 12px;text-align:left;font-size:11px;font-weight:500;color:var(--c-muted);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($tenantStats as $row)
                @php
                    $t = $row['tenant'];
                    $expired = $t->expires_at && $t->expires_at->isPast();
                @endphp
                <tr style="border-bottom:0.5px solid var(--c-line);" onmouseover="this.style.background='var(--color-background-secondary)'" onmouseout="this.style.background=''">
                    <td style="padding:10px 12px;">
                        <div style="font-weight:500;color:var(--c-ink);">{{ $t->name }}</div>
                        <div style="font-size:11px;color:var(--c-muted);">{{ $t->subdomain }}.tcm.com</div>
                    </td>
                    <td style="padding:10px 12px;">
                        @php
                            $planColors = [
                                'basic'    => 'background:var(--color-background-secondary);color:var(--c-muted);',
                                'standard' => 'background:var(--c-blue-lt);color:var(--c-blue);',
                                'premium'  => 'background:var(--c-gold-lt);color:var(--c-gold);',
                            ];
                            $pStyle = $planColors[$t->subscription] ?? 'background:var(--c-blue-lt);color:var(--c-blue);';
                        @endphp
                        <span class="badge" style="{{ $pStyle }}">{{ ucfirst($t->subscription) }}</span>
                    </td>
                    @foreach(['trainers','trainees','courses','enrollments','assessments','certificates'] as $col)
                    <td style="padding:10px 12px;text-align:center;font-weight:500;color:var(--c-ink2);">{{ $row[$col] }}</td>
                    @endforeach
                    <td style="padding:10px 12px;font-size:12px;color:{{ $expired ? 'var(--c-red)' : 'var(--c-muted)' }};white-space:nowrap;">
                        {{ $t->expires_at ? $t->expires_at->format('M d, Y') : '—' }}
                        @if($expired) <span style="font-weight:500;">(expired)</span> @endif
                    </td>
                    <td style="padding:10px 12px;">
                        <a href="{{ route('superadmin.tenants.show', $t) }}"
                           style="font-size:12px;padding:4px 10px;border-radius:6px;border:0.5px solid var(--c-line);color:var(--c-blue);text-decoration:none;white-space:nowrap;">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:32px;color:var(--c-muted);">
        <i class="fas fa-inbox" style="font-size:28px;opacity:.3;display:block;margin-bottom:8px;"></i>
        No approved tenants yet.
    </div>
    @endif
</div>