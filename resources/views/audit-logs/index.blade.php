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
                        <!-- Clickable Row Handler -->
                        <tr onclick="openAuditModal({{ $log->id }})" class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors cursor-pointer">
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

    <!-- Audit Log Detail Modal -->
    <div id="auditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Audit Log Specifications</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Detailed breakdown of user action and data modifications.</p>
                </div>
                <button onclick="closeAuditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="px-8 py-6 space-y-6 max-h-[65vh] overflow-y-auto text-sm">
                <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-[#161B26] p-4 rounded-2xl border border-gray-200 dark:border-[#1e2536]">
                    <div><span class="text-gray-500 dark:text-slate-400 block text-xs">User</span> <span id="modalUser" class="font-semibold text-gray-900 dark:text-white">-</span></div>
                    <div><span class="text-gray-500 dark:text-slate-400 block text-xs">Action</span> <span id="modalEvent" class="font-semibold text-gray-900 dark:text-white">-</span></div>
                    <div><span class="text-gray-500 dark:text-slate-400 block text-xs">Target Model</span> <span id="modalTarget" class="font-semibold text-gray-900 dark:text-white">-</span></div>
                    <div><span class="text-gray-500 dark:text-slate-400 block text-xs">IP Address</span> <span id="modalIp" class="font-mono text-gray-900 dark:text-white">-</span></div>
                    <div class="col-span-2"><span class="text-gray-500 dark:text-slate-400 block text-xs">Timestamp</span> <span id="modalTimestamp" class="text-gray-900 dark:text-white">-</span></div>
                </div>

                <!-- Changes / Payload View -->
                <div>
                    <h4 class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Activity Details & Modifications</h4>
                    <div class="border border-gray-200 dark:border-[#1e2536] rounded-2xl overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-500 dark:text-slate-400 border-b border-gray-200 dark:border-[#1e2536]">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Field / Property</th>
                                    <th class="px-4 py-3 font-semibold">Previous Value</th>
                                    <th class="px-4 py-3 font-semibold">New Value</th>
                                </tr>
                            </thead>
                            <tbody id="modalChangesTable" class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-400">Loading details...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-8 py-4 bg-gray-50 dark:bg-[#161B26] border-t-2 border-gray-200 dark:border-[#1e2536] flex justify-end">
                <button onclick="closeAuditModal()" class="px-5 py-2.5 bg-gray-200 dark:bg-[#1e2536] text-gray-700 dark:text-slate-200 rounded-xl text-sm font-semibold hover:bg-gray-300 dark:hover:bg-[#283248] transition-colors">
                    Close
                </button>
            </div>
        </div>
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

    function openAuditModal(id) {
        const modal = document.getElementById('auditModal');
        modal.classList.remove('hidden');
        
        document.getElementById('modalUser').innerText = 'Loading...';
        document.getElementById('modalEvent').innerText = 'Loading...';
        document.getElementById('modalTarget').innerText = 'Loading...';
        document.getElementById('modalIp').innerText = 'Loading...';
        document.getElementById('modalTimestamp').innerText = 'Loading...';
        
        document.getElementById('modalChangesTable').innerHTML = `
            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Fetching specifications...</td></tr>
        `;

        fetch(`/audit-logs/${id}`)
            .then(response => response.json())
            .then(res => {
                if(res.success) {
                    const data = res.data;
                    document.getElementById('modalUser').innerText = data.user;
                    document.getElementById('modalEvent').innerText = data.event;
                    document.getElementById('modalTarget').innerText = data.target_model;
                    document.getElementById('modalIp').innerText = data.ip_address;
                    document.getElementById('modalTimestamp').innerText = data.timestamp;
                    
                    let rows = '';
                    if (data.changes && data.changes.length > 0) {
                        data.changes.forEach(change => {
                            rows += `
                                <tr class="hover:bg-gray-50 dark:hover:bg-[#161B26]/30">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">${change.field}</td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-slate-400 line-through">${change.old}</td>
                                    <td class="px-4 py-3 font-semibold text-emerald-600 dark:text-emerald-400">${change.new}</td>
                                </tr>
                            `;
                        });
                    } else {
                        rows = `<tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No specific field changes recorded for this action.</td></tr>`;
                    }
                    
                    document.getElementById('modalChangesTable').innerHTML = rows;
                }
            })
            .catch(err => {
                document.getElementById('modalChangesTable').innerHTML = `
                    <tr><td colspan="3" class="px-4 py-6 text-center text-red-400">Failed to load specifications.</td></tr>
                `;
            });
    }

    function closeAuditModal() {
        document.getElementById('auditModal').classList.add('hidden');
    }
</script>
@endpush