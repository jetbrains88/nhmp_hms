@extends('layouts.app')

@section('title', 'Illness Tags — Doctor Setup')
@section('page-title', 'Illness Tag Library')
@section('breadcrumb', 'Doctor Setup / Illness Tags')

@section('content')
<div x-data="illnessTagRegistry()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(79,70,229,0.2)] hover:shadow-[-10px_0_40px_-5px_rgba(79,70,229,0.3)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
        title="Open Tag Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-indigo-500/10 blur-md rounded-full group-hover:bg-indigo-500/20 transition-colors duration-300"></div>
            <i class="fas fa-tags relative z-10 group-hover:rotate-12 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Illness Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS (Compact Grid)
    ═══════════════════════════════════════════════ --}}
    <div class="flex overflow-x-auto pb-6 gap-6 gap-y-10 mt-4 no-scrollbar custom-scrollbar">
        <!-- Total Tags -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg shadow-indigo-500/10 border border-indigo-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-400 shadow-xl shadow-indigo-900/20 border border-indigo-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-tags text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-indigo-500 uppercase">Total Tags</p>
                <h4 class="text-3xl font-bold text-indigo-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-indigo-200 pt-2 text-indigo-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Clinical Classification</span>
                </div>
            </div>
        </div>

        <!-- Active Tags -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="filters.status = 'active'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-xl shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2 text-teal-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Currently Visible</span>
                </div>
            </div>
        </div>

        <!-- Chronic Tags -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="filters.category = 'chronic'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-pink-400 shadow-xl shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-heartbeat text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Chronic</p>
                <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.chronic">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-200 pt-2 text-rose-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Long-term Records</span>
                </div>
            </div>
        </div>

        <!-- Acute Tags -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="filters.category = 'acute'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-xl shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-bolt text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-amber-600 uppercase">Acute</p>
                <h4 class="text-3xl font-bold text-amber-700 drop-shadow-sm font-mono" x-text="stats.acute">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-amber-200 pt-2 text-amber-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Urgent Mapping</span>
                </div>
            </div>
        </div>

        <!-- Infectious Tags -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl shadow-lg shadow-orange-500/10 border border-orange-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="filters.category = 'infectious'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-orange-600 to-orange-400 shadow-xl shadow-orange-900/20 border border-orange-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-virus text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-orange-600 uppercase">Infectious</p>
                <h4 class="text-3xl font-bold text-orange-700 drop-shadow-sm font-mono" x-text="stats.infectious">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-orange-200 pt-2 text-orange-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-orange-600"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Communicable Risks</span>
                </div>
            </div>
        </div>

        <!-- Inactive Card -->
        <div class="flex-shrink-0 w-[280px] relative flex flex-col bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl shadow-lg shadow-slate-500/10 border border-slate-100 hover:-translate-y-2 transition-all duration-300 group cursor-pointer" @click="filters.status = 'inactive'; fetchData()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-slate-600 to-gray-400 shadow-xl shadow-slate-900/20 border border-slate-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-toggle-off text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-slate-500 uppercase">Inactive</p>
                <h4 class="text-3xl font-bold text-slate-700 drop-shadow-sm font-mono" x-text="stats.inactive">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-slate-200 pt-2 text-slate-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Hidden Entities</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════════════ --}}
    <div class="grid lg:grid-cols-12 gap-6 items-start text-sm">
        
        {{-- Left Column - Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                
                {{-- Toolbar --}}
                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class="fas fa-book-medical text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800 tracking-tight">Illness Library</h2>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-0.5">Clinical Classification System</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 uppercase pr-2 border-r border-slate-100 whitespace-nowrap">View</span>
                            <select x-model="filters.per_page" @change="fetchData()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none border-none p-0 pr-4">
                                <option value="15">15 Tags</option>
                                <option value="25">25 Tags</option>
                                <option value="50">50 Tags</option>
                            </select>
                        </div>

                        <button @click="openModal()" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2 group">
                            <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                            New Entry
                        </button>

                        <button @click="showSidebar = !showSidebar" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-xl hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="overflow-x-auto min-h-[400px] relative">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th @click="sort('name')" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] cursor-pointer hover:text-indigo-600 transition-colors group">
                                    <div class="flex items-center gap-2 text-xs">
                                        Condition Name
                                        <i class="fas fa-sort text-[10px] opacity-20 group-hover:opacity-100"></i>
                                    </div>
                                </th>
                                <th @click="sort('category')" class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] cursor-pointer hover:text-indigo-600 transition-colors group text-xs text-center">Category</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-xs">ICD-10 Code</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-xs">Clinical Note</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-xs text-center border-l border-slate-50">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-xs text-right whitespace-nowrap">Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="inline-flex flex-col items-center gap-4 animate-pulse">
                                            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 border border-indigo-200">
                                                <i class="fas fa-sync fa-spin"></i>
                                            </div>
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Querying Tags...</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && tags.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <i class="fas fa-tags text-4xl text-slate-100 mb-4 scale-150"></i>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">No matching illness profiles</p>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="tag in tags" :key="tag.id">
                                <tr class="hover:bg-indigo-50/30 transition-all duration-300 group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs shadow-sm border" :class="getCategoryIconClass(tag.category)">
                                                <i :class="getCategoryIcon(tag.category)"></i>
                                            </div>
                                            <div class="text-sm font-black text-slate-800 tracking-tight" x-text="tag.name"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border" :class="getCategoryBadgeClass(tag.category)" x-text="tag.category"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-[10px] font-mono font-black text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" x-text="tag.icd_code || '---'"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-[11px] text-slate-500 font-medium line-clamp-1 italic" x-text="tag.description || 'No notes available'"></p>
                                    </td>
                                    <td class="px-6 py-4 text-center border-l border-slate-50/50">
                                        <button @click="toggleStatus(tag)" 
                                                class="w-8 h-8 rounded-full border flex items-center justify-center transition-all duration-500"
                                                :class="tag.is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-600 shadow-sm' : 'bg-slate-50 border-slate-200 text-slate-300'">
                                            <i class="fas fa-power-off text-[10px]" :class="tag.is_active ? 'animate-pulse' : ''"></i>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openModal(tag)" class="w-8 h-8 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Edit Profile">
                                                <i class="fas fa-pencil-alt text-[10px]"></i>
                                            </button>
                                            <button @click="deleteTag(tag)" class="w-8 h-8 flex items-center justify-center bg-white border border-rose-100 text-rose-500 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="Delete Profile">
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
                <div class="p-6 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-slate-100">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        Index <span class="text-indigo-600" x-text="meta.from || 0"></span>-<span class="text-indigo-600" x-text="meta.to || 0"></span>
                        of <span class="text-indigo-600" x-text="meta.total || 0"></span> conditions
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <template x-for="link in paginationLinks" :key="link.label">
                            <button @click="goToPage(link.url)" 
                                    :disabled="!link.url"
                                    class="min-w-[36px] h-10 px-2.5 rounded-xl text-[9px] font-black uppercase tracking-tighter transition-all duration-300 border shadow-sm"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-700 shadow-indigo-200' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50 disabled:opacity-30'"
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
            
            <div class="bg-white rounded-[2.5rem] p-8 text-slate-800 shadow-2xl relative overflow-hidden border border-slate-100">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative space-y-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:rotate-6 duration-300">
                                <i class="fas fa-filter text-indigo-600"></i>
                            </div>
                            <div>
                                <h3 class="font-black uppercase tracking-[0.2em] text-xs text-slate-800">Tag Filters</h3>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Refine registry</p>
                            </div>
                        </div>
                        <button @click="showSidebar = false" class="text-slate-300 hover:text-rose-500 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Search --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Search Terms</label>
                        <div class="relative group">
                            <input x-model="filters.search" @input.debounce.400ms="fetchData()"
                                   type="text" placeholder="Condition or ICD..." 
                                   class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-xs text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-400 transition-all outline-none font-bold">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Condition Class</label>
                        <div class="grid grid-cols-1 gap-2">
                            <template x-for="cat in ['chronic', 'acute', 'infectious', 'other']">
                                <button @click="filters.category = (filters.category === cat ? '' : cat); fetchData()"
                                        class="flex items-center justify-between px-6 py-4 rounded-3xl border transition-all duration-300 group shadow-sm active:scale-95"
                                        :class="filters.category === cat ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200'">
                                    <span class="text-[11px] font-black uppercase tracking-widest flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full" :class="filters.category === cat ? 'bg-white' : 'bg-slate-300'"></div>
                                        <span x-text="cat"></span>
                                    </span>
                                    <i class="fas fa-chevron-right text-[10px] opacity-20 group-hover:opacity-100 transition-all" :class="filters.category === cat ? 'translate-x-1 opacity-100' : ''"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Status</label>
                        <div class="flex gap-2 bg-slate-50 p-2 rounded-[2rem] border border-slate-100 shadow-inner">
                            <button @click="filters.status = 'active'; fetchData()" class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all" :class="filters.status === 'active' ? 'bg-white text-emerald-600 shadow-sm border border-emerald-100' : 'text-slate-400 hover:text-slate-600'">Active</button>
                            <button @click="filters.status = 'inactive'; fetchData()" class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all" :class="filters.status === 'inactive' ? 'bg-white text-rose-600 shadow-sm border border-rose-100' : 'text-slate-400 hover:text-slate-600'">Inactive</button>
                        </div>
                    </div>

                    <button @click="clearFilters()" class="w-full py-5 mt-8 bg-slate-100 hover:bg-indigo-600 text-slate-400 hover:text-white rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 border border-slate-100 hover:border-indigo-600 flex items-center justify-center gap-3 active:scale-95 font-bold">
                        <i class="fas fa-broom"></i> Reset Registry
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MODAL: CREATE/EDIT
    ═══════════════════════════════════════════════ --}}
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="closeModal()" x-show="showModal" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden border border-slate-100">
            
            <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                        <i class="fas" :class="editingTag ? 'fa-edit' : 'fa-plus'"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="editingTag ? 'Modify Profile' : 'New Condition profile'"></h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Clinical Classification Record</p>
                    </div>
                </div>
                <button @click="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-rose-600 transition-all shadow-sm">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form @submit.prevent="saveTag()" class="p-8 space-y-6">
                <!-- Condition Name -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">Condition Descriptor <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <input x-model="form.name" type="text" placeholder="e.g. Hypertension, Diabetes..." required class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-3.5 text-sm font-black text-slate-800 placeholder-slate-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/40 outline-none transition-all">
                        <i class="fas fa-microscope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500"></i>
                    </div>
                </div>

                <!-- Category and ICD Code -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">Clinic Class <span class="text-rose-500">*</span></label>
                        <select x-model="form.category" required class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3.5 text-sm font-black text-slate-800 outline-none focus:bg-white focus:border-indigo-500/40 transition-all appearance-none uppercase tracking-widest">
                            <option value="">Select...</option>
                            <option value="chronic">Chronic</option>
                            <option value="acute">Acute</option>
                            <option value="infectious">Infectious</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">ICD-10 Reference</label>
                        <div class="relative group">
                            <input x-model="form.icd_code" type="text" placeholder="ICD-10 Code" class="w-full bg-slate-50 border border-slate-100 rounded-2xl pl-12 pr-4 py-3.5 text-sm font-mono font-black text-indigo-600 placeholder-slate-300 focus:bg-white focus:border-indigo-500/40 outline-none transition-all uppercase">
                            <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500"></i>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 ml-1">Internal Notes</label>
                    <textarea x-model="form.description" rows="3" placeholder="Brief clinical description or guidance..." class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold text-slate-600 placeholder-slate-300 focus:bg-white focus:border-indigo-500/40 outline-none transition-all resize-none"></textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100 group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shadow-sm border border-slate-100 group-hover:rotate-6 transition-all">
                            <i class="fas fa-power-off text-xs"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">Entry Visibility</span>
                    </div>
                    <button type="button" @click="form.is_active = !form.is_active" 
                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-500 shadow-inner" 
                            :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-300'">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-xl transition-transform duration-500" :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                    <input type="hidden" x-model="form.is_active">
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="closeModal()" class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-2xl text-[9px] font-black uppercase tracking-[0.3em] transition-all">Discard</button>
                    <button type="submit" :disabled="saving" class="flex-1 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[9px] font-black uppercase tracking-[0.3em] transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3">
                        <i class="fas fa-save" x-show="!saving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="editingTag ? 'Update Profile' : 'Confirm Registry'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function illnessTagRegistry() {
    const STORAGE_KEY = 'illness_tags_filters_v2';

    return {
        tags: [],
        stats: { total: 0, active: 0, inactive: 0, chronic: 0, acute: 0, infectious: 0 },
        meta: {},
        paginationLinks: [],
        loading: false,
        showSidebar: true,
        showModal: false,
        editingTag: null,
        saving: false,

        filters: JSON.parse(localStorage.getItem(STORAGE_KEY) || JSON.stringify({
            search: '', category: '', status: '', per_page: '15', sort_by: 'name', sort_dir: 'asc'
        })),

        form: { name: '', category: '', icd_code: '', description: '', is_active: true },

        init() {
            this.fetchStats();
            this.fetchData();
            this.$watch('filters', (v) => localStorage.setItem(STORAGE_KEY, JSON.stringify(v)), { deep: true });
        },

        async fetchStats() {
            try {
                const r = await fetch("{{ route('doctor.setup.illness-tags.stats') }}");
                this.stats = await r.json();
            } catch (e) { console.error('Stats fetch failed'); }
        },

        async fetchData(url = null) {
            this.loading = true;
            const params = new URLSearchParams(this.filters);
            const endpoint = url || `{{ route('doctor.setup.illness-tags.data') }}?${params}`;
            
            try {
                const r = await fetch(endpoint);
                const data = await r.json();
                this.tags = data.data;
                this.meta = { total: data.total, from: data.from, to: data.to };
                this.paginationLinks = data.links;
            } catch (e) { console.error('Data fetch failed'); }
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
            this.filters = { search: '', category: '', status: '', per_page: '15', sort_by: 'name', sort_dir: 'asc' };
            this.fetchData();
        },

        openModal(tag = null) {
            this.editingTag = tag;
            if (tag) {
                this.form = { ...tag, is_active: !!tag.is_active };
            } else {
                this.form = { name: '', category: '', icd_code: '', description: '', is_active: true };
            }
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingTag = null;
        },

        async saveTag() {
            this.saving = true;
            const isEdit = !!this.editingTag;
            const url = isEdit ? `/doctor/setup/illness-tags/${this.editingTag.id}` : `{{ route('doctor.setup.illness-tags.store') }}`;
            
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
            } catch (e) { console.error('Save failed'); }
            this.saving = false;
        },

        async toggleStatus(tag) {
            try {
                const r = await fetch(`/doctor/setup/illness-tags/${tag.id}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    tag.is_active = data.tag.is_active;
                    this.fetchStats();
                }
            } catch (e) { console.error('Toggle failed'); }
        },

        async deleteTag(tag) {
            if (!confirm(`Permanently remove ${tag.name} from library?`)) return;
            try {
                const r = await fetch(`/doctor/setup/illness-tags/${tag.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await r.json();
                if (data.success) {
                    this.fetchData();
                    this.fetchStats();
                }
            } catch (e) { console.error('Delete failed'); }
        },

        // Style Helpers
        getCategoryBadgeClass(cat) {
            const map = {
                chronic:    'bg-rose-50 text-rose-600 border-rose-100',
                acute:      'bg-amber-50 text-amber-600 border-amber-100',
                infectious: 'bg-orange-50 text-orange-600 border-orange-100',
                other:      'bg-blue-50 text-blue-600 border-blue-100'
            };
            return map[cat] || 'bg-slate-50 text-slate-600 border-slate-100';
        },

        getCategoryIcon(cat) {
            const map = { chronic: 'fa-heartbeat', acute: 'fa-bolt', infectious: 'fa-virus', other: 'fa-tag' };
            return 'fas ' + (map[cat] || 'fa-tag');
        },

        getCategoryIconClass(cat) {
            const map = {
                chronic:    'bg-rose-50 text-rose-500 border-rose-100',
                acute:      'bg-amber-50 text-amber-500 border-amber-100',
                infectious: 'bg-orange-50 text-orange-500 border-orange-100',
                other:      'bg-blue-50 text-blue-500 border-blue-100'
            };
            return map[cat] || 'bg-slate-50 text-slate-400 border-slate-100';
        }
    };
}
</script>
@endpush
