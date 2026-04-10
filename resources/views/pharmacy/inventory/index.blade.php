@extends('layouts.app')

@section('title', 'Medicine Inventory Management')
@section('page-title', 'Inventory Management')
@section('breadcrumb', 'Pharmacy / Inventory')

@section('content')
    <div x-data="inventoryManager()" x-init="init()" class="space-y-8 relative">

        <!-- Premium Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">
            {{-- Total Batches Card (Blue) --}}
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'All')">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-boxes text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Total Batches</p>
                    <h4 class="text-3xl font-black text-blue-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-[9px] text-blue-700 font-black uppercase tracking-tight">Active Inventory
                            Items</span>
                    </div>
                </div>
            </div>

            {{-- Low Stock Card (Amber) --}}
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'low')">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-lg shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-amber-500 uppercase opacity-70">Low Stock</p>
                    <h4 class="text-3xl font-black text-amber-700 drop-shadow-sm font-mono" x-text="stats.low_stock ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"
                            :class="{ 'animate-pulse': stats.low_stock > 0 }"></span>
                        <span class="text-[9px] text-amber-700 font-black uppercase tracking-tight">Inventory Refill
                            Alerts</span>
                    </div>
                </div>
            </div>

            {{-- Near Expiry Card (Purple) --}}
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'near_expiry')">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-hourglass-half text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-purple-500 uppercase opacity-70">Near Expiry</p>
                    <h4 class="text-3xl font-black text-purple-700 drop-shadow-sm font-mono"
                        x-text="stats.near_expiry ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"
                            :class="{ 'animate-pulse': stats.near_expiry > 0 }"></span>
                        <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">Attention Needed
                            Soon</span>
                    </div>
                </div>
            </div>

            {{-- Out of Stock Card (Rose) --}}
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'out')">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-red-400 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-times-circle text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-rose-500 uppercase opacity-70">Out of Stock</p>
                    <h4 class="text-3xl font-black text-rose-700 drop-shadow-sm font-mono" x-text="stats.out_of_stock ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"
                            :class="{ 'animate-pulse': stats.out_of_stock > 0 }"></span>
                        <span class="text-[9px] text-rose-700 font-black uppercase tracking-tight">Depleted Inventory
                            Items</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Floating Filter Toggle --}}
        <button @click="showSidebar = true" x-show="!showSidebar"
            class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-br from-indigo-600 to-purple-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(99,102,241,0.3)] hover:pr-5 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-400/30 group cursor-pointer"
            title="Open Filters">
            <i
                class="fas fa-sliders-h text-sm group-hover:rotate-90 transition-transform duration-500 text-indigo-100 group-hover:text-white"></i>
            <span style="writing-mode: vertical-rl;"
                class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-100/80 group-hover:text-white transition-colors">Inventory
                Filters</span>
        </button>

        {{-- 12-Col Grid Layout --}}
        <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

            {{-- Left Column - Table --}}
            <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">

                    {{-- Panel Header --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-100/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm">
                                    <i class="fas fa-boxes-stacked text-2xl text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight">
                                        Inventory Management
                                        <span class="text-lg font-normal text-gray-500 ml-2">(<span
                                                x-text="pagination.total"></span> records)</span>
                                    </h2>
                                    <p class="text-gray-500 text-sm font-medium mt-1">Monitor medicine batches, expiry dates
                                        and stock levels.</p>
                                </div>
                            </div>
                            <div class="flex gap-3 items-center">
                                <div
                                    class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                    <span
                                        class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row
                                        Density</span>
                                    <select x-model="filters.length" @change="fetchInventory()"
                                        class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                        <option value="15">15 Per Page</option>
                                        <option value="30">30 Per Page</option>
                                        <option value="50">50 Per Page</option>
                                        <option value="100">100 Per Page</option>
                                    </select>
                                </div>

                                <div
                                    class="flex items-center gap-1 bg-white border border-indigo-100 rounded-xl p-1 shadow-sm">
                                    <button @click="viewMode = 'table'"
                                        :class="viewMode === 'table' ? 'bg-indigo-600 text-white shadow-md' :
                                            'text-slate-400 hover:text-indigo-600'"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all"
                                        title="Table View">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md' :
                                            'text-slate-400 hover:text-indigo-600'"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all"
                                        title="Grid View">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                </div>

                                <a href="{{ route('pharmacy.inventory.bulk-upload-form') }}"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-bold shadow-sm hover:shadow hover:border-slate-300 transition-all cursor-pointer"
                                    title="Bulk Upload CSV">
                                    <i class="fas fa-file-csv text-emerald-600"></i> Bulk Upload
                                </a>

                                <button @click.prevent="modals.addStock = true"
                                    class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg hover:scale-105 transition-all cursor-pointer"
                                    title="Add Stock">
                                    <i class="fas fa-plus"></i> Add Stock
                                </button>
                                <button @click="fetchInventory()" :disabled="loading"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm group"
                                    title="Refresh">
                                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700"
                                        :class="loading ? 'animate-spin' : ''"></i>
                                </button>
                                <button @click="showSidebar = !showSidebar"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm relative"
                                    :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                    <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                                    <span x-show="hasActiveFilters()"
                                        class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 border-2 border-white rounded-full"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Batches Table/Grid View --}}
                    <div class="relative min-h-[300px]">
                        {{-- Loading overlay --}}
                        <div x-show="loading"
                            class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl">
                            <div class="flex flex-col items-center gap-3">
                                <div
                                    class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-inner">
                                </div>
                                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest animate-pulse">
                                    Syncing Data...</p>
                            </div>
                        </div>

                        <!-- Table View -->
                        <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200"
                                    :class="density === 'condensed' ? 'condensed-table' : 'spacious-table'">
                                    <thead class="bg-white border-b border-indigo-50">
                                        <tr>
                                            <th class="px-5 py-4 border-b border-slate-50 cursor-pointer group/th"
                                                @click="sortBy('name')">
                                                <div
                                                    class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover/th:text-indigo-600 transition-colors">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100 group-hover/th:bg-indigo-600 group-hover/th:text-white transition-all">
                                                        <i class="fas fa-pills text-[10px]"></i>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span>Medicine &amp; Batch</span>
                                                        <i class="fas"
                                                            :class="filters.sort_by === 'name' ? (filters
                                                                .sort_direction === 'asc' ? 'fa-sort-up' :
                                                                'fa-sort-down') : 'fa-sort opacity-30'"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-5 py-4 border-b border-slate-50">
                                                <div
                                                    class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                        <i class="fas fa-tag text-[10px]"></i>
                                                    </div>
                                                    Category &amp; Form
                                                </div>
                                            </th>
                                            <th class="px-5 py-4 border-b border-slate-50 cursor-pointer group/th"
                                                @click="sortBy('stock')">
                                                <div
                                                    class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover/th:text-indigo-600 transition-colors">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100 group-hover/th:bg-indigo-600 group-hover/th:text-white transition-all">
                                                        <i class="fas fa-cubes text-[10px]"></i>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span>Stock Level</span>
                                                        <i class="fas"
                                                            :class="filters.sort_by === 'stock' ? (filters
                                                                .sort_direction === 'asc' ? 'fa-sort-up' :
                                                                'fa-sort-down') : 'fa-sort opacity-30'"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-5 py-4 border-b border-slate-50 cursor-pointer group/th"
                                                @click="sortBy('expiry_date')">
                                                <div
                                                    class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover/th:text-indigo-600 transition-colors">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100 group-hover/th:bg-indigo-600 group-hover/th:text-white transition-all">
                                                        <i class="fas fa-calendar-alt text-[10px]"></i>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span>Expiration</span>
                                                        <i class="fas"
                                                            :class="filters.sort_by === 'expiry_date' ? (filters
                                                                .sort_direction === 'asc' ? 'fa-sort-up' :
                                                                'fa-sort-down') : 'fa-sort opacity-30'"></i>
                                                    </div>
                                                </div>
                                            </th>
                                            <th class="px-5 py-4 text-center border-b border-slate-50">
                                                <div
                                                    class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                        <i class="fas fa-toggle-on text-[10px]"></i>
                                                    </div>
                                                    Status
                                                </div>
                                            </th>
                                            <th class="px-5 py-4 text-center border-b border-slate-50">
                                                <div
                                                    class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                        <i class="fas fa-bolt-lightning text-[10px]"></i>
                                                    </div>
                                                    Actions
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100"
                                        x-show="!loading && medicines && medicines.length > 0">
                                        <template x-for="batch in medicines" :key="batch.id">
                                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                                <!-- Medicine Info -->
                                                <td class="px-5 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <div class="text-base font-black text-navy-800 leading-tight"
                                                            x-text="batch.medicine_name"></div>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span
                                                                class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold"
                                                                x-text="'#' + batch.batch_number"></span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Category & Form -->
                                                <td class="px-5 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col gap-1">
                                                        <span
                                                            class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md text-[10px] w-fit"
                                                            x-text="batch.category_name"></span>
                                                        <span
                                                            class="text-slate-500 font-bold text-[10px] uppercase tracking-wider px-1"
                                                            x-text="batch.form"></span>
                                                    </div>
                                                </td>

                                                <!-- Stock Level -->
                                                <td class="px-5 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col min-w-[120px]">
                                                        <div class="flex justify-between items-baseline mb-1">
                                                            <span class="text-xl font-black"
                                                                :class="batch.stock <= batch.reorder_level ? 'text-rose-600' :
                                                                    'text-emerald-600'"
                                                                x-text="batch.stock"></span>
                                                            <span
                                                                class="text-[10px] text-slate-400 font-bold uppercase ml-1">Remaining</span>
                                                        </div>
                                                        <div
                                                            class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                                            <div class="h-full transition-all duration-500"
                                                                :class="batch.stock_color"
                                                                :style="'width: ' + batch.stock_percentage + '%'"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Expiry -->
                                                <td class="px-5 py-4 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-black text-slate-700"
                                                                x-text="batch.expiry_date"></span>
                                                            <template x-if="batch.is_about_to_expire">
                                                                <i class="fas fa-exclamation-triangle text-amber-500 animate-pulse"
                                                                    title="Near Expiry"></i>
                                                            </template>
                                                        </div>
                                                        <span class="text-[10px] font-bold uppercase tracking-widest"
                                                            :class="batch.is_about_to_expire ? 'text-amber-600' :
                                                                'text-slate-400'"
                                                            x-text="batch.is_about_to_expire ? 'Expiring Soon' : 'Valid Batch'"></span>
                                                    </div>
                                                </td> <!-- Status Toggle -->
                                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                                    <button @click="toggleStatus(batch)" x-data="{ hover: false }"
                                                        @mouseenter="hover = true" @mouseleave="hover = false"
                                                        class="h-9 px-3 min-w-[100px] inline-flex items-center justify-center gap-2 rounded-xl transition-all shadow-sm border focus:outline-none group"
                                                        :class="batch.is_active ?
                                                            (hover ?
                                                                'bg-rose-50 text-rose-600 border-rose-100 shadow-rose-100' :
                                                                'bg-emerald-50 text-emerald-600 border-emerald-100') :
                                                            (hover ?
                                                                'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-100' :
                                                                'bg-rose-50 text-rose-600 border-rose-100')"
                                                        :title="batch.is_active ? 'Deactivate Batch' : 'Activate Batch'">
                                                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                                            :class="batch.is_active ?
                                                                (hover ? 'bg-rose-500 animate-bounce' :
                                                                    'bg-emerald-500 animate-pulse') :
                                                                (hover ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500')">
                                                        </div>
                                                        <span
                                                            class="text-[9px] font-black uppercase tracking-widest transition-all duration-300"
                                                            x-text="batch.is_active ? (hover ? 'Deactivate' : 'Active') : (hover ? 'Activate' : 'Hidden')"></span>
                                                    </button>
                                                </td>

                                                <!-- Actions -->
                                                <td
                                                    class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <a href="#" @click.prevent="fetchBatchDetail(batch.id)"
                                                            class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100"
                                                            title="View History">
                                                            <i class="fas fa-history text-[10px]"></i>
                                                        </a>
                                                        <button @click="openAdjustStockModal(batch)"
                                                            class="h-8 w-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm border border-amber-100"
                                                            title="Adjust Stock">
                                                            <i class="fas fa-sliders text-[10px]"></i>
                                                        </button>
                                                        <button @click="openEditBatchModal(batch)"
                                                            class="h-8 w-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-500 hover:text-white transition-all shadow-sm border border-indigo-100"
                                                            title="Edit Batch">
                                                            <i class="fas fa-edit text-[10px]"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>

                                    <!-- Loading State -->
                                    <tbody x-show="loading">
                                        <tr>
                                            <td colspan="6" class="px-6 py-20 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-4 shadow-inner">
                                                    </div>
                                                    <p class="text-slate-600 font-black uppercase tracking-widest text-xs">
                                                        Loading
                                                        Inventory System...</p>
                                                    <p class="text-[10px] text-slate-400 mt-1 font-bold">PLEASE WAIT WHILE
                                                        WE SYNCHRONIZE
                                                        BATCH DATA</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <!-- Empty State -->
                                    <tbody x-show="!loading && (!medicines || medicines.length === 0)">
                                        <tr>
                                            <td colspan="6" class="px-6 py-32 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="w-24 h-24 mb-6 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 shadow-inner">
                                                        <i class="fas fa-box-open text-5xl"></i>
                                                    </div>
                                                    <h3 class="text-xl font-black text-slate-400">Inventory Empty</h3>
                                                    <p class="text-slate-300 mt-2 font-medium max-w-sm mx-auto">
                                                        No active batches found matching your current filters. Start by
                                                        adding new stock or
                                                        adjust your search.
                                                    </p>
                                                    <a href="{{ route('pharmacy.inventory.create') }}"
                                                        class="mt-8 inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-bold hover:shadow-lg transition-all shadow-blue-200/50">
                                                        <i class="fas fa-plus-circle"></i>
                                                        Add First Stock Batch
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Grid View -->
                        <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <template x-for="batch in medicines" :key="batch.id">
                                    <div
                                        class="group relative bg-white border-2 border-slate-100 rounded-[2.5rem] p-6 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 overflow-hidden">
                                        <!-- Expiry Banner -->
                                        <div class="absolute top-0 right-0 left-0 h-1"
                                            :class="batch.is_about_to_expire ? 'bg-amber-500' : 'bg-emerald-500'"></div>

                                        <!-- Batch Info Header -->
                                        <div class="flex justify-between items-start mb-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Batch
                                                    Number</span>
                                                <span
                                                    class="px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-xs font-black uppercase tracking-widest"
                                                    x-text="batch.batch_number"></span>
                                            </div>
                                            <div class="text-right flex flex-col items-end">
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Expires
                                                    On</span>
                                                <div class="flex items-center gap-1.5"
                                                    :class="batch.is_about_to_expire ? 'text-amber-600' : 'text-slate-700'">
                                                    <i class="fas fa-clock text-[10px]"></i>
                                                    <span class="text-xs font-black" x-text="batch.expiry_date"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-center text-center">
                                            <!-- Medicine & Form -->
                                            <div class="relative group/avatar cursor-pointer mb-5">
                                                <div
                                                    class="absolute inset-0 bg-indigo-500 blur-2xl opacity-0 group-hover/avatar:opacity-20 transition-opacity duration-500 rounded-full">
                                                </div>
                                                <div
                                                    class="relative h-20 w-20 rounded-[2rem] bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-500/20 group-hover:scale-105 group-hover:rotate-3 transition-all duration-500 border-4 border-white">
                                                    <i class="fas fa-pills"></i>
                                                </div>
                                            </div>

                                            <h3 class="text-xl font-black text-slate-800 leading-tight mb-1"
                                                x-text="batch.medicine_name"></h3>
                                            <div
                                                class="flex items-center gap-2 text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6">
                                                <span x-text="batch.category_name"></span>
                                                <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                                                <span x-text="batch.form"></span>
                                            </div>

                                            <button @click="toggleStatus(batch)"
                                                class="w-full h-11 mb-8 flex items-center justify-center gap-3 rounded-2xl transition-all shadow-sm border focus:outline-none group"
                                                :class="batch.is_active ?
                                                    'bg-emerald-50 text-emerald-600 border-emerald-100 hover:bg-emerald-500 hover:text-white' :
                                                    'bg-rose-50 text-rose-600 border-rose-100 hover:bg-rose-500 hover:text-white'">
                                                <div class="w-2.5 h-2.5 rounded-full shadow-sm group-hover:bg-white"
                                                    :class="batch.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'">
                                                </div>
                                                <span class="text-xs font-black uppercase tracking-widest"
                                                    x-text="batch.is_active ? 'Batch Active' : 'Batch Inactive'"></span>
                                            </button>
                                        </div>

                                        <!-- Actions Footer -->
                                        <div
                                            class="w-full pt-6 border-t border-slate-50 flex items-center justify-center gap-2">
                                            <a href="#" @click.prevent="fetchBatchDetail(batch.id)"
                                                class="h-10 px-4 flex items-center justify-center gap-2 rounded-xl bg-slate-50 text-blue-500 hover:bg-blue-600 hover:text-white transition-all shadow-sm group/btn text-[10px] font-black uppercase tracking-widest">
                                                <i
                                                    class="fas fa-history group-hover/btn:scale-110 transition-transform"></i>
                                                Logs
                                            </a>
                                            <button @click="openAdjustStockModal(batch)"
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-sliders"></i>
                                            </button>
                                            <button @click="openEditBatchModal(batch)"
                                                class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-indigo-500 hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                            </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="!loading && medicines && medicines.length > 0"
                    class="p-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from ?? 0"></span> - <span
                                class="text-slate-900" x-text="pagination.to ?? 0"></span>
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span>
                            Capacity: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                        </div>

                        <div class="flex items-center gap-2">
                            <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i
                                    class="fas fa-angles-left text-[10px]"></i></button>
                            <button @click="changePage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i
                                    class="fas fa-chevron-left text-[10px]"></i> <span
                                    class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Prev</span></button>

                            <div class="flex items-center gap-1 px-1">
                                <template x-for="page in getPageRange()" :key="page">
                                    <button @click="page !== '...' && changePage(page)"
                                        :class="page === pagination.current_page ?
                                            'bg-indigo-600 text-white shadow-lg border-indigo-600 scale-105' :
                                            'bg-white text-slate-600 border-slate-200 hover:border-indigo-600'"
                                        x-text="page"
                                        class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"></button>
                                </template>
                            </div>

                            <button @click="changePage(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><span
                                    class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Next</span> <i
                                    class="fas fa-chevron-right text-[10px]"></i></button>
                            <button @click="changePage(pagination.last_page)"
                                :disabled="pagination.current_page === pagination.last_page"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i
                                    class="fas fa-angles-right text-[10px]"></i></button>
                        </div>
                    </div>
                </div>
            </div>{{-- /bg-white panel --}}
        </div>{{-- /left-col --}}

        {{-- Right Column - Sticky Sidebar --}}
        <div x-show="showSidebar" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
            class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2 group/sidebar">

            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                <div
                    class="p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-600 shadow-sm bg-blue-50 border border-blue-100">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Inventory Filters</h2>
                    </div>
                    <button @click="showSidebar = false" class="text-slate-400 hover:text-blue-600 transition-colors"><i
                            class="fas fa-times"></i></button>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Search Filter --}}
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                            <i class="fas fa-search text-blue-500"></i> Search Inventory
                        </label>
                        <div class="relative group">
                            <input x-model.debounce.500ms="filters.search" @input="fetchInventory()" type="text"
                                placeholder="Medicine, Batch No..."
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl pl-10 pr-4 py-3 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-400 transition-all outline-none font-bold ring-0">
                            <i
                                class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Status Category --}}
                    <div class="space-y-4">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-shield-virus text-indigo-500"></i> Stock Status
                        </label>
                        <div class="grid grid-cols-1 gap-2">
                            <button @click="filters.stock_status = 'All'; fetchInventory()"
                                :class="filters.stock_status === 'All' ?
                                    'bg-indigo-600 text-white shadow-lg shadow-indigo-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Global Data</span>
                                <i class="fas fa-globe-americas transition-opacity"
                                    :class="filters.stock_status === 'All' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filters.stock_status = 'low'; fetchInventory()"
                                :class="filters.stock_status === 'low' ?
                                    'bg-amber-500 text-white shadow-lg shadow-amber-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Low Stock</span>
                                <i class="fas fa-exclamation-triangle transition-opacity"
                                    :class="filters.stock_status === 'low' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filters.stock_status = 'out'; fetchInventory()"
                                :class="filters.stock_status === 'out' ?
                                    'bg-rose-500 text-white shadow-lg shadow-rose-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Out of Stock</span>
                                <i class="fas fa-times-circle transition-opacity"
                                    :class="filters.stock_status === 'out' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filters.stock_status = 'near_expiry'; fetchInventory()"
                                :class="filters.stock_status === 'near_expiry' ?
                                    'bg-purple-500 text-white shadow-lg shadow-purple-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Expired / Near Expiry</span>
                                <i class="fas fa-calendar-times transition-opacity"
                                    :class="filters.stock_status === 'near_expiry' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Visibility Bento Grid --}}
                    <div class="space-y-4">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-eye text-indigo-500"></i> Visibility Vault
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            {{-- All Batches - Wide Item --}}
                            <button @click="filters.status = 'all'; fetchInventory()"
                                class="col-span-2 relative overflow-hidden group p-4 rounded-2xl border-2 transition-all duration-300 transform active:scale-[0.98]"
                                :class="filters.status === 'all' ?
                                    'bg-gradient-to-br from-indigo-600 to-blue-700 border-indigo-200 shadow-lg shadow-indigo-100' :
                                    'bg-white border-slate-100 hover:border-indigo-200 hover:shadow-md'">
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="flex flex-col text-left">
                                        <span class="text-[8px] font-black uppercase tracking-[0.2em]"
                                            :class="filters.status === 'all' ? 'text-indigo-100' : 'text-slate-400'">Discovery</span>
                                        <span class="text-[11px] font-black uppercase tracking-widest mt-0.5"
                                            :class="filters.status === 'all' ? 'text-white' : 'text-slate-700'">Full
                                            Inventory</span>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300"
                                        :class="filters.status === 'all' ? 'bg-white/20 text-white' :
                                            'bg-slate-50 text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600'">
                                        <i class="fas fa-layer-group text-sm"></i>
                                    </div>
                                </div>
                                <div
                                    class="absolute -right-4 -bottom-4 w-24 h-24 bg-white opacity-[0.03] rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                                </div>
                            </button>

                            {{-- Active Only --}}
                            <button @click="filters.status = 'active'; fetchInventory()"
                                class="relative overflow-hidden group p-4 rounded-2xl border-2 transition-all duration-300 transform active:scale-[0.95]"
                                :class="filters.status === 'active' ?
                                    'bg-gradient-to-br from-emerald-500 to-teal-600 border-emerald-200 shadow-lg shadow-emerald-100' :
                                    'bg-white border-slate-100 hover:border-emerald-200 hover:shadow-md'">
                                <div class="relative z-10 flex flex-col items-center text-center gap-2">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300"
                                        :class="filters.status === 'active' ? 'bg-white/20 text-white' :
                                            'bg-slate-50 text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600'">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest mt-1"
                                        :class="filters.status === 'active' ? 'text-white' : 'text-slate-700'">Active</span>
                                </div>
                                <div
                                    class="absolute -left-2 -top-2 w-12 h-12 bg-white opacity-[0.05] rounded-full blur-xl animate-pulse">
                                </div>
                            </button>

                            {{-- Inactive Only --}}
                            <button @click="filters.status = 'inactive'; fetchInventory()"
                                class="relative overflow-hidden group p-4 rounded-2xl border-2 transition-all duration-300 transform active:scale-[0.95]"
                                :class="filters.status === 'inactive' ?
                                    'bg-gradient-to-br from-rose-500 to-pink-600 border-rose-200 shadow-lg shadow-rose-100' :
                                    'bg-white border-slate-100 hover:border-rose-200 hover:shadow-md'">
                                <div class="relative z-10 flex flex-col items-center text-center gap-2">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300"
                                        :class="filters.status === 'inactive' ? 'bg-white/20 text-white' :
                                            'bg-slate-50 text-slate-400 group-hover:bg-rose-50 group-hover:text-rose-600'">
                                        <i class="fas fa-eye-slash text-sm"></i>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest mt-1"
                                        :class="filters.status === 'inactive' ? 'text-white' : 'text-slate-700'">Hidden</span>
                                </div>
                                <div
                                    class="absolute -right-2 -top-2 w-12 h-12 bg-white opacity-[0.05] rounded-full blur-xl animate-pulse">
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Category Select --}}
                    <div class="space-y-3">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                            <i class="fas fa-layer-group text-blue-500"></i> Category Select
                        </label>
                        <div class="relative group">
                            <i
                                class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                            <select x-model="filters.category" @change="fetchInventory()"
                                class="w-full px-4 py-3 pl-10 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs ring-0 text-slate-600 cursor-pointer appearance-none">
                                <option value="All">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Sort By Select --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Assortment</label>
                        <div class="relative group">
                            <i
                                class="fas fa-sort-amount-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500"></i>
                            <select x-model="filters.sort_by" @change="fetchInventory()"
                                class="w-full px-4 py-3 pl-10 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs ring-0 text-slate-600 cursor-pointer appearance-none">
                                <option value="expiry_date">Expiry Date (Earliest)</option>
                                <option value="name">Medicine Name (A-Z)</option>
                                <option value="stock">Stock (Low to High)</option>
                                <option value="stock_desc">Stock (High to Low)</option>
                            </select>
                            <i
                                class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Page Density Filter --}}
                    <div class="space-y-3">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-compress-alt text-indigo-500"></i> Page Density
                        </label>
                        <div class="grid grid-cols-2 gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200/50">
                            <button type="button" @click="density = 'condensed'"
                                :class="density === 'condensed' ?
                                    'bg-white text-indigo-600 shadow-sm border border-slate-200' :
                                    'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Condensed
                            </button>
                            <button type="button" @click="density = 'spacious'"
                                :class="density === 'spacious' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' :
                                    'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Spacious
                            </button>
                        </div>
                    </div>

                    {{-- Records Per Page --}}
                    <div class="space-y-3 pb-4">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-list-ol text-indigo-500"></i> Records Per Page
                        </label>
                        <div class="grid grid-cols-4 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                            <template x-for="limit in [10, 25, 50, 100]" :key="limit">
                                <button @click="filters.length = limit; fetchInventory()"
                                    :class="filters.length == limit ? 'bg-white text-indigo-600 shadow-sm border-0' :
                                        'text-slate-400 hover:text-indigo-600 border-0'"
                                    class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all"
                                    x-text="limit">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 mt-auto flex flex-col gap-2">
                    <button @click="clearFilters()"
                        class="rose-reset-btn w-full py-5 text-white rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 flex items-center justify-center gap-3 active:scale-95">
                        <i class="fas fa-broom"></i> Reset Filters
                    </button>
                    <button @click="showSidebar = false"
                        class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all flex items-center justify-between px-6">
                        <span>Hide Panel</span>
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>{{-- /12-Col Grid Layout --}}
    {{-- Edit Batch Details Modal --}}
    <div x-show="modals.editBatch" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modals.editBatch" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-slate-900/75 "
                @click="modals.editBatch = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modals.editBatch" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-lg overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:p-0">

                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Edit Batch Details</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="editBatchForm.medicine_name"></p>
                    </div>
                    <button @click="modals.editBatch = false"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="submitEditBatch()">
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Batch Number</label>
                                <input type="text" x-model="editBatchForm.batch_number" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Expiry Date</label>
                                <input type="date" x-model="editBatchForm.expiry_date" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Unit Price
                                    (Purchase)</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rs.</span>
                                    <input type="number" step="0.01" x-model="editBatchForm.unit_price" required
                                        class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                                </div>
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sale Price
                                    (Retail)</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rs.</span>
                                    <input type="number" step="0.01" x-model="editBatchForm.sale_price" required
                                        class="w-full pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="col-span-2 pt-2">
                            <label
                                class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Inventory
                                Visibility</label>
                            <div class="flex items-center p-3 bg-slate-50 rounded-2xl border border-slate-100 gap-4 group cursor-pointer"
                                @click="editBatchForm.is_active = !editBatchForm.is_active">
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="editBatchForm.is_active" class="sr-only">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full transition-all duration-300"
                                        :class="editBatchForm.is_active ? 'bg-emerald-500' : 'bg-rose-400'"></div>
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 transform shadow-sm"
                                        :class="editBatchForm.is_active ? 'translate-x-5' : 'translate-x-0'"></div>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-700"
                                        x-text="editBatchForm.is_active ? 'Batch is Active' : 'Batch is Hidden'"></span>
                                    <span class="text-[10px] text-slate-400 font-medium"
                                        x-text="editBatchForm.is_active ? 'Visible to all departments and dispensing' : 'Excluded from lists and billing searches'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start space-x-3 mt-4">
                            <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                            <div class="text-xs text-amber-800 leading-relaxed">
                                <strong>Important:</strong> Changes to batch numbers or expiry dates will be reflected in
                                all related patient
                                prescriptions and dispensations for this specific batch.
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-3">
                        <button type="button" @click="modals.editBatch = false"
                            class="px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 rounded-xl transition-all">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center"
                            :disabled="isSubmitting">
                            <template x-if="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin mr-2"></i>
                            </template>
                            <span x-text="isSubmitting ? 'Saving...' : 'Save Changes'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Adjust Stock Modal --}}
    <div x-show="modals.adjustStock" style="display: none" class="fixed inset-0 z-[100] overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-30" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-30" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 " @click="modals.adjustStock = false"></div>

            <div
                class="relative align-bottom bg-white rounded-[2rem] text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-100 flex flex-col">
                {{-- Header --}}
                <div
                    class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-5 rounded-t-[2rem] flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-black text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-sliders text-amber-100"></i>
                        </div>
                        Adjust Stock Level
                    </h3>
                    <button @click="modals.adjustStock = false"
                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/20 text-white/70 hover:text-white transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 md:p-8">
                    <div
                        class="mb-6 bg-slate-50 border border-slate-100 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1"
                                x-text="adjustStockForm.batch_number"></p>
                            <h4 class="text-base font-black text-slate-800" x-text="adjustStockForm.medicine_name"></h4>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Current</p>
                            <span class="text-2xl font-black text-amber-600"
                                x-text="adjustStockForm.current_stock"></span>
                        </div>
                    </div>

                    <form id="adjustStockForm" @submit.prevent="submitAdjustStock" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" @click="adjustStockForm.type = 'add'"
                                :class="adjustStockForm.type === 'add' ?
                                    'bg-emerald-50 text-emerald-600 border-emerald-500 shadow-sm' :
                                    'bg-white text-slate-500 border-slate-200 hover:border-slate-300'"
                                class="px-4 py-4 rounded-xl border-2 font-black uppercase tracking-widest text-[10px] transition-all duration-300 flex flex-col items-center gap-2">
                                <i class="fas fa-plus-circle text-xl"
                                    :class="adjustStockForm.type === 'add' ? 'text-emerald-500' : 'text-slate-300'"></i>
                                Add Stock
                            </button>
                            <button type="button" @click="adjustStockForm.type = 'subtract'"
                                :class="adjustStockForm.type === 'subtract' ?
                                    'bg-rose-50 text-rose-600 border-rose-500 shadow-sm' :
                                    'bg-white text-slate-500 border-slate-200 hover:border-slate-300'"
                                class="px-4 py-4 rounded-xl border-2 font-black uppercase tracking-widest text-[10px] transition-all duration-300 flex flex-col items-center gap-2">
                                <i class="fas fa-minus-circle text-xl"
                                    :class="adjustStockForm.type === 'subtract' ? 'text-rose-500' : 'text-slate-300'"></i>
                                Subtract Stock
                            </button>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Adjustment
                                Quantity <span class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <i
                                    class="fas fa-boxes absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-amber-500 transition-colors"></i>
                                <input type="number" min="1" x-model="adjustStockForm.quantity" required
                                    placeholder="Enter amount to adjust"
                                    class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-amber-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0">

                                <div
                                    class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 pointer-events-none">
                                    <i class="fas fa-arrow-right text-[10px] text-slate-300"></i>
                                    <span class="text-sm font-black text-slate-800 bg-slate-200/50 px-2 rounded-md"
                                        x-text="adjustStockForm.type === 'add' ? (parseInt(adjustStockForm.current_stock) + (parseInt(adjustStockForm.quantity) || 0)) : (parseInt(adjustStockForm.current_stock) - (parseInt(adjustStockForm.quantity) || 0))">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Reason / Remarks
                                <span class="text-rose-500">*</span></label>
                            <textarea x-model="adjustStockForm.remarks" required rows="2"
                                placeholder="Explain why stock is being adjusted..."
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-amber-400 focus:bg-white transition-all font-medium text-sm text-slate-800 outline-none ring-0 resize-none"></textarea>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div
                    class="bg-slate-50 px-6 py-5 rounded-b-[2rem] border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="modals.adjustStock = false"
                        class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="button" @click="submitAdjustStock()" :disabled="isSubmitting"
                        class="px-8 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm flex items-center gap-2 disabled:opacity-70 disabled:hover:translate-y-0">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="isSubmitting ? 'Processing...' : 'Confirm Adjustment'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Batch Modal (Dynamic Content) --}}
    <div x-show="modals.viewBatch" style="display: none" class="fixed inset-0 z-[100] overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm"
                @click="modals.viewBatch = false"></div>

            <div
                class="relative align-bottom bg-white rounded-[2rem] text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border border-slate-100 flex flex-col h-[85vh]">
                {{-- Header --}}
                <div
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 rounded-t-[2rem] flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-black text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-history text-blue-100"></i>
                        </div>
                        Batch History & Logs
                    </h3>
                    <button @click="modals.viewBatch = false"
                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/20 text-white/70 hover:text-white transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Body (Modern AJAX-driven Detail View) --}}
                <div class="p-0 overflow-y-auto custom-scrollbar flex-1 relative bg-slate-50 rounded-b-[2rem]">
                    {{-- Loading State --}}
                    <div x-show="batchLoading"
                        class="absolute inset-0 flex flex-col items-center justify-center z-10 bg-slate-50/80 backdrop-blur-sm">
                        <div class="relative">
                            <div class="w-16 h-16 border-4 border-slate-100 border-t-blue-600 rounded-full animate-spin">
                            </div>
                            <i
                                class="fas fa-pills absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-blue-600/30 text-xl animate-pulse"></i>
                        </div>
                        <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Synchronizing
                            Data...</p>
                    </div>

                    <template x-if="batchDetail">
                        <div class="p-6 md:p-8 space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-300">
                            {{-- Quick Info Cards --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Stock
                                        Remaining</p>
                                    <div class="flex items-end gap-2">
                                        <span class="text-2xl font-black text-slate-800"
                                            x-text="batchDetail.remaining_quantity"></span>
                                        <span class="text-[10px] font-bold text-slate-400 pb-1">Units</span>
                                    </div>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status
                                    </p>
                                    <template x-if="batchDetail.is_expired">
                                        <span
                                            class="inline-flex px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase rounded-lg">Expired</span>
                                    </template>
                                    <template x-if="!batchDetail.is_expired && batchDetail.expiring_soon">
                                        <span
                                            class="inline-flex px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-lg">Expiring
                                            Soon</span>
                                    </template>
                                    <template x-if="!batchDetail.is_expired && !batchDetail.expiring_soon">
                                        <span
                                            class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-lg">Active</span>
                                    </template>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Expiry
                                        Date</p>
                                    <span class="text-sm font-black text-slate-700"
                                        x-text="moment(batchDetail.expiry_date).format('DD MMM YYYY')"></span>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Batch
                                        Number</p>
                                    <span class="text-sm font-black text-indigo-600"
                                        x-text="batchDetail.batch_number"></span>
                                </div>
                            </div>

                            {{-- Activity Log Table --}}
                            <div class="space-y-4">
                                <h4
                                    class="px-2 text-xs font-black text-slate-800 uppercase tracking-[0.2em] flex items-center gap-2">
                                    <i class="fas fa-list-ul text-indigo-500 text-[10px]"></i>
                                    Activity & Transaction Log
                                </h4>

                                <div
                                    class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-slate-700">
                                    <div class="overflow-x-auto overflow-y-auto max-h-[40vh] custom-scrollbar">
                                        <table class="w-full text-left">
                                            <thead class="sticky top-0 z-10">
                                                <tr class="bg-slate-50 border-b border-slate-100">
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        Date</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        Action</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        Change</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                                        Balance</th>
                                                    <th
                                                        class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        User</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50">
                                                <template x-for="log in batchLogs" :key="log.id">
                                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex flex-col">
                                                                <span class="text-xs font-black text-slate-700"
                                                                    x-text="moment(log.created_at).format('DD MMM, YYYY')"></span>
                                                                <span class="text-[9px] font-bold text-slate-400"
                                                                    x-text="moment(log.created_at).format('h:mm A')"></span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 capitalize">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full shadow-[0_0_8px_rgba(0,0,0,0.1)]"
                                                                    :class="{
                                                                        'bg-emerald-500 shadow-emerald-500/20': log
                                                                            .type === 'purchase',
                                                                        'bg-rose-500 shadow-rose-500/20': log
                                                                            .type === 'dispense',
                                                                        'bg-indigo-500 shadow-indigo-500/20': log
                                                                            .type === 'adjustment',
                                                                        'bg-amber-500 shadow-amber-500/20': log
                                                                            .type === 'transfer'
                                                                    }">
                                                                </div>
                                                                <span class="text-xs font-bold text-slate-600"
                                                                    x-text="log.type"></span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="text-xs font-black"
                                                                :class="(['purchase', 'adjustment'].includes(log.type) && log
                                                                    .new_stock > log.previous_stock) || (log
                                                                    .type === 'transfer' && log.new_stock > log
                                                                    .previous_stock) ? 'text-emerald-600' :
                                                                'text-rose-600'"
                                                                x-text="((['purchase', 'adjustment'].includes(log.type) && log.new_stock > log.previous_stock) || (log.type === 'transfer' && log.new_stock > log.previous_stock) ? '+' : '-') + log.quantity">
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 text-right">
                                                            <span class="text-xs font-black text-slate-800"
                                                                x-text="log.new_stock"></span>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex flex-col">
                                                                <span class="text-xs font-bold text-slate-600"
                                                                    x-text="log.user ? log.user.name : 'System Agent'"></span>
                                                                <span class="text-[9px] font-medium text-slate-400 italic"
                                                                    x-text="log.notes || 'No notes available'"></span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template x-if="batchLogs.length === 0">
                                                    <tr>
                                                        <td colspan="5"
                                                            class="py-12 text-center text-slate-400 font-medium text-sm italic bg-white rounded-b-3xl">
                                                            No activity recorded for this batch yet.
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State (Initial) --}}
                    <template x-if="!batchDetail && !batchLoading">
                        <div class="p-12 text-center text-slate-400 flex flex-col items-center justify-center h-full">
                            <i class="fas fa-folder-open text-4xl mb-4 opacity-20"></i>
                            <p class="font-bold">Select a batch to view details</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Stock Modal --}}
    <div x-show="modals.addStock" style="display: none" class="fixed inset-0 z-[100] overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 " @click="modals.addStock = false"></div>

            <div
                class="relative align-bottom bg-white rounded-[2rem] text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-slate-100 flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 rounded-t-[2rem] flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-black text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="fas fa-plus text-indigo-100"></i>
                        </div>
                        Add New Stock Batch
                    </h3>
                    <button @click="modals.addStock = false"
                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/20 text-white/70 hover:text-white transition-all">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1 relative">
                    <form id="addStockForm" @submit.prevent="submitAddStock" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Medicine Selection (Searchable Custom Picker) --}}
                            <div class="col-span-1 md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Select
                                    Medicine <span class="text-rose-500">*</span></label>

                                {{-- Trigger Button --}}
                                <div class="relative">
                                    <button type="button"
                                        @click="addStockPickerOpen = !addStockPickerOpen; if(addStockPickerOpen) $nextTick(() => $refs.medSearch?.focus())"
                                        class="w-full px-4 py-3.5 bg-slate-50 border-2 rounded-xl transition-all font-bold text-sm text-left flex items-center justify-between gap-3"
                                        :class="addStockPickerOpen ? 'border-indigo-400 bg-white' :
                                            'border-slate-100 hover:border-slate-200'">
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            <i class="fas fa-pills shrink-0"
                                                :class="addStockForm.medicine_id ? 'text-indigo-400' : 'text-slate-300'"></i>
                                            <div class="min-w-0 flex-1" x-show="addStockForm.medicine_id">
                                                <p class="font-black text-slate-800 truncate"
                                                    x-text="addStockForm.medicine_name"></p>
                                                <p class="text-[10px] text-slate-400 font-medium"
                                                    x-text="addStockForm.medicine_category"></p>
                                            </div>
                                            <span x-show="!addStockForm.medicine_id"
                                                class="text-slate-400 font-medium">Search &amp; select a medicine...</span>
                                        </div>
                                        <i class="fas shrink-0 text-slate-400 transition-transform duration-200"
                                            :class="addStockPickerOpen ? 'fa-chevron-up text-indigo-500' : 'fa-chevron-down'"></i>
                                    </button>

                                    {{-- Dropdown Panel --}}
                                    <div x-show="addStockPickerOpen" @click.outside="addStockPickerOpen = false"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="absolute top-full left-0 right-0 mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-2xl shadow-slate-300/40 z-[200] overflow-hidden">

                                        {{-- Search bar --}}
                                        <div class="p-3 border-b border-slate-100 bg-slate-50/50">
                                            <div class="relative">
                                                <i
                                                    class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                                                <input x-ref="medSearch" type="text" x-model="addStockMedSearch"
                                                    placeholder="Type name or generic name..."
                                                    class="w-full pl-8 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 outline-none focus:border-indigo-400 transition-all">
                                            </div>
                                        </div>

                                        {{-- Category pills --}}
                                        <div class="px-3 py-2 border-b border-slate-100 flex gap-1.5 flex-wrap bg-white">
                                            <button type="button" @click="addStockCatFilter = ''"
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest transition-all"
                                                :class="addStockCatFilter === '' ?
                                                    'bg-indigo-600 text-white shadow-sm' :
                                                    'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                                                All
                                            </button>
                                            <template x-for="cat in getAddStockCategories()" :key="cat.id">
                                                <button type="button" @click="addStockCatFilter = cat.id"
                                                    class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest transition-all"
                                                    :class="addStockCatFilter === cat.id ?
                                                        'bg-indigo-600 text-white shadow-sm' :
                                                        'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                                    x-text="cat.name">
                                                </button>
                                            </template>
                                        </div>

                                        {{-- Medicine list --}}
                                        <div class="max-h-60 overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                                            <template x-if="filteredMeds().length === 0">
                                                <div class="px-4 py-8 text-center">
                                                    <i class="fas fa-search-minus text-3xl text-slate-200 mb-3 block"></i>
                                                    <p class="text-sm text-slate-400 font-medium">No medicines found</p>
                                                </div>
                                            </template>
                                            <template x-for="med in filteredMeds()" :key="med.id">
                                                <button type="button"
                                                    @click="addStockForm.medicine_id = med.id; addStockForm.medicine_name = med.name; addStockForm.medicine_category = med.category ? med.category.name : ''; addStockPickerOpen = false; addStockMedSearch = ''"
                                                    class="w-full px-4 py-3 text-left transition-colors flex items-center gap-3"
                                                    :class="addStockForm.medicine_id === med.id ? 'bg-indigo-50' :
                                                        'hover:bg-slate-50/80'">
                                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                                        :class="addStockForm.medicine_id === med.id ? 'bg-indigo-100' :
                                                            'bg-slate-100'">
                                                        <i class="fas fa-pills text-xs"
                                                            :class="addStockForm.medicine_id === med.id ? 'text-indigo-600' :
                                                                'text-slate-400'"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-bold text-sm text-slate-800 truncate"
                                                            x-text="med.name"></p>
                                                        <p class="text-[10px] text-slate-400 font-medium truncate"
                                                            x-text="[med.generic_name, med.category?.name].filter(Boolean).join(' · ')">
                                                        </p>
                                                    </div>
                                                    <i class="fas fa-check-circle text-indigo-500 text-sm shrink-0"
                                                        x-show="addStockForm.medicine_id === med.id"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- Batch Number --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Batch Number
                                    <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input type="text" x-model="addStockForm.batch_number" required
                                        placeholder="e.g. BATCH-2026-01"
                                        class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0 uppercase placeholder:normal-case">
                                </div>
                            </div>

                            {{-- Expiry Date --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Expiry Date
                                    <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-calendar-alt absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input type="date" x-model="addStockForm.expiry_date" required
                                        min="{{ date('Y-m-d') }}"
                                        class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0">
                                </div>
                            </div>

                            {{-- Unit Price --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Unit Price
                                    (Cost)</label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input type="number" step="0.01" min="0"
                                        x-model="addStockForm.unit_price" placeholder="0.00"
                                        class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0">
                                </div>
                            </div>

                            {{-- Sale Price --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Sale Price
                                    <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input type="number" step="0.01" min="0"
                                        x-model="addStockForm.sale_price" required placeholder="0.00"
                                        class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0">
                                </div>
                            </div>

                            {{-- Initial Stock --}}
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Initial
                                    Stock Quantity <span class="text-rose-500">*</span></label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-boxes absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    <input type="number" min="1" x-model="addStockForm.stock" required
                                        placeholder="Enter total quantity received"
                                        class="w-full px-4 py-3.5 pl-11 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-bold text-sm text-slate-800 outline-none ring-0">
                                </div>
                            </div>

                            {{-- Notes / Remarks --}}
                            <div class="col-span-1 md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500">Remarks /
                                    Supplier Notes</label>
                                <textarea x-model="addStockForm.remarks" rows="2" placeholder="Optional details about this batch import"
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:bg-white transition-all font-medium text-sm text-slate-800 outline-none ring-0 resize-none"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div
                    class="bg-slate-50 px-6 py-5 rounded-b-[2rem] border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="modals.addStock = false"
                        class="px-6 py-2.5 rounded-xl text-slate-600 font-bold hover:bg-slate-200 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="button" @click="submitAddStock()" :disabled="isSubmitting"
                        class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm flex items-center gap-2 disabled:opacity-70 disabled:hover:translate-y-0">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        <span x-text="isSubmitting ? 'Saving...' : 'Add Stock'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>{{-- /space-y-8 / inventoryManager x-data --}}

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script>
        function inventoryManager() {
            return {
                // State
                medicines: [],
                medicinesList: @json($medicines ?? []), // Support Add Stock modal
                loading: false,
                viewMode: 'table',
                showSidebar: false,

                // Modals
                modals: {
                    addStock: false,
                    adjustStock: false,
                    viewBatch: false,
                    editBatch: false
                },
                selectedBatch: null,
                selectedBatchId: null,
                batchDetail: null,
                batchLogs: [],
                batchLoading: false,

                stats: {
                    total: 0,
                    low_stock: 0,
                    near_expiry: 0,
                    out_of_stock: 0
                },
                defaultFilters: {
                    category: 'All',
                    stock_status: 'All',
                    sort_by: 'expiry_date',
                    sort_direction: 'asc',
                    length: 10,
                    search: '',
                    status: 'all'
                },
                filters: {
                    category: 'All',
                    stock_status: 'All',
                    sort_by: 'expiry_date',
                    sort_direction: 'asc',
                    length: 10,
                    search: '',
                    status: 'all'
                },
                // Medicine picker state
                addStockPickerOpen: false,
                addStockMedSearch: '',
                addStockCatFilter: '',

                // Forms State
                addStockForm: {
                    medicine_id: '',
                    medicine_name: '',
                    medicine_category: '',
                    batch_number: '',
                    expiry_date: '',
                    unit_price: '',
                    sale_price: '',
                    stock: '',
                    remarks: ''
                },
                adjustStockForm: {
                    batch_id: null,
                    medicine_name: '',
                    batch_number: '',
                    current_stock: 0,
                    type: 'add',
                    quantity: '',
                    remarks: ''
                },
                editBatchForm: {
                    id: null,
                    medicine_name: '',
                    batch_number: '',
                    expiry_date: '',
                    unit_price: '',
                    sale_price: '',
                    is_active: true
                },
                viewModalUrl: '',
                isSubmitting: false,

                density: 'spacious',
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },

                // Initialize
                init() {
                    // Try to load filters from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    for (let key in this.filters) {
                        if (urlParams.has(key)) {
                            this.filters[key] = urlParams.get(key);
                        }
                    }
                    if (urlParams.has('page')) {
                        this.pagination.current_page = parseInt(urlParams.get('page'));
                    }

                    this.fetchInventory();
                },

                // Medicine picker helpers
                filteredMeds() {
                    return this.medicinesList.filter(med => {
                        const catMatch = !this.addStockCatFilter ||
                            (med.category && med.category.id === this.addStockCatFilter);
                        const q = this.addStockMedSearch.toLowerCase().trim();
                        const textMatch = !q ||
                            med.name.toLowerCase().includes(q) ||
                            (med.generic_name || '').toLowerCase().includes(q) ||
                            (med.brand || '').toLowerCase().includes(q);
                        return catMatch && textMatch;
                    });
                },

                getAddStockCategories() {
                    const seen = new Set();
                    return this.medicinesList
                        .filter(m => m.category && m.category.id && !seen.has(m.category.id) && seen.add(m.category.id))
                        .map(m => ({
                            id: m.category.id,
                            name: m.category.name
                        }))
                        .sort((a, b) => a.name.localeCompare(b.name));
                },

                // Set a specific filter and fetch
                setFilter(key, value) {
                    this.filters[key] = value;
                    this.pagination.current_page = 1;
                    this.fetchInventory();
                },

                // Reset all filters
                clearFilters() {
                    this.filters = {
                        ...this.defaultFilters
                    };
                    this.pagination.current_page = 1;
                    this.fetchInventory();
                },

                // Check for active filters
                hasActiveFilters() {
                    return this.filters.search !== '' ||
                        this.filters.stock_status !== 'All' ||
                        this.filters.category !== 'All' ||
                        this.filters.status !== 'all';
                },

                // Change sort and fetch
                sortBy(field) {
                    if (this.filters.sort_by === field) {
                        this.filters.sort_direction = this.filters.sort_direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.filters.sort_by = field;
                        this.filters.sort_direction = 'asc';
                    }
                    this.pagination.current_page = 1;
                    this.fetchInventory();
                },

                // Change page
                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.pagination.current_page = page;
                    this.fetchInventory();
                },

                // Fetch inventory data
                async fetchInventory() {
                    this.loading = true;
                    try {
                        const queryParams = new URLSearchParams({
                            ...this.filters,
                            page: this.pagination.current_page
                        });

                        const response = await fetch(`/pharmacy/inventory/list?${queryParams}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Data fetch failed');

                        const result = await response.json();
                        if (result.success) {
                            this.medicines = result.data;
                            this.stats = result.stats;
                            this.pagination = {
                                ...this.pagination,
                                current_page: result.pagination.current_page,
                                last_page: result.pagination.last_page,
                                total: result.pagination.total,
                                from: (result.pagination.current_page - 1) * result.pagination.per_page + 1,
                                to: Math.min(result.pagination.current_page * result.pagination.per_page, result
                                    .pagination.total)
                            };

                            // Update URL
                            const newUrl = new URL(window.location.href);
                            for (let key in this.filters) {
                                if (this.filters[key] !== this.defaultFilters[key]) {
                                    newUrl.searchParams.set(key, this.filters[key]);
                                } else {
                                    newUrl.searchParams.delete(key);
                                }
                            }
                            if (this.pagination.current_page > 1) {
                                newUrl.searchParams.set('page', this.pagination.current_page);
                            } else {
                                newUrl.searchParams.delete('page');
                            }
                            window.history.pushState({}, '', newUrl);
                        }
                    } catch (error) {
                        console.error('Error fetching inventory:', error);
                        if (window.showError) {
                            window.showError('Error loading inventory data', 'Connection Failure');
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                getPageRange() {
                    let current = this.pagination.current_page;
                    let last = this.pagination.last_page;
                    let delta = 2;
                    let left = current - delta;
                    let right = current + delta + 1;
                    let range = [];
                    let rangeWithDots = [];
                    let l;

                    for (let i = 1; i <= last; i++) {
                        if (i === 1 || i === last || (i >= left && i < right)) {
                            range.push(i);
                        }
                    }

                    for (let i of range) {
                        if (l) {
                            if (i - l === 2) {
                                rangeWithDots.push(l + 1);
                            } else if (i - l !== 1) {
                                rangeWithDots.push('...');
                            }
                        }
                        rangeWithDots.push(i);
                        l = i;
                    }

                    return rangeWithDots;
                },

                // Helper for status label
                getStockStatusLabel(status) {
                    const labels = {
                        'All': 'All Stock',
                        'low': 'Low Stock',
                        'out': 'Out of Stock',
                        'near_expiry': 'Near Expiry'
                    };
                    return labels[status] || status;
                },

                // Submit Add Stock
                async submitAddStock() {
                    if (!this.addStockForm.medicine_id || !this.addStockForm.batch_number || !this.addStockForm
                        .expiry_date || !this.addStockForm.sale_price || !this.addStockForm.stock) {
                        showError('Please fill in all required fields.', 'Validation Error');
                        return;
                    }

                    // Prepare payload mapping frontend fields to backend rules
                    const payload = {
                        medicine_id: this.addStockForm.medicine_id,
                        batch_number: this.addStockForm.batch_number,
                        expiry_date: this.addStockForm.expiry_date,
                        unit_price: this.addStockForm.unit_price,
                        sale_price: this.addStockForm.sale_price,
                        quantity: this.addStockForm.stock,
                        notes: this.addStockForm.remarks,
                        rc_number: this.addStockForm.rc_number || ''
                    };

                    this.isSubmitting = true;
                    try {
                        const response = await fetch('{{ route('pharmacy.inventory.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!response.ok) {
                            const result = await response.json().catch(() => ({
                                message: 'Server error occurred'
                            }));
                            if (result.errors) {
                                const msgs = Object.values(result.errors).flat().join('\n');
                                throw new Error(msgs);
                            }
                            throw new Error(result.message || 'Failed to add stock batch.');
                        }

                        const result = await response.json();

                        showSuccess('New stock batch added successfully.', 'Stock Added');
                        this.modals.addStock = false;

                        // Reset form
                        this.addStockForm = {
                            medicine_id: '',
                            medicine_name: '',
                            medicine_category: '',
                            batch_number: '',
                            expiry_date: '',
                            unit_price: '',
                            sale_price: '',
                            stock: '',
                            remarks: ''
                        };

                        // Refresh inventory
                        this.fetchInventory();
                    } catch (error) {
                        console.error('Add stock error:', error);
                        showError(error.message || 'An unexpected error occurred.', 'Error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                // Submit Adjust Stock
                async submitAdjustStock() {
                    // Safety check: Don't allow adjustments on inactive batches
                    const batch = this.medicines.find(b => b.id === this.adjustStockForm.batch_id);
                    if (batch && !batch.is_active) {
                        showError('This batch is currently INACTIVE. You must activate it before making adjustments.',
                            'Activation Required');
                        return;
                    }

                    if (!this.adjustStockForm.quantity || this.adjustStockForm.quantity <= 0) {
                        showError('Please enter a valid quantity.', 'Validation Error');
                        return;
                    }
                    if (!this.adjustStockForm.remarks) {
                        showError('Remarks are required for adjustment.', 'Validation Error');
                        return;
                    }

                    // Compute the final quantity the server expects
                    const currentStock = parseInt(this.adjustStockForm.current_stock) || 0;
                    const delta = parseInt(this.adjustStockForm.quantity) || 0;
                    const newQuantity = this.adjustStockForm.type === 'add' ?
                        currentStock + delta :
                        Math.max(0, currentStock - delta);

                    this.isSubmitting = true;
                    try {
                        const response = await fetch(
                            `/pharmacy/inventory/batch/${this.adjustStockForm.batch_id}/adjust`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    new_quantity: newQuantity,
                                    reason: this.adjustStockForm.remarks
                                })
                            });

                        if (!response.ok) {
                            const result = await response.json().catch(() => ({
                                message: 'Server error'
                            }));
                            if (result.errors) {
                                const msgs = Object.values(result.errors).flat().join('\n');
                                throw new Error(msgs);
                            }
                            throw new Error(result.message || 'Failed to adjust stock.');
                        }

                        const result = await response.json();

                        showSuccess(result.message || 'Stock adjusted successfully.', 'Stock Updated');
                        this.modals.adjustStock = false;

                        // Reset form
                        this.adjustStockForm = {
                            batch_id: null,
                            medicine_name: '',
                            batch_number: '',
                            current_stock: 0,
                            type: 'add',
                            quantity: '',
                            remarks: ''
                        };

                        // Refresh inventory
                        this.fetchInventory();
                    } catch (error) {
                        console.error('Adjust stock error:', error);
                        showError(error.message || 'An unexpected error occurred.', 'Error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                // Open View Modal (Modern AJAX Version)
                async fetchBatchDetail(id) {
                    this.selectedBatchId = id;
                    this.batchLoading = true;
                    this.batchDetail = null;
                    this.batchLogs = [];
                    this.modals.viewBatch = true;

                    try {
                        const response = await fetch(`/pharmacy/inventory/batch/${id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Fetch error response:', errorText);
                            throw new Error('Server returned an error. Please check logs.');
                        }

                        const result = await response.json();
                        if (result.success) {
                            this.batchDetail = result.batch;
                            this.batchLogs = result.logs;
                        } else {
                            throw new Error(result.message || 'Error loading data');
                        }
                    } catch (error) {
                        console.error('Batch detail fetch error:', error);
                        showError(error.message || 'Could not load batch details.', 'Error');
                        this.modals.viewBatch = false;
                    } finally {
                        this.batchLoading = false;
                    }
                },

                // Open Adjust Stock Modal
                openAdjustStockModal(batch) {
                    this.adjustStockForm.batch_id = batch.id;
                    this.adjustStockForm.medicine_name = batch.medicine_name;
                    this.adjustStockForm.current_stock = batch.stock;
                    this.adjustStockForm.batch_number = batch.batch_number;
                    this.adjustStockForm.type = 'add';
                    this.adjustStockForm.quantity = '';
                    this.adjustStockForm.remarks = '';
                    this.modals.adjustStock = true;
                },

                // Open Edit Batch Modal
                openEditBatchModal(batch) {
                    this.editBatchForm = {
                        id: batch.id,
                        medicine_name: batch.medicine_name,
                        batch_number: batch.batch_number,
                        expiry_date: batch.expiry_date,
                        unit_price: batch.unit_price,
                        sale_price: batch.sale_price,
                        is_active: batch.is_active
                    };
                    this.modals.editBatch = true;
                },

                // Submit Edit Batch
                async submitEditBatch() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch(`/pharmacy/inventory/batch/${this.editBatchForm.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.editBatchForm)
                        });

                        const result = await response.json();
                        if (!response.ok) {
                            if (result.errors) {
                                throw new Error(Object.values(result.errors).flat().join('\n'));
                            }
                            throw new Error(result.message || 'Update failed');
                        }

                        showSuccess('Batch details updated successfully.', 'Update Success');
                        this.modals.editBatch = false;
                        this.fetchInventory();
                    } catch (error) {
                        console.error('Update batch error:', error);
                        showError(error.message || 'An unexpected error occurred.', 'Error');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                // Toggle batch status
                async toggleStatus(batch) {
                    const original = batch.is_active;
                    batch.is_active = !original; // optimistic update

                    try {
                        const response = await fetch(`/pharmacy/inventory/batch/${batch.id}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();
                        if (!result.success) throw new Error(result.message);

                        if (window.showSuccess) {
                            showSuccess(`${batch.batch_number} is now ${batch.is_active ? 'Active' : 'Inactive'}`,
                                'Status Updated');
                        }
                    } catch (error) {
                        batch.is_active = original; // rollback
                        console.error('Error toggling status:', error);
                        if (window.showError) {
                            showError(error.message || 'Failed to update status', 'Update Failed');
                        }
                    }
                }
            }
        }
    </script>
@endpush
