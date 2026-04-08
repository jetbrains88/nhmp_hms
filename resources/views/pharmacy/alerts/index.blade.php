@extends('layouts.app')

@section('title', 'Stock Alerts - NHMP HMS')
@section('page-title', 'Stock Alerts')
@section('breadcrumb', 'Pharmacy / Alerts')

@section('content')
<div x-data="stockAlerts()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-rose-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(225,29,72,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(225,29,72,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-rose-100 group cursor-pointer"
        title="Open Security Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-rose-500/10 blur-xl rounded-full group-hover:bg-rose-500/20 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-rose-400">Security Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 gap-y-10 mt-4">
        <!-- Total Active Alerts Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setFilter('active')">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-exclamation-triangle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-rose-500 uppercase opacity-70">Total Active</p>
                <h4 class="text-3xl font-black text-rose-700 drop-shadow-sm font-mono" x-text="stats.total_active ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-[9px] text-rose-700 font-black uppercase tracking-tight">Active Alerts</span>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('low_stock')">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-lg shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-boxes text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-amber-500 uppercase opacity-70">Low Stock</p>
                <h4 class="text-3xl font-black text-amber-700 drop-shadow-sm font-mono" x-text="stats.low_stock ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-amber-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                    <span class="text-[9px] text-amber-700 font-black uppercase tracking-tight">Reorder Level</span>
                </div>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl shadow-lg shadow-red-500/10 border border-red-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('out_of_stock')">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 shadow-lg shadow-red-900/20 border border-red-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-times-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-red-500 uppercase opacity-70">Out Of Stock</p>
                <h4 class="text-3xl font-black text-red-700 drop-shadow-sm font-mono" x-text="stats.out_of_stock ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-red-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                    <span class="text-[9px] text-red-700 font-black uppercase tracking-tight">Zero Balance</span>
                </div>
            </div>
        </div>

        <!-- Expiring Soon Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('expiring_soon')">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-days text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-purple-500 uppercase opacity-70">Expiring Soon</p>
                <h4 class="text-3xl font-black text-purple-700 drop-shadow-sm font-mono" x-text="stats.expiring_soon ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">30 Day Window</span>
                </div>
            </div>
        </div>

        <!-- Resolved Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setFilter('resolved')">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-emerald-500 uppercase opacity-70">Resolved</p>
                <h4 class="text-3xl font-black text-emerald-700 drop-shadow-sm font-mono" x-text="stats.total_resolved ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                    <span class="text-[9px] text-emerald-700 font-black uppercase tracking-tight">History Log</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Table Container --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">
            
                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 border-b border-rose-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-rose-100 shadow-sm transition-transform hover:scale-105 duration-300 text-rose-600">
                                <i class="fas fa-bell text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-orange-600 tracking-tight flex items-center gap-3">
                                    Stock Alerts
                                    <span class="text-lg font-normal text-gray-500">
                                        (<span x-text="pagination.total"></span> alerts)
                                    </span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Monitor and resolve critical inventory conditions.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 items-center">
                            <div class="flex items-center gap-2 bg-white border border-rose-100 rounded-xl px-3 py-1.5 shadow-sm">
                                <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row Density</span>
                                <select x-model="pagination.per_page" @change="fetchAlerts(1)" class="bg-transparent text-rose-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                    <option value="15">15 Per Page</option>
                                    <option value="30">30 Per Page</option>
                                    <option value="50">50 Per Page</option>
                                    <option value="100">100 Per Page</option>
                                </select>
                            </div>

                            <button @click="fetchAlerts()" 
                                class="w-10 h-10 flex items-center justify-center bg-white border border-rose-100 text-rose-600 rounded-xl hover:bg-rose-50 transition-colors shadow-sm group">
                                <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700" :class="loading ? 'animate-spin opacity-50' : ''"></i>
                            </button>
                            
                            <button @click="showSidebar = !showSidebar"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-rose-100 text-rose-600 rounded-xl hover:bg-rose-50 transition-colors shadow-sm relative"
                                :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                                <span x-show="hasActiveFilters()" class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 border-2 border-white rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bulk Actions Toolbar --}}
                <div x-show="selectedAlerts.length > 0" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="-translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     class="bg-rose-600 px-6 py-3 flex items-center justify-between text-white sticky top-0 z-10 shadow-2xl">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                            <span x-text="selectedAlerts.length"></span> Alerts Selected
                        </span>
                        <button @click="showBulkResolveModal = true" class="px-3 py-1.5 bg-white text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-rose-50 transition-all shadow-lg">Resolve Selection</button>
                    </div>
                    <button @click="selectedAlerts = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 flex items-center gap-2">
                        Dismiss <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- View Content --}}
                <div class="relative min-h-[400px]">
                    <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 border-4 border-rose-100 border-t-rose-600 rounded-full animate-spin shadow-inner"></div>
                            <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest animate-pulse">Syncing Data...</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" :class="density === 'condensed' ? 'condensed-table' : 'spacious-table'">
                            <thead class="bg-white border-b border-rose-100">
                                <tr>
                                    <th class="px-5 py-4 w-12 text-center">
                                        <input type="checkbox" @change="toggleSelectAll" :checked="selectedAlerts.length === data.data?.length && data.data?.length > 0"
                                            class="w-5 h-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                                    </th>
                                    <th class="px-5 py-4">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                                                <i class="fas fa-pills text-[10px]"></i>
                                            </div>
                                            <button @click="sortBy('medicine.name')" class="flex items-center gap-1.5 hover:text-rose-700 transition-colors group">
                                                Medicine / Type
                                                <i class="fas text-[10px] opacity-20 group-hover:opacity-100" :class="sort.field === 'medicine.name' ? (sort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort'"></i>
                                            </button>
                                        </div>
                                    </th>
                                    <th class="px-5 py-4">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                                                <i class="fas fa-info-circle text-[10px]"></i>
                                            </div>
                                            Alert Details
                                        </div>
                                    </th>
                                    <th class="px-5 py-4">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                                                <i class="fas fa-calendar text-[10px]"></i>
                                            </div>
                                            Raised On
                                        </div>
                                    </th>

                                    <th class="px-5 py-4 text-center whitespace-nowrap w-32 border-b border-slate-50">
                                        <div
                                            class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-100">
                                                <i class="fas fa-bolt-lightning text-[10px]"></i>
                                            </div>
                                            Control Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-if="loading">
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-500">
                                            <i class="fas fa-circle-notch fa-spin text-3xl text-rose-400 mb-2"></i>
                                            <p class="text-sm font-medium">Syncing Data...</p>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="!loading && (!data.data || data.data.length === 0)">
                                    <tr>
                                        <td colspan="5" class="py-24 text-center">
                                            <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                                <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                            </div>
                                            <h3 class="text-2xl font-black text-slate-800 mb-2">No Alerts Found</h3>
                                            <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">Everything seems to be in order. No active inventory threats detected.</p>
                                            <button @click="clearFilters()" class="px-8 py-3 bg-rose-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-rose-500/40 hover:scale-105 transition-all">Reset Sync</button>
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="alert in data.data" :key="alert.id">
                                    <tr class="hover:bg-rose-50/40 transition-colors group" :class="density === 'condensed' ? 'bg-white' : ''">
                                        <td class="px-5 border-b border-slate-50 transition-all text-center" :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                            <input type="checkbox" :value="alert.id" x-model="selectedAlerts" class="w-5 h-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        </td>
                                        <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-rose-50 text-rose-600 border border-rose-100 shrink-0">
                                                    <i class="fas fa-pills" :class="alert.alert_type === 'low_stock' ? 'fa-boxes' : (alert.alert_type === 'out_of_stock' ? 'fa-times' : 'fa-calendar')"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-navy-800 uppercase tracking-tight" x-text="alert.medicine?.name"></p>
                                                    <div class="flex gap-1.5 mt-1">
                                                        <span x-show="alert.alert_type === 'low_stock'" class="text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">Low Stock</span>
                                                        <span x-show="alert.alert_type === 'out_of_stock'" class="text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-red-100 text-red-700">Out Of Stock</span>
                                                        <span x-show="alert.alert_type === 'expiring_soon'" class="text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-purple-100 text-purple-700">Expiring</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                            <template x-if="alert.alert_type === 'low_stock'">
                                                <div class="text-[10px] font-bold text-slate-600 uppercase tracking-wide">
                                                    Stock: <span class="text-amber-600 font-black" x-text="alert.medicine?.stock ?? 0"></span> 
                                                    / Reorder: <span x-text="alert.medicine?.reorder_level ?? 0"></span>
                                                </div>
                                            </template>
                                            <template x-if="alert.alert_type === 'out_of_stock'">
                                                <div class="text-[10px] font-black text-red-600 uppercase tracking-widest animate-pulse">Critical: Zero Balance</div>
                                            </template>
                                            <template x-if="alert.alert_type === 'expiring_soon'">
                                                <div class="text-[10px] font-bold text-purple-600 uppercase tracking-tight" x-text="getDaysUntilExpiry(alert.medicine?.expiry_date)"></div>
                                            </template>
                                        </td>
                                        <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-slate-800" x-text="formatDate(alert.created_at)"></span>
                                                <span class="text-[9px] font-bold text-slate-400" x-text="formatTime(alert.created_at)"></span>
                                            </div>
                                        </td>

                                        <td class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap" :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button x-show="!alert.is_resolved" @click="openResolveModal(alert)" class="h-8 px-3 flex items-center justify-center bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100 text-[10px] font-black uppercase tracking-widest gap-2">
                                                    <i class="fas fa-check-circle"></i> Resolve
                                                </button>
                                                <a :href="`/pharmacy/medicines/${alert.medicine_id}`" class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100" title="View Medicine">
                                                    <i class="fas fa-eye text-[10px]"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination Footer --}}
                <div x-show="!loading && data.data?.length > 0" class="p-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from"></span> - <span class="text-slate-900" x-text="pagination.to"></span> 
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                            Capacity: <span class="text-rose-600" x-text="pagination.total"></span> Entries
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button @click="fetchAlerts(1)" :disabled="pagination.current_page === 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-rose-600 hover:text-rose-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-left text-[10px]"></i></button>
                            <button @click="fetchAlerts(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-rose-600 hover:text-rose-600 disabled:opacity-30 transition-all"><i class="fas fa-chevron-left text-[10px]"></i> <span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Prev</span></button>
                            
                            <div class="flex items-center gap-1 px-1">
                                <template x-for="page in visiblePages" :key="page">
                                    <button @click="page !== '…' && fetchAlerts(page)" 
                                        :class="page === pagination.current_page ? 'bg-rose-600 text-white shadow-lg border-rose-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-rose-600'"
                                        x-text="page" class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"></button>
                                </template>
                            </div>

                            <button @click="fetchAlerts(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-rose-600 hover:text-rose-600 disabled:opacity-30 transition-all"><span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Next</span> <i class="fas fa-chevron-right text-[10px]"></i></button>
                            <button @click="fetchAlerts(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-rose-600 hover:text-rose-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-right text-[10px]"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Sticky Sidebar --}}
        <div class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto pr-2" 
             x-show="showSidebar" x-transition.opacity>
            
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Filters</h2>
                    </div>
                    <button @click="showSidebar = false" class="text-slate-400 hover:text-rose-600 transition-colors"><i class="fas fa-times"></i></button>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Search --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-search text-rose-500"></i> Search Inventory
                        </label>
                        <div class="relative group">
                            <input type="text" x-model.debounce.500ms="filters.search" @input="fetchAlerts(1)" placeholder="Medicine SKU..." class="w-full pl-10 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-rose-400 transition-all font-bold text-xs ring-0">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-rose-500"></i>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-shield-virus text-rose-500"></i> Security Status
                        </label>
                        <div class="grid grid-cols-1 gap-2">
                            <button @click="filters.status = 'active'; fetchAlerts(1)"
                                :class="filters.status === 'active' ?
                                    'bg-rose-600 text-white shadow-lg shadow-rose-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Active</span>
                                <i class="fas fa-exclamation-circle transition-opacity" :class="filters.status === 'active' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filters.status = 'resolved'; fetchAlerts(1)"
                                :class="filters.status === 'resolved' ?
                                    'bg-emerald-600 text-white shadow-lg shadow-emerald-200' :
                                    'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                <span>Resolved</span>
                                <i class="fas fa-check-circle transition-opacity" :class="filters.status === 'resolved' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Page Density Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-compress-alt text-rose-500"></i> Page Density
                        </label>
                        <div class="grid grid-cols-2 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                            <button @click="density = 'condensed'"
                                :class="density === 'condensed' ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Condensed
                            </button>
                            <button @click="density = 'spacious'"
                                :class="density === 'spacious' ? 'bg-white text-rose-600 shadow-sm font-black' : 'text-slate-500 hover:text-slate-700'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                Spacious
                            </button>
                        </div>
                    </div>

                    {{-- Records Per Page --}}
                    <div class="space-y-3 pb-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-list-ol text-rose-500"></i> Records Per Page
                        </label>
                        <div class="grid grid-cols-4 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                            <template x-for="limit in [10, 25, 50, 100]" :key="limit">
                                <button @click="pagination.per_page = limit; fetchAlerts(1)" 
                                    :class="pagination.per_page == limit ? 'bg-white text-rose-600 shadow-sm border-0' : 'text-slate-400 hover:text-rose-600 border-0'"
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

    {{-- Modals --}}
    @include('pharmacy.alerts.modals')

