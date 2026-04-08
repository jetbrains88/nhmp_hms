@extends('layouts.app')
@section('title', 'Medicine Categories Management - NHMP HMS')
@section('page-title', 'Medicine Categories')

@section('content')
    <div x-data="categoryManagement()" x-init="init()" x-cloak class="space-y-8 relative">

        {{-- Futuristic Floating Filter Toggle --}}
        <button @click="showSidebar = true" x-show="!showSidebar"
            x-transition:enter="transition ease-out duration-500 delay-100"
            x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-indigo-600 to-purple-600 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(79,70,229,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(79,70,229,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-400/50 group cursor-pointer"
            title="Open Category Filters">
            <div class="relative">
                <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
                <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
            </div>
            <span style="writing-mode: vertical-rl;"
                class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">Category Filters</span>
        </button>

        {{-- Stats Cards Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 gap-y-10 mt-4">
            <!-- Total Categories Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-lg shadow-indigo-500/10 border border-indigo-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                @click="clearFilters()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-400 shadow-lg shadow-indigo-900/20 border border-indigo-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-layer-group text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Total Categories
                    </p>
                    <h4 class="text-3xl font-black text-blue-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-indigo-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                        <span class="text-[9px] text-indigo-700 font-black uppercase tracking-tight">System Registry</span>
                    </div>
                </div>
            </div>

            <!-- Active Categories Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                @click="filterStatus = 'active'; searchCategories()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-check-double text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-emerald-600 uppercase opacity-70">Active Nodes</p>
                    <h4 class="text-3xl font-black text-emerald-700 drop-shadow-sm font-mono" x-text="stats.active">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-emerald-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        <span class="text-[9px] text-emerald-700 font-black uppercase tracking-tight">Operational
                            State</span>
                    </div>
                </div>
            </div>

            <!-- Inactive Categories Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
                @click="filterStatus = 'inactive'; searchCategories()">
                <div
                    class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-red-400 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-power-off text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-rose-600 uppercase opacity-70">Dormant Nodes</p>
                    <h4 class="text-3xl font-black text-rose-700 drop-shadow-sm font-mono" x-text="stats.inactive">0</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                        <span class="text-[9px] text-rose-700 font-black uppercase tracking-tight">Standby State</span>
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
                <div
                    class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">

                    {{-- Panel Header with Light Gradient --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                                    <i class="fas fa-tags text-2xl text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2
                                        class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                        Medicine Categories
                                        <span class="text-lg font-normal text-gray-600">
                                            (<span x-text="pagination.total"></span> records)
                                        </span>
                                    </h2>
                                    <p class="text-gray-600 text-sm font-medium mt-1">Classify and organize your pharmacy
                                        inventory</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 items-center">
                                <button @click="openAddModal()"
                                    class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-500/30 transition-all active:scale-95 group">
                                    <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                                    New Category
                                </button>

                                <div class="h-8 w-px bg-indigo-100 mx-1"></div>

                                <button @click="showSidebar = !showSidebar"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm relative group"
                                    :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                    <i class="fas" :class="showSidebar ? 'fa-eye-slash text-rose-500' : 'fa-filter'"></i>
                                    <span x-show="hasActiveFilters()"
                                        class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 border-2 border-white rounded-full"></span>
                                </button>

                                <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                    <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row Density</span>
                                    <select x-model="pagination.per_page" @change="fetchCategories()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                        <option value="15">15 Per Page</option>
                                        <option value="25">25 Per Page</option>
                                        <option value="50">50 Per Page</option>
                                        <option value="100">100 Per Page</option>
                                    </select>
                                </div>

                                <button @click="fetchCategories()"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm group"
                                    title="Refresh Sync">
                                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700"
                                        :class="loading ? 'animate-spin text-indigo-400' : ''"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bulk Actions Toolbar (Sticky below view controller) --}}
                    <div x-show="selectedIds.length > 0" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="-translate-y-full" x-transition:enter-end="translate-y-0"
                        class="bg-indigo-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl rounded-xl mx-6 mb-4">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                                <span x-text="selectedIds.length"></span> Categories Selected
                            </span>
                            <div class="flex items-center gap-2">
                                <button @click="confirmBulkAction('deactivate')"
                                    class="px-3 py-1.5 bg-amber-500 shadow-lg shadow-amber-500/20 hover:bg-amber-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Deactivate
                                    Selection</button>
                                <button @click="confirmBulkAction('activate')"
                                    class="px-3 py-1.5 bg-emerald-500 shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Activate
                                    Selection</button>
                            </div>
                        </div>
                        <button @click="selectedIds = []"
                            class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2 cursor-pointer">
                            Dismiss <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- View Content --}}
                    <div class="relative min-h-[400px]">
                        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-inner"></div>
                                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest animate-pulse">Syncing Data...</p>
                            </div>
                        </div>
                        {{-- Table View --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-white border-b border-indigo-100">
                                    <tr>
                                        <th class="px-5 py-4 w-12">
                                            <div class="flex items-center justify-center">
                                                <input type="checkbox" @change="toggleAll($event)"
                                                    :checked="selectedIds.length === categories.length && categories.length > 0"
                                                    class="w-5 h-5 rounded-lg border-indigo-200 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer hover:border-indigo-400">
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 border-b border-slate-50">
                                            <div
                                                class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-tag text-[10px]"></i>
                                                </div>
                                                <button @click="sortBy('name')"
                                                    class="flex items-center gap-1.5 hover:text-indigo-700 transition-colors group">
                                                    Category Name
                                                    <i class="fas text-[10px] transition-all duration-300 opacity-0 group-hover:opacity-100"
                                                        :class="getSortIcon('name')"></i>
                                                </button>
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 border-b border-slate-50">
                                            <div
                                                class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-align-left text-[10px]"></i>
                                                </div>
                                                <span>Description Summary</span>
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 text-center border-b border-slate-50">
                                            <div
                                                class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-sort-numeric-down text-[10px]"></i>
                                                </div>
                                                <button @click="sortBy('display_order')"
                                                    class="flex items-center gap-1.5 hover:text-indigo-700 transition-colors group">
                                                    Priority
                                                    <i class="fas text-[10px] transition-all duration-300 opacity-0 group-hover:opacity-100"
                                                        :class="getSortIcon('display_order')"></i>
                                                </button>
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 text-center border-b border-slate-50">
                                            <div
                                                class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-toggle-on text-[10px]"></i>
                                                </div>
                                                <span>Status</span>
                                            </div>
                                        </th>
                                        <th class="px-5 py-4 text-center whitespace-nowrap w-32 border-b border-slate-50">
                                            <div
                                                class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                    <i class="fas fa-bolt-lightning text-[10px]"></i>
                                                </div>
                                                Control Actions
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

                                    <template x-if="!loading && categories.length === 0">
                                        <tr>
                                            <td colspan="6" class="py-24 text-center">
                                                <div
                                                    class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl">
                                                    <i class="fas fa-ghost text-4xl text-slate-300"></i>
                                                </div>
                                                <h3 class="text-2xl font-black text-slate-800 mb-2">No Categories Found
                                                </h3>
                                                <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault
                                                    appears empty for current criteria. Perhaps adjust your security
                                                    filters?</p>
                                                <button @click="clearFilters()"
                                                    class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset
                                                    Security Clearance</button>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="category in categories" :key="category.id">
                                        <tr class="hover:bg-blue-50/40 transition-colors group"
                                            :class="density === 'condensed' ? 'bg-white' : ''">
                                            <td class="px-5 border-b border-slate-50 transition-all"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <div class="flex items-center justify-center">
                                                    <input type="checkbox" :value="category.id" x-model="selectedIds"
                                                        class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                                </div>
                                            </td>
                                            <td class="px-5 border-b border-slate-50 transition-all"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-indigo-600 text-white shrink-0">
                                                        <i class="fas fa-folder text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-navy-800" x-text="category.name">
                                                        </p>
                                                        <div class="text-[10px] font-bold text-slate-400 tracking-wider"
                                                            x-text="category.slug || '-'"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 border-b border-slate-50 transition-all"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <p class="text-xs font-bold text-slate-600 line-clamp-1 max-w-xs"
                                                    x-text="category.description || '-'"></p>
                                            </td>
                                            <td class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <span
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-bold text-sm border border-slate-200"
                                                    x-text="category.display_order"></span>
                                            </td>
                                            <td class="px-5 border-b border-slate-50 transition-all text-center"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <div class="flex flex-col items-center gap-1">
                                                    <button @click="toggleStatus(category)"
                                                        class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors focus:outline-none"
                                                        :class="category.is_active ? 'bg-emerald-500' : 'bg-rose-500'">
                                                        <span
                                                            class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-sm"
                                                            :class="category.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                                                    </button>
                                                    <div class="text-[9px] font-black uppercase tracking-widest"
                                                        :class="category.is_active ? 'text-emerald-600' : 'text-rose-600'"
                                                        x-text="category.is_active ? 'Active' : 'Inactive'"></div>
                                                </div>
                                            </td>
                                            <td class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap"
                                                :class="density === 'condensed' ? 'py-2' : 'py-5'">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button @click="openViewModal(category)"
                                                        class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100"
                                                        title="View Detail">
                                                        <i class="fas fa-eye text-[10px]"></i>
                                                    </button>
                                                    <button @click="openEditModal(category)"
                                                        class="h-8 w-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-indigo-100"
                                                        title="Modify Node">
                                                        <i class="fas fa-edit text-[10px]"></i>
                                                    </button>
                                                    <button @click="confirmBulkAction('delete_single', category)"
                                                        class="h-8 w-8 flex items-center justify-center bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm border border-rose-100"
                                                        title="Purge Record">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Loading Overlay (Optional) --}}
                        <div x-show="loading"
                            class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                            <div class="flex flex-col items-center gap-4">
                                <div
                                    class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-lg">
                                </div>
                                <span
                                    class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing
                                    Vault...</span>
                            </div>
                        </div>

                    </div>

                    {{-- Premium Pagination --}}
                    <div x-show="!loading && categories.length > 0"
                        class="p-8 bg-slate-50 border-t border-slate-100 mt-auto rounded-b-3xl">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="flex flex-col md:flex-row items-center gap-4">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Displaying <span class="text-slate-900" x-text="pagination.from || 0"></span> - <span
                                        class="text-slate-900" x-text="pagination.to || 0"></span>
                                    <span
                                        class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span>
                                    Source: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- First Button --}}
                                <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                                    class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                                    <i class="fas fa-angles-left text-[10px]"></i>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                                </button>

                                {{-- Previous Button --}}
                                <button @click="changePage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page === 1"
                                    class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Prev</span>
                                </button>

                                <div class="flex items-center gap-1.5 mx-2">
                                    <template x-for="page in getPageRange()" :key="page">
                                        <button @click="page !== '...' && changePage(page)"
                                            :class="page === pagination.current_page ?
                                                'bg-indigo-600 text-white shadow-lg shadow-indigo-200 border-indigo-600 scale-105' :
                                                'bg-white text-slate-600 border-slate-200 hover:border-indigo-600 hover:text-indigo-600'"
                                            :disabled="page === '...'"
                                            class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"
                                            x-text="page">
                                        </button>
                                    </template>
                                </div>

                                {{-- Next Button --}}
                                <button @click="changePage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </button>

                                {{-- Last Button --}}
                                <button @click="changePage(pagination.last_page)"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Last</span>
                                    <i class="fas fa-angles-right text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Right Column - Security Filters Sidebar --}}
            <div class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2 group/sidebar"
                x-show="showSidebar" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-12">

                {{-- Unified Filter Card --}}
                <div
                    class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">

                    {{-- Master Header --}}
                    <div
                        class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="fas fa-filter text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 text-base tracking-tight">Category Filters</h2>
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine
                                    Catalog Data</p>
                            </div>
                        </div>
                        <button @click="showSidebar = false"
                            class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm"
                            title="Hide Filters">
                            <i class="fas fa-angle-right"></i>
                        </button>
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="overflow-y-auto scrollbar-hide flex-1 space-y-5 p-5" style="scrollbar-width: none;">

                        {{-- Active Intelligence --}}
                        <div x-show="hasActiveFilters()" class="space-y-2 pt-1">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active
                                Intelligence:</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <template x-if="searchQuery">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                        <i class="fas fa-search opacity-50"></i>
                                        <span x-text="searchQuery"></span>
                                        <button @click="searchQuery=''; searchCategories()"
                                            class="hover:text-rose-600 transition-colors"><i
                                                class="fas fa-times-circle"></i></button>
                                    </span>
                                </template>
                                <template x-if="filterStatus">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                        <i class="fas fa-check-circle opacity-50"></i>
                                        <span x-text="filterStatus === 'active' ? 'Active Only' : 'Inactive Only'"></span>
                                        <button @click="filterStatus=''; searchCategories()"
                                            class="hover:text-rose-600 transition-colors"><i
                                                class="fas fa-times-circle"></i></button>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div x-show="hasActiveFilters()" class="border-b border-dashed border-slate-200"></div>

                        {{-- Search Module --}}
                        <div class="space-y-3">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-search text-indigo-500"></i> Localize Point
                            </label>
                            <div class="relative group">
                                <input type="text" x-model.debounce.500ms="searchQuery" @input="searchCategories()"
                                    placeholder="Search Category Name..."
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                                <i
                                    class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                        </div>

                        {{-- Status Filter - Bento Toggle Style --}}
                        <div class="space-y-4">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-shield-virus text-emerald-500"></i> Status State
                            </label>
                            <div class="grid grid-cols-1 gap-2">
                                <button @click="filterStatus = ''; searchCategories()"
                                    :class="filterStatus === '' ?
                                        'bg-indigo-600 text-white shadow-lg shadow-indigo-200' :
                                        'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                    class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                    <span>Global Data</span>
                                    <i class="fas fa-globe-americas transition-opacity"
                                        :class="filterStatus === '' ? 'opacity-100' : 'opacity-40'"></i>
                                </button>
                                <button @click="filterStatus = 'active'; searchCategories()"
                                    :class="filterStatus === 'active' ?
                                        'bg-emerald-600 text-white shadow-lg shadow-emerald-200' :
                                        'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                    class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                    <span>Authorized Only</span>
                                    <i class="fas fa-check-circle transition-opacity"
                                        :class="filterStatus === 'active' ? 'opacity-100' : 'opacity-40'"></i>
                                </button>
                                <button @click="filterStatus = 'inactive'; searchCategories()"
                                    :class="filterStatus === 'inactive' ?
                                        'bg-gradient-to-r from-rose-600 to-rose-400 text-white shadow-lg shadow-rose-200' :
                                        'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                    class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                                    <span>Locked Vaults</span>
                                    <i class="fas fa-lock transition-opacity"
                                        :class="filterStatus === 'inactive' ? 'opacity-100' : 'opacity-40'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Page Density Filter --}}
                        <div class="space-y-3">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-compress-alt text-indigo-500"></i> Page Density
                            </label>
                            <div class="grid grid-cols-2 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                                <button @click="density = 'condensed'"
                                    :class="density === 'condensed' ? 'bg-white text-indigo-600 shadow-sm' :
                                        'text-slate-500 hover:text-slate-700'"
                                    class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                    Condensed
                                </button>
                                <button @click="density = 'spacious'"
                                    :class="density === 'spacious' ? 'bg-white text-indigo-600 shadow-sm font-black' :
                                        'text-slate-500 hover:text-slate-700'"
                                    class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                                    Spacious
                                </button>
                            </div>
                        </div>

                        {{-- Records Per Page - Bento Toggle Style --}}
                        <div class="space-y-3">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-ol text-indigo-500"></i> Records Per Page
                            </label>
                            <div class="grid grid-cols-4 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                                <template x-for="size in [10, 25, 50, 100]" :key="size">
                                    <button @click="pagination.per_page = size; searchCategories()"
                                        :class="pagination.per_page == size ? 'bg-white text-indigo-600 shadow-sm' :
                                            'text-slate-500 hover:text-slate-700'"
                                        class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all"
                                        x-text="size">
                                    </button>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- Clearance & Actions (Sticky Bottom) --}}
                    <div class="p-6 pt-0 flex flex-col gap-2 shrink-0">
                        <button @click="clearFilters()" class="rose-reset-btn w-full py-5 text-white rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 flex items-center justify-center gap-3 active:scale-95">
                            <i class="fas fa-broom"></i> Reset Filters
                        </button>
                        <button @click="showSidebar = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all flex items-center justify-between px-6">
                            <span>Hide Panel</span>
                            <i class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add/Edit Modal (Matching Premium Rounded aesthetic) --}}
        <div x-show="showAddModal" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showAddModal" class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm"
                    @click="closeAddModal()" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

                <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative inline-block w-full max-w-lg overflow-hidden text-left align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle border border-slate-100">

                    {{-- Modal Header --}}
                    <div
                        class="px-6 py-5 border-b border-indigo-100/50 bg-gradient-to-r from-blue-50 to-indigo-50 flex justify-between items-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm text-indigo-600 relative z-10">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight relative z-10"
                                x-text="editing ? 'Edit Category' : 'Create Category'"></h3>
                        </div>
                        <button @click="closeAddModal()"
                            class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors z-10 cursor-pointer shadow-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                        <form @submit.prevent="saveCategory" class="space-y-5">
                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label
                                    class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Category
                                    Name <span class="text-rose-500">*</span></label>
                                <input type="text" x-model="form.name" required placeholder="e.g. Antibiotics"
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                            </div>

                            <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                <label
                                    class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Description</label>
                                <textarea x-model="form.description" rows="3" placeholder="Brief description of this category..."
                                    class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400 resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5 focus-within:text-indigo-600 transition-colors">
                                    <label
                                        class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 flex items-center gap-2">Display
                                        Order</label>
                                    <input type="number" x-model="form.display_order" placeholder="0"
                                        class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:bg-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-400/10 transition-all font-bold text-slate-800 placeholder:text-slate-400">
                                </div>

                                <div class="space-y-1.5 flex flex-col justify-center">
                                    <label
                                        class="text-[10px] uppercase tracking-widest font-black text-slate-500 ml-1 mb-2">Category
                                        Status</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500">
                                        </div>
                                        <span class="ml-3 text-sm font-bold text-slate-700"
                                            x-text="form.is_active ? 'Active' : 'Inactive'"></span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="closeAddModal()"
                            class="px-5 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm cursor-pointer">
                            Cancel
                        </button>
                        <button type="button" @click="saveCategory()" :disabled="saving"
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 hover:-translate-y-0.5 transition-all shadow-md shadow-indigo-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                            <span x-text="saving ? 'Saving...' : (editing ? 'Update Category' : 'Commit Node')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Generic Confirmation Modal --}}
        <div x-show="showConfirmModal" class="fixed inset-0 z-[70] overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm"
                    @click="showConfirmModal = false"></div>

                <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 text-center border border-slate-100">
                    <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-6"
                        :class="confirmConfig.type === 'danger' ? 'bg-rose-100 text-rose-600' :
                            'bg-indigo-100 text-indigo-600'">
                        <i class="fas text-3xl" :class="confirmConfig.icon"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2" x-text="confirmConfig.title"></h3>
                    <p class="text-xs font-bold text-slate-500 mb-8 px-4 uppercase tracking-wider leading-relaxed"
                        x-text="confirmConfig.message"></p>

                    <div class="flex items-center justify-center gap-3">
                        <button @click="showConfirmModal = false"
                            class="px-5 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-colors w-full cursor-pointer">Abort
                            Action</button>
                        <button @click="executeConfirmedAction()" :disabled="confirming"
                            class="px-5 py-3 text-white rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-md w-full flex items-center justify-center gap-2 cursor-pointer"
                            :class="confirmConfig.type === 'danger' ?
                                'bg-gradient-to-r from-rose-500 to-rose-700 hover:from-rose-600 hover:to-rose-800 shadow-rose-500/30' :
                                'bg-gradient-to-r from-indigo-500 to-indigo-700 hover:from-indigo-600 hover:to-indigo-800 shadow-indigo-500/30'">
                            <i class="fas fa-spinner fa-spin" x-show="confirming"></i>
                            <span x-text="confirming ? 'Processing...' : confirmConfig.confirmText"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- View Modal --}}
        <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6" x-transition.opacity
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeViewModal"></div>

                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg z-10 overflow-hidden relative border border-slate-100"
                    x-transition.scale>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-400 p-8 text-white relative">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center shadow-xl border border-white/20">
                                    <i class="fas fa-folder text-xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black tracking-tight" x-text="dataToView?.name || 'Details'">
                                    </h3>
                                    <p class="text-slate-300 text-[10px] font-black uppercase tracking-widest mt-1">
                                        Category Data Record</p>
                                </div>
                            </div>
                            <button @click="closeViewModal"
                                class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors cursor-pointer">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Slug (URL
                                    string)</p>
                                <p class="font-mono text-sm text-slate-800 font-bold mt-1"
                                    x-text="dataToView?.slug || '-'"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Display Order
                                </p>
                                <div class="flex items-center mt-1">
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-indigo-50 text-indigo-600 border border-indigo-100"
                                        x-text="dataToView?.display_order || '0'"></span>
                                </div>
                            </div>
                            <div class="space-y-1 col-span-2 relative">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Description</p>
                                <p class="text-sm font-medium text-slate-700 mt-1 pr-4"
                                    x-text="dataToView?.description || 'No description provided'"></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Auth Status</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="dataToView?.is_active ? 'text-white bg-emerald-500 border border-emerald-200' :
                                            'text-white bg-rose-500 border border-rose-200'">
                                        <i class="fas"
                                            :class="dataToView?.is_active ? 'fa-check-circle' : 'fa-power-off'"></i>
                                        <span x-text="dataToView?.is_active ? 'Active' : 'Inactive'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                        <button @click="closeViewModal"
                            class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm cursor-pointer">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryManagement() {
            return {
                showSidebar: false,
                showViewModal: false,
                showAddModal: false,
                loading: false,
                saving: false,
                confirming: false,
                editing: false,

                categories: [],
                stats: {
                    total: 0,
                    active: 0,
                    inactive: 0
                },

                searchQuery: '',
                filterStatus: '',
                sortField: 'display_order',
                sortDirection: 'asc',
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },

                form: {
                    id: null,
                    name: '',
                    description: '',
                    display_order: 0,
                    is_active: true
                },
                density: 'spacious',
                selectedIds: [],
                dataToDelete: null,
                dataToView: null,

                // Confirmation Modal State
                showConfirmModal: false,
                confirming: false,
                confirmConfig: {
                    title: '',
                    message: '',
                    icon: '',
                    confirmText: '',
                    type: 'primary',
                    action: null
                },

                async init() {
                    await this.fetchCategories();
                    await this.fetchStats();
                },

                hasActiveFilters() {
                    return this.searchQuery !== '' || this.filterStatus !== '';
                },

                getSortIcon(field) {
                    if (this.sortField !== field) return 'fa-sort text-slate-300';
                    return this.sortDirection === 'asc' ? 'fa-sort-up text-indigo-600 scale-125' :
                        'fa-sort-down text-indigo-600 scale-125';
                },

                searchCategories() {
                    this.pagination.current_page = 1;
                    this.fetchCategories();
                },

                async fetchCategories() {
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

                    try {
                        const response = await fetch(`/pharmacy/medicine-categories/data?${params.toString()}`);
                        const data = await response.json();

                        this.categories = data.data;
                        this.pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            per_page: data.per_page,
                            total: data.total,
                            from: data.from,
                            to: data.to
                        };
                    } catch (error) {
                        window.Notification.error('Failed to load categories catalog');
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchStats() {
                    try {
                        const response = await fetch('/pharmacy/medicine-categories/stats');
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
                        this.fetchCategories();
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
                    this.fetchCategories();
                },

                clearFilters() {
                    this.searchQuery = '';
                    this.filterStatus = '';
                    this.sortField = 'display_order';
                    this.sortDirection = 'asc';
                    this.pagination.current_page = 1;
                    this.fetchCategories();
                },

                toggleAll(e) {
                    if (e.target.checked) {
                        this.selectedIds = this.categories.map(t => t.id);
                    } else {
                        this.selectedIds = [];
                    }
                },

                openViewModal(category) {
                    this.dataToView = category;
                    this.showViewModal = true;
                },

                closeViewModal() {
                    this.showViewModal = false;
                    setTimeout(() => {
                        this.dataToView = null;
                    }, 300);
                },

                openAddModal() {
                    this.editing = false;
                    this.form = {
                        id: null,
                        name: '',
                        description: '',
                        display_order: 0,
                        is_active: true
                    };
                    this.showAddModal = true;
                },

                openEditModal(category) {
                    this.editing = true;
                    this.form = {
                        ...category,
                        is_active: !!category.is_active
                    };
                    this.showAddModal = true;
                },

                closeAddModal() {
                    this.showAddModal = false;
                    setTimeout(() => {
                        this.form = {
                            id: null,
                            name: '',
                            description: '',
                            display_order: 0,
                            is_active: true
                        };
                    }, 300);
                },

                confirmDelete(category) {
                    this.dataToDelete = category;
                    this.showDeleteModal = true;
                },

                async toggleStatus(category) {
                    try {
                        const response = await fetch(`/pharmacy/medicine-categories/${category.id}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (response.ok) {
                            category.is_active = data.is_active;
                            window.Notification.success(data.message);
                            this.fetchStats();
                        } else {
                            window.Notification.error(data.message || 'Failed to update status');
                        }
                    } catch (error) {
                        window.Notification.error('A network error occurred');
                    }
                },

                async saveCategory() {
                    if (!this.form.name) {
                        window.Notification.warning('Please fill in required fields');
                        return;
                    }

                    this.saving = true;
                    const url = this.editing ? `/pharmacy/medicine-categories/${this.form.id}` :
                        '/pharmacy/medicine-categories';
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
                            await this.fetchCategories();
                            await this.fetchStats();
                        } else {
                            if (data.errors) {
                                window.Notification.error(Object.values(data.errors)[0][0]);
                            } else {
                                window.Notification.error(data.message || 'Failed to save Category');
                            }
                        }
                    } catch (error) {
                        window.Notification.error('A network error occurred');
                    } finally {
                        this.saving = false;
                    }
                },

                confirmBulkAction(type, item = null) {
                    switch (type) {
                        case 'activate':
                            this.confirmConfig = {
                                title: 'Bulk Activation',
                                message: `You are about to activate ${this.selectedIds.length} categories. Continue?`,
                                icon: 'fa-check-circle',
                                confirmText: 'Activate Now',
                                type: 'primary',
                                action: () => this.executeBulkUpdate(true)
                            };
                            break;
                        case 'deactivate':
                            this.confirmConfig = {
                                title: 'Bulk Deactivation',
                                message: `You are about to deactivate ${this.selectedIds.length} categories. Continue?`,
                                icon: 'fa-power-off',
                                confirmText: 'Deactivate Now',
                                type: 'danger',
                                action: () => this.executeBulkUpdate(false)
                            };
                            break;
                        case 'delete':
                            this.confirmConfig = {
                                title: 'Mass Purge',
                                message: `WARNING: You are about to permanently delete ${this.selectedIds.length} categories. This action is irreversible.`,
                                icon: 'fa-trash-alt',
                                confirmText: 'Purge Categories',
                                type: 'danger',
                                action: () => this.executeBulkDelete()
                            };
                            break;
                        case 'delete_single':
                            this.dataToDelete = item;
                            this.confirmConfig = {
                                title: 'Purge Category',
                                message: `Are you sure you want to permanently delete "${item.name}"? This will affect all associated inventory data.`,
                                icon: 'fa-exclamation-triangle',
                                confirmText: 'Confirm Purge',
                                type: 'danger',
                                action: () => this.executeDelete()
                            };
                            break;
                    }
                    this.showConfirmModal = true;
                },

                async executeConfirmedAction() {
                    if (this.confirmConfig.action) {
                        this.confirming = true;
                        await this.confirmConfig.action();
                        this.confirming = false;
                        this.showConfirmModal = false;
                    }
                },

                async executeBulkUpdate(isActive) {
                    try {
                        const response = await fetch('/pharmacy/medicine-categories/bulk-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: this.selectedIds,
                                is_active: isActive
                            })
                        });

                        if (response.ok) {
                            window.Notification.success(`Successfully updated ${this.selectedIds.length} categories`);
                            this.selectedIds = [];
                            await this.fetchCategories();
                            await this.fetchStats();
                        } else {
                            window.Notification.error('Failed to perform bulk status update');
                        }
                    } catch (error) {
                        window.Notification.error('A network error occurred');
                    }
                },

                async executeBulkDelete() {
                    try {
                        const response = await fetch('/pharmacy/medicine-categories/bulk-destroy', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: this.selectedIds
                            })
                        });

                        if (response.ok) {
                            window.Notification.success(`Successfully purged ${this.selectedIds.length} categories`);
                            this.selectedIds = [];
                            await this.fetchCategories();
                            await this.fetchStats();
                        } else {
                            window.Notification.error('Failed to perform mass purge');
                        }
                    } catch (error) {
                        window.Notification.error('A network error occurred');
                    }
                },

                async executeDelete() {
                    if (!this.dataToDelete) return;
                    try {
                        const response = await fetch(`/pharmacy/medicine-categories/${this.dataToDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.Notification.success('Category purged successfully');
                            if (this.categories.length === 1 && this.pagination.current_page > 1) {
                                this.pagination.current_page--;
                            }
                            await this.fetchCategories();
                            await this.fetchStats();
                        } else {
                            window.Notification.error('Failed to purge Category');
                        }
                    } catch (error) {
                        window.Notification.error('A network error occurred');
                    } finally {
                        this.dataToDelete = null;
                    }
                }
            };
        }
    </script>
@endsection
