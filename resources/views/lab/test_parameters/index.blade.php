@extends('layouts.app')
@section('title', 'Lab Test Parameters Management - NHMP HMS')
@section('page-title', 'Test Parameters')

@section('content')
<div x-data="testParameterManagement()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-blue-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(59,130,246,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(59,130,246,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-blue-400/50 group cursor-pointer"
        title="Open Parameter Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-blue-50">Param Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">
        <!-- Total Parameters Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterTestType = ''; filterGroup = ''; searchQuery = ''; searchParameters(); showSidebar = true">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-stream text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-blue-600 uppercase">Total Params</p>
                <h4 class="text-3xl font-bold text-blue-800 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-200 pt-2 text-blue-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">System Variables</span>
                </div>
            </div>
        </div>

        <!-- Connected Types Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="showSidebar = true">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-vial text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-600 uppercase">Test Types</p>
                <h4 class="text-3xl font-bold text-purple-800 drop-shadow-sm font-mono" x-text="stats.types">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Connected Classifications</span>
                </div>
            </div>
        </div>

        <!-- Parameter Groups Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="showSidebar = true">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-emerald-600 uppercase">Param Groups</p>
                <h4 class="text-3xl font-bold text-emerald-800 drop-shadow-sm font-mono" x-text="stats.groups">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-200 pt-2 text-emerald-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Logical Groupings</span>
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
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-blue-200 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-microscope text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight flex items-center gap-3">
                                Test Parameters
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Configure individual measurable metrics for lab tests</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        {{-- Rows Selector moved here --}}
                        <div class="flex items-center gap-2 bg-white border border-blue-200 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="searchParameters()" class="bg-transparent text-blue-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <button @click="openAddModal()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            New Parameter
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-blue-200 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchParameters()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-blue-200 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors shadow-sm"
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
                 class="bg-blue-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                        <span x-text="selectedIds.length"></span> Params Selected
                    </span>
                    <div class="flex items-center gap-2">
                        <button @click="bulkDelete()" class="px-3 py-1.5 bg-rose-500/80 hover:bg-rose-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Purge</button>
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
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === parameters.length && parameters.length > 0"
                                            class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-all shadow-sm cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shadow-sm border border-blue-500/20">
                                            <i class="fas fa-stream text-xs"></i>
                                        </div>
                                        <button @click="sortBy('name')" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors group">
                                            Parameter Name
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-600 shadow-sm border border-purple-500/20">
                                            <i class="fas fa-vial text-xs"></i>
                                        </div>
                                        <button @click="sortBy('test_type')" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors group">
                                            Parent Test Type
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('test_type')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-500/20">
                                            <i class="fas fa-layer-group text-xs"></i>
                                        </div>
                                        <button @click="sortBy('group_name')" class="flex items-center gap-1.5 hover:text-blue-600 transition-colors group">
                                            Group
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('group_name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-500/20">
                                            <i class="fas fa-chart-line text-xs"></i>
                                        </div>
                                        <span>Reference / Ranges</span>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center whitespace-nowrap w-48">
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
                                        <i class="fas fa-circle-notch fa-spin text-3xl text-blue-400 mb-2"></i>
                                        <p class="text-sm font-medium">Syncing Data...</p>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && parameters.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                            <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Parameters Found</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                                        <button @click="clearFilters()" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="param in parameters" :key="param.id">
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :value="param.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-blue-600 text-white">
                                                <i class="fas fa-stream text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="param.name"></p>
                                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mt-1">
                                                    Type: <span x-text="param.input_type" class="text-gray-600"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center justify-center px-3 py-1   text-gray-700  text-xs font-black uppercase tracking-widest " x-text="param.lab_test_type?.name || 'N/A'"></span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center justify-center px-3 py-1  text-gray-700 text-xs font-black uppercase tracking-widest " x-text="param.group_name || 'Generic'"></span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div x-show="param.input_type === 'number'" class="text-xs">
                                            <span class="font-mono font-bold bg-indigo-50 text-indigo-700 px-2 py-1 rounded shadow-sm border border-indigo-100">
                                                <span x-text="param.min_range"></span> - <span x-text="param.max_range"></span>
                                            </span>
                                            <span class="text-slate-500 ml-1.5 font-bold uppercase tracking-wider text-[10px]" x-text="param.unit"></span>
                                        </div>
                                        <div x-show="param.input_type !== 'number' && param.reference_range" class="text-xs font-medium text-slate-600 bg-slate-50 px-2 py-1 rounded border border-slate-200 truncate max-w-[150px] mx-auto shadow-sm" :title="param.reference_range" x-text="param.reference_range"></div>
                                        <div x-show="param.input_type !== 'number' && !param.reference_range" class="text-xs text-slate-400 font-bold">-</div>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <button @click="openViewModal(param)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button @click="openEditModal(param)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button @click="confirmDelete(param)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Loading Overlay --}}
                <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin shadow-lg"></div>
                        <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] animate-pulse">Syncing Vault...</span>
                    </div>
                </div>

            </div>
            
            <div x-show="!loading && parameters.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto rounded-b-3xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from || 0"></span> - <span class="text-slate-900" x-text="pagination.to || 0"></span> 
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                            Source: <span class="text-blue-600" x-text="pagination.total"></span> Entries
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- First Button --}}
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-angles-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                        </button>

                        {{-- Previous Button --}}
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Prev</span>
                        </button>

                        <div class="flex items-center gap-1.5 mx-2">
                            <template x-for="page in getPageRange()" :key="page">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 border-blue-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-600 hover:text-blue-600'"
                                    :disabled="page === '...'"
                                    class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"
                                    x-text="page">
                                </button>
                            </template>
                        </div>

                        {{-- Next Button --}}
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>

                        {{-- Last Button --}}
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
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
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Param Filters</h2>
                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mt-0.5">Refine Data Points</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm" title="Hide Filters">
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
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-search opacity-50"></i>
                                    <span x-text="searchQuery"></span>
                                    <button @click="searchQuery=''; searchParameters()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                            <template x-if="filterTestType">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-vial opacity-50"></i>
                                    <span>Type Filter</span>
                                    <button @click="filterTestType=''; searchParameters()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div x-show="hasActiveFilters()" class="border-b border-dashed border-slate-200"></div>

                    {{-- Search Module --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-search text-blue-500"></i> Localize Point
                        </label>
                        <div class="relative group">
                            <input type="text" x-model.debounce.500ms="searchQuery" @input="searchParameters()"
                                placeholder="Search Name..."
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Test Type Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-vial text-indigo-500"></i> Parent Test Link
                        </label>
                        <select x-model="filterTestType" @change="searchParameters()"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="">All Test Types</option>
                            @foreach($testTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
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
                        class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:-translate-y-0.5 transition-all text-left flex items-center justify-between group shadow-md shadow-indigo-500/20">
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

            <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle border border-slate-100">
                
                {{-- Modal Header --}}
                <div class="px-6 py-5 border-b border-blue-100/50 bg-gradient-to-r from-blue-50 to-indigo-50 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-blue-200 shadow-sm text-blue-600 relative z-10">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight relative z-10" x-text="editing ? 'Edit Test Parameter' : 'Create Parameter'"></h3>
                    </div>
                    <button @click="closeAddModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="saveParameter" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5 focus-within:text-blue-600 transition-colors col-span-2 md:col-span-1">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Parent Lab Test Type <span class="text-rose-500">*</span></label>
                                <select x-model="form.lab_test_type_id" required
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 appearance-none cursor-pointer">
                                    <option value="" disabled>Select Test Type</option>
                                    @foreach($testTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5 focus-within:text-blue-600 transition-colors col-span-2 md:col-span-1">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Parameter Name <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="form.name" required placeholder="e.g. Hemoglobin"
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5 focus-within:text-blue-600 transition-colors col-span-2 md:col-span-1">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Group Category (Optional)</label>
                                <input type="text" x-model="form.group_name" placeholder="e.g. Differential Count"
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5 focus-within:text-blue-600 transition-colors col-span-2 md:col-span-1">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Input/Result Type <span class="text-rose-500">*</span></label>
                                <select x-model="form.input_type" required
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 appearance-none cursor-pointer">
                                    <option value="text">Text (e.g. Positive)</option>
                                    <option value="number">Numeric (e.g. 14.5)</option>
                                </select>
                            </div>

                            <div class="col-span-2 p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 border border-slate-200 rounded-2xl shadow-inner space-y-4" x-show="form.input_type === 'number'">
                                <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1 flex items-center gap-2"><i class="fas fa-sliders-h"></i> Numeric Range Configuration</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                        <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1">Min Value</label>
                                        <input type="number" step="0.001" x-model="form.min_range" placeholder="11.5"
                                            class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 font-mono font-bold text-slate-700 shadow-sm transition-all focus:bg-indigo-50/30">
                                    </div>
                                    <div class="space-y-1.5 focus-within:text-emerald-600 transition-colors">
                                        <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1">Max Value</label>
                                        <input type="number" step="0.001" x-model="form.max_range" placeholder="15.5"
                                            class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/10 font-mono font-bold text-slate-700 shadow-sm transition-all focus:bg-emerald-50/30">
                                    </div>
                                    <div class="space-y-1.5 focus-within:text-blue-600 transition-colors">
                                        <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1">Unit</label>
                                        <input type="text" x-model="form.unit" placeholder="g/dL"
                                            class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 font-mono font-bold text-slate-700 shadow-sm transition-all focus:bg-blue-50/30">
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 p-5 bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-200 rounded-2xl shadow-inner space-y-1.5 focus-within:text-slate-600 transition-colors" x-show="form.input_type === 'text'">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2"><i class="fas fa-comment-alt"></i> Text Reference / Normal Values</label>
                                <textarea x-model="form.reference_range" rows="2" placeholder="e.g. Negative, Not detected..."
                                    class="w-full px-4 py-3 bg-white border-2 border-slate-100 rounded-xl focus:border-slate-400 focus:ring-4 focus:ring-slate-400/10 resize-none font-bold text-slate-700 shadow-sm transition-all"></textarea>
                            </div>

                            <div class="space-y-1.5 focus-within:text-blue-600 transition-colors col-span-2">
                                <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Sort Order Priority</label>
                                <input type="number" x-model="form.order" placeholder="0"
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                                <p class="text-[10px] font-bold text-slate-400 ml-1 mt-1 uppercase tracking-wider">Lower numbers appear first on reports.</p>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="closeAddModal()" class="px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="saveParameter()" :disabled="saving" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 hover:-translate-y-0.5 transition-all shadow-md shadow-blue-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? 'Saving...' : (editing ? 'Update Parameter' : 'Commit Node')"></span>
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
                <h3 class="text-xl font-black text-slate-800 mb-2">Delete Parameter</h3>
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
                                <i class="fas fa-microscope text-xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight" x-text="dataToView?.name || 'Details'"></h3>
                                <p class="text-slate-300 text-[10px] font-black uppercase tracking-widest mt-1">Parameter Record</p>
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
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Type Link</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-purple-50 text-purple-600 border border-purple-100" x-text="dataToView?.lab_test_type?.name || 'N/A'"></span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category / Group</p>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-emerald-50 text-emerald-600 border border-emerald-100" x-text="dataToView?.group_name || 'Generic'"></span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Input Type</p>
                            <p class="font-mono text-sm text-slate-800 font-bold mt-1 uppercase" x-text="dataToView?.input_type"></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reference Range</p>
                            <div class="mt-1">
                                <template x-if="dataToView?.input_type === 'number'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-mono font-bold shadow-sm">
                                        <span x-text="dataToView?.min_range"></span> - <span x-text="dataToView?.max_range"></span>
                                        <span class="text-[10px] text-slate-500 uppercase tracking-widest font-sans ml-1" x-text="dataToView?.unit"></span>
                                    </span>
                                </template>
                                <template x-if="dataToView?.input_type !== 'number'">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-slate-50 border border-slate-200 text-slate-600 text-xs font-medium shadow-sm" x-text="dataToView?.reference_range || '-'"></span>
                                </template>
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
    function testParameterManagement() {
        return {
            showSidebar: false,
            showAddModal: false,
            showDeleteModal: false,
            showViewModal: false,
            loading: false,
            saving: false,
            deleting: false,
            editing: false,
            
            parameters: [],
            stats: { total: 0, groups: 0, types: 0 },
            
            searchQuery: '',
            filterTestType: '',
            sortField: 'name',
            sortDirection: 'asc',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
            
            form: { id: null, lab_test_type_id: '', name: '', group_name: '', unit: '', reference_range: '', min_range: null, max_range: null, input_type: 'text', order: 0 },
            selectedIds: [],
            dataToDelete: null,
            dataToView: null,

            async init() {
                await this.fetchParameters();
                await this.fetchStats();
            },

            hasActiveFilters() {
                return this.searchQuery !== '' || this.filterTestType !== '';
            },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort text-slate-300';
                return this.sortDirection === 'asc' ? 'fa-sort-up text-blue-600 scale-125' : 'fa-sort-down text-blue-600 scale-125';
            },

            searchParameters() {
                this.pagination.current_page = 1;
                this.fetchParameters();
            },

            async fetchParameters() {
                this.loading = true;
                this.selectedIds = [];
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    sort: this.sortField,
                    direction: this.sortDirection
                });

                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterTestType) params.append('test_type', this.filterTestType);

                try {
                    const response = await fetch(`/lab/test-parameters/data?${params.toString()}`);
                    const data = await response.json();
                    
                    this.parameters = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to
                    };
                } catch (error) {
                    window.Notification.error('Failed to load test parameters');
                } finally {
                    this.loading = false;
                }
            },

            async fetchStats() {
                try {
                    const response = await fetch('/lab/test-parameters/stats');
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
                    this.fetchParameters();
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
                this.fetchParameters();
            },

            clearFilters() {
                this.searchQuery = '';
                this.filterTestType = '';
                this.sortField = 'name';
                this.sortDirection = 'asc';
                this.pagination.current_page = 1;
                this.fetchParameters();
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedIds = this.parameters.map(t => t.id);
                } else {
                    this.selectedIds = [];
                }
            },

            openViewModal(param) {
                this.dataToView = param;
                this.showViewModal = true;
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => { this.dataToView = null; }, 300);
            },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, lab_test_type_id: '', name: '', group_name: '', unit: '', reference_range: '', min_range: null, max_range: null, input_type: 'text', order: 0 };
                this.showAddModal = true;
            },

            openEditModal(param) {
                this.editing = true;
                this.form = { ...param };
                this.showAddModal = true;
            },

            closeAddModal() {
                this.showAddModal = false;
                setTimeout(() => { this.form = { id: null, lab_test_type_id: '', name: '', group_name: '', unit: '', reference_range: '', min_range: null, max_range: null, input_type: 'text', order: 0 }; }, 300);
            },

            confirmDelete(param) {
                this.dataToDelete = param;
                this.showDeleteModal = true;
            },

            async saveParameter() {
                if (!this.form.name || !this.form.lab_test_type_id || !this.form.input_type) {
                    window.Notification.warning('Please fill in all required fields');
                    return;
                }

                this.saving = true;
                const url = this.editing ? `/lab/test-parameters/${this.form.id}` : '/lab/test-parameters';
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
                        window.Notification.success(data.message);
                        this.closeAddModal();
                        await this.fetchParameters();
                        await this.fetchStats();
                    } else {
                        if (data.errors) {
                            window.Notification.error(Object.values(data.errors)[0][0]);
                        } else {
                            window.Notification.error(data.message || 'Failed to save Parameter');
                        }
                    }
                } catch (error) {
                    window.Notification.error('A network error occurred');
                } finally {
                    this.saving = false;
                }
            },

            async deleteData() {
                if (!this.dataToDelete) return;
                
                this.deleting = true;
                try {
                    const response = await fetch(`/lab/test-parameters/${this.dataToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        window.Notification.success('Parameter purged successfully');
                        this.showDeleteModal = false;
                        this.dataToDelete = null;
                        
                        if (this.parameters.length === 1 && this.pagination.current_page > 1) {
                            this.pagination.current_page--;
                        }
                        await this.fetchParameters();
                        await this.fetchStats();
                    } else {
                        window.Notification.error('Failed to purge Parameter');
                    }
                } catch (error) {
                    window.Notification.error('A network error occurred');
                } finally {
                    this.deleting = false;
                }
            },

            async bulkDelete() {
                if (!confirm(`Are you sure you want to permanently purge ${this.selectedIds.length} parameters?`)) return;

                try {
                    const response = await fetch('/lab/test-parameters/bulk-destroy', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });

                    if (response.ok) {
                        window.Notification.success(`Successfully purged ${this.selectedIds.length} parameters`);
                        this.selectedIds = [];
                        await this.fetchParameters();
                        await this.fetchStats();
                    } else {
                        window.Notification.error('Failed to perform mass purge');
                    }
                } catch (error) {
                    window.Notification.error('A network error occurred');
                }
            }
        };
    }
</script>
@endsection