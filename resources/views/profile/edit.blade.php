<x-app-layout>
    <style>
        :root {
            --prf-surface: #ffffff;
            --prf-surface-secondary: #f8f9fa;
            --prf-border: #c5d8f5;
            --prf-text: #001a4d;
            --prf-text-sec: #5a7aaa;
            --prf-blue-primary: #0057B8;
            --prf-blue-dark: #003087;
            --prf-red: #CE1126;
            --prf-red-dark: #b50c1f;
            --prf-gold: #F5C518;
            --prf-alert-bg: #fff5f5;
            --prf-success: #15803d;
            --prf-warning: #856404;
            --prf-warning-bg: #fff3cd;
        }

        .dark {
            --prf-surface: #0d1f3c;
            --prf-surface-secondary: #0a1628;
            --prf-border: #1e3a6b;
            --prf-text: #dde8ff;
            --prf-text-sec: #9ca3af;
            --prf-blue-primary: #0057B8;
            --prf-blue-dark: #003087;
            --prf-red: #CE1126;
            --prf-red-dark: #b50c1f;
            --prf-gold: #F5C518;
            --prf-alert-bg: #1a0e0e;
            --prf-success: #15803d;
            --prf-warning: #856404;
            --prf-warning-bg: #332701;
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl dark:text-white" style="color:var(--prf-blue-dark);">
                    <i class="fas fa-user mr-2" style="color:var(--prf-red);"></i> {{ __('Profile') }}
                </h2>
                <p class="text-sm mt-0.5" style="color:var(--prf-text-sec);">{{ __('Manage your account settings and preferences') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Information Card --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background:var(--prf-surface); border-color:var(--prf-border); box-shadow:0 4px 24px rgba(0,48,135,0.07);">
                <div class="h-1" style="background:linear-gradient(90deg,var(--prf-red) 33%,var(--prf-blue-primary) 33% 66%,var(--prf-gold) 66%);"></div>
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Password Update Card --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background:var(--prf-surface); border-color:var(--prf-border); box-shadow:0 4px 24px rgba(0,48,135,0.07);">
                <div class="h-1" style="background:linear-gradient(90deg,var(--prf-red) 33%,var(--prf-blue-primary) 33% 66%,var(--prf-gold) 66%);"></div>
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account Card --}}
            <div class="rounded-2xl border overflow-hidden"
                 style="background:var(--prf-surface); border-color:var(--prf-border); box-shadow:0 4px 24px rgba(0,48,135,0.07);">
                <div class="h-1" style="background:linear-gradient(90deg,var(--prf-red) 33%,var(--prf-blue-primary) 33% 66%,var(--prf-gold) 66%);"></div>
                <div class="p-6 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
