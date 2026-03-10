@extends('layouts.app')

@section('title', 'Stock Alerts - NHMP HMS')
@section('page-title', 'Stock Alerts')
@section('breadcrumb', 'Pharmacy / Alerts')

@section('content')
<div x-data="stockAlerts()" x-init="init()" x-cloak class="space-y-6">

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Same style as User Management
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 gap-y-10 mt-8 p-4">

        {{-- Total Active Alerts Card (Red) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setFilter('active')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-red-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-exclamation-triangle text-2xl drop-shadow-md text-rose-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">
                    Total Active
                </p>
                <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono" x-text="stats.total_active ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-xs text-rose-700 font-medium">Active alerts</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view all active alerts</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Low Stock Card (Yellow) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl shadow-lg shadow-yellow-500/30 border border-yellow-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('low_stock')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-yellow-500 to-amber-300 shadow-lg shadow-yellow-900/40 border border-yellow-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-boxes text-2xl drop-shadow-md text-yellow-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-yellow-600 uppercase">
                    Low Stock
                </p>
                <h4 class="block antialiased text-3xl font-bold text-yellow-800 drop-shadow-md font-mono" x-text="stats.low_stock ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-yellow-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                    <span class="text-xs text-yellow-700 font-medium">Below reorder level</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to filter low stock alerts</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Out of Stock Card (Red/Orange) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-lg shadow-orange-500/30 border border-orange-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('out_of_stock')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-orange-500 to-red-300 shadow-lg shadow-orange-900/40 border border-orange-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-times-circle text-2xl drop-shadow-md text-orange-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-orange-600 uppercase">
                    Out of Stock
                </p>
                <h4 class="block antialiased text-3xl font-bold text-orange-800 drop-shadow-md font-mono" x-text="stats.out_of_stock ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-orange-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-600"></span>
                    <span class="text-xs text-orange-700 font-medium">Zero stock</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view out of stock alerts</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expiring Soon Card (Purple) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setAlertTypeFilter('expiring_soon')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-days text-2xl drop-shadow-md text-purple-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                    Expiring Soon
                </p>
                <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono" x-text="stats.expiring_soon ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    <span class="text-xs text-purple-700 font-medium">Within 30 days</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view expiring soon alerts</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resolved Card (Green) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setFilter('resolved')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-2xl drop-shadow-md text-emerald-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-emerald-600 uppercase">
                    Resolved
                </p>
                <h4 class="block antialiased text-3xl font-bold text-emerald-800 drop-shadow-md font-mono" x-text="stats.total_resolved ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                    <span class="text-xs text-emerald-700 font-medium">Completed</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view resolved alerts</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN ALERTS TABLE PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Panel Header --}}
        <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 border-b border-rose-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fas fa-bell text-rose-600"></i>
                        Stock Alerts Management
                        <span class="text-lg font-normal text-gray-500">
                            (<span x-text="pagination.total"></span> alerts)
                        </span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Monitor and resolve inventory alerts in real-time</p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    {{-- Per page --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Show:</span>
                        <select x-model="pagination.per_page" @change="fetchAlerts(1)"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-rose-500 outline-none">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    {{-- Bulk Actions --}}
                    <div class="flex gap-2">
                        <button @click="fetchAlerts(pagination.current_page)" :disabled="loading"
                            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                            <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
                            Refresh
                        </button>
                        <button @click="showBulkResolveModal = true" 
                                x-show="selectedAlerts.length > 0"
                                x-transition
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-check-double"></i>
                            Resolve Selected (<span x-text="selectedAlerts.length"></span>)
                        </button>
                        <button @click="showAdvancedFilters = !showAdvancedFilters"
                            :class="showAdvancedFilters ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-orange-500 to-orange-600'"
                            class="flex items-center gap-2 px-4 py-2 text-white rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-filter"></i>
                            Filters
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Advanced Filters (collapsible) ── --}}
            <div x-show="showAdvancedFilters" x-transition
                 class="mt-6 p-6 bg-white rounded-xl bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-2 relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-magnifying-glass text-blue-500 group-focus-within:text-rose-500 transition-colors"></i>
                        </div>
                        <input type="text" x-model="filters.search"
                               @input.debounce.500ms="fetchAlerts(1)"
                               placeholder="Search by medicine name, generic name..."
                               class="pl-11 w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none bg-white text-sm transition-all shadow-sm group-hover:shadow-md">
                    </div>

                    {{-- Alert Type Filter --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-exclamation-circle text-rose-500 mr-1"></i>Alert Type
                        </label>
                        <select x-model="filters.alert_type" @change="fetchAlerts(1)"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                            <option value="">All Types</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="expiring_soon">Expiring Soon</option>
                        </select>
                    </div>

                    {{-- Medicine Filter --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-pills text-blue-500 mr-1"></i>Medicine
                        </label>
                        <select x-model="filters.medicine_id" @change="fetchAlerts(1)"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                            <option value="">All Medicines</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    {{-- Date From --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-calendar-plus text-amber-500 mr-1"></i>Date From
                        </label>
                        <input type="date" x-model="filters.date_from" @change="fetchAlerts(1)"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-calendar-minus text-amber-500 mr-1"></i>Date To
                        </label>
                        <input type="date" x-model="filters.date_to" @change="fetchAlerts(1)"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-flag text-green-500 mr-1"></i>Status
                        </label>
                        <select x-model="filters.status" @change="fetchAlerts(1)"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                            <option value="active">Active Only</option>
                            <option value="resolved">Resolved Only</option>
                            <option value="">All Statuses</option>
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            <i class="fas fa-sort text-gray-500 mr-1"></i>Sort By
                        </label>
                        <select x-model="sort.field" @change="fetchAlerts(1)"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none bg-white text-sm">
                            <option value="created_at">Date Created</option>
                            <option value="alert_type">Alert Type</option>
                            <option value="medicine.name">Medicine Name</option>
                            <option value="resolved_at">Resolution Date</option>
                        </select>
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center justify-between gap-4 mt-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600">Quick Filters:</span>
                        <button @click="applyQuickFilter('today')" 
                                class="px-3 py-1.5 bg-rose-100 text-rose-700 rounded-full text-xs font-medium hover:bg-rose-200 transition-colors">
                            <i class="fas fa-sun mr-1"></i>Today
                        </button>
                        <button @click="applyQuickFilter('week')" 
                                class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium hover:bg-amber-200 transition-colors">
                            <i class="fas fa-calendar-week mr-1"></i>This Week
                        </button>
                        <button @click="applyQuickFilter('month')" 
                                class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200 transition-colors">
                            <i class="fas fa-calendar-alt mr-1"></i>This Month
                        </button>
                        <button @click="applyQuickFilter('expiring')" 
                                class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-full text-xs font-medium hover:bg-orange-200 transition-colors">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Expiring
                        </button>
                    </div>

                    <button @click="clearFilters()"
                            class="flex items-center justify-center text-white py-2.5 px-6
                                   bg-gradient-to-r from-rose-500 to-rose-600
                                   rounded-lg font-medium hover:from-rose-600 hover:to-rose-700
                                   transition-all gap-2 shadow-md hover:shadow-lg">
                        <i class="fas fa-filter-circle-xmark"></i>
                        Clear All Filters
                    </button>
                </div>

                {{-- Active Filters Summary --}}
                <div x-show="hasActiveFilters()"
                     class="flex flex-wrap items-center gap-2 mt-6 pt-4 border-t border-purple-200">
                    <span class="text-xs font-medium text-gray-500 mr-2">
                        <i class="fas fa-filter mr-1"></i>Active filters:
                    </span>

                    <template x-if="filters.search">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                            <i class="fas fa-search"></i>
                            <span x-text="filters.search"></span>
                            <button @click="filters.search=''; fetchAlerts(1)" class="ml-1 hover:text-blue-900">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    </template>

                    <template x-if="filters.alert_type">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-100 text-rose-700 rounded-full text-xs font-medium">
                            <i class="fas fa-exclamation-circle"></i>
                            <span x-text="filters.alert_type.replace('_', ' ').toUpperCase()"></span>
                            <button @click="filters.alert_type=''; fetchAlerts(1)" class="ml-1 hover:text-rose-900">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    </template>

                    <template x-if="filters.medicine_id">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                            <i class="fas fa-pills"></i>
                            <span x-text="getMedicineName(filters.medicine_id)"></span>
                            <button @click="filters.medicine_id=''; fetchAlerts(1)" class="ml-1 hover:text-indigo-900">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    </template>

                    <template x-if="filters.status && filters.status !== 'active'">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                            <i class="fas fa-flag"></i>
                            <span x-text="filters.status.toUpperCase()"></span>
                            <button @click="filters.status='active'; fetchAlerts(1)" class="ml-1 hover:text-emerald-900">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    </template>

                    <template x-if="filters.date_from || filters.date_to">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">
                            <i class="fas fa-calendar-alt"></i>
                            <span x-text="getDateRangeText()"></span>
                            <button @click="filters.date_from=''; filters.date_to=''; fetchAlerts(1)" class="ml-1 hover:text-amber-900">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </span>
                    </template>

                    <span x-show="getActiveFilterCount() > 0" 
                          class="text-xs text-gray-500 ml-2">
                        ( <span x-text="getActiveFilterCount()"></span> filters )
                    </span>
                    
                    <button @click="clearFilters()" 
                            class="text-xs text-rose-600 hover:text-rose-800 underline ml-auto">
                        Clear All
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Table with Checkboxes --}}
        <div class="overflow-x-auto relative min-h-[300px]">

            {{-- Loading overlay --}}
            <div x-show="loading"
                 class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 border-4 border-rose-200 border-t-rose-600 rounded-full animate-spin"></div>
                    <p class="text-sm text-rose-600 font-semibold">Loading alerts...</p>
                    <p class="text-xs text-gray-400">Please wait while we fetch the data</p>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
               <thead class="bg-gradient-to-r from-rose-100 to-orange-100">
                    <tr>
                        <th class="px-5 py-4 text-left w-10">
                            <input type="checkbox" 
                                   @change="toggleSelectAll" 
                                   :checked="selectedAlerts.length === data.data?.length && data.data?.length > 0"
                                   class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                        </th>
                        <th class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-pills text-blue-500"></i>
                                <button @click="sortBy('medicine.name')" class="flex items-center gap-1 hover:text-rose-600">
                                    Medicine
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-rose-600': sort.field === 'medicine.name' }"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                                <button @click="sortBy('alert_type')" class="flex items-center gap-1 hover:text-rose-600">
                                    Alert Type
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-rose-600': sort.field === 'alert_type' }"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-info-circle text-cyan-500"></i>
                                Details
                            </div>
                        </th>
                        <th class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-calendar-alt text-amber-500"></i>
                                <button @click="sortBy('created_at')" class="flex items-center gap-1 hover:text-rose-600">
                                    Raised On
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-rose-600': sort.field === 'created_at' }"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user-check text-green-500"></i>
                                Resolved By
                            </div>
                        </th>
                        <th class="px-5 py-4 text-center">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cogs text-orange-500"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && data.data && data.data.length > 0">
                    <template x-for="alert in data.data" :key="alert.id">
                        <tr class="hover:bg-rose-50/30 transition-colors duration-200 group"
                            :class="{ 'bg-rose-50/50': selectedAlerts.includes(alert.id) }">

                            {{-- Checkbox --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       :value="alert.id"
                                       x-model="selectedAlerts"
                                       class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            </td>

                            {{-- Medicine Column --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform border border-gray-200">
                                        <i class="fas fa-pills text-gray-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-navy-800" x-text="alert.medicine?.name"></p>
                                        <p class="text-xs text-gray-500" x-text="alert.medicine?.category?.name ?? 'Uncategorized'"></p>
                                    </div>
                                </div>
                            </td>

                            {{-- Alert Type Badge --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span x-show="alert.alert_type === 'low_stock'"
                                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Low Stock
                                </span>
                                <span x-show="alert.alert_type === 'out_of_stock'"
                                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle"></i>
                                    Out of Stock
                                </span>
                                <span x-show="alert.alert_type === 'expiring_soon'"
                                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                    <i class="fas fa-calendar-days"></i>
                                    Expiring Soon
                                </span>
                            </td>

                            {{-- Details Column --}}
                            <td class="px-5 py-4">
                                <template x-if="alert.alert_type === 'low_stock'">
                                    <div class="text-sm">
                                        <p class="text-gray-900 font-medium">Current Stock: <span class="text-rose-600 font-bold" x-text="alert.medicine?.stock ?? 0"></span></p>
                                        <p class="text-xs text-gray-500">Reorder Level: <span x-text="alert.medicine?.reorder_level ?? 0"></span></p>
                                    </div>
                                </template>
                                <template x-if="alert.alert_type === 'out_of_stock'">
                                    <div class="text-sm">
                                        <p class="text-red-600 font-bold">Stock Level: 0</p>
                                        <p class="text-xs text-gray-500">Immediate attention required</p>
                                    </div>
                                </template>
                                <template x-if="alert.alert_type === 'expiring_soon'">
                                    <div class="text-sm">
                                        <p class="text-gray-900">Expiry: <span class="text-orange-600 font-bold" x-text="formatDate(alert.medicine?.expiry_date)"></span></p>
                                        <p class="text-xs text-gray-500" x-text="getDaysUntilExpiry(alert.medicine?.expiry_date)"></p>
                                    </div>
                                </template>
                                <div x-show="alert.is_resolved && alert.resolution_notes" class="mt-2 text-xs text-gray-500 italic">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    <span x-text="alert.resolution_notes"></span>
                                </div>
                            </td>

                            {{-- Raised On --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900" x-text="formatDate(alert.created_at)"></span>
                                    <span class="text-xs text-gray-500" x-text="formatTime(alert.created_at)"></span>
                                </div>
                            </td>

                            {{-- Resolved By / Status --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div x-show="alert.is_resolved" class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" x-text="alert.resolved_by?.name ?? 'System'"></p>
                                        <p class="text-xs text-gray-500" x-text="alert.resolved_at ? formatDate(alert.resolved_at) : ''"></p>
                                    </div>
                                </div>
                                <div x-show="!alert.is_resolved" class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        <i class="fas fa-clock mr-1"></i>
                                        Pending
                                    </span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button x-show="!alert.is_resolved" 
                                            @click="openResolveModal(alert)"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-xs font-bold transition-all duration-200"
                                            title="Resolve Alert">
                                        <i class="fas fa-check-circle"></i>
                                        Resolve
                                    </button>
                                    <a :href="`/pharmacy/medicines/${alert.medicine_id}`" 
                                       class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg text-xs font-bold transition-all duration-200"
                                       title="View Medicine">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
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
                                    <i class="fas fa-check-circle text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No alerts found</h3>
                                <p class="text-gray-500 max-w-md mb-4">
                                    <span x-show="hasActiveFilters()">
                                        Try adjusting your filters to see more results
                                    </span>
                                    <span x-show="!hasActiveFilters()">
                                        All stock levels are within acceptable limits. No active alerts.
                                    </span>
                                </p>
                                <button @click="clearFilters()" x-show="hasActiveFilters()"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                    <i class="fas fa-sync-alt"></i>
                                    Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ── Pagination footer ── --}}
        <div x-show="!loading && data.data && data.data.length > 0" 
             class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                
                {{-- Pagination Info --}}
                <div class="text-sm text-gray-700">
                    Showing <span class="font-bold text-gray-900" x-text="pagination.from ?? 0"></span>
                    to <span class="font-bold text-gray-900" x-text="pagination.to ?? 0"></span>
                    of <span class="font-bold text-rose-600" x-text="pagination.total"></span> results
                </div>

                {{-- Pagination Controls --}}
                <nav class="flex items-center space-x-2" x-show="pagination.last_page > 1">
                    <button @click="fetchAlerts(1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        First
                    </button>
                    <button @click="fetchAlerts(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Previous
                    </button>

                    <template x-for="page in visiblePages" :key="page">
                        <button @click="page !== '...' && fetchAlerts(page)"
                            :class="page === pagination.current_page ?
                                'bg-gradient-to-r from-rose-600 to-rose-700 text-white border-rose-600' :
                                (page === '...' ? 'cursor-default border-gray-300 text-gray-400' : 'border-gray-300 text-gray-700 hover:bg-gray-50')"
                            :disabled="page === '...'"
                            class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                            <span x-text="page"></span>
                        </button>
                    </template>

                    <button @click="fetchAlerts(pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Next
                    </button>
                    <button @click="fetchAlerts(pagination.last_page)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Last
                    </button>
                </nav>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         RESOLVE ALERT MODAL
    ═══════════════════════════════════════════════ --}}
    <div x-show="showResolveModal" x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             @click.outside="closeResolveModal">

            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    Resolve Alert
                </h3>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <div class="mb-4 p-4 bg-amber-50 rounded-lg border border-amber-200">
                    <p class="text-sm text-gray-700">
                        <span class="font-bold">Medicine:</span> <span x-text="selectedAlert?.medicine?.name"></span><br>
                        <span class="font-bold">Alert Type:</span> 
                        <span x-text="selectedAlert?.alert_type?.replace('_', ' ').toUpperCase()"></span>
                    </p>
                </div>

                <form @submit.prevent="confirmResolve">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Resolution Notes <span class="text-gray-400 text-xs">(optional)</span>
                        </label>
                        <textarea x-model="resolutionNotes" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Enter any notes about how this alert was resolved..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeResolveModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirm Resolution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         BULK RESOLVE MODAL
    ═══════════════════════════════════════════════ --}}
    <div x-show="showBulkResolveModal" x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             @click.outside="closeBulkResolveModal">

            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <i class="fas fa-check-double text-white"></i>
                    </div>
                    Bulk Resolve Alerts
                </h3>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">
                    You are about to resolve <span class="font-bold text-rose-600" x-text="selectedAlerts.length"></span> selected alerts.
                </p>

                <form @submit.prevent="confirmBulkResolve">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Resolution Notes <span class="text-gray-400 text-xs">(optional)</span>
                        </label>
                        <textarea x-model="bulkResolutionNotes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter common resolution notes..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeBulkResolveModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-lg font-medium">
                            <i class="fas fa-check-double mr-2"></i>
                            Resolve All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function stockAlerts() {
    return {
        // State
        loading: false,
        showAdvancedFilters: false,
        showResolveModal: false,
        showBulkResolveModal: false,
        selectedAlert: null,
        selectedAlerts: [],
        resolutionNotes: '',
        bulkResolutionNotes: '',

        // Data
        data: { data: [], current_page: 1, last_page: 1 },
        
        stats: {
            total_active: {{ $stats['total_active'] }},
            low_stock: {{ $stats['low_stock'] }},
            out_of_stock: {{ $stats['out_of_stock'] }},
            expiring_soon: {{ $stats['expiring_soon'] }},
            total_resolved: {{ $stats['total_resolved'] }},
        },

        medicines: @json($medicines),

        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
            from: 0,
            to: 0,
        },

        filters: {
            search: '',
            alert_type: '',
            medicine_id: '',
            date_from: '',
            date_to: '',
            status: 'active',
        },

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
            pages.push(last);
            return pages;
        },

        // Lifecycle
        init() {
            this.fetchAlerts();
        },

        // Data fetching
        async fetchAlerts(page = 1) {
            if (this.loading) return;
            
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    search: this.filters.search || '',
                    alert_type: this.filters.alert_type || '',
                    medicine_id: this.filters.medicine_id || '',
                    date_from: this.filters.date_from || '',
                    date_to: this.filters.date_to || '',
                    status: this.filters.status || '',
                    per_page: this.pagination.per_page,
                    sort: this.sort.field,
                    direction: this.sort.direction,
                    _: Date.now()
                });

                const response = await fetch(`{{ route('pharmacy.alerts.data') }}?${params}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                const json = await response.json();
                this.data = json;

                this.pagination = {
                    current_page: json.current_page || 1,
                    last_page: json.last_page || 1,
                    per_page: json.per_page || 15,
                    total: json.total || 0,
                    from: json.from || 0,
                    to: json.to || 0
                };

                // Clear selections when page changes
                this.selectedAlerts = [];
            } catch (error) {
                console.error('Fetch error:', error);
                this.showToast('Failed to load alerts', 'error');
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                const response = await fetch(`{{ route('pharmacy.alerts.stats') }}`);
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        },

        // Filter methods
        setFilter(type) {
            this.filters.status = type;
            this.filters.alert_type = ''; // Clear specific type when clicking summary status
            this.filters.medicine_id = '';
            this.fetchAlerts(1);
        },

        setAlertTypeFilter(type) {
            this.filters.alert_type = type;
            this.filters.status = 'active';
            this.filters.medicine_id = ''; // Clear specific medicine when clicking type card
            this.fetchAlerts(1);
        },

        applyQuickFilter(type) {
            const today = new Date();
            
            switch(type) {
                case 'today':
                    const todayStr = today.toISOString().split('T')[0];
                    this.filters.date_from = todayStr;
                    this.filters.date_to = todayStr;
                    break;
                case 'week':
                    const weekAgo = new Date(today.setDate(today.getDate() - 7));
                    this.filters.date_from = weekAgo.toISOString().split('T')[0];
                    this.filters.date_to = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today.setMonth(today.getMonth() - 1));
                    this.filters.date_from = monthAgo.toISOString().split('T')[0];
                    this.filters.date_to = new Date().toISOString().split('T')[0];
                    break;
                case 'expiring':
                    this.filters.alert_type = 'expiring_soon';
                    this.filters.status = 'active';
                    break;
            }
            this.fetchAlerts(1);
        },

        clearFilters() {
            this.filters = {
                search: '',
                alert_type: '',
                medicine_id: '',
                date_from: '',
                date_to: '',
                status: 'active',
            };
            this.sort.field = 'created_at';
            this.sort.direction = 'desc';
            this.fetchAlerts(1);
        },

        hasActiveFilters() {
            return this.filters.search || 
                   this.filters.alert_type || 
                   this.filters.medicine_id || 
                   this.filters.date_from || 
                   this.filters.date_to ||
                   (this.filters.status && this.filters.status !== 'active');
        },

        getActiveFilterCount() {
            let count = 0;
            if (this.filters.search) count++;
            if (this.filters.alert_type) count++;
            if (this.filters.medicine_id) count++;
            if (this.filters.date_from || this.filters.date_to) count++;
            if (this.filters.status && this.filters.status !== 'active') count++;
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

        getMedicineName(id) {
            const medicine = this.medicines.find(m => m.id == id);
            return medicine ? medicine.name : 'Unknown';
        },

        // Sorting
        sortBy(field) {
            if (this.sort.field === field) {
                this.sort.direction = this.sort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort.field = field;
                this.sort.direction = 'asc';
            }
            this.fetchAlerts(1);
        },

        // Selection methods
        toggleSelectAll() {
            if (this.selectedAlerts.length === this.data.data.length) {
                this.selectedAlerts = [];
            } else {
                this.selectedAlerts = this.data.data.map(a => a.id);
            }
        },

        // Modal methods
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
            if (!this.selectedAlert) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/pharmacy/alerts/${this.selectedAlert.id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        resolution_notes: this.resolutionNotes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showToast(result.message, 'success');
                    this.closeResolveModal();
                    this.fetchAlerts(this.pagination.current_page);
                    this.fetchStats();
                }
            } catch (error) {
                console.error('Error resolving alert:', error);
                this.showToast('Failed to resolve alert', 'error');
            }
        },

        closeBulkResolveModal() {
            this.showBulkResolveModal = false;
            this.bulkResolutionNotes = '';
        },

        async confirmBulkResolve() {
            if (this.selectedAlerts.length === 0) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/pharmacy/alerts/bulk/resolve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        alert_ids: this.selectedAlerts,
                        resolution_notes: this.bulkResolutionNotes
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showToast(result.message, 'success');
                    this.closeBulkResolveModal();
                    this.selectedAlerts = [];
                    this.fetchAlerts(this.pagination.current_page);
                    this.fetchStats();
                }
            } catch (error) {
                console.error('Error bulk resolving alerts:', error);
                this.showToast('Failed to resolve alerts', 'error');
            }
        },

        // Helper methods
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

        getDaysUntilExpiry(expiryDate) {
            if (!expiryDate) return '';
            const today = new Date();
            const expiry = new Date(expiryDate);
            const diffTime = expiry - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays < 0) return 'Expired';
            if (diffDays === 0) return 'Expires today';
            return `${diffDays} days remaining`;
        },

        showToast(message, type = 'info') {
            if (window.showNotification) {
                window.showNotification(message, type);
            } else if (window.toastr) {
                window.toastr[type](message);
            } else {
                alert(message);
            }
        },
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection