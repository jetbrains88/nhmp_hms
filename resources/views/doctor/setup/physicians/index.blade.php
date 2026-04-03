@extends('layouts.app')

@section('title', 'Physician Registry — Doctor Setup')
@section('page-title', 'Physician Registry')
@section('breadcrumb', 'Doctor Setup / Physicians')

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
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Physician Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS
    ═══════════════════════════════════════════════ --}}
    <div class="flex overflow-x-auto pb-6 gap-6 gap-y-10 mt-4 no-scrollbar custom-scrollbar">
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg shadow-indigo-500/10 border border-indigo-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-400 shadow-xl shadow-indigo-900/20 border border-indigo-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-md text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-indigo-500 uppercase">Total Physicians</p>
                <h4 class="text-3xl font-bold text-indigo-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-indigo-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span class="text-[10px] text-indigo-700 font-bold uppercase tracking-tight">Referral Network</span>
                </div>
            </div>
        </div>

        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'active'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-xl shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active Contacts</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Available for Referrals</span>
                </div>
            </div>
        </div>

        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="viewMode = 'list'">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-xl shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-stethoscope text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-amber-600 uppercase">Specialties</p>
                <h4 class="text-3xl font-bold text-amber-700 drop-shadow-sm font-mono" x-text="stats.specialties">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-amber-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] text-amber-700 font-bold uppercase tracking-tight">Unique Medical Fields</span>
                </div>
            </div>
        </div>

        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl shadow-lg shadow-slate-500/10 border border-slate-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'inactive'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-slate-600 to-gray-400 shadow-xl shadow-slate-900/20 border border-slate-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-toggle-off text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-slate-500 uppercase">Inactive</p>
                <h4 class="text-3xl font-bold text-slate-700 drop-shadow-sm font-mono" x-text="stats.inactive">0</h4>
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
                                    Physician Registry
                                    <span class="text-lg font-normal text-gray-600">
                                        (<span x-text="meta.total"></span> records)
                                    </span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Specialized Medical Care referral network</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 items-center">
                            {{-- Rows Selector --}}
                            <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                                <select x-model="filters.per_page" @change="fetchData()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <button @click="openModal()"
                                class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-95 group">
                                <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                                Add Specialist
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

                {{-- Table Content --}}
                <div class="relative min-h-[400px]">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border-b border-indigo-100/50">
                                <tr>
                                    <th @click="sort('name')" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest cursor-pointer hover:text-indigo-600 transition-colors group">
                                        <div class="flex items-center gap-2">
                                            Specialist Name
                                            <i class="fas fa-sort text-[10px] text-slate-300 group-hover:text-indigo-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest cursor-pointer hover:text-indigo-600 transition-colors group">
                                        <div class="flex items-center gap-2">
                                            Medical Specialty
                                            <i class="fas fa-sort text-[10px] text-slate-300 group-hover:text-indigo-400"></i>
                                        </div>
                                    </th>
                                    <th @click="sort('is_active')" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest cursor-pointer hover:text-indigo-600 transition-colors group">
                                        <div class="flex items-center gap-2">
                                            Status
                                            <i class="fas fa-sort text-[10px] text-slate-300 group-hover:text-indigo-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
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
                                            <i class="fas fa-user-md text-5xl mb-4 opacity-20"></i>
                                            <p class="text-sm font-bold tracking-tight">No specialists found matching your search</p>
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="sp in physicians" :key="sp.id">
                                    <tr class="hover:bg-indigo-50/30 transition-all duration-300 group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-indigo-600 border border-indigo-200 group-hover:scale-110 transition-transform shadow-sm">
                                                    <i class="fas fa-user-md text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-slate-800 tracking-tight" x-text="sp.name"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-700 border border-blue-100" x-text="sp.medical_specialty ? sp.medical_specialty.name : sp.specialty"></span>
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
                                                <button @click="deletePhysician(sp)" class="w-8 h-8 flex items-center justify-center bg-white border border-rose-100 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm">
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
             class="lg:col-span-3 sticky top-8 lg:max-h-[calc(100vh-80px)] overflow-y-auto custom-scrollbar pr-1">
            
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
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Specialist Search</label>
                        <div class="relative group">
                            <input x-model="filters.search" @input.debounce.400ms="fetchData()"
                                   type="text" placeholder="Search specialists..." 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-xs text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-400 transition-all outline-none font-bold">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Specialty Filter --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Focus Areas</label>
                        <div class="space-y-2">
                            <select x-model="filters.medical_specialty_id" @change="fetchData()" 
                                    class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-4 text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-400 transition-all outline-none appearance-none">
                                <option value="">All Specialties</option>
                                @foreach($specialtiesList as $spec)
                                    <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Network Status</label>
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="status in ['active', 'inactive']">
                                <button @click="filters.status = (filters.status === status ? '' : status); fetchData()"
                                        class="flex items-center justify-between px-6 py-4 rounded-3xl border transition-all duration-300 group shadow-sm active:scale-95"
                                        :class="filters.status === status ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200'">
                                    <span class="text-[11px] font-black uppercase tracking-widest" x-text="status"></span>
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-transform group-hover:scale-110"
                                         :class="filters.status === status ? 'border-white/50' : 'border-slate-200'">
                                        <div x-show="filters.status === status" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button @click="clearFilters()" class="w-full py-5 mt-8 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 border border-slate-100 hover:border-rose-100 flex items-center justify-center gap-3 active:scale-95 font-bold">
                        <i class="fas fa-broom"></i> Reset Registry
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
                        <h3 class="text-xl font-black text-indigo-900 tracking-tight" x-text="editing ? 'Edit Specialist' : 'Register Specialist'"></h3>
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
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Full Doctor Name</label>
                    <div class="relative group">
                        <input x-model="form.name" type="text" placeholder="Dr. " required class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/50 transition-all outline-none">
                        <i class="fas fa-stethoscope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Specialty -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Medical Specialty</label>
                    <div class="relative group">
                        <select x-model="form.medical_specialty_id" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/50 transition-all outline-none appearance-none">
                            <option value="">Select Specialty...</option>
                            @foreach($specialtiesList as $spec)
                                <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-award absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                </div>



                <!-- Active Toggle -->
                <div class="flex items-center justify-between p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50 group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 group-hover:rotate-12 transition-transform">
                            <i class="fas fa-power-off text-xs"></i>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest text-indigo-900">Affiliation Status</span>
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
                        <span x-text="editing ? 'Update Specialist' : 'Register Specialist'"></span>
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
    const STORAGE_KEY = 'physician_registry_filters_v3';

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

        filters: JSON.parse(localStorage.getItem(STORAGE_KEY) || JSON.stringify({
            search: '', status: '', medical_specialty_id: '', per_page: '15', sort_by: 'name', sort_dir: 'asc'
        })),

        form: { name: '', medical_specialty_id: '', is_active: true },

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
            const params = new URLSearchParams(this.filters);
            const endpoint = url || `{{ route('doctor.setup.physicians.data') }}?${params}`;
            
            try {
                const r = await fetch(endpoint);
                const data = await r.json();
                this.physicians = data.data;
                this.meta = { total: data.total, from: data.from, to: data.to };
                this.paginationLinks = data.links;
            } catch (e) { console.error('Data fetch failed', e); }
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
            this.filters = { search: '', status: '', medical_specialty_id: '', per_page: '15', sort_by: 'name', sort_dir: 'asc' };
            this.fetchData();
        },

        openModal(sp = null) {
            this.editing = sp;
            if (sp) {
                this.form = { 
                    name: sp.name, 
                    medical_specialty_id: sp.medical_specialty_id, 
                    is_active: !!sp.is_active 
                };
            } else {
                this.form = { name: '', medical_specialty_id: '', is_active: true };
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
                }
            } catch (e) { console.error('Save failed', e); }
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
                }
            } catch (e) { console.error('Toggle failed', e); }
        },

        async deletePhysician(sp) {
            if (!confirm(`Permanently remove ${sp.name} from the registry?`)) return;
            try {
                const url = `{{ route('doctor.setup.physicians.destroy', ['externalSpecialist' => ':id']) }}`.replace(':id', sp.id);
                const r = await fetch(url, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    this.fetchData();
                    this.fetchStats();
                }
            } catch (e) { console.error('Delete failed', e); }
        }
    };
}
</script>
@endpush
