@extends('layouts.app')

@section('title', 'Dispense History - NHMP HMS')
@section('page-title', 'Dispense History')
@section('breadcrumb', 'Pharmacy / History')

@section('content')
<div x-data="historyApp({{ json_encode($stats) }})" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Same colors as User Management
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">

        {{-- Total Dispensed Card (Blue - matches Total Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-history text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Total Dispensed</p>
                <h4 class="text-3xl font-black text-blue-700 drop-shadow-sm font-mono" x-text="stats.total_dispensed ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[9px] text-blue-700 font-black uppercase tracking-tight">All-time dispensations</span>
                </div>
            </div>
        </div>

        {{-- Today's Dispensed Card (Green - matches Active Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setTodayFilter()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-day text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-emerald-500 uppercase opacity-70">Today's Dispensed</p>
                <h4 class="text-3xl font-black text-emerald-700 drop-shadow-sm font-mono" x-text="stats.today_dispensed ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600" :class="{ 'animate-pulse': stats.today_dispensed > 0 }"></span>
                    <span class="text-[9px] text-emerald-700 font-black uppercase tracking-tight">Click to filter today's records</span>
                </div>
            </div>
        </div>

        {{-- Total Units Card (Purple - matches Administrators) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-pills text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-purple-500 uppercase opacity-70">Total Units</p>
                <h4 class="text-3xl font-black text-purple-700 drop-shadow-sm font-mono" x-text="stats.total_quantity ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">Total units dispensed</span>
                </div>
            </div>
        </div>

        {{-- Unique Patients Card (Rose - matches Inactive Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-orange-400 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-rose-500 uppercase opacity-70">Unique Patients</p>
                <h4 class="text-3xl font-black text-rose-700 drop-shadow-sm font-mono" x-text="stats.unique_patients ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-[9px] text-rose-700 font-black uppercase tracking-tight">Patients served</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Filter Toggle (shown when sidebar is hidden) --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(99,102,241,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(99,102,241,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
        title="Open Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-indigo-500/10 blur-xl rounded-full group-hover:bg-indigo-500/20 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Table Container --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">

                {{-- Panel Header with Light Gradient --}}
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                                <i class="fas fa-history text-2xl text-indigo-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                    Dispense History
                                    <span class="text-lg font-normal text-gray-600">(<span x-text="pagination.total"></span> records)</span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Review and track all pharmaceutical dispensations</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 items-center">
                             <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row Density</span>
                                <select x-model="pagination.per_page" @change="fetchData(1)" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                    <option value="15">15 Per Page</option>
                                    <option value="30">30 Per Page</option>
                                    <option value="50">50 Per Page</option>
                                    <option value="100">100 Per Page</option>
                                </select>
                            </div>
                            <button @click="showSidebar = !showSidebar"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                            </button>
                            <button @click="fetchData(1)" 
                                class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                title="Refresh">
                                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Active Filters Summary --}}
                <div x-show="hasActiveFilters()" x-cloak
                    class="px-6 py-4 bg-indigo-50/30 border-b border-indigo-100/50 flex flex-wrap items-center gap-2">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-2 flex items-center gap-1">
                        <i class="fas fa-filter text-indigo-500"></i> Active:
                    </span>
                    
                    <template x-if="filters.search">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-100 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-tight shadow-sm transition-all hover:bg-indigo-50">
                            <i class="fas fa-search text-[8px] opacity-50"></i>
                            <span x-text="filters.search"></span>
                            <button @click="filters.search = ''; fetchData(1)" class="ml-1 hover:text-rose-600 transition-colors"><i class="fas fa-times-circle text-[10px]"></i></button>
                        </span>
                    </template>

                    <template x-if="filters.date_from || filters.date_to">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-100 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-tight shadow-sm transition-all hover:bg-indigo-50">
                            <i class="fas fa-calendar-alt text-[8px] opacity-50"></i>
                            <span x-text="getDateRangeText()"></span>
                            <button @click="filters.date_from = ''; filters.date_to = ''; fetchData(1)" class="ml-1 hover:text-rose-600 transition-colors"><i class="fas fa-times-circle text-[10px]"></i></button>
                        </span>
                    </template>

                    <template x-if="filters.medicine_category_id">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-100 text-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-tight shadow-sm transition-all hover:bg-indigo-50">
                            <i class="fas fa-tag text-[8px] opacity-50"></i>
                            <span x-text="getCategoryName(filters.medicine_category_id)"></span>
                            <button @click="filters.medicine_category_id = ''; fetchData(1)" class="ml-1 hover:text-rose-600 transition-colors"><i class="fas fa-times-circle text-[10px]"></i></button>
                        </span>
                    </template>

                    <button @click="clearFilters()" class="text-[9px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition-colors ml-auto border-b border-rose-200 border-dashed pb-0.5">Clear All</button>
                </div>

            {{-- table border separator --}}

        <div class="overflow-x-auto relative min-h-[300px]">

            {{-- Loading overlay --}}
            <div x-show="loading"
                 class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-inner"></div>
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest animate-pulse">Syncing Data...</p>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200" :class="density === 'condensed' ? 'condensed-table' : 'spacious-table'">
                <thead class="bg-white border-b border-indigo-50">
                    <tr>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-hashtag text-[10px]"></i>
                                </div>
                                <button @click="sortBy('id')" class="flex items-center gap-1 hover:text-indigo-700 transition-colors">
                                    ID <i class="fas text-[10px] opacity-30" :class="sort.field === 'id' ? (sort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-user text-[10px]"></i>
                                </div>
                                Patient Information
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-pills text-[10px]"></i>
                                </div>
                                <button @click="sortBy('medicine_name')" class="flex items-center gap-1 hover:text-indigo-700 transition-colors">
                                    Medicine <i class="fas text-[10px] opacity-30" :class="sort.field === 'medicine_name' ? (sort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-cubes text-[10px]"></i>
                                </div>
                                Batch / Qty
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-user-md text-[10px]"></i>
                                </div>
                                Pharmacist
                            </div>
                        </th>
                        <th class="px-5 py-4 border-b border-slate-50">
                            <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-clock text-[10px]"></i>
                                </div>
                                <button @click="sortBy('dispensed_at')" class="flex items-center gap-1 hover:text-indigo-700 transition-colors">
                                    Dispensed At <i class="fas text-[10px] opacity-30" :class="sort.field === 'dispensed_at' ? (sort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 text-center border-b border-slate-50">
                            <div class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                    <i class="fas fa-bolt-lightning text-[10px]"></i>
                                </div>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && data.data && data.data.length > 0">
                    <template x-for="item in data.data" :key="item.id">
                        <tr class="hover:bg-blue-50/30 transition-colors duration-200 group">

                            {{-- ID --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center shadow-sm group-hover:from-indigo-200 group-hover:to-purple-200 transition-colors">
                                        <i class="fas fa-pills text-indigo-500 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 font-mono" x-text="'#' + item.id"></span>
                                </div>
                            </td>

                            {{-- Patient Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-start space-x-3">
                                    {{-- Avatar with initials --}}
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-lg"
                                             :class="getAvatarColor(item.prescription?.diagnosis?.visit?.patient?.name ?? '')">
                                            <i class="fas fa-user-injured text-white"></i>
                                        </div>
                                    </div>

                                    {{-- Patient details --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate"
                                           x-text="item.prescription?.diagnosis?.visit?.patient?.name ?? 'Unknown Patient'"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center text-xs text-gray-600">
                                                <i class="fas fa-id-card mr-1 text-blue-500"></i>
                                                <span x-text="item.prescription?.diagnosis?.visit?.patient?.emrn ?? 'N/A'"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Medicine Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                     {{-- Quantity --}}
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-xl font-black text-indigo-600" x-text="item.quantity_dispensed"></span>
                                        <span class="text-xs text-gray-500">units</span>
                                    </div>
                                    <p class="text-sm font-bold text-gray-800"
                                       x-text="item.prescription?.medicine?.name ?? 'Unknown Medicine'"></p>
                                    <p class="text-xs text-gray-500 mt-1"
                                       x-text="item.prescription?.medicine?.generic_name ?? ''"></p>
                                </div>
                            </td>

                            {{-- Batch / Quantity Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-2">
                                     {{-- RC Number badge --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-gray-600 rounded-full text-xs font-bold w-fit">
                                        <i class="fas fa-tag mr-1 text-xs"></i>
                                        <span x-text="item.medicine_batch?.rc_number ?? 'SYSTEM'"></span>
                                    </span>
                                    {{-- Batch badge --}}
                                    <span class="inline-flex items-center px-2.5 py-1 text-gray-600 rounded-full text-xs font-bold w-fit">
                                        <i class="fas fa-tag mr-1 text-xs"></i>
                                        <span x-text="item.medicine_batch?.batch_number ?? 'SYSTEM'"></span>
                                    </span>

                                </div>
                            </td>

                            {{-- Pharmacist Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user-check text-gray-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700"
                                          x-text="item.dispensed_by?.name ?? 'System'"></span>
                                </div>
                            </td>

                            {{-- Dispensed At Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-800"
                                          x-text="formatDate(item.dispensed_at)"></span>
                                    <span class="text-xs text-gray-500 mt-1"
                                          x-text="formatTime(item.dispensed_at)"></span>
                                </div>
                            </td>

                            {{-- Actions Column --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center">
                                    <button @click="openDetails(item)" class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100" title="View Dispensation Details">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>

                {{-- Empty state --}}
                <tbody x-show="!loading && (!data.data || data.data.length === 0)">
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 mb-4 text-gray-300">
                                    <i class="fas fa-folder-open text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No records found</h3>
                                <p class="text-gray-500 max-w-md mb-4">
                                    <span x-show="filters.search || filters.date_from || filters.date_to">
                                        Try adjusting your filters or date range
                                    </span>
                                    <span x-show="!filters.search && !filters.date_from && !filters.date_to">
                                        No dispensation records in the system.
                                    </span>
                                </p>
                                <button @click="clearFilters()"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                    <i class="fas fa-sync-alt"></i>
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div x-show="!loading && data.data && data.data.length > 0" class="p-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    Displaying <span class="text-slate-900" x-text="pagination.from ?? 0"></span> - <span class="text-slate-900" x-text="pagination.to ?? 0"></span>
                    <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span>
                    Total: <span class="text-indigo-600" x-text="pagination.total"></span> Records
                </div>
                <div class="flex items-center gap-2">
                    <button @click="fetchData(1)" :disabled="pagination.current_page === 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-left text-[10px]"></i></button>
                    <button @click="fetchData(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-chevron-left text-[10px]"></i> <span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Prev</span></button>
                    <div class="flex items-center gap-1 px-1">
                        <template x-for="page in visiblePages" :key="page">
                            <button @click="page !== '...' && fetchData(page)"
                                :class="page === pagination.current_page ? 'bg-indigo-600 text-white shadow-lg border-indigo-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-600'"
                                x-text="page" class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"></button>
                        </template>
                    </div>
                    <button @click="fetchData(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Next</span> <i class="fas fa-chevron-right text-[10px]"></i></button>
                    <button @click="fetchData(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-right text-[10px]"></i></button>
                </div>
            </div>
        </div>
    </div>{{-- /panel --}}
</div>{{-- /left-col --}}

        {{-- Right Column - Sticky Sidebar --}}
        <div x-show="showSidebar" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2 group/sidebar">
            
            <div class="bg-white rounded-[2.5rem] p-8 text-slate-800 shadow-2xl relative overflow-hidden border border-slate-100 flex flex-col h-full">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm transition-transform hover:rotate-6 duration-300">
                            <i class="fas fa-filter text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-black uppercase tracking-[0.2em] text-xs text-slate-800">History Filters</h3>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Refine Data</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <div class="relative space-y-6 flex-1 overflow-y-auto pr-2 custom-scrollbar">

                    {{-- Search --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-search text-indigo-500"></i> Search Records
                        </label>
                        <div class="relative group">
                            <input type="text" x-model.debounce.500ms="filters.search" @input="fetchData(1)" placeholder="Patient, medicine..." 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-xs text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-blue-500/5 focus:border-blue-400 transition-all outline-none font-bold">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-400 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Default Filters --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-indigo-500"></i> Date From
                            </label>
                            <input type="date" x-model="filters.date_from" @change="fetchData(1)" class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs outline-none text-slate-600 text-center">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                                <i class="fas fa-calendar-check text-indigo-500"></i> Date To
                            </label>
                            <input type="date" x-model="filters.date_to" @change="fetchData(1)" class="w-full px-4 py-4 bg-slate-50 border border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs outline-none text-slate-600 text-center">
                        </div>
                    </div>

                    {{-- Category Select --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-tags text-indigo-500"></i> Category
                        </label>
                        <div class="relative group">
                            <select x-model="filters.medicine_category_id" @change="fetchData(1)" class="w-full px-4 py-4 pr-10 bg-slate-50 border border-slate-100 rounded-2xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs outline-none text-slate-800 cursor-pointer appearance-none shadow-sm">
                                <option value="">All Categories</option>
                                @foreach($medicineCategories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Medicine Select --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-pills text-indigo-500"></i> Medicine
                        </label>
                        <div class="relative group">
                            <select x-model="filters.medicine_id" @change="fetchData(1)" class="w-full px-4 py-4 pr-10 bg-slate-50 border border-slate-100 rounded-2xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs outline-none text-slate-800 cursor-pointer appearance-none shadow-sm">
                                <option value="">All Medicines</option>
                                <template x-for="medicine in filteredMedicines" :key="medicine.id">
                                    <option :value="medicine.id" x-text="medicine.name"></option>
                                </template>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Pharmacist Select --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-user-doctor text-indigo-500"></i> Dispensed By
                        </label>
                        <div class="relative group">
                            <select x-model="filters.dispensed_by" @change="fetchData(1)" class="w-full px-4 py-4 pr-10 bg-slate-50 border border-slate-100 rounded-2xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs outline-none text-slate-800 cursor-pointer appearance-none shadow-sm">
                                <option value="">All Pharmacists</option>
                                @foreach($pharmacists ?? [] as $pharmacist)
                                    <option value="{{ $pharmacist->id }}">{{ $pharmacist->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Quick Filters (Restored Bento Section) --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 ml-1">
                            <i class="fas fa-bolt text-amber-500"></i> Quick Intelligence
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="applyQuickFilter('today')" 
                                :class="filters.date_from && filters.date_from === new Date().toISOString().split('T')[0] ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 border-blue-600' : 'bg-blue-50 border-blue-100 text-blue-600 hover:bg-blue-100'"
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-full border transition-all shadow-sm active:scale-95">
                                <i class="fas fa-certificate"></i> Today
                            </button>
                            <button @click="applyQuickFilter('week')" 
                                :class="filters.quick_type === 'week' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200 border-emerald-600' : 'bg-emerald-50 border-emerald-100 text-emerald-600 hover:bg-emerald-100'"
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-full border transition-all shadow-sm active:scale-95">
                                <i class="fas fa-calendar-day"></i> This Week
                            </button>
                            <button @click="applyQuickFilter('month')" 
                                :class="filters.quick_type === 'month' ? 'bg-purple-600 text-white shadow-lg shadow-purple-200 border-purple-600' : 'bg-purple-50 border-purple-100 text-purple-600 hover:bg-purple-100'"
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-full border transition-all shadow-sm active:scale-95">
                                <i class="fas fa-calendar-alt"></i> This Month
                            </button>
                            <button @click="applyQuickFilter('expiring')" 
                                :class="filters.expiry_status === 'expiring_soon' ? 'bg-amber-600 text-white shadow-lg shadow-amber-200 border-amber-600' : 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100'"
                                class="flex items-center justify-center gap-2 px-3 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-full border transition-all shadow-sm active:scale-95">
                                <i class="fas fa-exclamation-triangle"></i> Expiring Soon
                            </button>
                        </div>
                    </div>

                    {{-- Page Density Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-compress-alt text-indigo-500"></i> Page Density
                        </label>
                        <div class="grid grid-cols-2 gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200/50">
                            <button type="button" @click="density = 'condensed'"
                                :class="density === 'condensed' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Condensed
                            </button>
                            <button type="button" @click="density = 'spacious'"
                                :class="density === 'spacious' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Spacious
                            </button>
                        </div>
                    </div>

                    {{-- Records Per Page --}}
                    <div class="space-y-3 pb-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-list-ol text-indigo-500"></i> Records Per Page
                        </label>
                        <div class="grid grid-cols-4 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                            <template x-for="limit in [10, 25, 50, 100]" :key="limit">
                                <button @click="pagination.per_page = limit; fetchData(1)" 
                                    :class="pagination.per_page == limit ? 'bg-white text-indigo-600 shadow-sm border-0' : 'text-slate-400 hover:text-indigo-600 border-0'"
                                    class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all" x-text="limit">
                                </button>
                            </template>
                        </div>
                    </div>

                </div>

                <div class="p-6 pt-0 mt-auto flex flex-col gap-2">
                    <button @click="clearFilters()" class="rose-reset-btn w-full py-5 text-white rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 flex items-center justify-center gap-3 active:scale-95">
                        <i class="fas fa-broom"></i> Reset Filters
                    </button>
                    <button @click="showSidebar = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all flex items-center justify-between px-6">
                        <span>Hide Panel</span>
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         DETAIL MODAL (Enhanced like user management modals)
    ═══════════════════════════════════════════════ --}}
    <div x-show="showDetailModal" x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
             @click.outside="showDetailModal = false">

            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white px-8 py-5 border-b border-gray-200 flex justify-between items-center z-10 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                    Dispensation Details
                </h3>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-8 space-y-6" x-show="selectedItem">

                {{-- Medicine highlight --}}
                <div class="p-5 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Medicine</p>
                    <p class="text-2xl font-black text-gray-900" x-text="selectedItem?.prescription?.medicine?.name"></p>
                    <p class="text-sm text-indigo-500 font-semibold mt-1"
                       x-text="selectedItem?.prescription?.medicine?.generic_name ?? ''"></p>
                    <div class="flex gap-2 mt-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold"
                              :class="selectedItem?.medicine_batch?.batch_number ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'">
                            <i class="fas fa-tag mr-1"></i>
                            <span x-text="selectedItem?.medicine_batch?.batch_number ?? 'No batch'"></span>
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                            <i class="fas fa-box mr-1"></i>
                            <span x-text="selectedItem?.medicine_batch?.stock ?? 'N/A' + ' in stock'"></span>
                        </span>
                    </div>
                </div>

                {{-- Patient + Dispensed info grid --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Patient --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-user mr-1 text-blue-500"></i> Patient
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shadow"
                                 :class="getAvatarColor(selectedItem?.prescription?.diagnosis?.visit?.patient?.name ?? '')">
                                <span x-text="getInitials(selectedItem?.prescription?.diagnosis?.visit?.patient?.name ?? '?')"></span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800"
                                   x-text="selectedItem?.prescription?.diagnosis?.visit?.patient?.name ?? 'Unknown'"></p>
                                <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fas fa-id-card text-indigo-400"></i>
                                    <span x-text="selectedItem?.prescription?.diagnosis?.visit?.patient?.emrn ?? 'N/A'"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-cubes mr-1 text-orange-500"></i> Quantity
                        </p>
                        <div class="flex items-baseline gap-1">
                            <p class="text-4xl font-black text-indigo-600" x-text="selectedItem?.quantity_dispensed"></p>
                            <p class="text-sm text-gray-500">units</p>
                        </div>
                    </div>

                    {{-- Date & Time --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-calendar-alt mr-1 text-amber-500"></i> Date & Time
                        </p>
                        <p class="text-sm font-bold text-gray-800" x-text="formatDate(selectedItem?.dispensed_at)"></p>
                        <p class="text-sm text-gray-500 mt-1" x-text="formatTime(selectedItem?.dispensed_at)"></p>
                    </div>

                    {{-- Prescription ID --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                            <i class="fas fa-prescription mr-1 text-green-500"></i> Prescription
                        </p>
                        <p class="text-lg font-black text-gray-700 font-mono" x-text="'#' + (selectedItem?.prescription_id ?? '—')"></p>
                        <p class="text-xs text-indigo-500 font-medium mt-1 flex items-center gap-1">
                            <i class="fas fa-circle" :class="{
                                'text-green-500': selectedItem?.prescription?.status === 'completed',
                                'text-yellow-500': selectedItem?.prescription?.status === 'pending',
                                'text-blue-500': selectedItem?.prescription?.status === 'partially_dispensed',
                                'text-red-500': selectedItem?.prescription?.status === 'cancelled'
                            }"></i>
                            <span x-text="selectedItem?.prescription?.status?.toUpperCase().replace('_', ' ') ?? '—'"></span>
                        </p>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">
                        <i class="fas fa-sticky-note mr-1"></i> Notes / Instructions
                    </p>
                    <p class="text-sm text-gray-700 italic"
                       x-text="selectedItem?.notes || 'No special instructions provided.'"></p>
                </div>

                {{-- Pharmacist strip --}}
                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest">Dispensed By</p>
                        <p class="text-base font-bold" x-text="selectedItem?.dispensed_by?.name ?? 'System'"></p>
                        <p class="text-xs text-indigo-200" x-text="selectedItem?.dispensed_by?.email ?? ''"></p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-8 py-5 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl bg-gray-50/50">
                <button @click="showDetailModal = false"
                        class="px-6 py-2.5 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-times mr-2"></i> Close
                </button>
                <button @click="printLabel(selectedItem)"
                        class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-print mr-2"></i> Print Label
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function historyApp(initialStats = {}) {
    return {
        // ─── State ──────────────────────────────────────────
        loading: false,
        showSidebar: false,
        showAdvancedFilters: false,
        showDetailModal: false,
        selectedItem: null,
        density: 'spacious',

        // Filtered medicines based on selected category
        filteredMedicines: [],

        // Medicine data arrays (populate from backend)
        medicineCategories: @json($medicineCategories ?? []),
        medicines: @json($medicines ?? []),
        medicineForms: @json($medicineForms ?? []),
        pharmacists: @json($pharmacists ?? []),

        data: { data: [], current_page: 1, last_page: 1 },

        stats: {
            total_dispensed: initialStats.total_dispensed || 0,
            today_dispensed: initialStats.today_dispensed || 0,
            total_quantity:  initialStats.total_quantity || 0,
            unique_patients: initialStats.unique_patients || 0,
        },

        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
            from: 0,
            to: 0,
        },

        filters: {
            search: '',
            date_from: '',
            date_to: '',
            medicine_category_id: '',
            medicine_id: '',
            medicine_form_id: '',
            batch_number: '',
            manufacturer: '',
            prescription_status: '',
            dispensed_by: '',
            min_quantity: '',
            max_quantity: '',
            expiry_status: '',
            stock_status: '',
            min_price: '',
            max_price: '',
            quick_type: ''
        },

        sort: { field: 'dispensed_at', direction: 'desc' },

        // ─── Computed ────────────────────────────────────────
        get visiblePages() {
            const cur = this.pagination.current_page;
            const last = this.pagination.last_page;
            if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);

            const pages = [1];
            if (cur > 3) pages.push('…');
            for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
            if (cur < last - 2) pages.push('…');
            pages.push(last);
            return pages;
        },

        // ─── Lifecycle ───────────────────────────────────────
        init() {
            this.fetchData();
            this.initFilters(); // Initialize the watcher
        },

        // Initialize watchers for dependent filters
        initFilters() {
            this.$watch('filters.medicine_category_id', (value) => {
                if (value) {
                    this.filteredMedicines = this.medicines.filter(m => m.category_id == value);
                } else {
                    this.filteredMedicines = this.medicines;
                }
                // Reset medicine selection when category changes
                this.filters.medicine_id = '';
            });
            
            // Initialize filtered medicines
            this.filteredMedicines = this.medicines;
        },

        // ─── Data Fetching ───────────────────────────────────
        async fetchData(page = 1) {
            if (this.loading) return;
            if (page < 1 || (page > this.pagination.last_page && this.pagination.last_page > 0)) return;
            
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    search: this.filters.search || '',
                    date_from: this.filters.date_from || '',
                    date_to: this.filters.date_to || '',
                    medicine_category_id: this.filters.medicine_category_id || '',
                    medicine_id: this.filters.medicine_id || '',
                    medicine_form_id: this.filters.medicine_form_id || '',
                    batch_number: this.filters.batch_number || '',
                    manufacturer: this.filters.manufacturer || '',
                    prescription_status: this.filters.prescription_status || '',
                    dispensed_by: this.filters.dispensed_by || '',
                    min_quantity: this.filters.min_quantity || '',
                    max_quantity: this.filters.max_quantity || '',
                    expiry_status: this.filters.expiry_status || '',
                    stock_status: this.filters.stock_status || '',
                    min_price: this.filters.min_price || '',
                    max_price: this.filters.max_price || '',
                    per_page: this.pagination.per_page,
                    sort: this.sort.field,
                    direction: this.sort.direction,
                    _: Date.now()
                });

                const response = await fetch(`{{ route('pharmacy.dispense.history.data') }}?${params}`, {
                    headers: { 
                        'Accept': 'application/json', 
                        'X-Requested-With': 'XMLHttpRequest' 
                    }
                });
                
                if (!response.ok) throw new Error('Network response was not ok');
                
                const json = await response.json();
                this.data = json;

                this.pagination = {
                    current_page: json.current_page || 1,
                    last_page: json.last_page || 1,
                    per_page: json.per_page || 10,
                    total: json.total || 0,
                    from: json.from || 0,
                    to: json.to || 0
                };
            } catch (error) {
                console.error('Fetch error:', error);
                this.showToast('Failed to load records', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ─── Filters ─────────────────────────────────────────
        clearFilters() {
            this.filters = {
                search: '',
                date_from: '',
                date_to: '',
                medicine_category_id: '',
                medicine_id: '',
                medicine_form_id: '',
                batch_number: '',
                manufacturer: '',
                prescription_status: '',
                dispensed_by: '',
                min_quantity: '',
                max_quantity: '',
                expiry_status: '',
                stock_status: '',
                min_price: '',
                max_price: ''
            };
            this.filters.quick_type = '';
            this.sort.field = 'dispensed_at';
            this.sort.direction = 'desc';
            this.fetchData(1);
        },

        setTodayFilter() {
            const today = new Date().toISOString().split('T')[0];
            this.filters.date_from = today;
            this.filters.date_to = today;
            this.showAdvancedFilters = true;
            this.fetchData(1);
        },

        // Quick filter methods
        applyQuickFilter(type) {
            const today = new Date();
            
            switch(type) {
                case 'today':
                    const todayStr = today.toISOString().split('T')[0];
                    this.filters.date_from = todayStr;
                    this.filters.date_to = todayStr;
                    break;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    this.filters.date_from = weekAgo.toISOString().split('T')[0];
                    this.filters.date_to = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(today.getMonth() - 1);
                    this.filters.date_from = monthAgo.toISOString().split('T')[0];
                    this.filters.date_to = new Date().toISOString().split('T')[0];
                    break;
                case 'expiring':
                    this.filters.expiry_status = 'expiring_soon';
                    break;
            }
            this.filters.quick_type = type;
            this.fetchData(1);
        },

        // ─── Sorting ─────────────────────────────────────────
        sortBy(field) {
            if (this.sort.field === field) {
                this.sort.direction = this.sort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.field = field;
                this.sort.direction = 'asc';
            }
            this.fetchData(1);
        },

        // ─── Active Filters Helpers ─────────────────────────
        hasActiveFilters() {
            return Object.values(this.filters).some(value => 
                value !== '' && value !== null && value !== undefined
            );
        },

        getActiveFilterCount() {
            let count = 0;
            if (this.filters.search) count++;
            if (this.filters.date_from || this.filters.date_to) count++;
            if (this.filters.medicine_category_id) count++;
            if (this.filters.medicine_id) count++;
            if (this.filters.medicine_form_id) count++;
            if (this.filters.batch_number) count++;
            if (this.filters.manufacturer) count++;
            if (this.filters.prescription_status) count++;
            if (this.filters.dispensed_by) count++;
            if (this.filters.min_quantity || this.filters.max_quantity) count++;
            if (this.filters.expiry_status) count++;
            if (this.filters.stock_status) count++;
            if (this.filters.min_price || this.filters.max_price) count++;
            return count;
        },

        getDateRangeText() {
            if (this.filters.date_from && this.filters.date_to) {
                return `${this.filters.date_from} to ${this.filters.date_to}`;
            } else if (this.filters.date_from) {
                return `From ${this.filters.date_from}`;
            } else if (this.filters.date_to) {
                return `Until ${this.filters.date_to}`;
            }
            return '';
        },

        getCategoryName(id) {
            if (!id) return '';
            const category = this.medicineCategories.find(c => c.id == id);
            return category ? category.name : 'Unknown';
        },

        getMedicineName(id) {
            if (!id) return '';
            const medicine = this.medicines.find(m => m.id == id);
            return medicine ? medicine.name : 'Unknown';
        },

        getFormName(id) {
            if (!id) return '';
            const form = this.medicineForms.find(f => f.id == id);
            return form ? form.name : 'Unknown';
        },

        // ─── Modal ───────────────────────────────────────────
        openDetails(item) {
            this.selectedItem = item;
            this.showDetailModal = true;
        },

        printLabel(item) {
            if (!item) return;
            window.open(`{{ url('pharmacy/prescriptions') }}/${item.prescription_id}/label`, '_blank');
        },

        // ─── Toast Notification ──────────────────────────────
        showToast(message, type = 'info') {
            if (window.showNotification) {
                window.showNotification(message, type);
            } else if (window.toastr) {
                window.toastr[type](message);
            } else {
                alert(message);
            }
        },

        // ─── Helpers ─────────────────────────────────────────
        formatDate(str) {
            if (!str) return '—';
            try {
                return new Date(str).toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
            } catch {
                return '—';
            }
        },

        formatTime(str) {
            if (!str) return '';
            try {
                return new Date(str).toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                });
            } catch {
                return '';
            }
        },

        getInitials(name) {
            if (!name) return '?';
            return name.split(' ')
                .filter(Boolean)
                .slice(0, 2)
                .map(w => w[0].toUpperCase())
                .join('');
        },

        getAvatarColor(name) {
            const colors = [
                'bg-gradient-to-br from-indigo-400 to-indigo-600',
            ];
            if (!name) return colors[0];
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return colors[Math.abs(hash) % colors.length];
        },
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection