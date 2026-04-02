@extends('layouts.app')

@section('title', 'System Audit Trail - NHMP HMS')
@section('page-title', 'Security & Audit Logs')
@section('breadcrumb', 'Administration / Audit Logs')

@section('content')
<div x-data="auditManagement({{ json_encode($entityTypes) }}, {{ json_encode($actions) }})" x-init="init()" x-cloak class="space-y-8 relative">

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
        title="Open Audit Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">Audit Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">

        <!-- Total Trails Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-fingerprint text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Total Trails</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                    <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">All Recorded Events</span>
                </div>
            </div>
        </div>

        <!-- Creations Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterAction = 'created'; applyFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plus-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Creations</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.created ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600" :class="{ 'animate-pulse': stats.created > 0 }"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">New Records</span>
                </div>
            </div>
        </div>

        <!-- Updates Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterAction = 'updated'; applyFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-edit text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-500 uppercase">Updates</p>
                <h4 class="text-3xl font-bold text-purple-700 drop-shadow-sm font-mono" x-text="stats.updated ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.updated > 0 }"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Record Modifications</span>
                </div>
            </div>
        </div>

        <!-- Deletions Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterAction = 'deleted'; applyFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-orange-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-trash-alt text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Deletions</p>
                <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.deleted ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-200 pt-2 text-rose-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600" :class="{ 'animate-pulse': stats.deleted > 0 }"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Deleted Records</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">
            
            {{-- Panel Header with Light Gradient --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-shield-alt text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                System Audit Trail
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Monitor and track all critical actions within the system</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="fetchLogs()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <a :href="`/admin/audit-logs/export?${getQueryParams()}`"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-file-csv group-hover:-translate-y-1 transition-transform duration-300"></i>
                            Export CSV
                        </a>

                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>

                        <button @click="fetchLogs()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            title="Refresh">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>

                <!-- Active Filters Summary Inline Row -->
                <div x-show="searchQuery || filterEntity || filterAction || filterUserId || filterBranchId || dateFrom || dateTo"
                     class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-indigo-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2">Active filters:</span>
                    
                    <template x-if="searchQuery">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                            <i class="fas fa-search text-[10px]"></i>
                            <span x-text="searchQuery"></span>
                            <button @click="searchQuery = ''; applyFilters()" class="hover:text-blue-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <template x-if="filterEntity">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100">
                            <i class="fas fa-cube text-[10px]"></i>
                            <span x-text="filterEntity.split('\\').pop()"></span>
                            <button @click="filterEntity = ''; applyFilters()" class="hover:text-indigo-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <template x-if="filterAction">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                            <i class="fas fa-tag text-[10px]"></i>
                            <span x-text="filterAction"></span>
                            <button @click="filterAction = ''; applyFilters()" class="hover:text-emerald-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <template x-if="filterUserId">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-violet-50 text-violet-700 rounded-lg text-xs font-bold border border-violet-100">
                            <i class="fas fa-user text-[10px]"></i>
                            <span x-text="getUserLabel(filterUserId)"></span>
                            <button @click="filterUserId = ''; applyFilters()" class="hover:text-violet-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <template x-if="filterBranchId">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-cyan-50 text-cyan-700 rounded-lg text-xs font-bold border border-cyan-100">
                            <i class="fas fa-code-branch text-[10px]"></i>
                            <span x-text="getBranchLabel(filterBranchId)"></span>
                            <button @click="filterBranchId = ''; applyFilters()" class="hover:text-cyan-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <template x-if="dateFrom || dateTo">
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100">
                            <i class="fas fa-calendar text-[10px]"></i>
                            <span x-text="`${dateFrom || '...'} to ${dateTo || '...'}`"></span>
                            <button @click="dateFrom = ''; dateTo = ''; applyFilters()" class="hover:text-amber-900"><i class="fas fa-times"></i></button>
                        </span>
                    </template>

                    <button @click="clearFilters()" class="text-[10px] font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest ml-auto transition-colors">
                        Clear All
                    </button>
                </div>
            </div>

            {{-- View Content (Table Area) --}}
            <div class="relative min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">
                            <tr>
                                <th class="px-5 py-5 text-left " >
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center text-sky-500 shadow-sm border border-sky-200 transition-all">
                                            <i class="fas fa-clock text-xs"></i>
                                        </div>
                                        <span>Timestamp</span>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('user_id')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <span>Performed By</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'user_id'"></i>
                                        <i class="fas fa-sort-up ml-1 text-indigo-500" x-show="sortField === 'user_id' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-indigo-500" x-show="sortField === 'user_id' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('entity_type')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500 shadow-sm border border-purple-200 transition-all">
                                            <i class="fas fa-cube text-xs"></i>
                                        </div>
                                        <span>Target Entity</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'entity_type'"></i>
                                        <i class="fas fa-sort-up ml-1 text-purple-500" x-show="sortField === 'entity_type' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-purple-500" x-show="sortField === 'entity_type' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('action')">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-200 transition-all">
                                            <i class="fas fa-tag text-xs"></i>
                                        </div>
                                        <span>Action</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'action'"></i>
                                        <i class="fas fa-sort-up ml-1 text-emerald-500" x-show="sortField === 'action' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-emerald-500" x-show="sortField === 'action' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('branch_id')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shadow-sm border border-amber-200 transition-all">
                                            <i class="fas fa-code-branch text-xs"></i>
                                        </div>
                                        <span>Branch</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'branch_id'"></i>
                                        <i class="fas fa-sort-up ml-1 text-amber-500" x-show="sortField === 'branch_id' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-amber-500" x-show="sortField === 'branch_id' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-right " >
                                    <div class="flex items-center justify-end gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 shadow-sm border border-slate-200 transition-all">
                                            <i class="fas fa-search-plus text-xs"></i>
                                        </div>
                                        <span>Details</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="log in logs" :key="log.id">
                                <tr class="hover:bg-indigo-50/40 transition-colors group">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm font-black text-gray-600" x-text="formatDate(log.created_at)"></div>
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter" x-text="formatTime(log.created_at)"></div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-black text-xs shadow-sm border border-indigo-100 group-hover:scale-105 transition-transform"
                                                 x-text="getInitials(log.user?.name || 'SY')"></div>
                                            <div>
                                                <div class="text-sm font-extrabold text-navy-800" x-text="log.user?.name || 'System Process'"></div>
                                                <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase" x-text="log.user ? `ID: ${log.user.id}` : 'Automated Task'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <span class="h-10 w-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 border border-slate-100">
                                                <i class="fas fa-cube text-xs"></i>
                                            </span>
                                            <div>
                                                <div class="text-sm font-black text-gray-600" x-text="log.entity_type.split('\\').pop()"></div>
                                                <div class="text-[10px] font-bold text-indigo-400 tracking-wider uppercase" x-text="`Ref: #${log.entity_id}`"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-sm border"
                                              :class="getActionClass(log.action)"
                                              x-text="log.action"></span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs font-bold text-gray-600" x-text="log.branch?.name || 'System Core'"></td>
                                    <td class="px-5 py-4 text-right">
                                        <button @click="viewDetails(log)"
                                                class="inline-flex items-center justify-center h-9 w-9 bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 rounded-xl transition-all hover:shadow-lg tooltip" title="View Audit Logs">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Loading State --}}
                <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-lg"></div>
                        <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing Trails...</span>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="!loading && logs.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-8 border-4 border-white shadow-xl">
                        <i class="fas fa-fingerprint text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">No Records Found</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The trail appears empty for the current criteria. Try adjusting filters.</p>
                    <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Filters</button>
                </div>
            </div>

            {{-- Premium Pagination --}}
            <div x-show="!loading && logs.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
                <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex flex-col md:flex-row items-center gap-4">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                            Displaying <span class="text-slate-900" x-text="pagination.from"></span> - <span class="text-slate-900" x-text="pagination.to"></span> 
                            <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                            Source: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <i class="fas fa-angles-left text-[10px]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                        </button>
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

                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                            <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
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
            
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">
                <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Audit Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Logs</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm" title="Hide Filters">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </div>

                <div class="overflow-y-auto scrollbar-hide flex-1 space-y-5 p-5" style="scrollbar-width: none;">
                    <div class="space-y-4">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-search text-indigo-500"></i> Localize Point
                            </label>
                            <div class="relative group">
                                <input type="text" x-model="searchQuery" @input.debounce.500ms="applyFilters()"
                                    placeholder="Search logs..."
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-tags text-purple-500"></i> Entity Type
                            </label>
                            <select x-model="filterEntity" @change="applyFilters()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Entities</option>
                                <template x-for="type in entityTypes" :key="type">
                                    <option :value="type" x-text="type.split('\\').pop()"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-shield-virus text-emerald-500"></i> Action Type
                            </label>
                            <select x-model="filterAction" @change="applyFilters()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Actions</option>
                                <template x-for="action in actions" :key="action">
                                    <option :value="action" x-text="action.charAt(0).toUpperCase() + action.slice(1)"></option>
                                </template>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-user text-indigo-500"></i> Performed By
                            </label>
                            <select x-model="filterUserId" @change="applyFilters()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Users</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-code-branch text-purple-500"></i> Branch
                            </label>
                            <select x-model="filterBranchId" @change="applyFilters()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Branches</option>
                                @foreach(\App\Models\Branch::orderBy('name')->get() as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3 border-t border-slate-100 pt-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-amber-500"></i> Date Window
                            </label>
                            <div class="flex flex-col gap-2">
                                <input type="date" x-model="dateFrom" @change="applyFilters()"
                                    class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-600 text-xs shadow-inner">
                                <div class="text-center text-slate-400 text-xs font-black uppercase">to</div>
                                <input type="date" x-model="dateTo" @change="applyFilters()"
                                    class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-600 text-xs shadow-inner">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-ol text-blue-500"></i> Display Rows
                            </label>
                            <div class="relative group">
                                <select x-model="pagination.per_page" @change="fetchLogs()"
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-xs shadow-inner appearance-none cursor-pointer group-hover:border-slate-200">
                                    <option value="10">10 Rows per page</option>
                                    <option value="20">20 Rows per page</option>
                                    <option value="50">50 Rows per page</option>
                                    <option value="100">100 Rows per page</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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

    <!-- Detail Modal -->
    @include('admin.audit.modals.view-details-modal')

</div>

<script>
function auditManagement(entityTypes = [], actions = []) {
    return {
        // Data
        entityTypes: entityTypes,
        actions: actions,
        logs: [],
        stats: {},
        loading: false,
        sortField: 'id',
        sortDirection: 'desc',
        showSidebar: false,
        searchQuery: '',

        // Filters
        filterEntity: '{{ request('entity_type') }}',
        filterAction: '{{ request('action') }}',
        filterUserId: '{{ request('user_id') }}',
        filterBranchId: '{{ request('branch_id') }}',
        dateFrom: '{{ request('date_from') }}',
        dateTo: '{{ request('date_to') }}',

        // Modals
        showDetailsModal: false,
        selectedLog: null,

        // Pagination
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 20,
            from: 0,
            to: 0
        },

        
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            if(typeof this.applyFilters === 'function') {
                this.applyFilters();
            } else if(typeof this.fetchData === 'function') {
                this.fetchData();
            }
        },

        init() {
            this.fetchStats();
            this.fetchLogs();
        },

        async fetchStats() {
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    entity_type: this.filterEntity,
                    action: this.filterAction,
                    user_id: this.filterUserId,
                    branch_id: this.filterBranchId,
                    date_from: this.dateFrom,
                    date_to: this.dateTo
                });
                const response = await fetch(`/admin/audit-logs/stats?${params}`);
                this.stats = await response.json();
            } catch (error) {
                console.error('Stats error:', error);
            }
        },

        async fetchLogs() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    sort: this.sortField,
                    direction: this.sortDirection,
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    search: this.searchQuery,
                    entity_type: this.filterEntity,
                    action: this.filterAction,
                    user_id: this.filterUserId,
                    branch_id: this.filterBranchId,
                    date_from: this.dateFrom,
                    date_to: this.dateTo
                });

                const response = await fetch(`/admin/audit-logs/data?${params}`);
                const data = await response.json();

                this.logs = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total,
                    per_page: data.per_page,
                    from: data.from,
                    to: data.to
                };
            } catch (error) {
                console.error('Fetch error:', error);
            } finally {
                this.loading = false;
            }
        },

        getQueryParams() {
            return new URLSearchParams({
                sort: this.sortField,
                direction: this.sortDirection,
                search: this.searchQuery,
                entity_type: this.filterEntity,
                action: this.filterAction,
                user_id: this.filterUserId,
                branch_id: this.filterBranchId,
                date_from: this.dateFrom,
                date_to: this.dateTo
            }).toString();
        },

        applyFilters() {
            this.pagination.current_page = 1;
            this.fetchLogs();
            this.fetchStats();
        },

        clearFilters() {
            this.searchQuery = '';
            this.filterEntity = '';
            this.filterAction = '';
            this.filterUserId = '';
            this.filterBranchId = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.applyFilters();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchLogs();
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

        viewDetails(log) {
            this.selectedLog = log;
            this.showDetailsModal = true;
        },

        // Helpers
        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        },

        formatFullDate(dateStr) {
            return new Date(dateStr).toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        },

        formatTime(dateStr) {
            return new Date(dateStr).toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        },

        getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        },

        getActionClass(action) {
            const classes = {
                'created': 'bg-emerald-50 text-emerald-600 border-emerald-200',
                'updated': 'bg-sky-50 text-sky-600 border-sky-200',
                'deleted': 'bg-rose-50 text-rose-600 border-rose-200'
            };
            return classes[action] || 'bg-slate-50 text-slate-600 border-slate-200';
        },

        getUserLabel(id) {
            const el = document.querySelector(`select[x-model="filterUserId"] option[value="${id}"]`);
            return el ? el.textContent.trim() : `User #${id}`;
        },

        getBranchLabel(id) {
            const el = document.querySelector(`select[x-model="filterBranchId"] option[value="${id}"]`);
            return el ? el.textContent.trim() : `Branch #${id}`;
        },

        formatFieldName(field) {
            return field.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
        }
    }
}
</script>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
