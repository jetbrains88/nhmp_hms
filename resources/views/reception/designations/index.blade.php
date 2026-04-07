@extends('layouts.app')
@section('title', 'Designations Management - NHMP HMS')
@section('page-title', 'Designations Hierarchy')

@section('content')
<div x-data="designationManagement()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(79,70,229,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(79,70,229,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
        title="Open Designation Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-indigo-500/10 blur-md rounded-full group-hover:bg-indigo-500/20 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Designation Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
        <!-- Total Designations Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg shadow-indigo-500/20 border border-indigo-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-400 shadow-lg shadow-indigo-900/30 border border-indigo-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-id-badge text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-blue-500 uppercase">Total Valid</p>
                <h4 class="text-3xl font-bold text-blue-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-indigo-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span class="text-[10px] text-indigo-700 font-bold uppercase tracking-tight">System-wide Designations</span>
                </div>
            </div>
        </div>

        <!-- Rank Groups Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-fuchsia-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-fuchsia-500 uppercase">Rank Groups</p>
                <h4 class="text-3xl font-bold text-fuchsia-700 drop-shadow-sm font-mono" x-text="stats.ranks">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-fuchsia-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-fuchsia-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Logical Categories</span>
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
            
            {{-- Panel Header with Light Gradient --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-id-badge text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                Designation Catalog
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Manage personnel ranks, cadres, and BPS levels</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        {{-- Rows Selector moved here --}}
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="searchDesignations()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <button @click="openAddModal()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-400 to-indigo-600 text-white hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            New Designation
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchDesignations()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            title="Refresh">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="selectedIds.length > 0" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="-translate-y-full"
                 x-transition:enter-end="translate-y-0"
                 class="bg-indigo-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl rounded-xl mx-4 mb-4">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                        <span x-text="selectedIds.length"></span> Designations Selected
                    </span>
                    <div class="flex items-center gap-2">
                        <button @click="confirmBulkAction('deactivate')" class="px-3 py-1.5 bg-amber-500 shadow-lg shadow-amber-500/20 hover:bg-amber-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Deactivate Selection</button>
                        <button @click="confirmBulkAction('activate')" class="px-3 py-1.5 bg-emerald-500 shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Activate Selection</button>
                    </div>
                </div>
                <button @click="selectedIds = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2 cursor-pointer">
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
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === designations.length && designations.length > 0"
                                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-5 py-5 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-tag text-[10px]"></i>
                                        </div>
                                        <button @click="sortBy('title')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Title / Abbr
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('title')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500 shadow-sm border border-purple-100">
                                            <i class="fas fa-sort-numeric-up text-[10px]"></i>
                                        </div>
                                        <button @click="sortBy('bps')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Scale (BPS)
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('bps')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-100">
                                            <i class="fas fa-users-cog text-[10px]"></i>
                                        </div>
                                        <button @click="sortBy('cadre_type')" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors group">
                                            Cadre Type
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('cadre_type')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                            <i class="fas fa-layer-group text-[10px]"></i>
                                        </div>
                                        <span>Rank Group</span>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center whitespace-nowrap border-b border-slate-50">
                                    <div class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shadow-sm border border-amber-100">
                                            <i class="fas fa-bolt text-[10px]"></i>
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

                            <template x-if="!loading && designations.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                            <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Designations Found</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                                        <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="item in designations" :key="item.id">
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :value="item.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex bg-gradient-to-r from-indigo-400 to-indigo-600 text-white items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-gray-100 border border-gray-200 text-gray-600">
                                                <i class="fas fa-id-badge text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="item.title"></p>
                                                <div x-show="item.short_form" class="text-[10px] font-bold text-gray-400 tracking-wider uppercase" x-text="`[${item.short_form}]`"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span x-show="item.bps" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black    text-gray-600     " x-text="`BPS-${item.bps}`"></span>
                                        <span x-show="!item.bps" class="text-xs text-gray-400 italic">N/A</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span x-show="item.cadre_type" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold      text-gray-600    capitalize" x-text="item.cadre_type.replace('_', ' ')"></span>
                                        <span x-show="!item.cadre_type" class="text-xs text-gray-400 italic">-</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span x-show="item.rank_group" class="text-xs font-bold text-gray-600 inline-flex items-center gap-2     px-3 py-1.5 rounded-lg  ">
                                            <span x-text="item.rank_group"></span>
                                        </span>
                                        <span x-show="!item.rank_group" class="text-[10px] text-gray-400 uppercase tracking-widest font-black italic">Unassigned</span>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <button @click="openViewModal(item)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-slate-100 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button @click="openEditModal(item)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-slate-100 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button @click="confirmDelete(item)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-slate-100 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
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
            <div x-show="!loading && designations.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto rounded-b-3xl">
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
        <div class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2 group/sidebar" 
             x-show="showSidebar" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-x-12" 
             x-transition:enter-end="opacity-100 translate-x-0" 
             x-transition:leave="transition ease-in duration-300" 
             x-transition:leave-start="opacity-100 translate-x-0" 
             x-transition:leave-end="opacity-0 translate-x-12">
            
            {{-- Unified Filter Card --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">
                
                {{-- Master Header --}}
                <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Designation Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Catalog Data</p>
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
                                    <button @click="searchQuery=''; searchDesignations()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                            <template x-if="filterRankGroup">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-600 border border-purple-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-layer-group opacity-50"></i>
                                    <span x-text="filterRankGroup"></span>
                                    <button @click="filterRankGroup=''; searchDesignations()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
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
                            <input type="text" x-model.debounce.500ms="searchQuery" @input="searchDesignations()"
                                placeholder="Search Title or Abbr..."
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                            <i class="fas fa-id-badge absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Rank Group Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-layer-group text-purple-500"></i> Segment Group
                        </label>
                        <select x-model="filterRankGroup" @change="searchDesignations()"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="">All Rank Groups</option>
                            <template x-for="rank in [...new Set(designations.map(d => d.rank_group).filter(r => r))]">
                                <option :value="rank" x-text="rank"></option>
                            </template>
                        </select>
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
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight relative z-10" x-text="editing ? 'Edit Designation' : 'Create Designation'"></h3>
                    </div>
                    <button @click="closeAddModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="saveDesignation" class="space-y-5">
                        <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Designation Title <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="form.title" required placeholder="e.g. Inspector"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Short Form / Abbr</label>
                                <input type="text" x-model="form.short_form" placeholder="e.g. INSP" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 placeholder:text-slate-400 uppercase">
                            </div>

                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">BPS Range (Sale)</label>
                                <input type="number" min="1" max="22" step="1" x-model="form.bps" placeholder="e.g. 16" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Cadre Classification</label>
                                <select x-model="form.cadre_type" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 appearance-none cursor-pointer">
                                    <option value="">Select Cadre</option>
                                    <option value="uniform">Uniform Cadre</option>
                                    <option value="non_uniform">Non-Uniform Cadre</option>
                                    <option value="technical">Technical Cadre</option>
                                    <option value="administrative">Administrative Cadre</option>
                                    <option value="medical">Medical Cadre</option>
                                    <option value="education">Education Cadre</option>
                                    <option value="legal">Legal Cadre</option>
                                    <option value="finance">Finance Cadre</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Seniority Group</label>
                                <input type="text" x-model="form.rank_group" placeholder="e.g. Junior Officer" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-medium text-slate-800 placeholder:text-slate-400">
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="closeAddModal()" class="px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="saveDesignation()" :disabled="saving" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? 'Saving...' : (editing ? 'Update Designation' : 'Commit Node')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Generic Confirmation Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showConfirmModal = false" x-transition.opacity></div>
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-8 text-center shadow-2xl border border-slate-100" x-transition.scale>
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-6" :class="confirmConfig.type === 'danger' ? 'bg-rose-100 text-rose-600' : (confirmConfig.type === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')">
                <i class="fas text-3xl" :class="confirmConfig.icon"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight" x-text="confirmConfig.title"></h3>
            <p class="text-xs font-bold text-slate-500 mb-8 px-4 uppercase tracking-wider leading-relaxed" x-text="confirmConfig.message"></p>
            <div class="flex gap-3">
                <button @click="showConfirmModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase cursor-pointer">Cancel Action</button>
                <button @click="executeConfirmedAction()" :disabled="confirming" class="flex-1 py-3 text-white rounded-2xl font-black text-xs uppercase shadow-xl transition-all cursor-pointer flex items-center justify-center gap-2" :class="confirmConfig.type === 'danger' ? 'bg-rose-600 shadow-rose-500/30 hover:bg-rose-700' : (confirmConfig.type === 'warning' ? 'bg-amber-600 shadow-amber-500/30 hover:bg-amber-700' : 'bg-blue-600 shadow-blue-500/30 hover:bg-blue-700')">
                    <i class="fas fa-spinner fa-spin" x-show="confirming"></i>
                    <span x-text="confirming ? 'Processing...' : confirmConfig.confirmText"></span>
                </button>
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
                                <i class="fas fa-id-badge text-xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight" x-text="dataToView?.title || 'Details'"></h3>
                                <p class="text-slate-300 text-[10px] font-black uppercase tracking-widest mt-1">Designation Data Record</p>
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
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Short Form / Abbr</p>
                            <p class="font-mono text-sm text-slate-800 font-bold mt-1" x-text="dataToView?.short_form || 'N/A'"></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Scale (BPS)</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-indigo-50 text-indigo-600 border border-indigo-100" x-text="dataToView?.bps ? `BPS-${dataToView.bps}` : 'N/A'"></span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cadre Classification</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 capitalize" x-text="dataToView?.cadre_type ? dataToView.cadre_type.replace('_', ' ') : '-'"></span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Seniority Group</p>
                            <div class="mt-1">
                                <span x-show="dataToView?.rank_group" class="text-xs font-bold text-slate-600 inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200" x-text="dataToView?.rank_group"></span>
                                <span x-show="!dataToView?.rank_group" class="text-[10px] text-slate-400 uppercase tracking-widest font-black italic">Unassigned</span>
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
    function designationManagement() {
        return {
            showSidebar: false,
            showViewModal: false,
            dataToView: null,
            loading: false,
            saving: false,
            confirming: false,
            editing: false,
            
            designations: [],
            stats: { total: 0, ranks: 0 },
            
            searchQuery: '',
            filterRankGroup: '',
            sortField: 'bps',
            sortDirection: 'desc',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
            
            form: { id: null, title: '', short_form: '', bps: '', cadre_type: '', rank_group: '' },
            selectedIds: [],
            dataToDelete: null,
            dataToView: null,

            // Confirmation Modal State
            showConfirmModal: false,
            confirmConfig: {
                title: '',
                message: '',
                icon: '',
                confirmText: '',
                type: 'primary',
                action: null
            },

            async init() {
                await this.fetchDesignations();
                await this.fetchStats();
            },

            hasActiveFilters() {
                return this.searchQuery !== '' || this.filterRankGroup !== '';
            },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort text-slate-300';
                return this.sortDirection === 'asc' ? 'fa-sort-up text-indigo-600 scale-125' : 'fa-sort-down text-indigo-600 scale-125';
            },

            searchDesignations() {
                this.pagination.current_page = 1;
                this.fetchDesignations();
            },

            async fetchDesignations() {
                this.loading = true;
                this.selectedIds = [];
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    sort: this.sortField,
                    direction: this.sortDirection
                });

                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterRankGroup) params.append('rank_group', this.filterRankGroup);

                try {
                    const response = await fetch(`/reception/designations/data?${params.toString()}`);
                    const data = await response.json();
                    
                    this.designations = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to
                    };
                } catch (error) {
                    showError('Failed to load designations catalog');
                } finally {
                    this.loading = false;
                }
            },

            async fetchStats() {
                try {
                    const response = await fetch('/reception/designations/stats');
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
                    this.fetchDesignations();
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
                this.fetchDesignations();
            },

            clearFilters() {
                this.searchQuery = '';
                this.filterRankGroup = '';
                this.sortField = 'bps';
                this.sortDirection = 'desc';
                this.pagination.current_page = 1;
                this.fetchDesignations();
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedIds = this.designations.map(t => t.id);
                } else {
                    this.selectedIds = [];
                }
            },

            openViewModal(item) {
                this.dataToView = item;
                this.showViewModal = true;
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => { this.dataToView = null; }, 300);
            },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, title: '', short_form: '', bps: '', cadre_type: '', rank_group: '' };
                this.showAddModal = true;
            },

            openEditModal(item) {
                this.editing = true;
                this.form = { ...item };
                this.showAddModal = true;
            },

            closeAddModal() {
                this.showAddModal = false;
                setTimeout(() => { this.form = { id: null, title: '', short_form: '', bps: '', cadre_type: '', rank_group: '' }; }, 300);
            },

            confirmDelete(item) {
                this.dataToDelete = item;
                this.showDeleteModal = true;
            },

            async saveDesignation() {
                if (!this.form.title) {
                    window.Notification.warning('Designation title required');
                    return;
                }

                this.saving = true;
                const url = this.editing ? `/reception/designations/${this.form.id}` : '/reception/designations';
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
                        window.Notification.success(data.message || 'Operation successful');
                        this.closeAddModal();
                        await this.fetchDesignations();
                        await this.fetchStats();
                    } else {
                        window.Notification.error(data.message || 'Validation failed');
                    }
                } catch (error) {
                    window.Notification.error('Network interruption');
                } finally {
                    this.saving = false;
                }
            },

            confirmDelete(item) {
                this.dataToDelete = item;
                this.confirmConfig = {
                    title: 'Purge Record',
                    message: `Targeting "${item.title}" for permanent removal?`,
                    icon: 'fa-trash-alt',
                    confirmText: 'Confirm Purge',
                    type: 'danger',
                    action: () => this.deleteData()
                };
                this.showConfirmModal = true;
            },

            async deleteData() {
                this.confirming = true;
                try {
                    const response = await fetch(`/reception/designations/${this.dataToDelete.id}`, {
                        method: 'DELETE',
                        headers: { 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    if (response.ok) {
                        window.Notification.success('Record Erased');
                        this.showConfirmModal = false;
                        await this.fetchDesignations();
                        await this.fetchStats();
                    } else {
                        const data = await response.json();
                        window.Notification.error(data.message || 'Purge failed');
                    }
                } catch (error) {
                    window.Notification.error('Network error during purge');
                } finally {
                    this.confirming = false;
                }
            },

            confirmBulkAction(type) {
                const actionText = type === 'activate' ? 'Activation' : 'Deactivation';
                this.confirmConfig = { 
                    title: `Mass ${actionText}`, 
                    message: `Confirm ${type} of ${this.selectedIds.length} records?`, 
                    icon: type === 'activate' ? 'fa-check-circle' : 'fa-times-circle', 
                    confirmText: `Mass ${type === 'activate' ? 'Activate' : 'Deactivate'}`, 
                    type: type === 'activate' ? 'primary' : 'warning', 
                    action: () => this.executeBulkStatusUpdate(type) 
                };
                this.showConfirmModal = true;
            },

            async executeBulkStatusUpdate(status) {
                this.confirming = true;
                try {
                    const r = await fetch('/reception/designations/bulk-status', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedIds, status: status })
                    });
                    if (r.ok) { 
                        window.Notification.success('Sector Cleaned'); 
                        this.selectedIds = []; 
                        this.showConfirmModal = false; 
                        await this.fetchDesignations(); 
                        await this.fetchStats(); 
                    } else {
                        const d = await r.json();
                        window.Notification.error(d.message || 'Mass update failed');
                    }
                } catch(e) { window.Notification.error('Network error during mass update'); }
                finally { this.confirming = false; }
            },

            executeConfirmedAction() {
                if (this.confirmConfig.action) this.confirmConfig.action();
            }
        };
    }
</script>
@endsection
