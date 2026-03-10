@extends('layouts.app')
@section('title', 'Lab Test Types Management - NHMP HMS')
@section('page-title', 'Lab Test Types')

@section('content')
<div x-data="testTypeManagement()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-purple-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(168,85,247,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(168,85,247,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-purple-400/50 group cursor-pointer"
        title="Open Type Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-purple-50">Type Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">
        <!-- Total Types Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterDepartment = ''; searchQuery = ''; fetchTestTypes(); showSidebar = true">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-vials text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-600 uppercase">Total Types</p>
                <h4 class="text-3xl font-bold text-purple-800 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">System Classifications</span>
                </div>
            </div>
        </div>

        <!-- Departments Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="showSidebar = true">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-building text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-blue-600 uppercase">Departments</p>
                <h4 class="text-3xl font-bold text-blue-800 drop-shadow-sm font-mono" x-text="stats.departments">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-200 pt-2 text-blue-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Active Lab Sections</span>
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
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-6 border-b border-purple-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-purple-200 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-vial text-2xl text-purple-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600 tracking-tight flex items-center gap-3">
                                Test Types
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Manage laboratory test classifications and sample requirements</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        {{-- Rows Selector moved here --}}
                        <div class="flex items-center gap-2 bg-white border border-purple-200 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="searchTestTypes()" class="bg-transparent text-purple-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <button @click="openAddModal()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            New Type
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-purple-200 text-purple-600 rounded-xl hover:bg-purple-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchTestTypes()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-purple-200 text-purple-600 rounded-xl hover:bg-purple-50 transition-colors shadow-sm"
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
                 class="bg-purple-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                        <span x-text="selectedIds.length"></span> Types Selected
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
                                        <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === testTypes.length && testTypes.length > 0"
                                            class="w-5 h-5 rounded border-slate-300 text-purple-600 focus:ring-purple-500 transition-all shadow-sm cursor-pointer">
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-600 shadow-sm border border-blue-500/20">
                                            <i class="fas fa-tag text-xs"></i>
                                        </div>
                                        <button @click="sortBy('name')" class="flex items-center gap-1.5 hover:text-purple-600 transition-colors group">
                                            Test Name
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-500/20">
                                            <i class="fas fa-building text-xs"></i>
                                        </div>
                                        <button @click="sortBy('department')" class="flex items-center gap-1.5 hover:text-purple-600 transition-colors group">
                                            Department
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('department')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-slate-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-500/20">
                                            <i class="fas fa-tint text-xs"></i>
                                        </div>
                                        <span>Sample Type</span>
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
                                    <td colspan="5" class="p-8 text-center text-slate-500">
                                        <i class="fas fa-circle-notch fa-spin text-3xl text-purple-400 mb-2"></i>
                                        <p class="text-sm font-medium">Syncing Data...</p>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && testTypes.length === 0">
                                <tr>
                                    <td colspan="5" class="py-24 text-center">
                                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                            <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                        </div>
                                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Test Types Found</h3>
                                        <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                                        <button @click="clearFilters()" class="px-8 py-3 bg-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-purple-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="type in testTypes" :key="type.id">
                                <tr class="hover:bg-purple-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :value="type.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-purple-600 focus:ring-purple-500 transition-all cursor-pointer">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-purple-600 text-white">
                                                <i class="fas fa-vial text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="type.name"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-gray-700 text-xs font-black uppercase tracking-widest" x-text="type.department || 'General'"></span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center justify-center px-3 py-1 text-gray-700 text-xs font-black uppercase tracking-widest" x-text="type.sample_type || 'N/A'"></span>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <button @click="viewParameters(type)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" title="View Parameters">
                                                <i class="fas fa-list-check"></i> Parameters
                                            </button>
                                            <button @click="openViewModal(type)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button @click="openEditModal(type)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button @click="confirmDelete(type)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all text-[10px] font-bold uppercase tracking-wider">
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
                        <div class="w-16 h-16 border-4 border-purple-100 border-t-purple-600 rounded-full animate-spin shadow-lg"></div>
                        <span class="text-xs font-black text-purple-600 uppercase tracking-[0.3em] animate-pulse">Syncing Vault...</span>
                    </div>
                </div>

            </div>
            
            {{-- Premium Pagination --}}
            <div x-show="!loading && testTypes.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto rounded-b-3xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from || 0"></span> - <span class="text-slate-900" x-text="pagination.to || 0"></span> 
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                            Source: <span class="text-purple-600" x-text="pagination.total"></span> Entries
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- First Button --}}
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-purple-600 hover:text-purple-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-angles-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                        </button>

                        {{-- Previous Button --}}
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-purple-600 hover:text-purple-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Prev</span>
                        </button>

                        <div class="flex items-center gap-1.5 mx-2">
                            <template x-for="page in getPageRange()" :key="page">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ? 'bg-purple-600 text-white shadow-lg shadow-purple-200 border-purple-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-purple-600 hover:text-purple-600'"
                                    :disabled="page === '...'"
                                    class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"
                                    x-text="page">
                                </button>
                            </template>
                        </div>

                        {{-- Next Button --}}
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-purple-600 hover:text-purple-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>

                        {{-- Last Button --}}
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-purple-600 hover:text-purple-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
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
                        <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Type Filters</h2>
                            <p class="text-[9px] font-black text-purple-400 uppercase tracking-widest mt-0.5">Refine Catalog Data</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-purple-600 hover:border-purple-200 hover:bg-purple-50 transition-all shadow-sm" title="Hide Filters">
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
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-600 border border-purple-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-search opacity-50"></i>
                                    <span x-text="searchQuery"></span>
                                    <button @click="searchQuery=''; searchTestTypes()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                            <template x-if="filterDepartment">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                    <i class="fas fa-building opacity-50"></i>
                                    <span x-text="filterDepartment"></span>
                                    <button @click="filterDepartment=''; searchTestTypes()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div x-show="hasActiveFilters()" class="border-b border-dashed border-slate-200"></div>

                    {{-- Search Module --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-search text-purple-500"></i> Localize Point
                        </label>
                        <div class="relative group">
                            <input type="text" x-model.debounce.500ms="searchQuery" @input="searchTestTypes()"
                                placeholder="Search Type Name..."
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                            <i class="fas fa-vial absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-purple-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Department Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-building text-indigo-500"></i> Department Link
                        </label>
                        <select x-model="filterDepartment" @change="searchTestTypes()"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="">All Departments</option>
                            <template x-for="dept in stats.available_departments" :key="dept">
                                <option :value="dept" x-text="dept"></option>
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
                <div class="px-6 py-5 border-b border-purple-100/50 bg-gradient-to-r from-purple-50 to-indigo-50 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-purple-200 shadow-sm text-purple-600 relative z-10">
                            <i class="fas fa-vial"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight relative z-10" x-text="editing ? 'Edit Test Type' : 'Create Test Type'"></h3>
                    </div>
                    <button @click="closeAddModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="saveTestType" class="space-y-5">
                        <div class="space-y-1.5 focus-within:text-purple-600 transition-colors">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Test Name <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="form.name" required placeholder="e.g. Complete Blood Count (CBC)"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-purple-400 focus:ring-4 focus:ring-purple-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                        </div>

                        <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Department</label>
                            <input type="text" x-model="form.department" placeholder="e.g. Hematology, Biochemistry"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                        </div>

                        <div class="space-y-1.5 focus-within:text-blue-600 transition-colors">
                            <label class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Sample Type</label>
                            <input type="text" x-model="form.sample_type" placeholder="e.g. Blood, Urine, Serum"
                                class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button type="button" @click="closeAddModal()" class="px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="saveTestType()" :disabled="saving" class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-purple-700 hover:to-indigo-700 hover:-translate-y-0.5 transition-all shadow-md shadow-purple-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? 'Saving...' : (editing ? 'Update Type' : 'Commit Node')"></span>
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
                <h3 class="text-xl font-black text-slate-800 mb-2">Delete Test Type</h3>
                <p class="text-xs font-bold text-slate-500 mb-6 px-4 uppercase tracking-wider leading-relaxed">System erasure of <br><span class="font-black text-slate-700 text-base normal-case tracking-normal" x-text="typeToDelete?.name"></span></p>
                <div class="flex items-center justify-center gap-3">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-colors w-full cursor-pointer">Cancel</button>
                    <button @click="deleteTestType()" :disabled="deleting" class="px-5 py-2.5 bg-gradient-to-r from-rose-500 to-rose-700 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-rose-600 hover:to-rose-800 hover:-translate-y-0.5 transition-all shadow-md shadow-rose-500/30 w-full flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-spinner fa-spin" x-show="deleting"></i>
                        <span x-text="deleting ? 'Purging...' : 'Confirm Purge'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Parameters Modal --}}
    <div x-show="showParametersModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showParametersModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="showParametersModal = false"></div>
            
            <div x-show="showParametersModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                class="relative inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-[2.5rem] sm:my-8 sm:align-middle border border-slate-100">
                
                {{-- Sticky Header --}}
                <div class="sticky top-0 z-20 px-8 py-6 border-b border-indigo-50 bg-white/80 backdrop-blur-md flex justify-between items-center overflow-hidden">
                    <div class="absolute top-0 right-0 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-lg text-white">
                            <i class="fas fa-list-check text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Test Parameters</h3>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Analytical Configuration</p>
                        </div>
                    </div>
                    <button @click="showParametersModal = false" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all z-10 shadow-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-8 py-6 max-h-[70vh] overflow-y-auto scrollbar-hide space-y-8">
                    
                    {{-- Test Context Header --}}
                    <div class="flex items-center space-x-4 p-5 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-3xl border border-indigo-100/50">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm shrink-0">
                            <i class="fas fa-vial text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-lg font-black text-slate-800 truncate" x-text="dataToView?.name"></h4>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="px-2 py-0.5 bg-indigo-600 text-white rounded-md text-[9px] font-black uppercase tracking-widest shadow-sm" x-text="dataToView?.department || 'General'"></span>
                                <span class="px-2 py-0.5 bg-white text-indigo-600 border border-indigo-100 rounded-md text-[9px] font-black uppercase tracking-widest shadow-sm" x-text="dataToView?.sample_type || 'N/A'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="parametersLoading" class="py-12 flex flex-col items-center justify-center gap-4 text-center">
                        <div class="relative">
                            <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
                            <i class="fas fa-microscope absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-indigo-400 animate-pulse"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em]">Decoding Structure</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Retrieving mapped metrics...</p>
                        </div>
                    </div>

                    {{-- Empty State --}}
                    <div x-show="!parametersLoading && currentParameters.length === 0" class="py-20 text-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 border border-slate-100 rotate-3 group-hover:rotate-0 transition-transform shadow-inner">
                            <i class="fas fa-vial-circle-check text-3xl text-slate-200"></i>
                        </div>
                        <h5 class="text-base font-black text-slate-700">No Parameters Defined</h5>
                        <p class="text-xs font-bold text-slate-400 max-w-[240px] mx-auto mt-2 uppercase tracking-wider leading-relaxed">This test type has not been mapped with analytical parameters.</p>
                    </div>

                    {{-- Grouped Parameters Grid --}}
                    <div x-show="!parametersLoading && currentParameters.length > 0" class="space-y-8">
                        <template x-for="(params, group) in groupedParameters()" :key="group">
                            <div class="space-y-4">
                                {{-- Group Title --}}
                                <div class="flex items-center gap-3">
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                                    <h5 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.3em] bg-white px-4 py-1 rounded-full border border-indigo-50 shadow-sm" x-text="group"></h5>
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                                </div>

                                {{-- Parameter Cards Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="param in params" :key="param.id">
                                        <div class="p-4 bg-white border border-indigo-400 rounded-2xl flex flex-col gap-3 hover:border-maroon-400 hover:shadow-xl hover:shadow-maroon-500/5 transition-all group relative overflow-hidden">
                                            <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-indigo-50 rounded-full group-hover:bg-indigo-50 transition-colors"></div>
                                            
                                            <div class="flex items-center justify-between gap-3 relative z-10">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-xl bg-indigo-600 border text-white border-indigo-600  flex items-center justify-center group-hover:bg-maroon-600 group-hover:border-maroon-600 transition-all shadow-sm">
                                                        <i class="fas fa-flask text-xs"></i>
                                                    </div>
                                                    <span class="text-xs font-black text-slate-700" x-text="param.name"></span>
                                                </div>
                                                <i class="fas fa-chevron-right text-[10px] text-indigo-600 group-hover:text-indigo-600 transition-colors"></i>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2 relative z-10 pt-1 border-t border-slate-50 group-hover:border-indigo-50 transition-colors">
                                                <template x-if="param.reference_range">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Clearance Range</span>
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-md text-[9px] font-black uppercase tracking-widest shadow-sm" x-text="param.reference_range"></span>
                                                    </div>
                                                </template>
                                                <div class="flex-1 flex flex-col items-end gap-0.5">
                                                    <template x-if="param.unit">
                                                        <div class="flex flex-col items-end">
                                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Measurement</span>
                                                            <span class="text-[10px] font-black text-indigo-600" x-text="param.unit"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button @click="showParametersModal = false" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:shadow-xl hover:shadow-indigo-500/20 hover:-translate-y-0.5 transition-all shadow-lg active:scale-95">
                        Dismiss Knowledge Component
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function testTypeManagement() {
        return {
            showSidebar: false,
            showAddModal: false,
            showDeleteModal: false,
            showViewModal: false,
            showParametersModal: false,
            dataToView: null,
            loading: false,
            parametersLoading: false,
            saving: false,
            deleting: false,
            editing: false,
            
            testTypes: [],
            stats: { total: 0, departments: 0, available_departments: [] },
            
            searchQuery: '',
            filterDepartment: '',
            sortField: 'name',
            sortDirection: 'asc',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
            
            form: { id: null, name: '', department: '', sample_type: '' },
            selectedIds: [],
            typeToDelete: null,
            currentParameters: [],

            async init() {
                await this.fetchTestTypes();
                await this.fetchStats();
            },

            hasActiveFilters() {
                return this.searchQuery !== '' || this.filterDepartment !== '';
            },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort text-slate-300';
                return this.sortDirection === 'asc' ? 'fa-sort-up text-purple-600 scale-125' : 'fa-sort-down text-purple-600 scale-125';
            },

            searchTestTypes() {
                this.pagination.current_page = 1;
                this.fetchTestTypes();
            },

            async fetchTestTypes() {
                this.loading = true;
                this.selectedIds = [];
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    sort: this.sortField,
                    direction: this.sortDirection
                });

                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterDepartment) params.append('department', this.filterDepartment);

                try {
                    const response = await fetch(`/lab/test-types/data?${params.toString()}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();
                    
                    this.testTypes = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to
                    };
                } catch (error) {
                    window.Notification.error('Failed to load test types');
                } finally {
                    this.loading = false;
                }
            },

            async viewParameters(type) {
                this.dataToView = type;
                this.showParametersModal = true;
                this.parametersLoading = true;
                this.currentParameters = [];

                try {
                    const response = await fetch(`/lab/test-parameters/data?test_type=${type.id}&per_page=100`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();
                    this.currentParameters = data.data || [];
                } catch (error) {
                    console.error('Error fetching parameters:', error);
                    if (window.notification) notification.error('Failed to load metrics');
                } finally {
                    this.parametersLoading = false;
                }
            },

            groupedParameters() {
                return this.currentParameters.reduce((groups, param) => {
                    const group = param.group_name || 'General Parameters';
                    if (!groups[group]) groups[group] = [];
                    groups[group].push(param);
                    return groups;
                }, {});
            },

            async fetchStats() {
                try {
                    const response = await fetch('/lab/test-types/stats');
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
                    this.fetchTestTypes();
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
                this.fetchTestTypes();
            },

            clearFilters() {
                this.searchQuery = '';
                this.filterDepartment = '';
                this.sortField = 'name';
                this.sortDirection = 'asc';
                this.pagination.current_page = 1;
                this.fetchTestTypes();
            },

            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedIds = this.testTypes.map(t => t.id);
                } else {
                    this.selectedIds = [];
                }
            },

            openViewModal(type) {
                this.dataToView = type;
                this.showViewModal = true;
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => { this.dataToView = null; }, 300);
            },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, name: '', department: '', sample_type: '' };
                this.showAddModal = true;
            },

            openEditModal(type) {
                this.editing = true;
                this.form = { ...type };
                this.showAddModal = true;
            },

            closeAddModal() {
                this.showAddModal = false;
                setTimeout(() => { this.form = { id: null, name: '', department: '', sample_type: '' }; }, 300);
            },

            confirmDelete(type) {
                this.typeToDelete = type;
                this.showDeleteModal = true;
            },

            async saveTestType() {
                if (!this.form.name) {
                    window.Notification.warning('Test name is required');
                    return;
                }

                this.saving = true;
                const url = this.editing ? `/lab/test-types/${this.form.id}` : '/lab/test-types';
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
                        await this.fetchTestTypes();
                        await this.fetchStats();
                    } else {
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            window.Notification.error(firstError);
                        } else {
                            window.Notification.error(data.message || 'Failed to save Test Type');
                        }
                    }
                } catch (error) {
                    window.Notification.error('A network error occurred');
                } finally {
                    this.saving = false;
                }
            },

            async deleteTestType() {
                if (!this.typeToDelete) return;
                
                this.deleting = true;
                try {
                    const response = await fetch(`/lab/test-types/${this.typeToDelete.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        window.Notification.success('Test Type purged successfully');
                        this.showDeleteModal = false;
                        this.typeToDelete = null;
                        
                        if (this.testTypes.length === 1 && this.pagination.current_page > 1) {
                            this.pagination.current_page--;
                        }
                        await this.fetchTestTypes();
                        await this.fetchStats();
                    } else {
                        window.Notification.error('Failed to purge Test Type');
                    }
                } catch (error) {
                    window.Notification.error('A network error occurred');
                } finally {
                    this.deleting = false;
                }
            },

            async bulkDelete() {
                if (!confirm(`Are you sure you want to permanently purge ${this.selectedIds.length} test types?`)) return;

                try {
                    const response = await fetch('/lab/test-types/bulk-destroy', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });

                    if (response.ok) {
                        window.Notification.success(`Successfully purged ${this.selectedIds.length} types`);
                        this.selectedIds = [];
                        await this.fetchTestTypes();
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