</div>

<script>
function stockAlerts() {
    return {
        // State
        showSidebar: false,
        loading: false,
        density: 'spacious',
        showResolveModal: false,
        showBulkResolveModal: false,
        selectedAlert: null,
        selectedAlerts: [],
        resolutionNotes: '',
        bulkResolutionNotes: '',

        // Data
        data: { data: [], current_page: 1, last_page: 1 },
        stats: { total_active: 0, low_stock: 0, out_of_stock: 0, expiring_soon: 0, total_resolved: 0 },
        medicines: @json($medicines),

        pagination: { current_page: 1, last_page: 1, per_page: 15, total: 0, from: 0, to: 0 },
        filters: { search: '', alert_type: '', medicine_id: '', date_from: '', date_to: '', status: 'active' },
        sort: { field: 'created_at', direction: 'desc' },

        // Computed
        get visiblePages() {
            const cur = this.pagination.current_page;
            const last = this.pagination.last_page;
            if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);
            const pages = [1];
            if (cur > 3) pages.push('…');
            for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
            if (cur < last - 2) pages.push('…');
            if (last > 1) pages.push(last);
            return pages;
        },

        init() {
            this.fetchAlerts();
            this.fetchStats();
        },

        async fetchAlerts(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    ...this.filters,
                    per_page: this.pagination.per_page,
                    sort: this.sort.field,
                    direction: this.sort.direction
                });

                const response = await fetch(`{{ route('pharmacy.alerts.data') }}?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                const json = await response.json();
                this.data = json;
                this.pagination = {
                    current_page: json.current_page,
                    last_page: json.last_page,
                    per_page: json.per_page,
                    total: json.total,
                    from: json.from,
                    to: json.to
                };
                this.selectedAlerts = [];
            } catch (error) {
                this.notify('Failed to load vault data', 'error');
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                const response = await fetch(`{{ route('pharmacy.alerts.stats') }}`);
                this.stats = await response.json();
            } catch (error) {}
        },

        setFilter(status) {
            this.filters.status = status;
            this.filters.alert_type = '';
            this.fetchAlerts(1);
        },

        setAlertTypeFilter(type) {
            this.filters.alert_type = type;
            this.filters.status = 'active';
            this.fetchAlerts(1);
        },

        clearFilters() {
            this.filters = { search: '', alert_type: '', medicine_id: '', date_from: '', date_to: '', status: 'active' };
            this.fetchAlerts(1);
        },

        hasActiveFilters() {
            return this.filters.search || this.filters.alert_type || (this.filters.status !== 'active');
        },

        sortBy(field) {
            if (this.sort.field === field) {
                this.sort.direction = this.sort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.field = field;
                this.sort.direction = 'asc';
            }
            this.fetchAlerts(1);
        },

        toggleSelectAll() {
            if (this.selectedAlerts.length === this.data.data.length) {
                this.selectedAlerts = [];
            } else {
                this.selectedAlerts = this.data.data.map(a => a.id);
            }
        },

        openResolveModal(alert) {
            this.selectedAlert = alert;
            this.resolutionNotes = '';
            this.showResolveModal = true;
        },

        closeResolveModal() {
            this.showResolveModal = false;
            this.selectedAlert = null;
            this.resolutionNotes = '';
        },

        async confirmResolve() {
            try {
                const response = await fetch(`/pharmacy/alerts/${this.selectedAlert.id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ resolution_notes: this.resolutionNotes })
                });

                const result = await response.json();
                if (result.success) {
                    this.notify(result.message, 'success');
                    this.showResolveModal = false;
                    this.fetchAlerts(this.pagination.current_page);
                    this.fetchStats();
                }
            } catch (error) {
                this.notify('Resolution failed', 'error');
            }
        },

        closeBulkResolveModal() {
            this.showBulkResolveModal = false;
            this.bulkResolutionNotes = '';
        },

        async confirmBulkResolve() {
            try {
                const response = await fetch(`/pharmacy/alerts/bulk/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        alert_ids: this.selectedAlerts,
                        resolution_notes: this.bulkResolutionNotes
                    })
                });

                const result = await response.json();
                if (result.success) {
                    this.notify(result.message, 'success');
                    this.showBulkResolveModal = false;
                    this.selectedAlerts = [];
                    this.fetchAlerts(1);
                    this.fetchStats();
                }
            } catch (error) {
                this.notify('Bulk resolution failed', 'error');
            }
        },

        formatDate(str) {
            if (!str) return '—';
            return new Date(str).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        },

        formatTime(str) {
            if (!str) return '';
            return new Date(str).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        },

        getDaysUntilExpiry(date) {
            if (!date) return '';
            const diff = new Date(date) - new Date();
            const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
            return days < 0 ? 'Expired' : `${days} Days Remain`;
        },

        notify(message, type) {
            if (window.Notification) {
                if (type === 'success') window.Notification.success(message);
                else if (type === 'error') window.Notification.error(message);
                else window.Notification.info(message);
            } else if (window.showNotification) {
                window.showNotification(message, type);
            } else {
                alert(message);
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
