@extends('layouts.app')

@section('title', 'Manage Tenants')

@section('content')
<style>
    :root {
        --sa-primary: #003087;
        --sa-accent: #0057B8;
        --sa-success: #16a34a;
        --sa-warning: #b38a00;
        --sa-danger: #CE1126;
        --sa-border: #c5d8f5;
        --sa-text: #001a4d;
        --sa-text-muted: #5a7aaa;
        --sa-bg: #ffffff;
    }
    .dark {
        --sa-bg: #0a1628;
        --sa-border: #1e3a6b;
        --sa-text: #dde8ff;
        --sa-text-muted: #6b8abf;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: rgba(22, 163, 74, 0.1);
        color: var(--sa-success);
    }

    .badge-warning {
        background: rgba(179, 138, 0, 0.1);
        color: var(--sa-warning);
    }

    .badge-danger {
        background: rgba(206, 17, 38, 0.1);
        color: var(--sa-danger);
    }
</style>

<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold" style="color: var(--sa-primary);">
                <i class="fas fa-layer-group mr-2" style="color: var(--sa-accent);"></i> Manage Tenants
            </h1>
            <p class="text-sm mt-1" style="color: var(--sa-text-muted);">
                View, approve, and manage all tenant accounts
            </p>
        </div>
        <a href="{{ route('superadmin.tenants.create') }}"
           class="px-6 py-3 rounded-lg font-medium text-white text-center transition hover:shadow-lg inline-block"
           style="background: var(--sa-accent);">
            <i class="fas fa-plus mr-2"></i> Register New Tenant
        </a>
    </div>

    {{-- Alerts --}}
    @if($errors->any())
        <div class="rounded-xl border-2 p-4" style="background: rgba(206, 17, 38, 0.05); border-color: var(--sa-danger);">
            <div style="color: var(--sa-danger);" class="font-semibold flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="rounded-xl border-2 p-4" style="background: rgba(22, 163, 74, 0.05); border-color: var(--sa-success);">
            <div style="color: var(--sa-success);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border-2 p-4" style="background: rgba(206, 17, 38, 0.05); border-color: var(--sa-danger);">
            <div style="color: var(--sa-danger);" class="font-semibold flex items-center gap-3">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-2 border-b" style="border-color: var(--sa-border);">
        <a href="{{ route('superadmin.tenants.index') }}?filter=all"
           class="px-4 py-3 font-medium border-b-2 -mb-0.5 transition"
           style="border-color: {{ request('filter', 'all') === 'all' ? 'var(--sa-accent)' : 'transparent' }}; color: {{ request('filter', 'all') === 'all' ? 'var(--sa-accent)' : 'var(--sa-text-muted)' }};">
            All ({{ $tenants->count() }})
        </a>
        <a href="{{ route('superadmin.tenants.index') }}?filter=approved"
           class="px-4 py-3 font-medium border-b-2 -mb-0.5 transition"
           style="border-color: {{ request('filter') === 'approved' ? 'var(--sa-success)' : 'transparent' }}; color: {{ request('filter') === 'approved' ? 'var(--sa-success)' : 'var(--sa-text-muted)' }};">
            Approved ({{ $approvedTenants->count() }})
        </a>
        <a href="{{ route('superadmin.tenants.index') }}?filter=pending"
           class="px-4 py-3 font-medium border-b-2 -mb-0.5 transition"
           style="border-color: {{ request('filter') === 'pending' ? 'var(--sa-warning)' : 'transparent' }}; color: {{ request('filter') === 'pending' ? 'var(--sa-warning)' : 'var(--sa-text-muted)' }};">
            Pending ({{ $pendingTenants->count() }})
        </a>
        <a href="{{ route('superadmin.tenants.index') }}?filter=rejected"
           class="px-4 py-3 font-medium border-b-2 -mb-0.5 transition"
           style="border-color: {{ request('filter') === 'rejected' ? 'var(--sa-danger)' : 'transparent' }}; color: {{ request('filter') === 'rejected' ? 'var(--sa-danger)' : 'var(--sa-text-muted)' }};">
            Rejected ({{ $rejectedTenants->count() }})
        </a>
    </div>

    {{-- Tenants Table --}}
    <div class="rounded-2xl border-2 overflow-hidden" style="background: var(--sa-bg); border-color: var(--sa-border);">
        @if($tenants->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: rgba(0, 48, 135, 0.05); border-bottom: 2px solid var(--sa-border);">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Organization</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Admin Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Subdomain</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Plan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Expires</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold" style="color: var(--sa-text);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr style="border-bottom: 1px solid var(--sa-border);">
                                <td class="px-6 py-4">
                                    <p class="font-semibold" style="color: var(--sa-text);">{{ $tenant->name }}</p>
                                </td>
                                <td class="px-6 py-4" style="color: var(--sa-text-muted);">
                                    {{ $tenant->admin_email }}
                                </td>
                                <td class="px-6 py-4" style="color: var(--sa-text-muted);">
                                    <code class="px-2 py-1 rounded" style="background: rgba(0, 48, 135, 0.1); color: var(--sa-accent);">{{ $tenant->subdomain }}.tcm.com</code>
                                </td>
                                <td class="px-6 py-4" style="color: var(--sa-text);">
                                    {{ ucfirst($tenant->subscription) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($tenant->status === 'approved')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Approved
                                        </span>
                                    @elseif($tenant->status === 'pending')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-hourglass-half mr-1"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle mr-1"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4" style="color: var(--sa-text-muted);">
                                    @if($tenant->expires_at)
                                        <small>{{ $tenant->expires_at->format('M d, Y') }}</small>
                                    @else
                                        <small>—</small>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.tenants.show', $tenant) }}"
                                       class="text-sm px-3 py-1 rounded transition"
                                       style="background: rgba(0, 87, 184, 0.1); color: var(--sa-accent);">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                <i class="fas fa-inbox text-5xl mb-4" style="color: var(--sa-text-muted); opacity: 0.5;"></i>
                <p style="color: var(--sa-text-muted);">No tenants found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
