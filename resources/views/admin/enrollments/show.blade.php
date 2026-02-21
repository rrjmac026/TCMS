@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.enrollments.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-clipboard-list mr-2" style="color:#CE1126;"></i> Enrollment Details
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">{{ $enrollment->trainee->name }} - {{ $enrollment->course->name }}</p>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        {{-- Header --}}
        <div class="p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6"
             style="background: linear-gradient(135deg, #003087 0%, #0057B8 100%); position:relative; overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
            <div style="position:absolute;bottom:-40px;left:-20px;width:120px;height:120px;border-radius:50%;background:rgba(245,197,24,0.07);"></div>

            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl font-900 text-white flex-shrink-0"
                 style="background:rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.20); position:relative; z-index:1;">
                <i class="fas fa-clipboard-list"></i>
            </div>

            <div style="position:relative;z-index:1;">
                <div class="text-xl font-800 text-white">{{ $enrollment->trainee->name }}</div>
                <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.65);">{{ $enrollment->trainee->email }}</div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @php
                        $statusColors = [
                            'pending' => ['bg' => 'rgba(234,179,8,0.25)', 'color' => '#FFDB05', 'icon' => 'fa-hourglass-half'],
                            'approved' => ['bg' => 'rgba(59,130,246,0.25)', 'color' => '#93c5fd', 'icon' => 'fa-check-circle'],
                            'completed' => ['bg' => 'rgba(34,197,94,0.25)', 'color' => '#86efac', 'icon' => 'fa-check-double'],
                            'dropped' => ['bg' => 'rgba(206,17,38,0.25)', 'color' => '#fca5a5', 'icon' => 'fa-times-circle'],
                        ];
                        $statusColor = $statusColors[$enrollment->status] ?? $statusColors['pending'];
                    @endphp
                    <span class="px-2.5 py-1 rounded-lg text-xs font-700"
                          style="background:{{ $statusColor['bg'] }}; border:1px solid {{ $statusColor['color'] }}; color:{{ $statusColor['color'] }};">
                        <i class="fas {{ $statusColor['icon'] }} mr-1" style="font-size:9px;"></i> {{ ucfirst($enrollment->status) }}
                    </span>
                </div>
            </div>

            <div class="sm:ml-auto flex gap-2" style="position:relative;z-index:1;">
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-700 transition hover:-translate-y-0.5"
                   style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.22); color:#fff;">
                    <i class="fas fa-pen text-xs"></i> Edit
                </a>
            </div>
        </div>

        {{-- Enrollment Info --}}
        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
            @php
                $details = [
                    ['icon' => 'fa-user', 'color' => '#0057B8', 'bg' => '#e8f0fb', 'label' => 'Trainee', 'value' => $enrollment->trainee->name],
                    ['icon' => 'fa-book', 'color' => '#CE1126', 'bg' => '#fff0f2', 'label' => 'Course', 'value' => $enrollment->course->name],
                    ['icon' => 'fa-code', 'color' => '#5a7aaa', 'bg' => '#f0f5ff', 'label' => 'Course Code', 'value' => $enrollment->course->code],
                    ['icon' => 'fa-calendar', 'color' => '#5a7aaa', 'bg' => '#f0f5ff', 'label' => 'Enrolled Date', 'value' => $enrollment->enrolled_at?->format('F d, Y')],
                ];
            @endphp

            @foreach ($details as $d)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs flex-shrink-0"
                         style="background:{{ $d['bg'] }}; color:{{ $d['color'] }};">
                        <i class="fas {{ $d['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">{{ $d['label'] }}</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">{{ $d['value'] ?? '—' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Attendance Records --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5;">

        <div class="p-6 border-b dark:border-[#1e3a6b]" style="border-color:#c5d8f5;">
            <h3 class="text-lg font-800 dark:text-white" style="color:#003087;">
                <i class="fas fa-clipboard-check mr-2" style="color:#CE1126;"></i> Attendance Records
            </h3>
        </div>

        <div class="overflow-x-auto">
            @if ($enrollment->attendanceRecords->count() > 0)
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#e8f0fb; border-bottom:1px solid #c5d8f5;">
                            <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Date</th>
                            <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Status</th>
                            <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-[#1e3a6b]" style="divide-color:#e8f0fb;">
                        @foreach ($enrollment->attendanceRecords as $record)
                            <tr class="transition hover:bg-[#f0f5ff] dark:hover:bg-[#122550]">
                                <td class="px-5 py-4 text-sm font-600" style="color:#001a4d;">
                                    {{ $record->date?->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $attendanceStatus = [
                                            'present' => ['bg' => 'rgba(34,197,94,0.15)', 'color' => '#22c55e', 'icon' => 'fa-check-circle'],
                                            'absent' => ['bg' => 'rgba(206,17,38,0.15)', 'color' => '#CE1126', 'icon' => 'fa-times-circle'],
                                            'late' => ['bg' => 'rgba(234,179,8,0.15)', 'color' => '#eab308', 'icon' => 'fa-clock'],
                                        ];
                                        $attStatus = $attendanceStatus[$record->status] ?? $attendanceStatus['absent'];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-700 inline-block"
                                          style="background:{{ $attStatus['bg'] }}; color:{{ $attStatus['color'] }};">
                                        <i class="fas {{ $attStatus['icon'] }} mr-1" style="font-size:9px;"></i> {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm" style="color:#5a7aaa;">
                                    {{ $record->remarks ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center" style="color:#5a7aaa;">
                    <i class="fas fa-clipboard text-4xl opacity-25 mb-3 block"></i>
                    <p class="font-600">No attendance records yet</p>
                    <p class="text-xs">Attendance will be recorded as training progresses.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Danger zone --}}
    <div class="rounded-2xl border p-6 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#f5c5cb;">
        <h3 class="text-sm font-800 mb-1 flex items-center gap-2" style="color:#CE1126;">
            <i class="fas fa-triangle-exclamation"></i> Danger Zone
        </h3>
        <p class="text-xs mb-4" style="color:#5a7aaa;">Deleting this enrollment is permanent and cannot be undone.</p>
        <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}"
              onsubmit="return confirm('Permanently delete this enrollment for {{ addslashes($enrollment->trainee->name) }}?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#CE1126,#A50E1E); box-shadow:0 3px 12px rgba(206,17,38,0.25);">
                <i class="fas fa-trash"></i> Delete Enrollment
            </button>
        </form>
    </div>

</div>
@endsection
