<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">

    {{-- Platform-wide totals --}}
    <div class="ac">
        <div class="ac-title">
            <i class="fas fa-globe" style="font-size:13px;color:var(--c-blue);"></i>
            Platform totals
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach([
                ['Trainees',    $platformTotals['trainees'],    'fa-user-graduate'],
                ['Trainers',    $platformTotals['trainers'],    'fa-chalkboard-teacher'],
                ['Courses',     $platformTotals['courses'],     'fa-book'],
                ['Enrollments', $platformTotals['enrollments'], 'fa-clipboard-list'],
                ['Assessments', $platformTotals['assessments'], 'fa-clipboard-check'],
                ['Certificates',$platformTotals['certificates'],'fa-certificate'],
            ] as [$lbl, $val, $ico])
            <div class="pill">
                <span class="pill-v">{{ number_format($val) }}</span>
                <span class="pill-l"><i class="fas {{ $ico }}" style="font-size:10px;"></i> {{ $lbl }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Monthly registrations --}}
    <div class="ac">
        <div class="ac-title">
            <i class="fas fa-calendar-alt" style="font-size:13px;color:var(--c-blue);"></i>
            Monthly registrations
        </div>
        @php
            $maxReg = max(max(array_column($monthlyRegistrations,'count')), 1);
        @endphp
        @foreach($monthlyRegistrations as $m)
        @php $pct = ($m['count'] / $maxReg) * 100; @endphp
        <div class="bar-r">
            <div style="width:52px;font-size:11px;color:var(--c-muted);text-align:right;flex-shrink:0;">{{ $m['label'] }}</div>
            <div class="bar-t">
                <div class="bar-f" style="width:{{ max($pct,2) }}%;"></div>
            </div>
            <div style="width:20px;font-size:11px;font-weight:500;color:var(--c-ink2);">{{ $m['count'] }}</div>
        </div>
        @endforeach
    </div>

</div>