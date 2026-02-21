@extends('layouts.app')

@section('title', 'Add Training Schedule')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.training-schedules.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-plus mr-2" style="color:#CE1126;"></i> Add Training Schedule
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Create a new training schedule</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        <form method="POST" action="{{ route('admin.training-schedules.store') }}" class="p-8 space-y-6">
            @csrf

            {{-- Course --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Course <span style="color:#CE1126;">*</span>
                </label>
                <select name="course_id"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->has('course_id') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->has('course_id') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                    <option value="">Select a Course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trainer --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Trainer <span style="color:#CE1126;">*</span>
                </label>
                <select name="trainer_id"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->has('trainer_id') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->has('trainer_id') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                    <option value="">Select a Trainer</option>
                    @foreach ($trainers as $trainer)
                        <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->name }}
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start Date --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Start Date <span style="color:#CE1126;">*</span>
                </label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('start_date') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('start_date') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('start_date')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    End Date <span style="color:#CE1126;">*</span>
                </label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('end_date') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('end_date') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('end_date')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Time Start --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Start Time <span style="color:#CE1126;">*</span>
                </label>
                <input type="time" name="time_start" value="{{ old('time_start') }}"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('time_start') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('time_start') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('time_start')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Time End --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    End Time <span style="color:#CE1126;">*</span>
                </label>
                <input type="time" name="time_end" value="{{ old('time_end') }}"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('time_end') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('time_end') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('time_end')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Location
                </label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="e.g. Room A-101, Building B"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('location') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('location') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('location')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Status <span style="color:#CE1126;">*</span>
                </label>
                <select name="status"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->has('status') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->has('status') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                    <option value="">Select Status</option>
                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#0057B8,#003087); box-shadow:0 3px 12px rgba(0,87,184,0.25);">
                    <i class="fas fa-save"></i> Save Schedule
                </button>
                <a href="{{ route('admin.training-schedules.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-600 border transition hover:bg-[#e8f0fb]"
                   style="border-color:#c5d8f5; color:#5a7aaa;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
