@extends('layouts.app')

@section('title', 'Training Schedules Management')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-calendar-check mr-2" style="color:#CE1126;"></i>
                Training Schedules Management
            </h1>
            <p class="text-sm mt-1" style="color:#5a7aaa;">
                Manage all training schedules and sessions.
            </p>
        </div>
        <a href="{{ route('admin.training-schedules.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-bold shadow transition hover:-translate-y-0.5"
           style="background: linear-gradient(135deg,#CE1126,#A50E1E); box-shadow:0 3px 12px rgba(206,17,38,0.28);">
            <i class="fas fa-plus"></i> Add Schedule
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background:#f0fdf4; border:1px solid #bbf7d0; color:#16a34a;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl border p-5 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5;">
        <form method="GET" action="{{ route('admin.training-schedules.index') }}"
              class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color:#5a7aaa;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by location, course or trainer..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white dark:placeholder-[#3a5a8a]"
                       style="border-color:#c5d8f5; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='#c5d8f5'; this.style.boxShadow='none'">
            </div>
            <select name="status"
                    class="px-4 py-2.5 rounded-xl border text-sm outline-none transition
                           dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                    style="border-color:#c5d8f5; color:#001a4d;"
                    onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                    onblur="this.style.borderColor='#c5d8f5'; this.style.boxShadow='none'">
                <option value="">All Status</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-white text-sm font-bold transition hover:-translate-y-0.5"
                    style="background:linear-gradient(135deg,#0057B8,#003087);">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if (request()->filled('search') || request()->filled('status'))
                <a href="{{ route('admin.training-schedules.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold border transition hover:bg-[#e8f0fb]"
                   style="border-color:#c5d8f5; color:#5a7aaa;">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background:#e8f0fb; border-bottom:1px solid #c5d8f5;">
                        <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">#</th>
                        <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Course</th>
                        <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Trainer</th>
                        <th class="px-5 py-3 text-left font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Location</th>
                        <th class="px-5 py-3 text-center font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Date</th>
                        <th class="px-5 py-3 text-center font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Status</th>
                        <th class="px-5 py-3 text-center font-700 text-xs uppercase tracking-wide" style="color:#0057B8;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-[#1e3a6b]" style="divide-color:#e8f0fb;">
                    @forelse ($schedules as $schedule)
                        <tr class="transition hover:bg-[#f0f5ff] dark:hover:bg-[#122550]">
                            <td class="px-5 py-4 font-mono text-xs" style="color:#5a7aaa;">
                                {{ $schedules->firstItem() + $loop->index }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-800 text-white flex-shrink-0"
                                         style="background:linear-gradient(135deg,#0057B8,#003087);">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <div class="font-700 dark:text-white" style="color:#001a4d;">{{ $schedule->course->name }}</div>
                                        <div class="text-xs" style="color:#5a7aaa;">{{ $schedule->course->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-700 text-white flex-shrink-0"
                                         style="background:linear-gradient(135deg,#F5C518,#E5B505);">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                                        {{ $schedule->trainer->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color:#5a7aaa;">
                                {{ $schedule->location ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center text-xs font-600" style="color:#5a7aaa;">
                                {{ $schedule->start_date->format('M d') }} - {{ $schedule->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-700 inline-block"
                                      style="background:{{ match($schedule->status) {
                                        'upcoming' => 'rgba(0,87,184,0.15)',
                                        'ongoing' => 'rgba(34,197,94,0.15)',
                                        'completed' => 'rgba(107,114,128,0.15)',
                                        'cancelled' => 'rgba(206,17,38,0.15)',
                                        default => 'rgba(107,114,128,0.15)'
                                      } }}; color:{{ match($schedule->status) {
                                        'upcoming' => '#0057B8',
                                        'ongoing' => '#22C55E',
                                        'completed' => '#6B7280',
                                        'cancelled' => '#CE1126',
                                        default => '#6B7280'
                                      } }};">
                                    <i class="fas {{ match($schedule->status) {
                                        'upcoming' => 'fa-clock',
                                        'ongoing' => 'fa-play-circle',
                                        'completed' => 'fa-check-circle',
                                        'cancelled' => 'fa-times-circle',
                                        default => 'fa-circle-question'
                                    } }} mr-1" style="font-size:9px;"></i> {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.training-schedules.show', $schedule) }}"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition hover:scale-110"
                                       style="background:#e8f0fb; color:#0057B8;" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.training-schedules.edit', $schedule) }}"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition hover:scale-110"
                                       style="background:rgba(245,197,24,0.15); color:#b38a00;" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.training-schedules.destroy', $schedule) }}"
                                          onsubmit="return confirm('Delete schedule for {{ addslashes($schedule->course->name) }}? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition hover:scale-110"
                                                style="background:#fff0f2; color:#CE1126;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3" style="color:#5a7aaa;">
                                    <i class="fas fa-calendar text-4xl opacity-25"></i>
                                    <p class="font-600">No training schedules found</p>
                                    <p class="text-xs">Try adjusting your search or filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($schedules->hasPages())
            <div class="px-5 py-4 border-t dark:border-[#1e3a6b]" style="border-color:#c5d8f5;">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
