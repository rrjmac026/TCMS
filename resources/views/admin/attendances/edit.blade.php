@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.attendances.show', $attendance) }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-pen mr-2" style="color:#CE1126;"></i> Edit Attendance
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Updating attendance for {{ $attendance->enrollment->trainee->name }}</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        <form method="POST" action="{{ route('admin.attendances.update', $attendance) }}" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Enrollment --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Trainee & Course <span style="color:#CE1126;">*</span>
                </label>
                <select name="enrollment_id"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->has('enrollment_id') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->has('enrollment_id') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                    <option value="">Select a Trainee & Course</option>
                    @foreach ($enrollments as $enrollment)
                        <option value="{{ $enrollment->id }}" {{ old('enrollment_id', $attendance->enrollment_id) == $enrollment->id ? 'selected' : '' }}>
                            {{ $enrollment->trainee->name }} — {{ $enrollment->course->name }} ({{ $enrollment->course->code }})
                        </option>
                    @endforeach
                </select>
                @error('enrollment_id')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Date <span style="color:#CE1126;">*</span>
                </label>
                <input type="date" name="date" value="{{ old('date', $attendance->date) }}"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('date') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('date') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('date')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Attendance Status <span style="color:#CE1126;">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach (['present' => ['label' => 'Present', 'icon' => 'fa-check-circle', 'color' => '#22C55E', 'bg' => 'rgba(34,197,94,0.10)'],
                               'absent' => ['label' => 'Absent', 'icon' => 'fa-times-circle', 'color' => '#CE1126', 'bg' => 'rgba(206,17,38,0.10)'],
                               'late' => ['label' => 'Late', 'icon' => 'fa-hourglass-end', 'color' => '#F5C518', 'bg' => 'rgba(245,197,24,0.10)']] as $key => $option)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="status" value="{{ $key }}" 
                                   {{ old('status', $attendance->status) === $key ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition text-center"
                                 style="border-color:{{ old('status', $attendance->status) === $key ? $option['color'] : '#c5d8f5' }}; background:{{ old('status', $attendance->status) === $key ? $option['bg'] : '#fff' }};">
                                <div class="text-2xl mb-2" style="color:{{ $option['color'] }};">
                                    <i class="fas {{ $option['icon'] }}"></i>
                                </div>
                                <div class="text-xs font-700 uppercase tracking-wide" style="color:{{ $option['color'] }};">
                                    {{ $option['label'] }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('status')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#0057B8,#003087); box-shadow:0 3px 12px rgba(0,87,184,0.25);">
                    <i class="fas fa-save"></i> Update Attendance
                </button>
                <a href="{{ route('admin.attendances.show', $attendance) }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-600 border transition hover:bg-[#e8f0fb]"
                   style="border-color:#c5d8f5; color:#5a7aaa;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
