@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center border text-sm transition hover:bg-[#e8f0fb]"
           style="border-color:#c5d8f5; color:#5a7aaa;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold dark:text-white" style="color:#003087;">
                <i class="fas fa-user mr-2" style="color:#CE1126;"></i> User Profile
            </h1>
            <p class="text-sm mt-0.5" style="color:#5a7aaa;">Viewing details for {{ $user->name }}</p>
        </div>
    </div>

    {{-- Profile Card --}}
    <div class="rounded-2xl border overflow-hidden dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
         style="background:#fff; border-color:#c5d8f5; box-shadow:0 4px 24px rgba(0,48,135,0.07);">

        <div class="h-1" style="background:linear-gradient(90deg,#CE1126 33%,#0057B8 33% 66%,#F5C518 66%);"></div>

        {{-- Profile hero --}}
        <div class="p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6"
             style="background: linear-gradient(135deg, #003087 0%, #0057B8 100%); position:relative; overflow:hidden;">
            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
            <div style="position:absolute;bottom:-40px;left:-20px;width:120px;height:120px;border-radius:50%;background:rgba(245,197,24,0.07);"></div>

            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl font-900 text-white flex-shrink-0"
                 style="background:rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.20); position:relative; z-index:1;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div style="position:relative;z-index:1;">
                <div class="text-xl font-800 text-white">{{ $user->name }}</div>
                <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.65);">{{ $user->email }}</div>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-700"
                          style="background:rgba(245,197,24,0.15); border:1px solid rgba(245,197,24,0.30); color:#F5C518;">
                        <i class="fas fa-shield-halved mr-1" style="font-size:9px;"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            <div class="sm:ml-auto flex gap-2" style="position:relative;z-index:1;">
                @if ($user->id !== auth()->id())
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-700 transition hover:-translate-y-0.5"
                       style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.22); color:#fff;">
                        <i class="fas fa-pen text-xs"></i> Edit
                    </a>
                @else
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-700 transition hover:-translate-y-0.5"
                       style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.22); color:#fff;">
                        <i class="fas fa-pen text-xs"></i> Edit
                    </a>
                @endif
            </div>
        </div>

        {{-- Detail rows --}}
        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
            @php
                $roleColors = [
                    'admin' => ['color' => '#0057B8', 'bg' => '#e8f0fb'],
                    'trainer' => ['color' => '#b38a00', 'bg' => '#fffbf0'],
                    'trainee' => ['color' => '#16a34a', 'bg' => '#f0fdf4'],
                ];
                $userRoleColor = $roleColors[$user->role] ?? $roleColors['trainee'];

                $details = [
                    ['icon' => 'fa-envelope', 'color' => '#CE1126', 'bg' => '#fff0f2', 'label' => 'Email Address', 'value' => $user->email],
                    ['icon' => 'fa-shield-halved', 'color' => $userRoleColor['color'], 'bg' => $userRoleColor['bg'], 'label' => 'Role', 'value' => ucfirst($user->role)],
                    ['icon' => 'fa-calendar', 'color' => '#5a7aaa', 'bg' => '#f0f5ff', 'label' => 'Joined', 'value' => $user->created_at?->format('F d, Y')],
                    ['icon' => 'fa-refresh', 'color' => '#CE1126', 'bg' => '#fff0f2', 'label' => 'Last Updated', 'value' => $user->updated_at?->format('F d, Y')],
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

    {{-- Danger zone --}}
    @if ($user->id !== auth()->id())
        <div class="rounded-2xl border p-6 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
             style="background:#fff; border-color:#f5c5cb;">
            <h3 class="text-sm font-800 mb-1 flex items-center gap-2" style="color:#CE1126;">
                <i class="fas fa-triangle-exclamation"></i> Danger Zone
            </h3>
            <p class="text-xs mb-4" style="color:#5a7aaa;">Deleting this user is permanent and cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-700 transition hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#CE1126,#A50E1E); box-shadow:0 3px 12px rgba(206,17,38,0.25);">
                    <i class="fas fa-trash"></i> Delete User
                </button>
            </form>
        </div>
    @else
        <div class="rounded-2xl border p-6 dark:bg-[#0d1f3c] dark:border-[#1e3a6b]"
             style="background:#fff; border-color:#e8f0fb;">
            <h3 class="text-sm font-800 mb-1 flex items-center gap-2" style="color:#0057B8;">
                <i class="fas fa-info-circle"></i> Your Account
            </h3>
            <p class="text-xs" style="color:#5a7aaa;">You cannot delete your own account. Contact an administrator if needed.</p>
        </div>
    @endif

</div>
@endsection
