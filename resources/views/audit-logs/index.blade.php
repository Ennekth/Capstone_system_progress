@extends('layouts.default')

@section('title', 'Audit Logs')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Track and audit all critical system operations and user activities.</p>
        </div>
    </div>

    <!-- Live Search & Event Filter Bar -->
    <form id="filterForm" method="GET" class="flex flex-col sm:flex-row gap-4 mb-8">
        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search logs by user, action, or model..." autocomplete="off"
               class="flex-1 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
        
        <select name="event" onchange="this.form.submit()" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer min-w-[180px]">
            <option value="">All Actions</option>
            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            <option value="restocked" {{ request('event') == 'restocked' ? 'selected' : '' }}>Restocked</option>
        </select>

        @if (request()->anyFilled(['search', 'event']))
            <a href="{{ route('audit-logs.index') }}" class="text-sm font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-3 rounded-xl transition-colors flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>

    <!-- Audit Logs Table -->
    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4">User</th>
                        <th class="px-8 py-4">Action</th>
                        <th class="px-8 py-4">Target Model</th>
                        <th class="px-8 py-4">IP Address</th>
                        <th class="px-8 py-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-semibold text-gray-900 dark:text-white">
                                {{ $log->user->full_name ?? $log->user->name ?? 'System / Guest' }}
                            </td>
                            <td class="px-8 py-4">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border 
                                    {{ $log->event === 'created' ? 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-400 dark:border-emerald-900' : '' }}
                                    {{ $log->event === 'updated' ? 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-950 dark:text-blue-400 dark:border-blue-900' : '' }}
                                    {{ $log->event === 'deleted' ? 'bg-red-100 text-red-700 border-red-200 dark:bg-red-950 dark:text-rose-400 dark:border-rose-900' : '' }}
                                    {{ !in_array($log->event, ['created', 'updated', 'deleted']) ? 'bg-purple-100 text-purple-700 border-purple-200 dark:bg-purple-950 dark:text-purple-400 dark:border-purple-900' : '' }}">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">
                                {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                            </td>
                            <td class="px-8 py-4 text-gray-500 dark:text-slate-400 text-xs font-mono">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                            <td class="px-8 py-4 text-gray-500 dark:text-slate-400 text-xs">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                                No audit log records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="px-8 py-4 border-t-2 border-gray-200 dark:border-[#1e2536]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 250);
            });
        }
    });
</script>
@endpush