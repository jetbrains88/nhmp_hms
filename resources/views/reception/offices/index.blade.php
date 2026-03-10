@extends('layouts.app')
@section('title', 'Offices Management - NHMP HMS')
@section('page-title', 'Offices Hierarchy')

@section('content')
<div x-data="officeManagement()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-indigo-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(79,70,229,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(79,70,229,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-400/50 group cursor-pointer"
        title="Open Office Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">Office Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 gap-y-10 mt-4">
        <!-- Total Offices Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-sitemap text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Total Offices</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600 animate-pulse"></span>
                    <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">System-wide Offices</span>
                </div>
            </div>
        </div>

        <!-- Active Offices Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterStatus = 'active'; searchOffices()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Currently Functional</span>
                </div>
            </div>
        </div>

        <!-- Inactive Offices Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterStatus = 'inactive'; searchOffices()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-pink-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-ban text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Inactive</p>
                <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.inactive">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-200 pt-2 text-rose-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Disabled Access</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Validation Vault Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">
            
             {{-- Panel Header with Light Gradient (Matches User Management) --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-building text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                Offices Network
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Manage physical & hierarchical structures</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        {{-- Rows Selector moved here --}}
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="searchOffices()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <button @click="openAddModal()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            New Office
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchOffices()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            title="Refresh">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bulk Actions Toolbar (Sticky below view controller) --}}
            <div x-show="selectedIds.length > 0" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="-translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 class="bg-indigo-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                        <span x-text="selectedIds.length"></span> Offices Selected
                    </span>
                    <div class="flex items-center gap-2">
                        <button @click="bulkDelete()" class="px-3 py-1.5 bg-rose-500/80 hover:bg-rose-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Purge Selection</button>
                    </div>
                </div>
                <button @click="selectedIds = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2">
                    Dismiss <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- View Content --}}
            <div class="relative min-h-[400px]">
                {{-- Table View --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">
                            <tr>
                                <th class="px-5 py-5 w-10">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === offices.length && offices.length > 0"
                                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shadow-sm border border-blue-500/20">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                        </div>
                                        <button @click="sortBy('name')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Office Name
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-600 shadow-sm border border-purple-500/20">
                                            <i class="fas fa-layer-group text-xs"></i>
                                        </div>
                                        <button @click="sortBy('type')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Type / Scope
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('type')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-500/20">
                                            <i class="fas fa-project-diagram text-xs"></i>
                                        </div>
                                        <span>Parent Body</span>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-600 shadow-sm border border-green-500/20">
                                            <i class="fas fa-toggle-on text-xs"></i>
                                        </div>
                                        <button @click="sortBy('is_active')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Status
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('is_active')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600 shadow-sm border border-amber-500/20">
                                            <i class="fas fa-bolt text-xs"></i>
                                        </div>
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-500">
                                        <i class="fas fa-circle-notch fa-spin text-3xl text-indigo-400 mb-2"></i>
                                        <p class="text-sm font-medium">Syncing Data...</p>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && offices.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                            <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Offices Found</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                                        <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="office in offices" :key="office.id">
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :value="office.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex bg-gradient-to-r from-indigo-400 to-blue-600 text-white items-center justify-center shadow-sm group-hover:scale-110 transition-transform  border border-gray-200 text-gray-600">
                                                <i class="fas fa-building text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-600" x-text="office.name"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 " x-text="office.type"></span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div x-show="office.parent" class="text-xs font-bold text-gray-600 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg ">
                                            <i class="fas fa-level-up-alt text-gray-400 rotate-90"></i>
                                            <span x-text="office.parent?.name"></span>
                                        </div>
                                        <div x-show="!office.parent" class="text-[10px] text-gray-400 uppercase tracking-widest font-black italic">Root Node</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center text-gray-600 gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all">
                                            <i class="fas" :class="office.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                            <span x-text="office.is_active ? 'Active' : 'Inactive'"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <button @click="openViewModal(office)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-50 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button @click="openEditModal(office)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-50 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button @click="toggleStatus(office)" class="w-full flex items-center text-gray-600  gap-1.5 px-3 py-1.5 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" 
                                                    >
                                                <i class="fas" :class="office.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                                <span x-text="office.is_active ? 'Disable' : 'Enable'"></span>
                                            </button>
                                            <button @click="confirmDelete(office)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-400 hover:text-rose-600 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Loading Overlay (Optional) --}}
                <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-lg"></div>
                        <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing Vault...</span>
                    </div>
                </div>

            </div>
            
            {{-- Premium Pagination --}}
            <div x-show="!loading && offices.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto rounded-b-3xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from || 0"></span> - <span class="text-slate-900" x-text="pagination.to || 0"></span> 
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                            Source: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- First Button --}}
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-angles-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                        </button>

                        {{-- Previous Button --}}
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Prev</span>
                        </button>

                        <div class="flex items-center gap-1.5 mx-2">
                            <template x-for="page in getPageRange()" :key="page">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 border-indigo-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-600 hover:text-indigo-600'"
                                    :disabled="page === '...'"
                                    class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"
                                    x-text="page">
                                </button>
                            </template>
                        </div>

                        {{-- Next Button --}}
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>

                        {{-- Last Button --}}
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Last</span>
                            <i class="fas fa-angles-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            </div>
        </div>

        {{-- Right Column - Security Filters Sidebar --}}
        <div class="lg:col-span-3 lg:sticky lg:top-0 lg:max-h-[calc(100vh-140px)] lg:overflow-y-auto scrollbar-hide pb-2" style="scrollbar-width: none;" x-show="showSidebar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
            
            {{-- Unified Filter Card --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">
                
                {{-- Master Header --}}
                <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Office Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Data</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm" title="Hide Filters">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="overflow-y-auto scrollbar-hide flex-1 space-y-5 p-5" style="scrollbar-width: none;">
                    
                    {{-- Active Intelligence --}}
                    <div x-show="hasActiveFilters()" class="space-y-2 pt-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Intelligence:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-if="searchQuery">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-search opacity-50"></i>
                                    <span x-text="searchQuery"></span>
                                    <button @click="searchQuery=''; searchOffices()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                            <template x-if="filterType">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-600 border border-purple-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-layer-group opacity-50"></i>
                                    <span x-text="filterType"></span>
                                    <button @click="filterType=''; searchOffices()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                            <template x-if="filterStatus">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-check-circle opacity-50"></i>
                                    <span x-text="filterStatus.toUpperCase()"></span>
                                    <button @click="filterStatus=''; searchOffices()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div x-show="hasActiveFilters()" class="border-b border-dashed border-slate-200"></div>

                    {{-- Search Module --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-search text-indigo-500"></i> Localize Point
                        </label>
                        <div class="relative group">
                            <input type="text" x-model.debounce.500ms="searchQuery" @input="searchOffices()"
                                placeholder="Search Name..."
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                            <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-layer-group text-purple-500"></i> Region / Area Type
                        </label>
                        <select x-model="filterType" @change="searchOffices()"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="">All Types</option>
                            <option value="Region">Region</option>
                            <option value="Zone">Zone</option>
                            <option value="Sector">Sector</option>
                            <option value="PLHQ">PLHQ</option>
                            <option value="Beat">Beat</option>
                            <option value="Office">Office</option>
                        </select>
                    </div>

                    {{-- Status Toggle --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-shield-virus text-emerald-500"></i> Auth Status
                        </label>
                        <div class="grid grid-cols-1 gap-2">
                            <button @click="filterStatus = ''; searchOffices()" 
                                :class="filterStatus === '' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Global Data</span>
                                <i class="fas fa-globe-americas transition-opacity" :class="filterStatus === '' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filterStatus = 'active'; searchOffices()" 
                                :class="filterStatus === 'active' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Active Only</span>
                                <i class="fas fa-check-circle transition-opacity" :class="filterStatus === 'active' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filterStatus = 'inactive'; searchOffices()" 
                                :class="filterStatus === 'inactive' ? 'bg-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Inactive Locked</span>
                                <i class="fas fa-lock transition-opacity" :class="filterStatus === 'inactive' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Clearance & Actions (Sticky Bottom) --}}
                <div class="p-5 pt-0 flex flex-col gap-2.5 shrink-0">
                    <button @click="clearFilters()" 
                        class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 hover:-translate-y-0.5 transition-all text-left flex items-center justify-between group">
                        <span>Purge All Filters</span>
                        <i class="fas fa-eraser group-hover:rotate-12 transition-transform opacity-90 group-hover:opacity-100"></i>
                    </button>
                    <button @click="showSidebar = false" 
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:-translate-y-0.5 transition-all text-left flex items-center justify-between group shadow-md shadow-indigo-500/20">
                        <span>Hide Filters</span>
                        <i class="fas fa-eye-slash group-hover:scale-110 transition-transform opacity-90 group-hover:opacity-100"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal (Matching Premium Rounded aesthetic) --}}
    <div x-show="showAddModal" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAddModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="closeAddModal()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block w-full max-w-lg overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle border border-slate-100">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-indigo-100/50 bg-gradient-to-r from-blue-50 to-indigo-50 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm text-indigo-600 relative z-10">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight relative z-10" x-text="editing ? 'Edit Office' : 'Create Office Node'"></h3>
                    </div>
                    <button @click="closeAddModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="saveOffice" class="space-y-5">
                        <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Office Name <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="form.name" required placeholder="e.g. Islamabad HQ"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Office Type <span class="text-rose-500">*</span></label>
                                <select x-model="form.type" required class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 appearance-none cursor-pointer">
                                    <option value="" disabled>Select Type</option>
                                    <option value="Region">Region</option>
                                    <option value="Zone">Zone</option>
                                    <option value="Sector">Sector</option>
                                    <option value="PLHQ">PLHQ</option>
                                    <option value="Beat">Beat</option>
                                    <option value="Office">Office</option>
                                </select>
                            </div>

                            <div class="space-y-1.5 flex flex-col justify-center mt-6">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-3 text-xs font-bold text-slate-700 uppercase tracking-widest" x-text="form.is_active ? 'Auth: Active' : 'Auth: Locked'"></span>
                                </label>
                            </div>
                        </div>

                        <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors mt-2">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Parent Link <span class="text-slate-400 lowercase tracking-normal font-medium ml-2">(Optional Hierarchy)</span></label>
                            <select x-model="form.parent_id" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 appearance-none cursor-pointer">
                                <option value="">No Parent (Root Node)</option>
                                <template x-for="opt in listOffices" :key="opt.id">
                                    <option :value="opt.id" x-text="opt.name" x-show="opt.id !== form.id"></option>
                                </template>
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="closeAddModal()" class="px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="saveOffice()" :disabled="saving" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? 'Saving...' : (editing ? 'Update Office' : 'Commit Node')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showDeleteModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div x-show="showDeleteModal" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 text-center border border-slate-100">
                <div class="w-16 h-16 rounded-full bg-rose-100 mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-rose-600"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Delete Office Node</h3>
                <p class="text-xs font-bold text-slate-500 mb-6 px-4 uppercase tracking-wider leading-relaxed">System erasure of <br><span class="font-black text-slate-700 text-base normal-case tracking-normal" x-text="dataToDelete?.name"></span></p>
                <div class="flex items-center justify-center gap-3">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-colors w-full cursor-pointer">Cancel</button>
                    <button @click="deleteData()" :disabled="deleting" class="px-5 py-2.5 bg-gradient-to-r from-rose-500 to-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-rose-600 hover:to-rose-800 hover:-translate-y-0.5 transition-all shadow-md shadow-rose-500/30 w-full flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="deleting"></i>
                        <span x-text="deleting ? 'Purging...' : 'Confirm Purge'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6" x-transition.opacity style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeViewModal"></div>
            
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg z-10 overflow-hidden relative border border-slate-100" x-transition.scale>
                <div class="bg-gradient-to-br from-sky-500 to-sky-400 p-8 text-white relative">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center shadow-xl border border-white/20">
                                <i class="fas fa-eye text-xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight" x-text="dataToView?.name || 'Details'"></h3>
                                <p class="text-slate-300 text-[10px] font-black uppercase tracking-widest mt-1">Office Data Record</p>
                            </div>
                        </div>
                        <button @click="closeViewModal" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors cursor-pointer">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Office Type / Scope</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100" x-text="dataToView?.type"></span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Parent Body</p>
                            <div class="mt-1">
                                <div x-show="dataToView?.parent" class="text-xs font-bold text-slate-600 inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                    <i class="fas fa-level-up-alt text-slate-400 rotate-90"></i>
                                    <span x-text="dataToView?.parent?.name"></span>
                                </div>
                                <div x-show="!dataToView?.parent" class="text-[10px] text-slate-400 uppercase tracking-widest font-black italic mt-2">Root Node</div>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Auth Status</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="dataToView?.is_active ? 'text-white bg-emerald-500 border border-emerald-200' : 'text-white bg-rose-500 border border-rose-200'">
                                    <i class="fas" :class="dataToView?.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                    <span x-text="dataToView?.is_active ? 'Active' : 'Inactive'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button @click="closeViewModal" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm cursor-pointer">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function officeManagement() {
        return {
            showSidebar: false,
            showAddModal: false,
            showDeleteModal: false,
            showViewModal: false,
            dataToView: null,
            loading: false,
            saving: false,
            deleting: false,
            editing: false,
            
            offices: [],
            listOffices: [], // raw array for the dropdown mapping
            stats: { total: 0, active: 0, inactive: 0 },
            
            searchQuery: '',
            filterStatus: '',
            filterType: '',
            sortField: 'name',
            sortDirection: 'asc',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
            
            form: { id: null, name: '', type: '', parent_id: '', is_active: true },
            selectedIds: [],
            dataToDelete: null,
            dataToView: null,

            async init() {
                await this.fetchOffices();
                await this.fetchStats();
            },

            hasActiveFilters() {
                return this.searchQuery !== '' || this.filterStatus !== '' || this.filterType !== '';
            },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort text-slate-300';
                return this.sortDirection === 'asc' ? 'fa-sort-up text-indigo-600 scale-125' : 'fa-sort-down text-indigo-600 scale-125';
            },

            searchOffices() {
                this.pagination.current_page = 1;
                this.fetchOffices();
            },

            async fetchOffices() {
                this.loading = true;
                this.selectedIds = [];
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    sort: this.sortField,
                    direction: this.sortDirection
                });

                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterStatus) params.append('status', this.filterStatus);
                if (this.filterType) params.append('type', this.filterType);

                const mappingParams = new URLSearchParams({ per_page: 500 });
                
                try {
                    const response = await fetch(`/reception/offices/data?${params.toString()}`);
                    const data = await response.json();
                    
                    this.offices = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to
                    };

                    const mappingResponse = await fetch(`/reception/offices/data?${mappingParams.toString()}`);
                    const mappingData = await mappingResponse.json();
                    this.listOffices = mappingData.data;

                } catch (error) {
                    showError('Failed to load offices vault');
                } finally {
                    this.loading = false;
                }
            },

            async fetchStats() {
                try {
                    const response = await fetch('/reception/offices/stats');
                    if (response.ok) {
                        this.stats = await response.json();
                    }
                } catch (error) {
                    console.error('Failed to load stats', error);
                }
            },

            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.pagination.current_page = page;
                    this.fetchOffices();
                }
            },

            getPageRange() {
                const current = this.pagination.current_page;
                const last = this.pagination.last_page;
                const delta = 2;
                const range = [];
                for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                    range.push(i);
                }
                if (current - delta > 2) range.unshift('...');
                if (current + delta < last - 1) range.push('...');
                range.unshift(1);
                if (last > 1) range.push(last);
                return range;
            },

            sortBy(field) {
                if (this.sortField === field) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortDirection = 'asc';
                }
                this.fetchOffices();
            },

            clearFilters() {
                this.searchQuery = '';
                this.filterStatus = '';
                this.filterType = '';
                this.sortField = 'name';
                this.sortDirection = 'asc';
                this.pagination.current_page = 1;
                this.fetchOffices();
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedIds = this.offices.map(t => t.id);
                } else {
                    this.selectedIds = [];
                }
            },

            openViewModal(office) {
                this.dataToView = office;
                this.showViewModal = true;
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => { this.dataToView = null; }, 300);
            },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, name: '', type: '', parent_id: '', is_active: true };
                this.showAddModal = true;
            },

            openEditModal(office) {
                this.editing = true;
                this.form = { ...office, is_active: !!office.is_active };
                this.showAddModal = true;
            },

            closeAddModal() {
                this.showAddModal = false;
                setTimeout(() => { this.form = { id: null, name: '', type: '', parent_id: '', is_active: true }; }, 300);
            },

            confirmDelete(office) {
                this.dataToDelete = office;
                this.showDeleteModal = true;
            },

            async toggleStatus(office) {
                try {
                    const response = await fetch(`/reception/offices/${office.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (response.ok) {
                        office.is_active = data.is_active;
                        showSuccess(data.message);
                        this.fetchStats();
                    } else {
                        showError(data.message || 'Failed to update status');
                    }
                } catch (error) {
                    showError('A network error occurred');
                }
            },

            async saveOffice() {
                if (!this.form.name || !this.form.type) {
                    showError('Please fill in required fields');
                    return;
                }

                this.saving = true;
                const url = this.editing ? `/reception/offices/${this.form.id}` : '/reception/offices';
                const method = this.editing ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showSuccess(data.message);
                        this.closeAddModal();
                        await this.fetchOffices();
                        await this.fetchStats();
                    } else {
                        if (data.errors) {
                            showError(Object.values(data.errors)[0][0]);
                        } else {
                            showError(data.message || 'Failed to save Office Node');
                        }
                    }
                } catch (error) {
                    showError('A network error occurred');
                } finally {
                    this.saving = false;
                }
            },

            async deleteData() {
                if (!this.dataToDelete) return;
                
                this.deleting = true;
                try {
                    const response = await fetch(`/reception/offices/${this.dataToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showSuccess('Office node purged successfully');
                        this.showDeleteModal = false;
                        this.dataToDelete = null;
                        
                        if (this.offices.length === 1 && this.pagination.current_page > 1) {
                            this.pagination.current_page--;
                        }
                        await this.fetchOffices();
                        await this.fetchStats();
                    } else {
                        showError(data.message || 'Failed to purge office');
                    }
                } catch (error) {
                    showError('A network error occurred');
                } finally {
                    this.deleting = false;
                }
            },

            async bulkDelete() {
                if (!confirm(`Are you sure you want to attempt purging ${this.selectedIds.length} offices? Note: Offices with children will be skipped.`)) return;

                try {
                    const response = await fetch('/reception/offices/bulk-destroy', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showSuccess(data.message);
                        this.selectedIds = [];
                        await this.fetchOffices();
                        await this.fetchStats();
                    } else {
                        showError(data.message || 'Failed to perform mass purge');
                    }
                } catch (error) {
                    showError('A network error occurred');
                }
            }
        };
    }
</script>
@endsection
