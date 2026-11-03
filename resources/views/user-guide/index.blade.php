@extends('layouts.default')

@section('title', 'System User Guide')

@section('content')

    <!-- System Introduction & Overview Banner -->
    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-6 border-b-2 border-gray-100 dark:border-[#1e2536]">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">Official System Documentation</span>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Hydrotech Predictive Inventory & POS System</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Comprehensive operational manual detailing sidebar navigation access, module permissions, and the 4Ws framework (What, Who, Where, When).</p>
            </div>
            <div class="px-4 py-2 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] text-right">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Your Assigned Role</span>
                <span class="text-sm font-extrabold uppercase text-emerald-600 dark:text-emerald-400">{{ $role }}</span>
            </div>
        </div>
    </div>

    <!-- Role-Specific Sidenav & Modules 4Ws Operational Guide -->
    <div class="space-y-8">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Your Sidenav Menu Access & Operational 4Ws Guide</h2>
        </div>

        {{-- ================= ADMIN GUIDE ================= --}}
        @if ($role === 'admin')
            <div class="bg-white dark:bg-[#10141D] border-2 border-purple-200 dark:border-purple-900/60 rounded-3xl shadow-sm p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-900">Administrator Manual</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sidenav Access: Full System Control & Governance</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-slate-400">
                    As an <strong class="text-gray-800 dark:text-slate-200">Admin</strong>, your sidebar navigation includes: <span class="font-semibold text-gray-700 dark:text-slate-300">Dashboard, Point of Sale, Sales & Transactions, Catalog Management, Forecasting & EOQ, Inventory, Audit Logs, User Management, and User Guide</span>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm text-gray-600 dark:text-slate-300">
                    <!-- Dashboard -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📊 Dashboard</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays system analytics, inventory metrics, low-stock alerts, and daily sales summaries.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Dashboard" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted immediately upon login.</li>
                        </ul>
                    </div>
                    <!-- Point of Sale -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🛒 Point of Sale</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Handles cart items, parked orders, receipts, and special discounts (Senior/PWD, Employee, Vouchers).</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, POS Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Point of Sale" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used during customer counter checkouts.</li>
                        </ul>
                    </div>
                    <!-- Sales & Transactions -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">💳 Sales & Transactions</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Provides historical transaction logs, data export/import, and filter bars.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Sales & Transactions" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Reviewed for financial reconciliation.</li>
                        </ul>
                    </div>
                    <!-- Catalog Management -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📦 Catalog Management</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Controls product creation, categories, subcategories, pricing, and SKU codes.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Catalog Management" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used when introducing new product lines.</li>
                        </ul>
                    </div>
                    <!-- Forecasting & EOQ -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📈 Forecasting & EOQ</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Configures SMA lookback windows and economic order quantities.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Forecasting & EOQ" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Adjusted to optimize reorder thresholds.</li>
                        </ul>
                    </div>
                    <!-- Inventory -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🏢 Inventory</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Tracks store/warehouse quantities, status pills, transfers, and restock approvals.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, Warehouse Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Inventory" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Monitored continuously to prevent stockouts.</li>
                        </ul>
                    </div>
                    <!-- Audit Logs -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🔍 Audit Logs</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Records logins, logouts, record changes, inventory movements, and change diffs.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Restricted to Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Audit Logs" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted during security audits and compliance tracking.</li>
                        </ul>
                    </div>
                    <!-- User Management -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">👤 User Management</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Creates, updates, activates, deactivates, and deletes staff, manager, and warehouse accounts.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Strictly restricted to Admin.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "User Management" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used during staff onboarding or credential updates.</li>
                        </ul>
                    </div>
                    <!-- User Guide -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📖 User Guide</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays this official role-filtered system documentation.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Accessible to all authenticated users.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "User Guide" at the bottom of the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted whenever workflow guidance is needed.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= MANAGER GUIDE ================= --}}
        @if ($role === 'manager')
            <div class="bg-white dark:bg-[#10141D] border-2 border-blue-200 dark:border-blue-900/60 rounded-3xl shadow-sm p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-900">Manager Manual</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sidenav Access: Inventory Transfers, Approvals & Forecasting</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-slate-400">
                    As a <strong class="text-gray-800 dark:text-slate-200">Manager</strong>, your sidebar navigation includes: <span class="font-semibold text-gray-700 dark:text-slate-300">Dashboard, Point of Sale, Sales & Transactions, Catalog Management, Forecasting & EOQ, Inventory, Audit Logs, and User Guide</span>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm text-gray-600 dark:text-slate-300">
                    <!-- Dashboard -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📊 Dashboard</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays system analytics, inventory metrics, and daily sales summaries.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Dashboard" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted immediately upon login.</li>
                        </ul>
                    </div>
                    <!-- Point of Sale -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🛒 Point of Sale</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Handles cart items, parked orders, receipts, and special discounts.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, POS Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Point of Sale" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used during customer counter checkouts.</li>
                        </ul>
                    </div>
                    <!-- Sales & Transactions -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">💳 Sales & Transactions</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Provides historical transaction logs and filter bars.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Sales & Transactions" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Reviewed for financial reconciliation.</li>
                        </ul>
                    </div>
                    <!-- Catalog Management -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📦 Catalog Management</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Controls product creation, categories, pricing, and SKU codes.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Catalog Management" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used when introducing new product lines.</li>
                        </ul>
                    </div>
                    <!-- Forecasting & EOQ -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📈 Forecasting & EOQ</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Configures SMA lookback windows and economic order quantities.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Forecasting & EOQ" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Adjusted to optimize reorder thresholds.</li>
                        </ul>
                    </div>
                    <!-- Inventory -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🏢 Inventory</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Tracks store/warehouse quantities, status pills, transfers, and restock approvals.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Admin, Manager, Warehouse Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Inventory" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Monitored continuously to prevent stockouts.</li>
                        </ul>
                    </div>
                    <!-- Audit Logs -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🔍 Audit Logs</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Records logins, logouts, record changes, inventory movements, and change diffs.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Restricted to Admin and Manager.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Audit Logs" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted during security audits.</li>
                        </ul>
                    </div>
                    <!-- User Guide -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📖 User Guide</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays this official role-filtered system documentation.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Accessible to all authenticated users.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "User Guide" at the bottom of the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted whenever workflow guidance is needed.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= POS STAFF GUIDE ================= --}}
        @if ($role === 'staff')
            <div class="bg-white dark:bg-[#10141D] border-2 border-emerald-200 dark:border-emerald-900/60 rounded-3xl shadow-sm p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">POS Staff Manual</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sidenav Access: POS Checkout, Cart & Restock Requests</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-slate-400">
                    As <strong class="text-gray-800 dark:text-slate-200">POS Staff</strong>, your streamlined sidebar navigation includes: <span class="font-semibold text-gray-700 dark:text-slate-300">Point of Sale, Sales & Transactions, My Restock Requests, and User Guide</span>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-sm text-gray-600 dark:text-slate-300">
                    <!-- Point of Sale -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">🛒 Point of Sale</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Processes retail checkouts, parked orders, receipts, and special discounts (Senior/PWD, Employee Name/ID, Vouchers).</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> POS Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Point of Sale" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Used continuously during customer counter transactions.</li>
                        </ul>
                    </div>
                    <!-- Sales & Transactions -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">💳 Sales & Transactions</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Views personal or store retail transaction history and receipt logs.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> POS Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Sales & Transactions" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Checked to verify past customer purchases.</li>
                        </ul>
                    </div>
                    <!-- My Restock Requests -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📋 My Restock Requests</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Submits and tracks store restock requests awaiting manager approval.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> POS Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "My Restock Requests" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Initiated whenever store inventory levels run low.</li>
                        </ul>
                    </div>
                    <!-- User Guide -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📖 User Guide</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays this official role-filtered system documentation.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Accessible to all authenticated users.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "User Guide" at the bottom of the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted whenever workflow guidance is needed.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= WAREHOUSE STAFF GUIDE ================= --}}
        @if ($role === 'warehouse_staff')
            <div class="bg-white dark:bg-[#10141D] border-2 border-amber-200 dark:border-amber-900/60 rounded-3xl shadow-sm p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-900">Warehouse Manual</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sidenav Access: Warehouse Inventory & Restock Mode</h3>
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-slate-400">
                    As <strong class="text-gray-800 dark:text-slate-200">Warehouse Staff</strong>, your specialized sidebar navigation includes: <span class="font-semibold text-gray-700 dark:text-slate-300">Inventory Management, My Restock Requests, and User Guide</span>.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600 dark:text-slate-300">
                    <!-- Inventory Management -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📦 Inventory Management</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Monitors SKU/code synchronized stock levels and status pills across warehouse locations.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Warehouse Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "Inventory Management" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Checked daily to verify warehouse stock quantities.</li>
                        </ul>
                    </div>
                    <!-- My Restock Requests -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📋 My Restock Requests</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Processes bulk warehouse restocks and fulfills pending item requests.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Warehouse Staff.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "My Restock Requests" on the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Executed upon receiving supplier deliveries or fulfillment tasks.</li>
                        </ul>
                    </div>
                    <!-- User Guide -->
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] space-y-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-base">📖 User Guide</h4>
                        <ul class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            <li><strong class="text-gray-700 dark:text-slate-300">What:</strong> Displays this official role-filtered system documentation.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Who:</strong> Accessible to all authenticated users.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">Where:</strong> Click "User Guide" at the bottom of the sidebar.</li>
                            <li><strong class="text-gray-700 dark:text-slate-300">When:</strong> Consulted whenever workflow guidance is needed.</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection