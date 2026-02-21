@extends('layouts.app')

@section('title', 'Training Schedule Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.training-schedules.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-calendar-check mr-2" style="color:#CE1126;"></i> Schedule Details
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Viewing details for {{ $trainingSchedule->course->name }}</p>
        </div>
    </div>

    {{-- Schedule Card --}}
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
                <i class="fas fa-calendar-check"></i>
            </div>

            <div style="position:relative;z-index:1;">
                <div class="text-xl font-800 text-white">{{ $trainingSchedule->course->name }}</div>
                <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.65);">{{ $trainingSchedule->course->code }}</div>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-700"
                          style="background:{{ match($trainingSchedule->status) {
                            'upcoming' => 'rgba(0,87,184,0.25)',
                            'ongoing' => 'rgba(34,197,94,0.25)',
                            'completed' => 'rgba(107,114,128,0.25)',
                            'cancelled' => 'rgba(206,17,38,0.25)',
                            default => 'rgba(107,114,128,0.25)'
                          } }}; border:1px solid {{ match($trainingSchedule->status) {
                            'upcoming' => 'rgba(0,87,184,0.40)',
                            'ongoing' => 'rgba(34,197,94,0.40)',
                            'completed' => 'rgba(107,114,128,0.40)',
                            'cancelled' => 'rgba(206,17,38,0.40)',
                            default => 'rgba(107,114,128,0.40)'
                          } }}; color:{{ match($trainingSchedule->status) {
                            'upcoming' => '#fff',
                            'ongoing' => '#fff',
                            'completed' => '#fff',
                            'cancelled' => '#fff',
                            default => '#fff'
                          } }};">
                        <i class="fas {{ match($trainingSchedule->status) {
                            'upcoming' => 'fa-clock',
                            'ongoing' => 'fa-play-circle',
                            'completed' => 'fa-check-circle',
                            'cancelled' => 'fa-times-circle',
                            default => 'fa-circle-question'
                        } }} mr-1" style="font-size:9px;"></i> {{ ucfirst($trainingSchedule->status) }}
                    </span>
                </div>
            </div>

            <div class="sm:ml-auto flex gap-2" style="position:relative;z-index:1;">
                <a href="{{ route('admin.training-schedules.edit', $trainingSchedule) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-700 transition hover:-translate-y-0.5"
                   style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.22); color:#fff;">
                    <i class="fas fa-pen text-xs"></i> Edit
                </a>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-8 space-y-8">
            {{-- Course & Trainer Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Course --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:#e8f0fb; color:#0057B8;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">Course</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->course->name }}
                        </div>
                        <div class="text-xs mt-1" style="color:#5a7aaa;">
                            {{ $trainingSchedule->course->code }} • {{ $trainingSchedule->course->duration_hours }} hours
                        </div>
                    </div>
                </div>

                {{-- Trainer --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:rgba(245,197,24,0.15); color:#F5C518;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">Trainer</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->trainer->name }}
                        </div>
                        <div class="text-xs mt-1" style="color:#5a7aaa;">
                            @if ($trainingSchedule->trainer->email)
                                {{ $trainingSchedule->trainer->email }}
                            @else
                                No email provided
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border-color:#c5d8f5; margin:0;">

            {{-- Schedule Times --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Start Date --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:#fff0f2; color:#CE1126;">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">Start Date</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->start_date->format('F d, Y') }}
                        </div>
                        <div class="text-xs mt-1" style="color:#5a7aaa;">
                            {{ $trainingSchedule->start_date->format('l') }}
                        </div>
                    </div>
                </div>

                {{-- End Date --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:#f0fdf4; color:#16a34a;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">End Date</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->end_date->format('F d, Y') }}
                        </div>
                        <div class="text-xs mt-1" style="color:#5a7aaa;">
                            {{ $trainingSchedule->end_date->format('l') }}
                        </div>
                    </div>
                </div>

                {{-- Start Time --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:#e8f0fb; color:#0057B8;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">Start Time</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ \Carbon\Carbon::parse($trainingSchedule->time_start)->format('h:i A') }}
                        </div>
                    </div>
                </div>

                {{-- End Time --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:#e8f0fb; color:#0057B8;">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">End Time</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ \Carbon\Carbon::parse($trainingSchedule->time_end)->format('h:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border-color:#c5d8f5; margin:0;">

            {{-- Location & Additional Info --}}
            <div class="grid grid-cols-1 gap-6">
                {{-- Location --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm flex-shrink-0"
                         style="background:rgba(245,197,24,0.15); color:#F5C518;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xs font-700 uppercase tracking-wide mb-0.5" style="color:#5a7aaa;">Location</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->location ?? 'Not specified' }}
                        </div>
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <div class="text-xs font-700 uppercase tracking-wide mb-1" style="color:#5a7aaa;">Created</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->created_at->format('F d, Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-700 uppercase tracking-wide mb-1" style="color:#5a7aaa;">Updated</div>
                        <div class="text-sm font-600 dark:text-white" style="color:#001a4d;">
                            {{ $trainingSchedule->updated_at->format('F d, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('admin.training-schedules.edit', $trainingSchedule) }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
           style="background:linear-gradient(135deg,#0057B8,#003087); box-shadow:0 3px 12px rgba(0,87,184,0.25);">
            <i class="fas fa-pen"></i> Edit Schedule
        </a>
        <form method="POST" action="{{ route('admin.training-schedules.destroy', $trainingSchedule) }}" class="flex-1 sm:flex-initial"
              onsubmit="return confirm('Delete this training schedule? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-700 transition hover:scale-105"
                    style="background:#fff0f2; border:1px solid #ffccd5; color:#CE1126;">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
        <a href="{{ route('admin.training-schedules.index') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-600 border transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

</div>
@endsection
