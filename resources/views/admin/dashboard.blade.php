@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
            <i class="fas fa-gauge-high mr-2" style="color:#CE1126;"></i> Dashboard
        </h1>
        <p class="text-sm mt-1" style="color:#5a7aaa;">
            Welcome back, <span class="font-700" style="color:#0057B8;">{{ Auth::user()->name }}</span>!
            Here's what's happening in your training center.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                [
                    'label' => 'Total Trainers',
                    'value' => $totalTrainers,
                    'icon'  => 'fa-chalkboard-teacher',
                    'color' => '#0057B8',
                    'bg'    => '#e8f0fb',
                    'route' => route('admin.trainers.index'),
                ],
                [
                    'label' => 'Total Trainees',
                    'value' => $totalTrainees,
                    'icon'  => 'fa-users',
                    'color' => '#CE1126',
                    'bg'    => '#fff0f2',
                    'route' => route('admin.trainees.index'),
                ],
                [
                    'label' => 'Active Courses',
                    'value' => $totalCourses,
                    'icon'  => 'fa-book-open',
                    'color' => '#b38a00',
                    'bg'    => 'rgba(245,197,24,0.12)',
                    'route' => route('admin.courses.index'),
                ],
                [
                    'label' => 'Total Enrollments',
                    'value' => $totalEnrollments,
                    'icon'  => 'fa-file-signature',
                    'color' => '#16a34a',
                    'bg'    => '#f0fdf4',
                    'route' => route('admin.enrollments.index'),
                ],
            ];
        @endphp

        @foreach ($stats as $stat)
            <a href="{{ $stat['route'] }}"
               class="rounded-2xl border p-5 transition hover:-translate-y-0.5 hover:shadow-md dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
               style="background:#fff; border-color:#c5d8f5;">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm"
                         style="background:{{ $stat['bg'] }}; color:{{ $stat['color'] }};">
                        <i class="fas {{ $stat['icon'] }}"></i>
                    </div>
                </div>
                <div class="text-2xl font-800 dark:text-white" style="color:#001a4d;">{{ $stat['value'] }}</div>
                <div class="text-xs mt-0.5 font-600" style="color:#5a7aaa;">{{ $stat['label'] }}</div>
            </a>
        @endforeach
    </div>

    {{-- Second Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Pending Enrollments --}}
        <div class="rounded-2xl border p-5 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
             style="background:#fff; border-color:#c5d8f5;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-700" style="color:#003087;">Enrollment Status</h3>
                <i class="fas fa-file-signature text-xs" style="color:#5a7aaa;"></i>
            </div>
            <div class="space-y-3">
                @php
                    $enrollmentStats = [
                        ['label' => 'Pending',   'value' => $pendingEnrollments,   'color' => '#b38a00', 'bg' => 'rgba(245,197,24,0.12)'],
                        ['label' => 'Approved',  'value' => $approvedEnrollments,  'color' => '#16a34a', 'bg' => '#f0fdf4'],
                        ['label' => 'Completed', 'value' => $completedEnrollments, 'color' => '#0057B8', 'bg' => '#e8f0fb'],
                        ['label' => 'Dropped',   'value' => $droppedEnrollments,   'color' => '#CE1126', 'bg' => '#fff0f2'],
                    ];
                @endphp
                @foreach ($enrollmentStats as $e)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background:{{ $e['color'] }};"></div>
                            <span class="text-xs font-600" style="color:#5a7aaa;">{{ $e['label'] }}</span>
                        </div>
                        <span class="text-xs font-700 px-2 py-0.5 rounded-lg"
                              style="background:{{ $e['bg'] }}; color:{{ $e['color'] }};">
                            {{ $e['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Active Schedules --}}
        <div class="rounded-2xl border p-5 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
             style="background:#fff; border-color:#c5d8f5;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-700" style="color:#003087;">Schedules</h3>
                <i class="fas fa-calendar text-xs" style="color:#5a7aaa;"></i>
            </div>
            <div class="space-y-3">
                @php
                    $scheduleStats = [
                        ['label' => 'Upcoming',  'value' => $upcomingSchedules,  'color' => '#0057B8', 'bg' => '#e8f0fb'],
                        ['label' => 'Ongoing',   'value' => $ongoingSchedules,   'color' => '#16a34a', 'bg' => '#f0fdf4'],
                        ['label' => 'Completed', 'value' => $completedSchedules, 'color' => '#5a7aaa', 'bg' => '#f0f5ff'],
                        ['label' => 'Cancelled', 'value' => $cancelledSchedules, 'color' => '#CE1126', 'bg' => '#fff0f2'],
                    ];
                @endphp
                @foreach ($scheduleStats as $s)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background:{{ $s['color'] }};"></div>
                            <span class="text-xs font-600" style="color:#5a7aaa;">{{ $s['label'] }}</span>
                        </div>
                        <span class="text-xs font-700 px-2 py-0.5 rounded-lg"
                              style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
                            {{ $s['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Assessment Results --}}
        <div class="rounded-2xl border p-5 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
             style="background:#fff; border-color:#c5d8f5;">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-700" style="color:#003087;">Assessment Results</h3>
                <i class="fas fa-clipboard-check text-xs" style="color:#5a7aaa;"></i>
            </div>
            <div class="space-y-3">
                @php
                    $assessmentStats = [
                        ['label' => 'Competent',         'value' => $competentCount,       'color' => '#16a34a', 'bg' => '#f0fdf4'],
                        ['label' => 'Not Yet Competent', 'value' => $notYetCompetentCount, 'color' => '#CE1126', 'bg' => '#fff0f2'],
                        ['label' => 'Total Assessed',    'value' => $totalAssessments,     'color' => '#0057B8', 'bg' => '#e8f0fb'],
                    ];
                @endphp
                @foreach ($assessmentStats as $a)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background:{{ $a['color'] }};"></div>
                            <span class="text-xs font-600" style="color:#5a7aaa;">{{ $a['label'] }}</span>
                        </div>
                        <span class="text-xs font-700 px-2 py-0.5 rounded-lg"
                              style="background:{{ $a['bg'] }}; color:{{ $a['color'] }};">
                            {{ $a['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Recent Enrollments --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5;">
        <div class="px-5 py-4 border-b flex items-center justify-between dark:border-[#1e3a6b]"
             style="border-color:#c5d8f5;">
            <h3 class="text-sm font-700" style="color:#003087;">
                <i class="fas fa-clock-rotate-left mr-1" style="color:#CE1126;"></i> Recent Enrollments
            </h3>
            <a href="{{ route('admin.enrollments.index') }}"
               class="text-xs font-600 transition hover:underline" style="color:#0057B8;">
                View all
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#e8f0fb; border-bottom:1px solid #c5d8f5;">
                        <th class="px-5 py-3 text-left text-xs font-700 uppercase tracking-wide" style="color:#0057B8;">Trainee</th>
                        <th class="px-5 py-3 text-left text-xs font-700 uppercase tracking-wide" style="color:#0057B8;">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-700 uppercase tracking-wide" style="color:#0057B8;">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-700 uppercase tracking-wide" style="color:#0057B8;">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-[#1e3a6b]" style="divide-color:#e8f0fb;">
                    @forelse ($recentEnrollments as $enrollment)
                        <tr class="transition hover:bg-[#f0f5ff] dark:hover:bg-[#122550]">
                            <td class="px-5 py-3">
                                <div class="font-600 dark:text-white" style="color:#001a4d;">{{ $enrollment->trainee->name }}</div>
                                <div class="text-xs" style="color:#5a7aaa;">{{ $enrollment->trainee->email }}</div>
                            </td>
                            <td class="px-5 py-3 text-xs font-600" style="color:#1a3a6b;">
                                {{ $enrollment->course->name }}
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $statusStyles = [
                                        'pending'   => ['bg' => 'rgba(245,197,24,0.12)', 'color' => '#b38a00'],
                                        'approved'  => ['bg' => '#f0fdf4',               'color' => '#16a34a'],
                                        'completed' => ['bg' => '#e8f0fb',               'color' => '#0057B8'],
                                        'dropped'   => ['bg' => '#fff0f2',               'color' => '#CE1126'],
                                    ];
                                    $style = $statusStyles[$enrollment->status] ?? ['bg' => '#f0f5ff', 'color' => '#5a7aaa'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-600"
                                      style="background:{{ $style['bg'] }}; color:{{ $style['color'] }};">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs" style="color:#5a7aaa;">
                                {{ $enrollment->enrolled_at?->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-xs" style="color:#5a7aaa;">
                                No recent enrollments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection