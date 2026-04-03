@extends('layouts.app')

@section('title', 'Prescriptions Dashboard - NHMP HMS')
@section('page-title', 'Prescriptions Dashboard')
@section('breadcrumb', 'Pharmacy / Prescriptions')

@section('content')
<div x-data="pharmacyPrescriptions()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-emerald-500 to-teal-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(16,185,129,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(16,185,129,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-emerald-400/50 group cursor-pointer"
        title="Open Prescription Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-emerald-50">Sort & Filter</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">
        <!-- Pending Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/20 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'pending'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-lg shadow-amber-900/30 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-hourglass-half text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-orange-500 uppercase">Pending Review</p>
                <h4 class="text-3xl font-bold text-orange-700 drop-shadow-sm font-mono" x-text="stats.pending ?? '0'">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-amber-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-[10px] text-amber-700 font-bold uppercase tracking-tight">Awaiting Dispense</span>
                </div>
            </div>
        </div>

        <!-- Partial Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'partially_dispensed'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-pills text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Partially Dispensed</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.partially_dispensed ?? '0'">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2 text-sky-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Requires more medicine</span>
                </div>
            </div>
        </div>

        <!-- Output Today Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filters.status = 'completed'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Completed Today</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.dispensed_today ?? '0'">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2 pb-1">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Fully Dispensed</span>
                </div>
            </div>
        </div>

        <!-- Patients Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-injured text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-500 uppercase">Total Load</p>
                <h4 class="text-3xl font-bold text-purple-700 drop-shadow-sm font-mono" x-text="patientGroups.length">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Active Patients listed</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Patient Groups Container --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">
            
                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 border-b border-emerald-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-emerald-100 shadow-sm transition-transform hover:scale-105 duration-300">
                                <i class="fas fa-clipboard-list text-2xl text-emerald-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-700 tracking-tight flex items-center gap-3">
                                    Pending Dispensations
                                    <span class="text-lg font-normal text-gray-500">
                                        (<span x-text="totalPrescriptions"></span> medicines)
                                    </span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Grouped by patient — dispense efficiently in one workflow.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="flex items-center gap-2 bg-white border border-emerald-100 rounded-xl px-3 py-1.5 shadow-sm">
                                <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                                <select x-model="perPage" @change="currentPage = 1; window.scrollTo({ top: 0, behavior: 'smooth' })" class="bg-transparent text-emerald-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            
                            <button @click="showSidebar = !showSidebar"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-emerald-100 text-emerald-600 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm"
                                :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                            </button>
                            
                            <button @click="fetchData()" 
                                class="w-10 h-10 flex items-center justify-center bg-white border border-emerald-100 text-emerald-600 rounded-xl hover:bg-emerald-50 transition-colors shadow-sm"
                                title="Refresh">
                                <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- View Content - Grouped Lists --}}
                <div class="relative min-h-[400px] p-6 bg-gray-50/50">
                    
                    {{-- Loading State --}}
                    <template x-if="loading">
                        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                            <i class="fas fa-spinner fa-spin text-3xl mb-4 text-emerald-500"></i>
                            <p class="text-sm font-bold uppercase tracking-widest">Loading Records...</p>
                        </div>
                    </template>
                    
                    {{-- Empty State --}}
                    <template x-if="!loading && patientGroups.length === 0">
                        <div class="py-20 text-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100">
                            <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-200 mx-auto mb-6">
                                <i class="fas fa-prescription text-4xl"></i>
                            </div>
                            <h4 class="text-xl font-black text-slate-400">No prescriptions found</h4>
                            <p class="text-slate-400 mt-2 font-medium">All prescriptions have been dispensed, or no match for your filters.</p>
                        </div>
                    </template>

                    {{-- Patient Group Cards --}}
                    <div x-show="!loading" class="space-y-6">
                        <template x-for="group in paginatedGroups" :key="group.patient.id">
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300">

                                {{-- Patient Header --}}
                                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-lg flex-shrink-0 shadow-inner"
                                            x-text="group.patient.name?.charAt(0) ?? 'P'">
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 text-lg" x-text="group.patient.name"></p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400" x-text="'EMRN: ' + group.patient.emrn"></span>
                                                <span class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-500 rounded font-black uppercase tracking-widest" x-text="group.patient.gender"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button @click="openViewModal(group)" class="px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 border border-blue-100 hover:bg-blue-100 hover:border-blue-200 rounded-xl transition-all flex justify-center items-center gap-1.5 shadow-sm">
                                            <i class="fas fa-eye mb-px"></i> Log / View
                                        </button>
                                        <span class="text-[10px] uppercase tracking-widest bg-emerald-50 text-emerald-700 font-black px-3 py-1.5 rounded-xl border border-emerald-100 shadow-sm"
                                            x-text="group.prescriptions.length + ' prescribed'"></span>
                                    </div>
                                </div>

                                {{-- Prescription Rows --}}
                                <div class="divide-y divide-slate-50">
                                    <template x-for="rx in group.prescriptions" :key="rx.id">
                                        <div class="p-6 hover:bg-slate-50/50 transition-colors">
                                            <div class="flex flex-col lg:flex-row lg:items-center gap-6">

                                                {{-- Medicine Info --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start gap-4">
                                                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center shadow-sm border border-slate-100"
                                                            :class="rx.stock_ok ? 'bg-emerald-50' : 'bg-red-50'">
                                                            <i class="fas fa-capsules text-sm" :class="rx.stock_ok ? 'text-emerald-600' : 'text-red-500'"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-3 flex-wrap">
                                                                <p class="font-black text-slate-800 text-base" x-text="rx.medicine_name"></p>
                                                                <span x-show="rx.generic_name" class="text-[10px] uppercase font-bold text-slate-400 tracking-widest border border-slate-200 px-1.5 rounded" x-text="'(' + rx.generic_name + ')'"></span>
                                                                
                                                                {{-- Status Badge --}}
                                                                <span class="text-[9px] px-2 py-1 rounded font-black uppercase tracking-widest"
                                                                    :class="{
                                                                        'bg-amber-100 text-amber-700 border border-amber-200': rx.status === 'pending',
                                                                        'bg-blue-100 text-blue-700 border border-blue-200': rx.status === 'partially_dispensed',
                                                                        'bg-emerald-100 text-emerald-700 border border-emerald-200': rx.status === 'completed',
                                                                        'bg-slate-100 text-slate-600 border border-slate-200': rx.status === 'cancelled',
                                                                    }"
                                                                    x-text="rx.status === 'partially_dispensed' ? 'PARTIAL' : rx.status?.toUpperCase()">
                                                                </span>
                                                            </div>

                                                            {{-- Dose / Frequency --}}
                                                            <div class="flex flex-wrap gap-x-5 gap-y-1 mt-2.5 text-xs text-slate-500 font-medium">
                                                                <span x-show="rx.morning || rx.evening || rx.night">
                                                                    <i class="fas fa-clock mr-1 text-emerald-400"></i>
                                                                    <span class="font-bold text-slate-700" x-text="[rx.morning ? rx.morning+'M' : '', rx.evening ? rx.evening+'E' : '', rx.night ? rx.night+'N' : ''].filter(Boolean).join(' - ')"></span>
                                                                </span>
                                                                <span x-show="rx.days">
                                                                    <i class="fas fa-calendar-alt mr-1 text-blue-400"></i>
                                                                    <span class="font-bold text-slate-700" x-text="rx.days + (isNaN(rx.days) ? '' : ' day(s)')"></span>
                                                                </span>
                                                                <span x-show="rx.instructions" class="text-slate-400 italic">"<span x-text="rx.instructions"></span>"</span>
                                                                <span class="text-slate-400">
                                                                    <i class="fas fa-user-md mr-1 text-slate-300"></i>
                                                                    <span class="font-semibold" x-text="rx.doctor_name"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-6">
                                                    {{-- Qty Tracking --}}
                                                    <div class="flex-shrink-0 grid grid-cols-2 gap-3 text-center min-w-[200px]">
                                                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 flex flex-col justify-center">
                                                            <p class="text-[9px] font-black tracking-widest uppercase text-slate-400 mb-0.5">Required</p>
                                                            <p class="text-lg font-bold font-mono text-slate-700" x-text="rx.quantity"></p>
                                                        </div>
                                                        <div :class="rx.remaining_qty > 0 ? 'bg-amber-50 border border-amber-100' : 'bg-emerald-50 border border-emerald-100'" class="rounded-xl p-2.5 flex flex-col justify-center shadow-inner">
                                                            <p class="text-[9px] font-black tracking-widest uppercase mb-0.5" :class="rx.remaining_qty > 0 ? 'text-amber-500' : 'text-emerald-500'">Missing</p>
                                                            <p class="text-lg font-bold font-mono" :class="rx.remaining_qty > 0 ? 'text-amber-700' : 'text-emerald-700'" x-text="rx.remaining_qty"></p>
                                                        </div>
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="flex-shrink-0 flex flex-col gap-2 w-[140px]" x-show="rx.status !== 'completed' && rx.status !== 'cancelled'">
                                                        <button @click="openDispenseModal(rx, group.patient)"
                                                            class="w-full px-4 py-2.5 text-xs font-black text-white uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm"
                                                            :class="rx.stock > 0 ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:shadow-lg hover:shadow-emerald-500/30' : 'bg-slate-300 cursor-not-allowed'"
                                                            :disabled="rx.stock <= 0">
                                                            <i class="fas fa-pills mb-px"></i>
                                                            <span x-text="rx.status === 'partially_dispensed' ? 'More' : 'Dispense'"></span>
                                                        </button>
                                                    </div>

                                                    {{-- Completed badge --}}
                                                    <div class="flex-shrink-0 flex items-center justify-center w-[140px] h-full" x-show="rx.status === 'completed' || rx.status === 'cancelled'">
                                                        <div class="flex flex-col items-center justify-center bg-emerald-50 w-full h-full rounded-xl border border-emerald-100 py-3">
                                                            <i :class="rx.status === 'completed' ? 'fas fa-check-circle text-emerald-500 text-xl' : 'fas fa-ban text-slate-400 text-xl'"></i>
                                                            <span class="text-[9px] font-black uppercase tracking-widest mt-1" :class="rx.status === 'completed' ? 'text-emerald-700' : 'text-slate-500'" x-text="rx.status === 'completed' ? 'Completed' : 'Cancelled'"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>

        {{-- Right Column - Sticky Sidebar Filters --}}
        <div x-show="showSidebar" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-8"
             class="lg:col-span-3 lg:sticky lg:top-8 lg:max-h-[calc(100vh-80px)] lg:overflow-y-auto scrollbar-hide pb-2" style="scrollbar-width: none;">
            
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-filter"></i> Refine Views
                    </h3>
                    <button @click="showSidebar = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-5">
                    {{-- Search Filter --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Universal Patient Search</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input x-model="filters.search" @input.debounce.500ms="fetchData()" type="text" placeholder="EMRN, Name..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm font-medium transition-all focus:ring-4 focus:ring-emerald-500/10 placeholder:text-slate-400">
                        </div>
                    </div>

                    {{-- Status Policy Filter --}}
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Task State</label>
                        <div class="relative">
                            <i class="fas fa-tasks absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <select x-model="filters.status" @change="fetchData()" 
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 rounded-xl text-sm font-bold text-slate-600 transition-all focus:ring-4 focus:ring-emerald-500/10 appearance-none">
                                <option value="">Pending + Partial</option>
                                <option value="pending">Pending Only</option>
                                <option value="partially_dispensed">Partially Dispensed</option>
                                <option value="completed">Completed Status</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>
                    
                    {{-- Clear Filters --}}
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button @click="clearFilters()" class="text-[10px] font-black text-rose-500 uppercase tracking-widest border border-rose-200 bg-rose-50 hover:bg-rose-500 hover:text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                            Reset View <i class="fas fa-ban"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ─────────────────────── Dispense Modal ─────────────────────── --}}
<div x-data="dispenseModal()"
    x-show="open"
    @open-dispense-modal.window="handleOpen($event.detail)"
    @dispense-success.window="handleSuccess()"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
    @click.self="close()"
    x-cloak>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-emerald-100">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 shadow-inner">
            <div>
                <h3 class="text-xl font-black text-white truncate max-w-[300px]" x-text="rx ? rx.medicine_name : ''"></h3>
                <p class="text-emerald-100 text-[10px] uppercase font-bold tracking-widest mt-1" x-text="patient ? patient.name + ' — EMRN: ' + patient.emrn : ''"></p>
            </div>
            <button @click="close()" class="text-white/70 hover:text-white hover:rotate-90 w-10 h-10 flex items-center justify-center rounded-xl bg-black/10 hover:bg-black/20 transition-all border border-white/10">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="p-6 space-y-6" x-show="rx">

            {{-- Summary Row --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3 text-center shadow-sm">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Required</p>
                    <p class="text-2xl font-bold font-mono text-indigo-700 mt-1" x-text="rx?.quantity"></p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-center shadow-sm">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Dispensed</p>
                    <p class="text-2xl font-bold font-mono text-blue-700 mt-1" x-text="rx?.dispensed_qty"></p>
                </div>
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 text-center shadow-sm">
                    <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Missing</p>
                    <p class="text-2xl font-bold font-mono text-rose-700 mt-1" x-text="rx?.remaining_qty"></p>
                </div>
            </div>

            {{-- Alternative Medicine --}}
            <div x-show="rx?.generic_name">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">
                    Select Product Dispensing
                    <span x-show="rx?.generic_name" class="font-normal text-emerald-500 ml-1 bg-emerald-50 px-1 py-0.5 rounded" x-text="'(generic matches: ' + rx?.generic_name + ')'"></span>
                </label>
                <div class="relative">
                    <i class="fas fa-exchange-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <select x-model="form.alternative_medicine_id" id="modal-alternative-medicine"
                        class="w-full pl-10 pr-4 py-3 text-sm font-bold text-slate-600 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-400 outline-none bg-slate-50 transition-all appearance-none">
                        <option value="">Original Product: <span x-text="rx?.medicine_name"></span></option>
                        <template x-for="alt in alternatives" :key="alt.id">
                            <option :value="alt.id"
                                x-text="'Alt: ' + alt.name + (alt.brand ? ' (' + alt.brand + ')' : '') + ' — Available: ' + alt.stock">
                            </option>
                        </template>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                </div>
                <p x-show="alternatives.length === 0" class="text-[10px] text-slate-400 mt-1 font-medium"><i class="fas fa-info-circle mr-1"></i> No exact generic replacements detected in warehouse.</p>
                <p x-show="form.alternative_medicine_id" class="text-[10px] font-black text-amber-600 mt-1.5 flex items-center gap-1 bg-amber-50 px-2 py-1 rounded inline-flex border border-amber-100 uppercase tracking-widest">
                    <i class="fas fa-exclamation-triangle"></i> Substitution Alert will fire.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Quantity --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5" for="modal-quantity">
                        Outflow Count
                        <span class="text-rose-400 font-normal ml-1 border pl-1 rounded border-rose-200 bg-rose-50" x-text="'(Max limit: ' + (rx?.remaining_qty ?? 0) + ')'"></span>
                    </label>
                    <input x-model.number="form.quantity_dispensed" type="number" id="modal-quantity"
                        :min="1" :max="rx?.remaining_qty ?? 0"
                        class="w-full px-4 py-3 text-xl font-mono font-black text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-400 outline-none transition-all placeholder:text-slate-300" required>
                    <p x-show="form.quantity_dispensed > (rx?.remaining_qty ?? 0)" class="text-[10px] font-bold uppercase tracking-widest text-red-500 mt-1.5 bg-red-50 px-1 py-0.5 rounded text-center">
                        Exceeds prescription ceiling.
                    </p>
                </div>

                {{-- Batch Selection --}}
                <div x-show="rx?.batches?.length > 0">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5" for="modal-batch">Assign Batch</label>
                    <div class="relative">
                        <i class="fas fa-boxes absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <select x-model="form.medicine_batch_id" id="modal-batch"
                            class="w-full pl-10 pr-4 py-3 text-xs border border-slate-200 rounded-xl text-slate-600 font-bold focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-400 outline-none bg-slate-50 appearance-none transition-all h-[54px]">
                            <option value="">Auto (FEFO Rules)</option>
                            <template x-for="b in (rx?.batches ?? [])" :key="b.id">
                                <option :value="b.id"
                                    x-text="'Batch ' + b.batch_number + ' (Exp: ' + b.expiry + ') - Rem: ' + b.remaining">
                                </option>
                            </template>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]"></i>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5" for="modal-notes">Pharmacist Log <span class="text-slate-400 font-normal lowercase">(optional)</span></label>
                <div class="relative">
                    <i class="fas fa-comment-medical absolute left-3.5 top-3.5 text-slate-400"></i>
                    <textarea x-model="form.notes" id="modal-notes" rows="2"
                        placeholder="Log any clinical alterations..."
                        class="w-full pl-10 pr-4 py-3 text-sm font-medium border border-slate-200 bg-slate-50 rounded-xl focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-400 outline-none resize-none transition-all placeholder:text-slate-400 text-slate-700"></textarea>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="error" x-transition>
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm text-red-600">
                    <div class="bg-white rounded-full p-1"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="text-xs font-bold" x-text="error"></p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 pt-4 border-t border-slate-100">
                <button @click="close()" type="button"
                    class="flex-1 px-4 py-4 text-[10px] uppercase tracking-widest font-black text-slate-500 bg-slate-50 hover:bg-slate-100 hover:text-slate-700 border border-slate-200 rounded-xl transition-all shadow-sm">
                    Abort <i class="fas fa-times ml-1"></i>
                </button>
                <button @click="submit()" :disabled="submitting || form.quantity_dispensed > (rx?.remaining_qty ?? 0) || form.quantity_dispensed < 1"
                    class="flex-[2] px-4 py-4 text-xs font-black uppercase tracking-widest text-white bg-gradient-to-r from-emerald-500 to-teal-600 border border-emerald-500 rounded-xl hover:shadow-xl hover:shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2 group">
                    <i class="fas fa-spinner fa-spin text-sm" x-show="submitting"></i>
                    <i class="fas fa-check-double text-sm group-hover:scale-125 transition-transform" x-show="!submitting"></i>
                    <span x-text="submitting ? 'Executing...' : 'Sign & Dispense'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- LOG/VIEW MODAL --}}
<div x-data="rxViewModal()" 
     @open-rx-view.window="handleOpen($event.detail)"
     x-show="open" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" 
             @click="close()"></div>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200 transform" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative inline-block w-full max-w-4xl p-8 overflow-hidden text-left sm:my-8 bg-white border border-slate-100 rounded-[2rem] shadow-2xl shadow-indigo-500/10">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-file-medical text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800" x-text="group?.patient?.name + ' - Prescription Log'"></h2>
                        <p class="text-xs font-bold text-slate-400 mt-0.5 tracking-widest uppercase" x-text="'EMRN: ' + group?.patient?.emrn"></p>
                    </div>
                </div>
                <button @click="close()" class="w-10 h-10 flex flex-col items-center justify-center bg-slate-50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 rounded-xl transition-colors border border-slate-200 hover:border-rose-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">
                            <th class="px-4 py-3">Medicine</th>
                            <th class="px-4 py-3 text-center">Qty Required</th>
                            <th class="px-4 py-3 text-center">Dispensed</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                        <template x-for="rx in group?.prescriptions || []" :key="rx.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-4 min-w-[200px]">
                                    <span x-text="rx.medicine_name" class="block text-sm"></span>
                                    <span x-show="rx.generic_name" x-text="'(' + rx.generic_name + ')'" class="text-[10px] text-slate-400 uppercase tracking-widest"></span>
                                </td>
                                <td class="px-4 py-4 text-center font-mono text-slate-500" x-text="rx.quantity"></td>
                                <td class="px-4 py-4 text-center font-mono text-indigo-600" x-text="rx.dispensed_qty"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="text-[9px] px-2 py-1 rounded font-black uppercase tracking-widest"
                                        :class="{
                                            'bg-amber-100 text-amber-700': rx.status === 'pending',
                                            'bg-blue-100 text-blue-700': rx.status === 'partially_dispensed',
                                            'bg-emerald-100 text-emerald-700': rx.status === 'completed',
                                            'bg-slate-100 text-slate-600': rx.status === 'cancelled',
                                        }"
                                        x-text="rx.status">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function pharmacyPrescriptions() {
    const STORAGE_KEY = 'pharmacy_rx_filters_new';

    return {
        showSidebar: false,
        loading: false,
        patientGroups: [],
        stats: {
            pending: 0,
            partially_dispensed: 0,
            dispensed_today: 0
        },

        filters: JSON.parse(localStorage.getItem(STORAGE_KEY) || JSON.stringify({
            search: '', status: '',
        })),


        perPage: 15,
        currentPage: 1,

        get paginatedGroups() {
            let start = (this.currentPage - 1) * this.perPage;
            let end = start + parseInt(this.perPage);
            return this.patientGroups.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.patientGroups.length / this.perPage) || 1;
        },

        changePage(p) {
            if (p < 1 || p > this.totalPages) return;
            this.currentPage = p;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        getPageRange() {
            let current = this.currentPage;
            let last = this.totalPages;
            let delta = 2;
            let left = current - delta;
            let right = current + delta + 1;
            let range = [];
            let rangeWithDots = [];
            let l;

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= left && i < right)) {
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

        get totalPrescriptions() {
            return this.patientGroups.reduce((sum, g) => sum + g.prescriptions.length, 0);
        },

        init() {
            this.fetchData();
            this.$watch('filters', (v) => localStorage.setItem(STORAGE_KEY, JSON.stringify(v)), { deep: true });
        },

        refresh() { this.fetchData(); },

        async fetchData() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    status: this.filters.status,
                });
                
                const r = await fetch(`{{ route('pharmacy.prescriptions.index') }}?${params}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await r.json();
                this.patientGroups = data.patients ?? [];
                this.stats         = data.stats ?? {};
            } catch (err) {
               console.error("Prescriptions load error: ", err); 
            } finally {
                this.loading = false;
            }
        },

        clearFilters() {
            this.filters = { search: '', status: '' };
            this.fetchData();
        },

        openViewModal(group) {
            window.dispatchEvent(new CustomEvent('open-rx-view', { detail: { group } }));
        },
        openDispenseModal(rx, patient) {
            window.dispatchEvent(new CustomEvent('open-dispense-modal', { detail: { rx, patient } }));
        },
    };
}

function dispenseModal() {
    return {
        open: false,
        rx: null,
        patient: null,
        alternatives: [],
        submitting: false,
        error: '',

        form: {
            quantity_dispensed: 0,
            alternative_medicine_id: '',
            medicine_batch_id: '',
            notes: '',
        },

        async handleOpen({ rx, patient }) {
            this.rx = rx;
            this.patient = patient;
            this.error = '';
            this.form = {
                quantity_dispensed: rx.remaining_qty,
                alternative_medicine_id: '',
                medicine_batch_id: '',
                notes: '',
            };
            this.alternatives = [];
            this.open = true;

            // Fetch alternatives asynchronously
            if (rx.generic_name) {
                const r = await fetch(`/pharmacy/prescriptions/${rx.id}/alternatives`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await r.json();
                this.alternatives = data.alternatives ?? [];
            }
        },

        close() {
            this.open = false;
            this.rx = null;
            this.patient = null;
            this.alternatives = [];
        },

        async submit() {
            if (this.form.quantity_dispensed < 1 || this.form.quantity_dispensed > this.rx.remaining_qty) return;
            this.submitting = true;
            this.error = '';

            const payload = {
                quantity_dispensed: this.form.quantity_dispensed,
                notes: this.form.notes,
            };
            if (this.form.alternative_medicine_id) payload.alternative_medicine_id = this.form.alternative_medicine_id;
            if (this.form.medicine_batch_id)       payload.medicine_batch_id = this.form.medicine_batch_id;

            const r = await fetch(`/pharmacy/prescriptions/${this.rx.id}/dispense`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify(payload),
            });

            const data = await r.json();
            this.submitting = false;

            if (data.success || r.ok) {
                this.close();
                window.dispatchEvent(new CustomEvent('dispense-success'));
                // Refresh the list
                window.dispatchEvent(new CustomEvent('refresh-prescriptions'));
            } else {
                this.error = data.message ?? 'Dispense failed. Please try again.';
            }
        },
    };
}

function rxViewModal() {
    return {
        open: false,
        group: null,
        handleOpen({ group }) {
            this.group = group;
            this.open = true;
        },
        close() {
            this.open = false;
            this.group = null;
        }
    }
}

</script>
<script>
    // Listen for refresh events
    window.addEventListener('refresh-prescriptions', () => {
        // Trigger page reload or scoped fetch via Alpine 
        const root = document.querySelector('[x-data="pharmacyPrescriptions()"]');
        if (root && root._x_dataStack && root._x_dataStack[0]) {
            root._x_dataStack[0].fetchData();
        } else {
            window.location.reload();
        }
    });
</script>
@endpush
@endsection
