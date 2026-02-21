@extends('layouts.app')

@section('title', 'Add Trainee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.trainees.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-plus mr-2" style="color:#CE1126;"></i> Add Trainee
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Create a new trainee account</p>
        </div>
    </div>

    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        <form method="POST" action="{{ route('admin.trainees.store') }}" class="p-8 space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Full Name <span style="color:#CE1126;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. Maria Santos"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('name') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('name')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Email Address <span style="color:#CE1126;">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="e.g. maria@example.com"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('email') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('email') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('email')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Password <span style="color:#CE1126;">*</span>
                </label>
                <input type="password" name="password"
                       placeholder="Minimum 8 characters"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:{{ $errors->has('password') ? '#CE1126' : '#c5d8f5' }}; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='{{ $errors->has('password') ? '#CE1126' : '#c5d8f5' }}'; this.style.boxShadow='none'">
                @error('password')
                    <p class="text-xs mt-1" style="color:#CE1126;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-700 uppercase tracking-wide mb-1.5" style="color:#5a7aaa;">
                    Confirm Password <span style="color:#CE1126;">*</span>
                </label>
                <input type="password" name="password_confirmation"
                       placeholder="Repeat password"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition
                              dark:bg-[#0a1628] dark:border-[#1e3a6b] dark:text-white"
                       style="border-color:#c5d8f5; color:#001a4d;"
                       onfocus="this.style.borderColor='#0057B8'; this.style.boxShadow='0 0 0 3px rgba(0,87,184,0.10)'"
                       onblur="this.style.borderColor='#c5d8f5'; this.style.boxShadow='none'">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#CE1126,#A50E1E); box-shadow:0 3px 12px rgba(206,17,38,0.25);">
                    <i class="fas fa-save"></i> Save Trainee
                </button>
                <a href="{{ route('admin.trainees.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-600 border transition hover:bg-[#e8f0fb]"
                   style="border-color:#c5d8f5; color:#5a7aaa;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection