@extends('layouts.default')

@section('title', 'User Guide')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System User Guide</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Comprehensive documentation, workflows, and operational instructions for navigating the inventory system.</p>
        </div>
    </div>

    <div class="space-y-8">

        <!-- Quick Overview -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8">
            <h2 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Quick System Overview</h2>
            <p class="text-sm text-gray-600 dark:text-slate-300 leading-relaxed mb-6">
                The Hydrotech Predictive Inventory System is engineered to streamline multi-location stock tracking, point-of-sale transactions, automated demand forecasting, and inventory replenishment. Use the sidebar menu to navigate across modules according to your assigned role and permissions.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-gray-50/50 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536]">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white mb-2">📦 Inventory & Restock</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Monitor real-time store and warehouse stock quantities, inspect status badges, and execute batch restock workflows with per-item location routing.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50/50 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536]">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white mb-2">📊 Forecasting & EOQ</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Configure global and item-level Simple Moving Average (SMA) lookback windows, calibrate economic order quantities, and track audit histories.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50/50 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536]">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white mb-2">🛒 POS & Sales Records</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Process lightning-fast point-of-sale purchases, manage shopping carts, review transaction histories, and analyze detailed sales records.</p>
                </div>
            </div>
        </div>

        <!-- Detailed Module Documentation Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Inventory Management & Restock Mode -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">Inventory & Restock Mode</h2>
                
                <div class="space-y-4 text-sm text-gray-600 dark:text-slate-300">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">1. Stock Status Indicators</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Each product row provides clear status pills for Store, Warehouse, and Total quantities based on your reorder points:
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                            <span class="bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                            <span class="bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">2. Batch Restock Workflow</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Navigate to <strong class="text-gray-800 dark:text-slate-200">Restock Mode</strong> to process multiple items at once. Type quantities into input fields and use keyboard shortcuts (<kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] rounded text-xs font-semibold">Enter</kbd> or <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] rounded text-xs font-semibold">↓</kbd>) to seamlessly jump to the next item while highlighting active rows.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">3. Per-Item Destination Routing</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Each item row includes an individual location dropdown allowing you to route incoming restock quantities directly to either the <strong class="text-gray-800 dark:text-slate-200">Store</strong> or the <strong class="text-gray-800 dark:text-slate-200">Warehouse</strong> before final batch submission.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Forecast Settings & Analytics -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">Forecast Settings & Analytics</h2>
                
                <div class="space-y-4 text-sm text-gray-600 dark:text-slate-300">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">1. Global Default Window</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Defines the system-wide Simple Moving Average (SMA) lookback timeframe in days (default 30 days) applied to all inventory items for automated sales predictions unless customized.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">2. Category & Product Overrides</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Administrators and managers can define custom SMA lookback windows targeting either entire product categories or specific individual items to match seasonal demand patterns.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">3. Audit Tracking & Metadata</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            All overrides maintain complete transparency by logging the user who made the change (<strong class="text-gray-800 dark:text-slate-200">Updated By</strong>) along with exact date and time stamps (<strong class="text-gray-800 dark:text-slate-200">Last Updated</strong>).
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- POS & System Roles -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Point of Sale & Transactions -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">Point of Sale & Sales Records</h2>
                
                <div class="space-y-4 text-sm text-gray-600 dark:text-slate-300">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">1. POS Cart & Checkout</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Operators can quickly add products to the POS cart, apply cart-wide or item-level discounts, process secure checkouts, and instantly generate printable customer receipts.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">2. Sales Records & Filtering</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Review historical transaction logs and import sales datasets with responsive filter bars, instant search capabilities, and clear actions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- User Roles & Access Control -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 space-y-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">User Roles & Security</h2>
                
                <div class="space-y-4 text-sm text-gray-600 dark:text-slate-300">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">1. Role-Based Permissions</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            The system assigns specialized access: <strong class="text-gray-800 dark:text-slate-200">Admin</strong> (full management), <strong class="text-gray-800 dark:text-slate-200">Manager</strong> (operations & forecasting), <strong class="text-gray-800 dark:text-slate-200">Staff</strong> (POS & sales), and <strong class="text-gray-800 dark:text-slate-200">Warehouse Staff</strong> (stock viewing & requests).
                        </p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">2. Theme Customization</h3>
                        <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">
                            Toggle seamlessly between Light and Dark mode using the sidebar utility button. User theme preferences persist across sessions automatically.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection