@extends('layouts.app')

@section('title', 'Dispense History - NHMP HMS')
@section('page-title', 'Dispense History')
@section('breadcrumb', 'Pharmacy / History')

@section('content')
<div x-data="historyApp({{ json_encode($stats) }})" x-init="init()" x-cloak class="space-y-6">

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Same colors as User Management
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

        {{-- Total Dispensed Card (Blue - matches Total Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-history text-2xl drop-shadow-md text-blue-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                    Total Dispensed
                </p>
                <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono" x-text="stats.total_dispensed ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600" :class="{ 'animate-pulse': stats.total_dispensed > 0 }"></span>
                    <span class="text-xs text-blue-700 font-medium">All-time dispensations</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view all records</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Dispensed Card (Green - matches Active Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="setTodayFilter()" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-day text-2xl drop-shadow-md text-emerald-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">
                    Today's Dispensed
                </p>
                <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono" x-text="stats.today_dispensed ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600" :class="{ 'animate-pulse': stats.today_dispensed > 0 }"></span>
                    <span class="text-xs text-teal-700 font-medium">Click to filter today's records</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view today's records</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Units Card (Purple - matches Administrators) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-pills text-2xl drop-shadow-md text-purple-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                    Total Units
                </p>
                <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono" x-text="stats.total_quantity ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.total_quantity > 0 }"></span>
                    <span class="text-xs text-purple-700 font-medium">Total units dispensed</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Total medication units dispensed</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Unique Patients Card (Rose - matches Inactive Users) --}}
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-orange-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-2xl drop-shadow-md text-rose-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">
                    Unique Patients
                </p>
                <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono" x-text="stats.unique_patients ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600" :class="{ 'animate-pulse': stats.unique_patients > 0 }"></span>
                    <span class="text-xs text-rose-700 font-medium">Patients served</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Unique patients served</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN TABLE PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Panel Header --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                        <i class="fas fa-capsules text-blue-600"></i>
                        Dispense History
                        <span class="text-lg font-normal text-gray-600">
                            (<span x-text="pagination.total"></span> records)
                        </span>
                    </h2>
                    <p class="text-sm text-navy-600 mt-1">Track every medication dispensation with precision</p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    {{-- Records per page --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-700">Show:</span>
                        <select x-model="pagination.per_page" @change="fetchData(1)"
                            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="flex gap-2">
                        <button @click="fetchData(pagination.current_page)" :disabled="loading"
                            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
                            Refresh
                        </button>
                        <button @click="showAdvancedFilters = !showAdvancedFilters"
                            :class="showAdvancedFilters ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' : 'bg-gradient-to-r from-orange-500 to-orange-600 text-white'"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                            <i class="fas fa-filter"></i>
                            Filters
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Advanced Filters (collapsible) ── --}}
<div x-show="showAdvancedFilters" x-transition
     class="mt-6 p-6 bg-white rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

    {{-- First Row - Search & Basic Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Search (wider) --}}
        <div class="md:col-span-2 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" x-model="filters.search"
                   @input.debounce.500ms="fetchData(1)"
                   placeholder="Search by patient name, EMRN, medicine name, generic name, batch number..."
                   class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        </div>

        {{-- Date From --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-calendar-start text-amber-500 mr-1"></i>Date From
            </label>
            <input type="date" x-model="filters.date_from" @change="fetchData(1)"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        </div>

        {{-- Date To --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-calendar-end text-amber-500 mr-1"></i>Date To
            </label>
            <input type="date" x-model="filters.date_to" @change="fetchData(1)"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        </div>
    </div>

    {{-- Second Row - Medicine Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        {{-- Medicine Category Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-tags text-purple-500 mr-1"></i>Medicine Category
            </label>
            <select x-model="filters.medicine_category_id" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Categories</option>
                @foreach($medicineCategories ?? [] as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Medicine Filter (depends on category) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-pills text-blue-500 mr-1"></i>Medicine
            </label>
            <select x-model="filters.medicine_id" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Medicines</option>
                <template x-if="filteredMedicines && filteredMedicines.length > 0">
                    <template x-for="medicine in filteredMedicines" :key="medicine.id">
                        <option :value="medicine.id" x-text="medicine.name + (medicine.generic_name ? ' (' + medicine.generic_name + ')' : '')"></option>
                    </template>
                </template>
            </select>
        </div>

        {{-- Medicine Type/Form Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-capsules text-green-500 mr-1"></i>Medicine Form
            </label>
            <select x-model="filters.medicine_form_id" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Forms</option>
                @foreach($medicineForms ?? [] as $form)
                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Batch Number Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-cubes text-orange-500 mr-1"></i>Batch Number
            </label>
            <input type="text" x-model="filters.batch_number"
                   @input.debounce.500ms="fetchData(1)"
                   placeholder="Enter batch number..."
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        </div>
    </div>

    {{-- Third Row - Additional Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        {{-- Manufacturer Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-industry text-cyan-500 mr-1"></i>Manufacturer
            </label>
            <input type="text" x-model="filters.manufacturer"
                   @input.debounce.500ms="fetchData(1)"
                   placeholder="Filter by manufacturer..."
                   class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
        </div>

        {{-- Prescription Status Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-clipboard-check text-indigo-500 mr-1"></i>Prescription Status
            </label>
            <select x-model="filters.prescription_status" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="partially_dispensed">Partially Dispensed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        {{-- Dispensed By (Pharmacist) Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-user-md text-pink-500 mr-1"></i>Dispensed By
            </label>
            <select x-model="filters.dispensed_by" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Pharmacists</option>
                @foreach($pharmacists ?? [] as $pharmacist)
                    <option value="{{ $pharmacist->id }}">{{ $pharmacist->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Quantity Range Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-chart-bar text-rose-500 mr-1"></i>Quantity Range
            </label>
            <div class="flex items-center gap-2">
                <input type="number" x-model="filters.min_quantity"
                       @input.debounce.500ms="fetchData(1)"
                       placeholder="Min"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white text-sm">
                <span class="text-gray-500">-</span>
                <input type="number" x-model="filters.max_quantity"
                       @input.debounce.500ms="fetchData(1)"
                       placeholder="Max"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white text-sm">
            </div>
        </div>
    </div>

    {{-- Fourth Row - Expiry & Stock Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        {{-- Expiry Date Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-hourglass-half text-yellow-500 mr-1"></i>Expiry Status
            </label>
            <select x-model="filters.expiry_status" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Batches</option>
                <option value="expired">Expired</option>
                <option value="expiring_soon">Expiring Soon (30 days)</option>
                <option value="valid">Valid</option>
            </select>
        </div>

        {{-- Stock Status Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-boxes text-emerald-500 mr-1"></i>Stock Status
            </label>
            <select x-model="filters.stock_status" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">All Stock</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>

        {{-- Price Range Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-rupee-sign text-amber-500 mr-1"></i>Price Range
            </label>
            <div class="flex items-center gap-2">
                <input type="number" x-model="filters.min_price"
                       @input.debounce.500ms="fetchData(1)"
                       placeholder="Min"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white text-sm">
                <span class="text-gray-500">-</span>
                <input type="number" x-model="filters.max_price"
                       @input.debounce.500ms="fetchData(1)"
                       placeholder="Max"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white text-sm">
            </div>
        </div>

        {{-- Sort By Filter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-sort-amount-down text-gray-500 mr-1"></i>Sort By
            </label>
            <select x-model="sort.field" @change="fetchData(1)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="dispensed_at">Dispensed Date</option>
                <option value="medicine_name">Medicine Name</option>
                <option value="quantity_dispensed">Quantity</option>
                <option value="patient_name">Patient Name</option>
                <option value="batch_number">Batch Number</option>
                <option value="expiry_date">Expiry Date</option>
            </select>
        </div>
    </div>

    {{-- Fifth Row - Filter Actions --}}
    <div class="flex items-center justify-between gap-4 mt-6">
        <div class="flex items-center gap-3">
            {{-- Quick Filter Chips --}}
            <span class="text-sm font-medium text-gray-600">Quick Filters:</span>
            <button @click="applyQuickFilter('today')" 
                    class="px-3 py-1.5 bg-cyan-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200 transition-colors">
                <i class="fas fa-sun mr-1"></i>Today
            </button>
            <button @click="applyQuickFilter('week')" 
                    class="px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-medium hover:bg-green-200 transition-colors">
                <i class="fas fa-calendar-week mr-1"></i>This Week
            </button>
            <button @click="applyQuickFilter('month')" 
                    class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200 transition-colors">
                <i class="fas fa-calendar-alt mr-1"></i>This Month
            </button>
            <button @click="applyQuickFilter('expiring')" 
                    class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium hover:bg-amber-200 transition-colors">
                <i class="fas fa-exclamation-triangle mr-1"></i>Expiring Soon
            </button>
        </div>

        {{-- Clear All Button --}}
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

        {{-- Search Pill --}}
        <template x-if="filters.search">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                <i class="fas fa-search"></i>
                <span x-text="filters.search"></span>
                <button @click="filters.search=''; fetchData(1)" class="ml-1 hover:text-blue-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Date Range Pill --}}
        <template x-if="filters.date_from || filters.date_to">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">
                <i class="fas fa-calendar-alt"></i>
                <span x-text="getDateRangeText()"></span>
                <button @click="filters.date_from=''; filters.date_to=''; fetchData(1)" class="ml-1 hover:text-amber-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Category Pill --}}
        <template x-if="filters.medicine_category_id">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                <i class="fas fa-tags"></i>
                <span x-text="getCategoryName(filters.medicine_category_id)"></span>
                <button @click="filters.medicine_category_id=''; fetchData(1)" class="ml-1 hover:text-purple-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Medicine Pill --}}
        <template x-if="filters.medicine_id">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                <i class="fas fa-pills"></i>
                <span x-text="getMedicineName(filters.medicine_id)"></span>
                <button @click="filters.medicine_id=''; fetchData(1)" class="ml-1 hover:text-blue-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Form Pill --}}
        <template x-if="filters.medicine_form_id">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                <i class="fas fa-capsules"></i>
                <span x-text="getFormName(filters.medicine_form_id)"></span>
                <button @click="filters.medicine_form_id=''; fetchData(1)" class="ml-1 hover:text-green-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Batch Pill --}}
        <template x-if="filters.batch_number">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">
                <i class="fas fa-cubes"></i>
                Batch: <span x-text="filters.batch_number"></span>
                <button @click="filters.batch_number=''; fetchData(1)" class="ml-1 hover:text-orange-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Status Pill --}}
        <template x-if="filters.prescription_status">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-medium">
                <i class="fas fa-clipboard-check"></i>
                <span x-text="filters.prescription_status.replace('_', ' ').toUpperCase()"></span>
                <button @click="filters.prescription_status=''; fetchData(1)" class="ml-1 hover:text-indigo-900">
                    <i class="fas fa-times-circle"></i>
                </button>
            </span>
        </template>

        {{-- Count and Clear All --}}
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

        {{-- ── Table ── --}}
        <div class="overflow-x-auto relative min-h-[300px]">

            {{-- Loading overlay --}}
            <div x-show="loading"
                 class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="text-sm text-blue-600 font-semibold">Loading records...</p>
                    <p class="text-xs text-gray-400">Please wait while we fetch the data</p>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                    <tr>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-hashtag text-blue-500"></i>
                                <button @click="sortBy('id')" class="flex items-center gap-1 hover:text-indigo-600">
                                    ID
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-indigo-600': sort.field === 'id' }"></i>
                                </button>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user text-green-500"></i>
                                Patient Information
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-pills text-purple-500"></i>
                                <button @click="sortBy('medicine_name')" class="flex items-center gap-1 hover:text-indigo-600">
                                    Medicine
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-indigo-600': sort.field === 'medicine_name' }"></i>
                                </button>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cubes text-orange-500"></i>
                                Batch / Quantity
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user-md text-cyan-500"></i>
                                Pharmacist
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-left">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-clock text-amber-500"></i>
                                <button @click="sortBy('dispensed_at')" class="flex items-center gap-1 hover:text-indigo-600">
                                    Dispensed At
                                    <i class="fas fa-sort text-gray-400" :class="{ 'text-indigo-600': sort.field === 'dispensed_at' }"></i>
                                </button>
                            </div>
                        </th>
                        <th scope="col" class="px-5 py-4 text-center">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cogs text-orange-500"></i>
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
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <a href="javascript:void(0)" @click="openDetails(item)"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-gray-600 hover:text-gray-900 rounded-lg text-xs font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
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

        {{-- ── Pagination footer ── --}}
        <div x-show="!loading && data.data && data.data.length > 0" 
             class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                
                {{-- Pagination Info --}}
                <div class="text-sm text-gray-700">
                    Showing <span class="font-bold text-gray-900" x-text="pagination.from ?? 0"></span>
                    to <span class="font-bold text-gray-900" x-text="pagination.to ?? 0"></span>
                    of <span class="font-bold text-indigo-600" x-text="pagination.total"></span> results
                </div>

                {{-- Pagination Controls --}}
                <nav class="flex items-center space-x-2" x-show="pagination.last_page > 1">
                    {{-- First Page --}}
                    <button @click="fetchData(1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        First
                    </button>

                    {{-- Previous Page --}}
                    <button @click="fetchData(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Previous
                    </button>

                    {{-- Page Numbers --}}
                    <template x-for="page in visiblePages" :key="page">
                        <button @click="page !== '...' && fetchData(page)"
                            :class="page === pagination.current_page ?
                                'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-blue-600' :
                                (page === '...' ? 'cursor-default border-gray-300 text-gray-400' : 'border-gray-300 text-gray-700 hover:bg-gray-50')"
                            :disabled="page === '...'"
                            class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                            <span x-text="page"></span>
                        </button>
                    </template>

                    {{-- Next Page --}}
                    <button @click="fetchData(pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Next
                    </button>

                    {{-- Last Page --}}
                    <button @click="fetchData(pagination.last_page)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Last
                    </button>
                </nav>
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
            <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center z-10 rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                    Dispensation Details
                </h3>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5" x-show="selectedItem">

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
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
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
        showAdvancedFilters: false,
        showDetailModal: false,
        selectedItem: null,

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
            max_price: ''
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