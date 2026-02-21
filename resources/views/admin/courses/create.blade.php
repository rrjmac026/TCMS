@extends('layouts.app')

@section('title', 'Add Course')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.courses.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-plus mr-2" style="color:#CE1126;"></i> Add Course
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Create a new training course</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        <form method="POST" action="{{ route('admin.courses.store') }}" class="p-8 space-y-6">
            @csrf

            {{-- Course Code --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Course Code <span style="color:#CE1126;">*</span>
                </label>
                <input type="text" name="code" value="{{ old('code') }}"
                       placeholder="e.g. NC-I-ITE-001"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('code') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('code') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('code')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Course Name --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Course Name <span style="color:#CE1126;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. Information Technology Fundamentals"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('name')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Description
                </label>
                <textarea name="description" rows="4"
                          placeholder="Enter course description..."
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                                 dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                          style="border-color:{{ $errors->has('description') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                          onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                          onblur="this.style.borderColor='{{ $errors->has('description') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duration Hours --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Duration (Hours) <span style="color:#CE1126;">*</span>
                </label>
                <input type="number" name="duration_hours" value="{{ old('duration_hours') }}"
                       placeholder="e.g. 40"
                       min="1"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('duration_hours') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('duration_hours') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('duration_hours')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Level --}}
            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    NC Level
                </label>
                <select name="level"
                        class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                               dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                        style="border-color:{{ $errors->has('level') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                        onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                        onblur="this.style.borderColor='{{ $errors->has('level') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                    <option value="">Select Level</option>
                    <option value="NC I" {{ old('level') === 'NC I' ? 'selected' : '' }}>NC I</option>
                    <option value="NC II" {{ old('level') === 'NC II' ? 'selected' : '' }}>NC II</option>
                    <option value="NC III" {{ old('level') === 'NC III' ? 'selected' : '' }}>NC III</option>
                    <option value="NC IV" {{ old('level') === 'NC IV' ? 'selected' : '' }}>NC IV</option>
                    <option value="COC" {{ old('level') === 'COC' ? 'selected' : '' }}>COC</option>
                </select>
                @error('level')
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
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <i class="fas fa-save"></i> Save Course
                </button>
                <a href="{{ route('admin.courses.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-600 border transition hover:bg-[#e8f0fb]"
                   style="border-color:#c5d8f5; color:#5a7aaa;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
