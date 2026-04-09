@extends('layouts.app')

@section('title', 'Medicine Inventory - NHMP HMS')
@section('page-title', 'Medicine Inventory')
@section('breadcrumb', 'Pharmacy / Medicines')

@section('content')
    <div x-data="medicineCatalog()" x-init="init()" x-cloak class="space-y-8 relative">

        {{-- Futuristic Floating Filter Toggle --}}
        <button @click="showSidebar = true" x-show="!showSidebar"
            x-transition:enter="transition ease-out duration-500 delay-100"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-blue-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(79,70,229,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(79,70,229,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-blue-400/50 group cursor-pointer"
            title="Open Medicine Filters">
            <div class="relative">
                <div
                    class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300">
                </div>
                <i
                    class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
            </div>
            <span style="writing-mode: vertical-rl;"
                class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-blue-50">Filter
                Medicine</span>
        </button>

        {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
            <!-- Total Medicines Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="clearFilters()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-pills text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-xs font-bold tracking-wider text-blue-500 uppercase">Total Medicines</p>
                    <h4 class="text-3xl font-bold text-blue-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-[10px] text-blue-700 font-bold uppercase tracking-tight">Active Inventory</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="filters.stock = 'low'; fetchMedicines()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-pink-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Low Stock</p>
                    <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.low_stock">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-200 pt-2 pb-1 text-rose-700">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-tight">Needs Reordering</span>
                    </div>
                </div>
            </div>

            <!-- Rx Required Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="filters.prescription = 'required'; fetchMedicines()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-file-medical text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Rx Required</p>
                    <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.rx_required">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-200 pt-2 pb-1">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                        <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Restricted meds</span>
                    </div>
                </div>
            </div>

            <!-- Global Catalog Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/20 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="filters.search = ''; fetchMedicines()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-600 to-orange-400 shadow-lg shadow-amber-900/30 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-globe text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-xs font-bold tracking-wider text-orange-500 uppercase">Global Inventory</p>
                    <h4 class="text-3xl font-bold text-orange-700 drop-shadow-sm font-mono" x-text="stats.global">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-200 pt-2 pb-1">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                        <span class="text-[10px] text-amber-700 font-bold uppercase tracking-tight">Shared branch med</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
        <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

            {{-- Left Column - Catalog Table/Grid --}}
            <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
                <div
                    class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">

                    {{-- Panel Header with Light Gradient --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                                    <i class="fas fa-pills text-2xl text-blue-600"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight flex items-center gap-3">
                                        Medicine Inventory
                                        <span class="text-lg font-normal text-gray-600">
                                            (<span x-text="pagination.total"></span> items)
                                        </span>
                                    </h2>
                                    <p class="text-gray-600 text-sm font-medium mt-1">Manage global and branch-specific
                                        pharmaceutical stock</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 items-center">
                                <div
                                    class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                    <span
                                        class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row
                                        Density</span>
                                    <select x-model="pagination.per_page" @change="fetchMedicines()"
                                        class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                        <option value="15">15 Per Page</option>
                                        <option value="30">30 Per Page</option>
                                        <option value="50">50 Per Page</option>
                                        <option value="100">100 Per Page</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-1 bg-white border border-indigo-100 rounded-xl p-1 shadow-sm">
                                    <button @click="viewMode = 'table'"
                                        :class="viewMode === 'table' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-indigo-600'"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Table View">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-indigo-600'"
                                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Grid View">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                </div>

                                <a href="{{ route('pharmacy.medicines.create') }}"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-500/30 transition-all active:scale-95 group">
                                    <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                                    Add Medicine
                                </a>
                                <button @click="showSidebar = !showSidebar"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                    :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                    <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                                </button>

                                <button @click="fetchMedicines()"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                    title="Refresh">
                                    <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- View Content --}}
                    <div class="relative min-h-[400px]">

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

                        {{-- Table View --}}
                        <div x-show="viewMode === 'table'" x-transition class="overflow-x-auto">
                            <table class="w-full text-left"
                                :class="density === 'condensed' ? 'condensed-table' : 'spacious-table'">
                                <thead class="bg-white border-b border-indigo-50">
                                    <tr>
                                        <th class="px-5 py-4 border-b border-slate-50">
                                            <div
                                                class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-pills text-[10px]"></i>
                                                </div>
                                                Medicine Information
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 border-b border-slate-50">
                                            <div
                                                class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-tag text-[10px]"></i>
                                                </div>
                                                Category / Form
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 text-center border-b border-slate-50">
                                            <div
                                                class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-file-medical text-[10px]"></i>
                                                </div>
                                                Rx Policy
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 text-center border-b border-slate-50">
                                            <div
                                                class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-cubes text-[10px]"></i>
                                                </div>
                                                Stock
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
                                <tbody class="bg-white divide-y divide-gray-100" x-show="!loading">
                                    <template x-if="!loading && medicines.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-6 py-20 text-center">
                                                <div
                                                    class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4 border border-gray-100 shadow-inner">
                                                    <i class="fas fa-search text-3xl"></i>
                                                </div>
                                                <h3 class="text-lg font-black text-gray-600">No medicines found</h3>
                                                <p class="text-gray-400 mt-1 text-sm font-medium">Try adjusting your
                                                    filters or search query.</p>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="medicine in medicines" :key="medicine.id">
                                        <tr class="hover:bg-blue-50/30 transition-colors group">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm overflow-hidden flex-shrink-0">
                                                        <i class="fas fa-pills text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors"
                                                            x-text="medicine.name"></h4>
                                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mt-1"
                                                            x-text="medicine.generic_name"></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col gap-1.5">
                                                    <span
                                                        class="inline-flex w-max items-center justify-center px-2 py-1 rounded bg-indigo-50 border border-indigo-100 text-[10px] font-bold uppercase text-indigo-600"
                                                        x-text="medicine.category_name"></span>
                                                    <span class="text-xs text-gray-500 font-medium"><i
                                                            class="fas fa-capsules mr-1 text-gray-300"></i> <span
                                                            x-text="medicine.form_name"></span></span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-center">
                                                <template x-if="medicine.requires_prescription">
                                                    <span
                                                        class="px-2 py-1 bg-amber-50 text-amber-600 border border-amber-200 text-[9px] font-black rounded uppercase tracking-widest">
                                                        <i class="fas fa-file-medical mr-1"></i> Yes
                                                    </span>
                                                </template>
                                                <template x-if="!medicine.requires_prescription">
                                                    <span
                                                        class="px-2 py-1 text-gray-400 text-[9px] font-black rounded uppercase tracking-widest">
                                                        No
                                                    </span>
                                                </template>
                                            </td>
                                            <td class="px-5 py-4 text-center">
                                                <div class="font-mono text-sm font-bold flex flex-col items-center">
                                                    <span
                                                        :class="medicine.is_low_stock ? 'text-rose-600' : 'text-emerald-600'"
                                                        x-text="medicine.total_stock"></span>
                                                    <span class="text-[9px] text-gray-400 font-bold uppercase mt-0.5"
                                                        x-text="medicine.unit"></span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-center">
                                                <button @click="toggleStatus(medicine)" 
                                                    x-data="{ hover: false }" 
                                                    @mouseenter="hover = true" 
                                                    @mouseleave="hover = false"
                                                    class="h-9 px-3 min-w-[100px] inline-flex items-center justify-center gap-2 rounded-xl transition-all shadow-sm border focus:outline-none group"
                                                    :class="medicine.is_active ? 
                                                        (hover ? 'bg-rose-50 text-rose-600 border-rose-100 shadow-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') : 
                                                        (hover ? 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100')"
                                                    :title="medicine.is_active ? 'Deactivate Medicine' : 'Activate Medicine'">
                                                    <div class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                                                        :class="medicine.is_active ? 
                                                            (hover ? 'bg-rose-500 animate-bounce' : 'bg-emerald-500 animate-pulse') : 
                                                            (hover ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500')">
                                                    </div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest transition-all duration-300"
                                                        x-text="medicine.is_active ? (hover ? 'Deactivate' : 'Active') : (hover ? 'Activate' : 'Hidden')"></span>
                                                </button>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-wrap items-center justify-end gap-1.5 opacity-100 transition-opacity">
                                                    <a :href="medicine.view_url"
                                                        class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100"
                                                        title="View Details">
                                                        <i class="fas fa-eye text-[10px]"></i>
                                                    </a>
                                                    <a :href="medicine.edit_url"
                                                        class="h-8 w-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-500 hover:text-white transition-all shadow-sm border border-indigo-100"
                                                        title="Modify Medicine">
                                                        <i class="fas fa-edit text-[10px]"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Grid View --}}
                        <div x-show="viewMode === 'grid'" x-transition class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                                <template x-if="loading">
                                    <div class="col-span-full py-12 text-center text-gray-400">
                                        <i class="fas fa-spinner fa-spin text-3xl mb-4 text-blue-500"></i>
                                        <p class="text-sm font-bold uppercase tracking-widest">Loading Medicines...</p>
                                    </div>
                                </template>

                                <template x-if="!loading && medicines.length === 0">
                                    <div class="col-span-full py-20 text-center">
                                        <div
                                            class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mx-auto mb-4 border border-gray-100 shadow-inner">
                                            <i class="fas fa-search text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-black text-gray-600">No medicines found</h3>
                                        <p class="text-gray-400 mt-1 text-sm font-medium">Try adjusting your filters or
                                            search query.</p>
                                    </div>
                                </template>

                                <template x-for="medicine in medicines" :key="medicine.id">
                                    <div
                                        class="bg-white rounded-2xl p-6 shadow-lg shadow-slate-200/50 border border-slate-100 group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden flex flex-col h-full">
                                        <!-- Status Badge -->
                                        <div class="absolute top-4 right-4">
                                            <template x-if="medicine.requires_prescription">
                                                <span
                                                    class="px-2 py-1 rounded bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest border border-rose-100">
                                                    <i class="fas fa-file-medical mr-1"></i> Rx
                                                </span>
                                            </template>
                                        </div>

                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform flex-shrink-0">
                                                <i class="fas fa-pills"></i>
                                            </div>
                                            <div class="min-w-0 pr-8">
                                                <h3 class="text-lg font-black text-gray-800 leading-tight group-hover:text-blue-700 transition-colors truncate"
                                                    x-text="medicine.name"></h3>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-0.5 truncate"
                                                    x-text="medicine.generic_name"></p>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex-1 space-y-3">
                                            <div class="flex justify-between items-center text-sm">
                                                <span
                                                    class="text-slate-400 font-bold uppercase tracking-widest text-[9px]">Manufacturer</span>
                                                <span
                                                    class="text-slate-700 font-bold text-xs max-w-[120px] truncate text-right border-b border-dashed border-slate-200"
                                                    x-text="medicine.manufacturer"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span
                                                    class="text-slate-400 font-bold uppercase tracking-widest text-[9px]">Category</span>
                                                <span
                                                    class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded text-[10px]"
                                                    x-text="medicine.category_name"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span
                                                    class="text-slate-400 font-bold uppercase tracking-widest text-[9px]">Form</span>
                                                <span class="text-slate-600 font-bold text-xs"
                                                    x-text="medicine.form_name"></span>
                                            </div>
                                        </div>

                                        <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between">
                                            <div>
                                                <span class="text-2xl font-black font-mono"
                                                    :class="medicine.is_low_stock ? 'text-rose-600' : 'text-emerald-600'"
                                                    x-text="medicine.total_stock"></span>
                                                <span class="text-[10px] text-slate-400 font-bold uppercase ml-1"
                                                    x-text="medicine.unit"></span>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5 justify-end">
                                                <button @click="toggleStatus(medicine)" 
                                                    x-data="{ hover: false }" 
                                                    @mouseenter="hover = true" 
                                                    @mouseleave="hover = false"
                                                    class="h-9 px-3 min-w-[100px] inline-flex items-center justify-center gap-1.5 rounded-xl transition-all shadow-sm border focus:outline-none group"
                                                    :class="medicine.is_active ? 
                                                        (hover ? 'bg-rose-50 text-rose-600 border-rose-100 shadow-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') : 
                                                        (hover ? 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100')"
                                                    :title="medicine.is_active ? 'Deactivate Medicine' : 'Activate Medicine'">
                                                    <div class="w-1 h-1 rounded-full transition-all duration-300"
                                                        :class="medicine.is_active ? 
                                                            (hover ? 'bg-rose-500 animate-bounce' : 'bg-emerald-500 animate-pulse') : 
                                                            (hover ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500')">
                                                    </div>
                                                    <span class="text-[8px] font-black uppercase tracking-widest transition-all duration-300"
                                                        x-text="medicine.is_active ? (hover ? 'Deactivate' : 'Active') : (hover ? 'Activate' : 'Hidden')"></span>
                                                </button>
                                                <a :href="medicine.view_url"
                                                    class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100"
                                                    title="View Details">
                                                    <i class="fas fa-eye text-[10px]"></i>
                                                </a>
                                                <a :href="medicine.edit_url"
                                                    class="h-8 w-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-500 hover:text-white transition-all shadow-sm border border-indigo-100"
                                                    title="Modify Medicine">
                                                    <i class="fas fa-edit text-[10px]"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Stock Bar indicator -->
                                        <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r"
                                            :class="medicine.is_low_stock ? 'from-rose-500 to-rose-600' : 'from-emerald-400 to-emerald-600'"
                                            :style="`width: ${Math.min(100, (medicine.total_stock / (medicine.reorder_level || 10)) * 50)}%`"
                                        ></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Premium Pagination Footer --}}
                        <div x-show="!loading && medicines && medicines.length > 0" class="p-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Displaying <span class="text-slate-900" x-text="pagination.from"></span> - <span class="text-slate-900" x-text="pagination.to"></span> 
                                    <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                                    Capacity: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button @click="changePage(1)" :disabled="pagination.current_page === 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-left text-[10px]"></i></button>
                                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-chevron-left text-[10px]"></i> <span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Prev</span></button>
                                    
                                    <div class="flex items-center gap-1 px-1">
                                        <template x-for="page in getPageRange()" :key="page">
                                            <button @click="page !== '...' && changePage(page)" 
                                                :class="page === pagination.current_page ? 'bg-indigo-600 text-white shadow-lg border-indigo-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-600'"
                                                x-text="page" class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"></button>
                                        </template>
                                    </div>

                                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Next</span> <i class="fas fa-chevron-right text-[10px]"></i></button>
                                    <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-right text-[10px]"></i></button>
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right Column - Sticky Sidebar Filters --}}
                <div x-show="showSidebar" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-8"
                    class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto pr-2 custom-scrollbar">

                    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                        <div
                            class="p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-600 shadow-sm bg-blue-50 border border-blue-100">
                                    <i class="fas fa-filter text-sm"></i>
                                </div>
                                <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Filters</h2>
                            </div>
                            <button @click="showSidebar = false"
                                class="text-slate-400 hover:text-blue-600 transition-colors"><i
                                    class="fas fa-times"></i></button>
                        </div>

                        <div class="p-6 space-y-6">
                            {{-- Search Filter --}}
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                    <i class="fas fa-search text-blue-500"></i> Universal Search
                                </label>
                                <div class="relative group">
                                    <input x-model.debounce.500ms="filters.search" @input="fetchMedicines()"
                                        type="text" placeholder="Name, brand, generic..."
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl pl-10 pr-4 py-3 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-400 transition-all outline-none font-bold ring-0">
                                    <i
                                        class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                            </div>

                            {{-- Prescription Policy Filter - Bento Style --}}
                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                    <i class="fas fa-file-medical text-blue-500"></i> Dispensing Policy
                                </label>
                                <div class="grid grid-cols-1 gap-2">
                                    <button @click="filters.prescription = ''; fetchMedicines()"
                                        :class="filters.prescription === '' ?
                                            'bg-indigo-600 text-white shadow-lg shadow-indigo-200' :
                                            'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                        <span>Any Requirement</span>
                                        <i class="fas fa-globe transition-opacity"
                                            :class="filters.prescription === '' ? 'opacity-100' : 'opacity-40'"></i>
                                    </button>
                                    <button @click="filters.prescription = 'required'; fetchMedicines()"
                                        :class="filters.prescription === 'required' ?
                                            'bg-rose-500 text-white shadow-lg shadow-rose-200' :
                                            'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                        <span>Rx Required</span>
                                        <i class="fas fa-prescription transition-opacity"
                                            :class="filters.prescription === 'required' ? 'opacity-100' : 'opacity-40'"></i>
                                    </button>
                                    <button @click="filters.prescription = 'not_required'; fetchMedicines()"
                                        :class="filters.prescription === 'not_required' ?
                                            'bg-emerald-500 text-white shadow-lg shadow-emerald-200' :
                                            'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                        <span>OTC (No Rx)</span>
                                        <i class="fas fa-check-circle transition-opacity"
                                            :class="filters.prescription === 'not_required' ? 'opacity-100' : 'opacity-40'"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Category Select --}}
                            <div class="space-y-3">
                                <label
                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 flex items-center gap-2">
                                    <i class="fas fa-layer-group text-blue-500"></i> Category Select
                                </label>
                                <div class="relative group">
                                    <i
                                        class="fas fa-tags absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                                    <select x-model="filters.category" @change="fetchMedicines()"
                                        class="w-full px-4 py-3 pl-10 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs ring-0 text-slate-600 cursor-pointer appearance-none">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
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
                                        :class="density === 'spacious' ?
                                            'bg-white text-indigo-600 shadow-sm border border-slate-200' :
                                            'text-slate-500 hover:text-slate-700'"
                                        class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                        Spacious
                                    </button>
                                </div>
                            </div>

                            {{-- Records per page --}}
                            <div class="space-y-3 pb-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Records Per
                                    Page</label>
                                <div class="flex items-center gap-1.5 p-1.5 bg-slate-100 rounded-2xl">
                                    <template x-for="limit in [15, 25, 50, 100]" :key="limit">
                                        <button @click="pagination.per_page = limit; fetchMedicines()"
                                            :class="pagination.per_page == limit ? 'bg-white text-blue-600 shadow-sm' :
                                                'text-slate-400 hover:text-blue-600'"
                                            class="flex-1 py-2.5 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all">
                                            <span x-text="limit"></span>
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

            </div>
        </div>

        @push('scripts')
            <script>
                function medicineCatalog() {
                    return {
                        showSidebar: false,
                        loading: true,
                        viewMode: 'grid', // 'table' or 'grid'
                        medicines: [],
                        pagination: {
                            current_page: 1,
                            last_page: 1,
                            total: 0,
                            per_page: 15
                        },
                        density: 'spacious',
                        filters: {
                            search: '',
                            category: '',
                            prescription: '',
                            stock: '', // 'low' or ''
                            sort: 'name',
                            direction: 'asc'
                        },
                        stats: {
                            total: 0,
                            rx_required: 0,
                            global: 0,
                            low_stock: 0
                        },

                        init() {
                            this.fetchMedicines();
                        },

                        async fetchMedicines(page = 1) {
                            this.loading = true;
                            try {
                                const params = new URLSearchParams({
                                    page: page,
                                    length: this.pagination.per_page,
                                    sort_by: this.filters.sort,
                                    sort_direction: this.filters.direction
                                });

                                if (this.filters.search) params.append('search', this.filters.search);
                                if (this.filters.category) params.append('category', this.filters.category);
                                if (this.filters.prescription) params.append('prescription', this.filters.prescription);

                                const response = await fetch(`/pharmacy/medicines/api-list?${params.toString()}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                const result = await response.json();

                                if (result.success) {
                                    let resultData = result.data;
                                    // local filtering for low stock if clicked from card
                                    if (this.filters.stock === 'low') {
                                        resultData = resultData.filter(m => m.is_low_stock);
                                    }

                                    this.medicines = resultData;

                                    if (result.pagination) {
                                        this.pagination = result.pagination;
                                    }
                                    if (result.stats) {
                                        this.stats = result.stats;
                                    }
                                }
                            } catch (error) {
                                console.error('Error fetching medicines:', error);
                            } finally {
                                this.loading = false;
                            }
                        },

                        clearFilters() {
                            this.filters = {
                                search: '',
                                category: '',
                                prescription: '',
                                stock: '',
                                sort: 'name',
                                direction: 'asc'
                            };
                            this.fetchMedicines();
                        },

                        sortBy(column) {
                            if (this.filters.sort === column) {
                                this.filters.direction = this.filters.direction === 'asc' ? 'desc' : 'asc';
                            } else {
                                this.filters.sort = column;
                                this.filters.direction = 'asc';
                            }
                            this.fetchMedicines(1);
                        },

                        sortIcon(column) {
                            if (this.filters.sort !== column) return 'fa-sort text-gray-300';
                            return this.filters.direction === 'asc' ? 'fa-sort-up text-blue-600' : 'fa-sort-down text-blue-600';
                        },

                        changePage(page) {
                            if (page >= 1 && page <= this.pagination.last_page) {
                                this.fetchMedicines(page);
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
                                if (i == 1 || i == last || i >= left && i < right) {
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

                        async toggleStatus(medicine) {
                            const original = medicine.is_active;
                            medicine.is_active = !original; // optimistic update

                            try {
                                const response = await fetch(`/pharmacy/medicines/${medicine.id}/toggle-status`, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                const result = await response.json();
                                if (!result.success) throw new Error(result.message);

                                if (window.showSuccess) {
                                    showSuccess(`${medicine.name} is now ${medicine.is_active ? 'Active' : 'Hidden'}`, 'Status Updated');
                                }
                            } catch (error) {
                                medicine.is_active = original; // revert on failure
                                console.error('Toggle status error:', error);
                                if (window.showError) {
                                    showError(error.message || 'Failed to update status', 'Update Failed');
                                }
                            }
                        }
                    };
                }
            </script>
        @endpush
    @endsection
