@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Activity Logs</h1>
        <p class="text-sm text-gray-500 mt-1">Monitor login activity across all tenants</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase font-bold tracking-wide">Total Logs</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase font-bold tracking-wide">Today's Logins</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $stats['today'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase font-bold tracking-wide">Failed Today</div>
            <div class="text-2xl font-black text-red-600 mt-1">{{ $stats['failed_today'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase font-bold tracking-wide">Unique IPs Today</div>
            <div class="text-2xl font-black text-gray-800 dark:text-white mt-1">{{ $stats['unique_ips'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search email, name, IP..."
                class="col-span-2 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">

            <select name="tenant_id" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">All Tenants</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>

            <select name="action" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">All Actions</option>
                <option value="login_success" {{ request('action') === 'login_success' ? 'selected' : '' }}>Login Success</option>
                <option value="login_failed"  {{ request('action') === 'login_failed'  ? 'selected' : '' }}>Login Failed</option>
                <option value="logout"        {{ request('action') === 'logout'        ? 'selected' : '' }}>Logout</option>
            </select>

            <select name="role" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">All Roles</option>
                <option value="admin"   {{ request('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                <option value="trainer" {{ request('role') === 'trainer' ? 'selected' : '' }}>Trainer</option>
                <option value="trainee" {{ request('role') === 'trainee' ? 'selected' : '' }}>Trainee</option>
            </select>

            <select name="success" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">Success & Failed</option>
                <option value="1" {{ request('success') === '1' ? 'selected' : '' }}>Successful Only</option>
                <option value="0" {{ request('success') === '0' ? 'selected' : '' }}>Failed Only</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">

            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg px-4 py-2">
                    Filter
                </button>
                <a href="{{ route('superadmin.activity-logs.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 text-sm font-semibold rounded-lg px-4 py-2 dark:text-white">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Tenant</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">IP Address</th>
                        <th class="px-4 py-3 text-left">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800 dark:text-white">{{ $log->user_name ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $log->user_email ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-700 dark:text-gray-300">{{ $log->tenant_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($log->role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $log->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $log->role === 'trainer' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $log->role === 'trainee' ? 'bg-green-100 text-green-700' : '' }}
                                    ">
                                        {{ ucfirst($log->role) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Unknown</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $log->action === 'login_success' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $log->action === 'login_failed'  ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $log->action === 'logout'        ? 'bg-blue-100 text-blue-700' : '' }}
                                ">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $log->action === 'login_success' ? 'bg-green-500' : '' }}
                                        {{ $log->action === 'login_failed'  ? 'bg-red-500' : '' }}
                                        {{ $log->action === 'logout'        ? 'bg-blue-500' : '' }}
                                    "></span>
                                    {{ $log->action_label }}
                                </span>
                                @if ($log->failure_reason)
                                    <div class="text-xs text-red-400 mt-0.5">{{ $log->failure_reason }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs text-gray-600 dark:text-gray-300">{{ $log->ip_address ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-700 dark:text-gray-300">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                <i class="fas fa-shield-alt text-3xl mb-2 block opacity-30"></i>
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection