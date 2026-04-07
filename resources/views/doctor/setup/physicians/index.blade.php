@extends('layouts.app')

@section('title', 'Medical Specialties — Doctor Setup')
@section('page-title', 'Medical Specialties')
@section('breadcrumb', 'Doctor Setup / Specialties')

@section('content')
<div x-data="physicianRegistry()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(79,70,229,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(79,70,229,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
        title="Open Registry Filters">
    <div class="relative">
        <div class="absolute inset-0 bg-indigo-500/10 blur-md rounded-full group-hover:bg-indigo-500/20 transition-colors duration-300"></div>
        <i class="fas fa-sliders-h relative z-10 group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
    </div>
    <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Specialty Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS (Full Width Bento-style)
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="relative flex flex-col bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg shadow-indigo-500/10 border border-indigo-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-400 shadow-xl shadow-indigo-900/20 border border-indigo-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-stethoscope text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-indigo-500 uppercase">Total Specialties</p>
                <h4 class="text-3xl font-bold text-indigo-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-indigo-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span class="text-[10px] text-indigo-700 font-bold uppercase tracking-tight">Referral Network</span>
                </div>
            </div>
        </div>

        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'active'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-xl shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active Contacts</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active || 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Active Medical Fields</span>
                </div>
            </div>
        </div>

        <div class="relative flex flex-col bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl shadow-lg shadow-slate-500/10 border border-slate-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'inactive'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-slate-600 to-gray-400 shadow-xl shadow-slate-900/20 border border-slate-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-toggle-off text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-slate-500 uppercase">Inactive</p>
                <h4 class="text-3xl font-bold text-slate-700 drop-shadow-sm font-mono" x-text="stats.inactive || 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-slate-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    <span class="text-[10px] text-slate-600 font-bold uppercase tracking-tight">Archived Registry</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">
                
                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-file-medical text-2xl text-indigo-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 tracking-tight flex items-center gap-3">
                                    Medical Specialties
                                    <span class="text-lg font-normal text-gray-600">
                                        (<span x-text="meta.total"></span> records)
                                    </span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Available Medical Fields for referrals</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 items-center">
                            <button @click="openModal()"
                                class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-95 group">
                                <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                                Add Specialty
                            </button>
                            
                            <button @click="showSidebar = !showSidebar"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                            </button>
                            
                            <button @click="fetchData()" 
                                class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                title="Refresh Data">
                                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bulk Actions Toolbar --}}
                <div x-show="selectedIds.length > 0" 
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="-translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     class="bg-indigo-600 px-6 py-3 flex items-center justify-between text-white sticky top-0 z-10 shadow-2xl rounded-b-xl mx-6">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                            <span x-text="selectedIds.length"></span> Specialties Selected
                        </span>
                        <div class="flex items-center gap-2">
                            <button @click="confirmBulkAction('activate')" class="px-3 py-1.5 bg-emerald-500/80 hover:bg-emerald-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Activate</button>
                            <button @click="confirmBulkAction('deactivate')" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Deactivate</button>
                            <button @click="confirmBulkAction('delete')" class="px-3 py-1.5 bg-rose-500/80 hover:bg-rose-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Purge</button>
                        </div>
                    </div>
                    <button @click="selectedIds = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2">
                        Dismiss <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Table Content --}}
                <div class="relative min-h-[400px]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border-b border-indigo-100/50">
                                <tr>
                                    <th class="px-6 py-4 w-10">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === physicians.length && physicians.length > 0"
                                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer">
                                        </div>
                                    </th>
                                    <th @click="sort('name')" class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] cursor-pointer hover:text-indigo-600 transition-colors group border-b border-slate-50">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-stethoscope text-[10px]"></i>
                                            Specialty Name
                                            <i class="fas fa-sort text-[10px] opacity-20 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </th>
                                    <th @click="sort('is_active')" class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] cursor-pointer hover:text-indigo-600 transition-colors group border-b border-slate-50">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-toggle-on text-[10px]"></i>
                                            Status
                                            <i class="fas fa-sort text-[10px] opacity-20 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] text-right border-b border-slate-50">
                                        <div class="flex items-center justify-end gap-2">
                                            <i class="fas fa-tools text-[10px]"></i>
                                            Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                <template x-if="loading">
                                    <tr>
                                        <td colspan="5" class="py-24 text-center">
                                            <div class="inline-flex flex-col items-center gap-4">
                                                <div class="relative w-12 h-12">
                                                    <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
                                                    <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
                                                </div>
                                                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest animate-pulse">Syncing Network...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="!loading && physicians.length === 0">
                                    <tr>
                                        <td colspan="5" class="py-24 text-center text-slate-400">
                                            <i class="fas fa-stethoscope text-5xl mb-4 opacity-20"></i>
                                            <p class="text-sm font-bold tracking-tight">No specialties found matching your search</p>
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="sp in physicians" :key="sp.id">
                                    <tr class="hover:bg-indigo-50/30 transition-all duration-300 group">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" :value="sp.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200 group-hover:scale-110 transition-transform shadow-sm">
                                                    <i class="fas fa-stethoscope text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-slate-800 tracking-tight" x-text="sp.name"></div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <button @click="toggleStatus(sp)" 
                                                    class="relative inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest transition-all duration-300"
                                                    :class="sp.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="sp.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                                                <span x-text="sp.is_active ? 'Active' : 'Inactive'"></span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="openModal(sp)" class="w-8 h-8 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                    <i class="fas fa-pencil-alt text-[10px]"></i>
                                                </button>
                                                <button @click="confirmBulkAction('delete', sp)" class="w-8 h-8 flex items-center justify-center bg-white border border-rose-100 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Floating Bottom Pagination --}}
                    <div class="p-6 bg-gradient-to-t from-gray-50/80 to-transparent flex flex-col md:flex-row justify-between items-center gap-6 border-t border-gray-50 mt-auto">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-database text-indigo-300"></i>
                            Showing <span class="text-indigo-600" x-text="meta.from || 0"></span> 
                            to <span class="text-indigo-600" x-text="meta.to || 0"></span> 
                            of <span class="text-indigo-600" x-text="meta.total || 0"></span> entries
                        </div>
                        
                        <div class="flex items-center gap-1.5">
                            <template x-for="link in paginationLinks" :key="link.label">
                                <button @click="goToPage(link.url)" 
                                        :disabled="!link.url"
                                        class="min-w-[40px] h-10 px-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center border shadow-sm"
                                        :class="link.active ? 
                                                'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/30' : 
                                                'bg-white text-slate-500 border-indigo-100 hover:bg-indigo-50 hover:border-indigo-200 disabled:opacity-30 disabled:grayscale'"
                                        x-html="link.label">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Sticky Sidebar --}}
        <div x-show="showSidebar" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="lg:col-span-3 sticky top-8 lg:max-h-[calc(100vh-80px)] overflow-y-auto custom-scrollbar pr-1 group/sidebar">
            
            <div class="bg-white rounded-[2.5rem] p-8 text-slate-800 shadow-2xl relative overflow-hidden border border-slate-100">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-indigo-50 rounded-full blur-3xl opacity-50 italic"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 bg-blue-50 rounded-full blur-2xl opacity-50"></div>

                <div class="relative space-y-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:rotate-6 duration-300">
                                <i class="fas fa-filter text-indigo-600"></i>
                            </div>
                            <div>
                                <h3 class="font-black uppercase tracking-[0.2em] text-xs text-slate-800">Network Filters</h3>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Filter by field</p>
                            </div>
                        </div>
                        <button @click="showSidebar = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Search Input --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Specialty Search</label>
                        <div class="relative group">
                            <input x-model="filters.search" @input.debounce.400ms="fetchData()"
                                   type="text" placeholder="Search specialties..." 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-xs text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-400 transition-all outline-none font-bold">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Page Intelligence Section (Bento Box) --}}
                    <div class="bg-slate-50 rounded-[2.5rem] p-6 border border-slate-100 shadow-inner space-y-8">
                        {{-- Status Filter --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-toggle-on text-emerald-500"></i> Status State
                            </label>
                            <div class="grid grid-cols-3 gap-1 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
                                <button @click="filters.status = ''; fetchData()" 
                                    :class="filters.status === '' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:text-slate-700'"
                                    class="py-2.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                    All
                                </button>
                                <button @click="filters.status = 'active'; fetchData()" 
                                    :class="filters.status === 'active' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-700'"
                                    class="py-2.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                    Act
                                </button>
                                <button @click="filters.status = 'inactive'; fetchData()" 
                                    :class="filters.status === 'inactive' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-700'"
                                    class="py-2.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                    Off
                                </button>
                            </div>
                        </div>

                        {{-- Page Density --}}
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-ol text-indigo-500"></i> Page Density
                            </label>
                            <div class="grid grid-cols-3 gap-1 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
                                <template x-for="size in ['15', '25', '50']">
                                    <button @click="filters.per_page = size; fetchData()" 
                                        :class="filters.per_page === size ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-indigo-600'"
                                        class="py-2.5 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                        <span x-text="size"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <button @click="clearFilters()" class="w-full py-5 mt-8 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 border border-slate-100 hover:border-rose-100 flex items-center justify-center gap-3 active:scale-95 font-bold">
                        <i class="fas fa-broom"></i> Reset Registry
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Generic Confirmation Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-[70] overflow-y-auto px-4 py-6" x-transition.opacity style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showConfirmModal = false"></div>
            
            <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 text-center border border-slate-100">
                <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-6" :class="confirmConfig.type === 'danger' ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600'">
                    <i class="fas text-3xl" :class="confirmConfig.icon"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2" x-text="confirmConfig.title"></h3>
                <p class="text-xs font-bold text-slate-500 mb-8 px-4 uppercase tracking-wider leading-relaxed" x-text="confirmConfig.message"></p>
                
                <div class="flex items-center justify-center gap-3">
                    <button @click="showConfirmModal = false" class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-colors w-full cursor-pointer">Abort Action</button>
                    <button @click="executeConfirmedAction()" :disabled="confirming" class="px-5 py-3 text-white rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-md w-full flex items-center justify-center gap-2 cursor-pointer" :class="confirmConfig.type === 'danger' ? 'bg-gradient-to-r from-rose-500 to-rose-700 hover:from-rose-600 hover:to-rose-800 shadow-rose-500/30' : 'bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 shadow-blue-500/30'">
                        <i class="fas fa-spinner fa-spin" x-show="confirming"></i>
                        <span x-text="confirming ? 'Processing...' : confirmConfig.confirmText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         PHYSICIAN MODAL
    ═══════════════════════════════════════════════ --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
        <div @click.away="closeModal()" x-show="showModal" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden border border-gray-100">
            
            {{-- Modal Header --}}
            <div class="px-8 py-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-indigo-100/50 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-sm">
                        <i class="fas" :class="editing ? 'fa-user-edit' : 'fa-user-plus'"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-indigo-900 tracking-tight" x-text="editing ? 'Edit Medical Specialty' : 'Add Medical Specialty'"></h3>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Medical Referral Network</p>
                    </div>
                </div>
                <button @click="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-100 text-gray-400 hover:text-rose-600 hover:border-rose-100 transition-all shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="save()" class="p-8 space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Specialty Name</label>
                    <div class="relative group">
                        <input x-model="form.name" type="text" placeholder="e.g. Cardiology" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/50 transition-all outline-none">
                        <i class="fas fa-stethoscope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center justify-between p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 group-hover:rotate-12 transition-transform">
                            <i class="fas fa-power-off text-xs"></i>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-indigo-900">Active Status</span>
                    </div>
                    <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-500 shadow-inner" :class="form.is_active ? 'bg-indigo-600' : 'bg-slate-300'">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-xl transition-transform duration-500" :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                    <input type="hidden" x-model="form.is_active">
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="closeModal()" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-2xl text-xs font-black uppercase tracking-widest transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-indigo-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save" x-show="!saving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="editing ? 'Update Specialty' : 'Add Specialty'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function physicianRegistry() {
    const STORAGE_KEY = 'medical_specialties_filters_v2';

    return {
        physicians: [],
        stats: { total: 0, active: 0, inactive: 0, specialties: 0 },
        meta: {},
        paginationLinks: [],
        loading: false,
        showSidebar: true,
        showModal: false,
        editing: null,
        saving: false,

        selectedIds: [],
        showConfirmModal: false,
        confirming: false,
        confirmConfig: {
            title: '',
            message: '',
            icon: '',
            confirmText: '',
            type: 'primary',
            action: null,
            payload: null
        },

        filters: JSON.parse(localStorage.getItem(STORAGE_KEY) || JSON.stringify({
            search: '', status: '', per_page: '15', sort_by: 'name', sort_dir: 'asc'
        })),

        form: { name: '', is_active: true },

        init() {
            this.fetchStats();
            this.fetchData();
            this.$watch('filters', (v) => localStorage.setItem(STORAGE_KEY, JSON.stringify(v)), { deep: true });
        },

        async fetchStats() {
            try {
                const r = await fetch("{{ route('doctor.setup.physicians.stats') }}");
                this.stats = await r.json();
            } catch (e) { console.error('Stats fetch failed', e); }
        },

        async fetchData(url = null) {
            this.loading = true;
            this.selectedIds = [];
            const params = new URLSearchParams(this.filters);
            const endpoint = url || `{{ route('doctor.setup.physicians.data') }}?${params}`;
            
            try {
                const r = await fetch(endpoint);
                const data = await r.json();
                this.physicians = data.data;
                this.meta = { total: data.total, from: data.from, to: data.to };
                this.paginationLinks = data.links;
            } catch (e) { 
                window.showError('Registry sync failure');
            }
            this.loading = false;
        },

        sort(field) {
            if (this.filters.sort_by === field) {
                this.filters.sort_dir = this.filters.sort_dir === 'asc' ? 'desc' : 'asc';
            } else {
                this.filters.sort_by = field;
                this.filters.sort_dir = 'asc';
            }
            this.fetchData();
        },

        goToPage(url) {
            if (url) this.fetchData(url);
        },

        clearFilters() {
            this.filters = { search: '', status: '', per_page: '15', sort_by: 'name', sort_dir: 'asc' };
            this.fetchData();
        },

        openModal(sp = null) {
            this.editing = sp;
            if (sp) {
                this.form = { 
                    name: sp.name, 
                    is_active: !!sp.is_active 
                };
            } else {
                this.form = { name: '', is_active: true };
            }
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editing = null;
        },

        async save() {
            this.saving = true;
            const isEdit = !!this.editing;
            const url = isEdit 
                ? `{{ route('doctor.setup.physicians.update', ['externalSpecialist' => ':id']) }}`.replace(':id', this.editing.id)
                : `{{ route('doctor.setup.physicians.store') }}`;
            
            try {
                const r = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.form)
                });
                const data = await r.json();
                if (data.success) {
                    this.closeModal();
                    this.fetchData();
                    this.fetchStats();
                    window.showSuccess(data.message || 'Specialty updated');
                }
            } catch (e) { 
                window.showError('Neural uplink failure during save');
            }
            this.saving = false;
        },

        async toggleStatus(sp) {
            try {
                const url = `{{ route('doctor.setup.physicians.toggle', ['externalSpecialist' => ':id']) }}`.replace(':id', sp.id);
                const r = await fetch(url, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    sp.is_active = data.specialist.is_active;
                    this.fetchStats();
                    window.showSuccess(`Specialty "${sp.name}" is now ${sp.is_active ? 'Active' : 'Archived'}`);
                }
            } catch (e) { window.showError('Toggle sequence interrupted'); }
        },

        // Selection Helpers
        toggleAll(e) {
            if (e.target.checked) {
                this.selectedIds = this.physicians.map(p => p.id);
            } else {
                this.selectedIds = [];
            }
        },

        // Confirmation Modal Logic
        confirmBulkAction(type, singleItem = null) {
            const count = singleItem ? 1 : this.selectedIds.length;
            
            if (type === 'delete') {
                this.confirmConfig = {
                    title: singleItem ? 'Purge Specialty?' : 'Purge Registry Nodes?',
                    message: singleItem 
                        ? `Permanently remove "${singleItem.name}" from medical specialty registry?`
                        : `Identify and remove ${count} specialties from the global database? Action is irreversible.`,
                    icon: 'fa-trash-alt',
                    confirmText: 'Execute Purge',
                    type: 'danger',
                    action: 'bulkDestroy',
                    payload: singleItem ? [singleItem.id] : this.selectedIds
                };
            } else {
                const active = type === 'activate';
                this.confirmConfig = {
                    title: active ? 'Re-engage Specialties?' : 'De-optimize Registry?',
                    message: `Set ${count} specialties to ${active ? 'Active' : 'Archived'} status?`,
                    icon: active ? 'fa-bolt' : 'fa-power-off',
                    confirmText: active ? 'Resume Access' : 'Suspend Specialties',
                    type: 'primary',
                    action: 'bulkStatus',
                    payload: {
                        ids: singleItem ? [singleItem.id] : this.selectedIds,
                        active: active
                    }
                };
            }
            this.showConfirmModal = true;
        },

        async executeConfirmedAction() {
            this.confirming = true;
            try {
                if (this.confirmConfig.action === 'bulkStatus') {
                    await this.executeBulkStatus();
                } else if (this.confirmConfig.action === 'bulkDestroy') {
                    await this.executeBulkDestroy();
                }
            } finally {
                this.confirming = false;
                this.showConfirmModal = false;
            }
        },

        async executeBulkStatus() {
            const { ids, active } = this.confirmConfig.payload;
            try {
                const r = await fetch("{{ route('doctor.setup.physicians.bulk-status') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids, is_active: active })
                });
                const data = await r.json();
                if (data.success) {
                    window.showSuccess(data.message);
                    this.fetchData();
                    this.fetchStats();
                    this.selectedIds = [];
                }
            } catch (e) { window.showError('Bulk status update failed'); }
        },

        async executeBulkDestroy() {
            const ids = this.confirmConfig.payload;
            try {
                const r = await fetch("{{ route('doctor.setup.physicians.bulk-destroy') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ ids })
                });
                const data = await r.json();
                if (data.success) {
                    window.showSuccess(data.message);
                    this.fetchData();
                    this.fetchStats();
                    this.selectedIds = [];
                }
            } catch (e) { window.showError('Bulk purge failed'); }
        }
    };
}
</script>
@endpush
