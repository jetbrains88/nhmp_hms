@extends('layouts.app')

@section('title', 'Rx Abbreviations — Doctor Setup')
@section('page-title', 'Prescription Abbreviations')
@section('breadcrumb', 'Doctor Setup / Rx Abbreviations')

@section('content')
<div x-data="rxAbbreviationRegistry()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(79,70,229,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(79,70,229,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
        title="Open Rx Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-indigo-500/10 blur-md rounded-full group-hover:bg-indigo-500/20 transition-colors duration-300"></div>
            <i class="fas fa-file-prescription relative z-10 group-hover:rotate-12 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Shorthand Filters</span>
    </button>

    <div class="flex overflow-x-auto pb-6 gap-6 mt-4 no-scrollbar custom-scrollbar">
        <template x-for="(stat, key) in statCards" :key="key">
            <div class="flex-shrink-0 w-[240px] relative flex flex-col rounded-2xl shadow-lg border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                 :class="stat.label === 'Offline' ? 'bg-gradient-to-br from-slate-50 to-gray-50 border-slate-200 shadow-slate-500/10' : 'bg-gradient-to-br from-violet-50 to-purple-50 border-purple-100 shadow-purple-500/10'"
                 @click="stat.filter ? (filters.category = stat.filter, fetchData()) : (key === 0 ? clearFilters() : (key === 1 ? (filters.status = 'active', fetchData()) : null))">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl shadow-xl border group-hover:scale-110 transition-transform duration-300"
                     :style="`background: ${stat.gradient}; border-color: rgba(255,255,255,0.2)`">
                    <i :class="stat.icon + ' text-xl text-white drop-shadow-md'"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-xs font-bold tracking-wider uppercase" :class="stat.label === 'Offline' ? 'text-slate-500' : 'text-purple-500'" x-text="stat.label"></p>
                    <h4 class="text-3xl font-bold drop-shadow-sm font-mono" :class="stat.label === 'Offline' ? 'text-slate-700' : 'text-purple-700'" x-text="stat.value">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t pt-2" :class="stat.label === 'Offline' ? 'border-slate-200 text-slate-700' : 'border-purple-200 text-purple-700'">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full animate-pulse" :class="stat.label === 'Offline' ? 'bg-slate-600' : 'bg-purple-600'"></span>
                        <span class="text-[10px] font-bold uppercase tracking-tight" x-text="stat.filter || 'Shorthand Records'"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-12 gap-6 items-start text-sm">
        
        {{-- Left Column - Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                
                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 p-6 border-b border-purple-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-purple-100 shadow-sm hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-keyboard text-2xl text-purple-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-purple-600 tracking-tight flex items-center gap-3">
                                    Case Shorthand
                                    <span class="text-lg font-normal text-gray-600">
                                        (<span x-text="meta.total"></span> records)
                                    </span>
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Prescription Linguistics Engine</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 items-center">
                            {{-- Rows Selector --}}
                            <div class="flex items-center gap-2 bg-white border border-purple-100 rounded-xl px-3 py-1.5 shadow-sm">
                                <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                                <select x-model="filters.per_page" @change="fetchData()" class="bg-transparent text-purple-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>

                            <button @click="openModal()"
                                class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/30 transition-all active:scale-95 group">
                                <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                                New Abbreviation
                            </button>
                            
                            <button @click="showSidebar = !showSidebar"
                                class="w-10 h-10 flex items-center justify-center bg-white border border-purple-100 text-purple-600 rounded-xl hover:bg-purple-50 transition-colors shadow-sm"
                                :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="overflow-x-auto min-h-[460px] relative">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="bg-white">
                                <th @click="sort('abbreviation')" class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] cursor-pointer hover:text-purple-600 transition-colors group border-b border-slate-50">
                                    <div class="flex items-center gap-2">
                                        Shorthand
                                        <i class="fas fa-sort text-[10px] opacity-20 group-hover:opacity-100"></i>
                                    </div>
                                </th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">Full Meaning</th>
                                <th @click="sort('category')" class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50 text-center">Category</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50 text-center">Doses</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="5" class="py-32 text-center">
                                        <div class="inline-flex flex-col items-center gap-5">
                                            <div class="relative w-16 h-16">
                                                <div class="absolute inset-0 border-4 border-purple-100 rounded-full"></div>
                                                <div class="absolute inset-0 border-4 border-purple-600 rounded-full border-t-transparent animate-spin"></div>
                                            </div>
                                            <span class="text-[10px] font-black text-purple-600 uppercase tracking-[0.3em] animate-pulse font-mono tracking-widest">Parsing Lexicon...</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && abbreviations.length === 0">
                                <tr>
                                    <td colspan="5" class="py-32 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <i class="fas fa-file-signature text-6xl text-slate-200 mb-6 group-hover:rotate-12 transition-transform"></i>
                                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest">No shorthand records found</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="abbr in abbreviations" :key="abbr.id">
                                <tr class="hover:bg-purple-50/30 transition-all duration-300 group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center shadow-sm group-hover:shadow-md group-hover:border-purple-200 transition-all duration-300">
                                                <span class="text-xs font-black text-purple-700 font-mono tracking-tighter" x-text="abbr.abbreviation"></span>
                                            </div>
                                            <div x-show="abbr.is_active" class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-slate-700 tracking-tight" x-text="abbr.full_meaning"></div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Rx Standard</div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border" :class="getCategoryBadgeClass(abbr.category)" x-text="abbr.category"></span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <template x-if="abbr.doses_per_day">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-black text-slate-800 font-mono" x-text="abbr.doses_per_day + 'x'"></span>
                                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Daily</span>
                                            </div>
                                        </template>
                                        <template x-if="!abbr.doses_per_day">
                                            <span class="text-slate-200 text-xs">—</span>
                                        </template>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                            <button @click="toggleStatus(abbr)"
                                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-sm"
                                                    :class="abbr.is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-600 hover:text-white' : 'bg-slate-100 text-slate-300 border border-slate-200 hover:bg-slate-200'">
                                                <i class="fas fa-check text-[10px]"></i>
                                            </button>
                                            <button @click="openModal(abbr)" class="w-8 h-8 flex items-center justify-center bg-white border border-purple-100 text-purple-600 rounded-xl hover:bg-purple-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-pencil-alt text-[10px]"></i>
                                            </button>
                                            <button @click="deleteAbbr(abbr)" class="w-8 h-8 flex items-center justify-center bg-white border border-rose-100 text-rose-500 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-8 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-8 border-t border-slate-100">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></div>
                        Glossary Map <span class="text-purple-600" x-text="meta.from || 0"></span>-<span class="text-purple-600" x-text="meta.to || 0"></span>
                        of <span class="text-purple-600" x-text="meta.total || 0"></span> phrases
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <template x-for="link in paginationLinks" :key="link.label">
                            <button @click="goToPage(link.url)" 
                                    :disabled="!link.url"
                                    class="min-w-[40px] h-11 px-3 rounded-2xl text-[10px] font-black transition-all duration-300 border shadow-sm flex items-center justify-center"
                                    :class="link.active ? 
                                            'bg-purple-600 text-white border-purple-700 shadow-purple-200' : 
                                            'bg-white text-slate-500 border-slate-200 hover:bg-purple-50 hover:border-purple-200 disabled:opacity-20'"
                                    x-html="link.label">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Sticky Sidebar --}}
        <div x-show="showSidebar" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-12"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2">
            
            <div class="bg-white rounded-[3rem] p-8 text-slate-800 shadow-2xl relative overflow-hidden border border-slate-100 group/sidebar">
                <div class="absolute -top-16 -right-16 w-32 h-48 bg-purple-50 rounded-full blur-3xl opacity-50 italic transition-transform group-hover/sidebar:scale-110 duration-1000"></div>
                
                <div class="relative space-y-10">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center border border-purple-100 shadow-sm transition-transform hover:rotate-6 duration-300">
                                <i class="fas fa-filter text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-black uppercase tracking-[0.2em] text-xs text-slate-800">Glossary Filters</h3>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 italic">Scan the lexicon</p>
                            </div>
                        </div>
                        <button @click="showSidebar = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Search --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Scan Glossary</label>
                        <div class="relative group">
                            <input x-model="filters.search" @input.debounce.400ms="fetchData()"
                                   type="text" placeholder="BID, TID, PO..." 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-xs text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-purple-500/5 focus:border-purple-400 transition-all outline-none font-bold">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-purple-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic">Lexicon Class</label>
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="cat in ['Frequency & Timing', 'Route of Administration', 'Instructions', 'Medication Terms']">
                                <button @click="filters.category = (filters.category === cat ? '' : cat); fetchData()"
                                        class="flex items-center justify-between px-6 py-4 rounded-[1.5rem] border transition-all duration-500 text-left relative group shadow-sm active:scale-95"
                                        :class="filters.category === cat ? 'bg-purple-600 text-white border-purple-600 shadow-purple-200 z-10' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200'">
                                    <span class="text-[11px] font-black uppercase tracking-tight leading-none pr-4" x-text="cat"></span>
                                    <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-all" :class="filters.category === cat ? 'translate-x-1 opacity-100' : ''"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Per Page Toggle --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic">Page Density</label>
                        <div class="flex p-2 bg-slate-50 rounded-[1.5rem] border border-slate-100 shadow-inner gap-2">
                            <template x-for="size in ['25', '50', '100']">
                                <button @click="filters.per_page = size; fetchData()" 
                                        class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300"
                                        :class="filters.per_page === size ? 'bg-white text-purple-600 shadow-sm border border-purple-100' : 'text-slate-400 hover:text-slate-600'">
                                    <span x-text="size"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button @click="clearFilters()" class="w-full py-5 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-[2rem] text-[10px] font-black uppercase tracking-[0.4em] transition-all duration-300 border border-slate-100 hover:border-rose-100 flex items-center justify-center gap-3 active:scale-95 font-bold">
                        <i class="fas fa-broom"></i> Reset Glossary
                    </button>
                    
                    <div class="pt-10 opacity-40 group-hover/sidebar:opacity-100 transition-opacity duration-1000">
                        <div class="p-6 rounded-[2rem] bg-black/20 border border-white/5 text-center">
                            <i class="fas fa-lightbulb text-amber-300 mb-3 block text-lg"></i>
                            <p class="text-[10px] text-purple-200 font-bold leading-relaxed tracking-wide italic">"BID means Bis In Die (Twice a Day). Accurate abbreviations speed up clinical workflows."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MODAL: LEXICON EDITOR
    ═══════════════════════════════════════════════ --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl">
        <div @click.away="closeModal()" x-show="showModal" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="scale-90 opacity-0 translate-y-12" x-transition:enter-end="scale-100 opacity-100 translate-y-0" class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white relative">
            
            {{-- Modal Header --}}
            <div class="px-10 py-8 bg-gradient-to-br from-violet-50 to-purple-50 border-b border-purple-100/50 flex justify-between items-center relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-purple-200/20 rounded-full blur-2xl"></div>
                <div class="flex items-center gap-5 relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-purple-600 text-white flex items-center justify-center shadow-xl shadow-purple-200 border border-purple-400">
                        <i class="fas" :class="editing ? 'fa-edit' : 'fa-keyboard-o'"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter" x-text="editing ? 'Update Code' : 'New Shorthand'"></h3>
                        <p class="text-[10px] font-black text-purple-500 uppercase tracking-[0.3em] mt-1">Lexicon Entry Modification</p>
                    </div>
                </div>
                <button @click="closeModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-100 text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all shadow-sm">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form @submit.prevent="save()" class="p-10 space-y-8">
                <!-- Row 1: Code and Category -->
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic">Shorthand Code <span class="text-rose-500">*</span></label>
                        <div class="relative group">
                            <input x-model="form.abbreviation" type="text" placeholder="e.g. BID" required class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] pl-12 pr-4 py-4 text-sm font-black text-purple-700 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-purple-500/5 focus:border-purple-500/50 outline-none transition-all uppercase font-mono tracking-widest">
                            <i class="fas fa-terminal absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-purple-500 transition-colors"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic">Category <span class="text-rose-500">*</span></label>
                        <select x-model="form.category" required class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] px-6 py-4 text-[11px] font-black text-slate-700 outline-none focus:bg-white focus:border-purple-500/50 transition-all appearance-none uppercase tracking-widest cursor-pointer">
                            <option value="">Select...</option>
                            <option value="Frequency & Timing">Frequency & Timing</option>
                            <option value="Route of Administration">Route of Administration</option>
                            <option value="Instructions">Instructions</option>
                            <option value="Medication Terms">Medication Terms</option>
                        </select>
                    </div>
                </div>

                <!-- Meaning -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic">Deciphered Phrase <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <input x-model="form.full_meaning" type="text" placeholder="Full medical meaning..." required class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] pl-12 pr-4 py-4 text-sm font-black text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-purple-500/5 focus:border-purple-500/50 outline-none transition-all">
                        <i class="fas fa-language absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-purple-500 transition-colors"></i>
                    </div>
                </div>

                <!-- Doses -->
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1 italic text-center">Daily Conversion Factor <span class="text-slate-300 font-normal">(Numerical frequency)</span></label>
                    <div class="relative group">
                        <input x-model="form.doses_per_day" type="number" min="0" max="24" placeholder="0 if not applicable" class="w-full bg-slate-50 border border-slate-100 rounded-[1.25rem] pl-12 pr-4 py-4 text-sm font-black text-indigo-600 placeholder-slate-300 focus:bg-white focus:border-purple-500/50 outline-none transition-all text-center">
                        <i class="fas fa-calculator absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-purple-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <!-- Visibility -->
                <div class="flex items-center justify-between p-5 bg-purple-50 hover:bg-purple-100/50 rounded-3xl border border-purple-100/50 transition-colors group/toggle">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-white border border-purple-200 flex items-center justify-center text-purple-600 shadow-sm group-hover/toggle:rotate-12 transition-transform">
                            <i class="fas fa-eye text-xs"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-purple-900 block font-mono">Registry Visibility</span>
                            <span class="text-[8px] font-bold text-purple-400 uppercase tracking-widest mt-0.5" x-text="form.is_active ? 'Public Access' : 'Private Draft'"></span>
                        </div>
                    </div>
                    <button type="button" @click="form.is_active = !form.is_active" 
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-all duration-700 shadow-inner overflow-hidden border-2 border-transparent" 
                            :class="form.is_active ? 'bg-purple-600 active:scale-95' : 'bg-slate-200'">
                        <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-2xl transition-transform duration-500 ease-in-out" :class="form.is_active ? 'translate-x-7' : 'translate-x-1'"></span>
                    </button>
                    <input type="hidden" x-model="form.is_active">
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4 pt-6">
                    <button type="button" @click="closeModal()" class="flex-1 py-5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-[2rem] text-[10px] font-black uppercase tracking-[0.4em] transition-all duration-300 border border-slate-200/50">Abort</button>
                    <button type="submit" :disabled="saving" class="flex-1 py-5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.4em] transition-all shadow-2xl shadow-purple-200 flex items-center justify-center gap-3 active:scale-95 border border-purple-500/20">
                        <i class="fas fa-cloud-upload-alt" x-show="!saving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="editing ? 'Commit Change' : 'Deploy Entry'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function rxAbbreviationRegistry() {
    const STORAGE_KEY = 'rx_abbrev_filters_v2';

    return {
        abbreviations: [],
        stats: { total: 0, active: 0, inactive: 0, frequency: 0, route: 0, timing: 0, dosage: 0 },
        meta: {},
        paginationLinks: [],
        loading: false,
        showSidebar: true,
        showModal: false,
        editing: null,
        saving: false,

        filters: JSON.parse(localStorage.getItem(STORAGE_KEY) || JSON.stringify({
            search: '', category: '', status: '', per_page: '25', sort_by: 'category', sort_dir: 'asc'
        })),

        form: { abbreviation: '', full_meaning: '', category: '', doses_per_day: '', is_active: true },

        init() {
            this.fetchStats();
            this.fetchData();
            this.$watch('filters', (v) => localStorage.setItem(STORAGE_KEY, JSON.stringify(v)), { deep: true });
        },

        async fetchStats() {
            try {
                const r = await fetch("{{ route('doctor.setup.prescription-abbreviations.stats') }}");
                const data = await r.json();
                this.statCards = [
                    { label: 'Total', value: data.total, icon: 'fas fa-book', gradient: 'linear-gradient(135deg,#4f46e5,#7c3aed)' },
                    { label: 'Active', value: data.active, icon: 'fas fa-check-double', gradient: 'linear-gradient(135deg,#10b981,#059669)' },
                    { label: 'Freq', value: data.frequency, filter: 'Frequency & Timing', icon: 'fas fa-clock', gradient: 'linear-gradient(135deg,#6366f1,#4338ca)' },
                    { label: 'Route', value: data.route, filter: 'Route of Administration', icon: 'fas fa-syringe', gradient: 'linear-gradient(135deg,#14b8a6,#0d9488)' },
                    { label: 'Rules', value: data.timing, filter: 'Instructions', icon: 'fas fa-pen-nib', gradient: 'linear-gradient(135deg,#f59e0b,#d97706)' },
                    { label: 'Terms', value: data.dosage, filter: 'Medication Terms', icon: 'fas fa-pills', gradient: 'linear-gradient(135deg,#ec4899,#db2777)' },
                    { label: 'Offline', value: data.inactive, icon: 'fas fa-cloud-moon', gradient: 'linear-gradient(135deg,#64748b,#475569)' },
                ];
                this.stats = data;
            } catch (e) { console.error('Stats error'); }
        },

        async fetchData(url = null) {
            this.loading = true;
            const params = new URLSearchParams(this.filters);
            const endpoint = url || `{{ route('doctor.setup.prescription-abbreviations.data') }}?${params}`;
            
            try {
                const r = await fetch(endpoint);
                const data = await r.json();
                this.abbreviations = data.data;
                this.meta = { total: data.total, from: data.from, to: data.to };
                this.paginationLinks = data.links;
            } catch (e) { console.error('Data error'); }
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
            this.filters = { search: '', category: '', status: '', per_page: '25', sort_by: 'category', sort_dir: 'asc' };
            this.fetchData();
        },

        openModal(abbr = null) {
            this.editing = abbr;
            if (abbr) {
                this.form = { ...abbr, doses_per_day: abbr.doses_per_day || '', is_active: !!abbr.is_active };
            } else {
                this.form = { abbreviation: '', full_meaning: '', category: '', doses_per_day: '', is_active: true };
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
            const url = isEdit ? `/doctor/setup/prescription-abbreviations/${this.editing.id}` : `{{ route('doctor.setup.prescription-abbreviations.store') }}`;
            
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
            } catch (e) { console.error('Save error'); }
            this.saving = false;
        },

        async toggleStatus(abbr) {
            try {
                const r = await fetch(`/doctor/setup/prescription-abbreviations/${abbr.id}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    abbr.is_active = data.abbreviation.is_active;
                    this.fetchStats();
                }
            } catch (e) { console.error('Toggle error'); }
        },

        async deleteAbbr(abbr) {
            if (!confirm(`Permanently remove shorthand entry "${abbr.abbreviation}"?`)) return;
            try {
                const r = await fetch(`/doctor/setup/prescription-abbreviations/${abbr.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    this.fetchData();
                    this.fetchStats();
                }
            } catch (e) { console.error('Delete error'); }
        },

        // Style Helpers
        getCategoryBadgeClass(cat) {
            const map = {
                'Frequency & Timing':      'bg-indigo-50 text-indigo-600 border-indigo-100',
                'Route of Administration': 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'Instructions':            'bg-amber-50 text-amber-600 border-amber-100',
                'Medication Terms':        'bg-pink-50 text-pink-600 border-pink-100'
            };
            return map[cat] || 'bg-slate-50 text-slate-400 border-slate-100';
        }
    };
}
</script>
@endpush
