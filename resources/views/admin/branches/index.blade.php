@extends('layouts.app')

@section('title', 'Branch Management - NHMP HMS')
@section('page-title', 'Branch Management')
@section('breadcrumb', 'Administration / Branches')

@section('content')
<div x-data="branchManagement({{ json_encode($offices) }})" x-init="init()" x-cloak class="space-y-8 relative">

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
        title="Open Branch Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">Branch Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 gap-y-10 mt-4">

        <!-- Total Branches Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-hospital text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Total Branches</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                    <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">All Hospital Branches</span>
                </div>
            </div>
        </div>

        <!-- Active Centers Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterStatus = 'active'; applyFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active Centers</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600" :class="{ 'animate-pulse': stats.active > 0 }"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Currently Operating</span>
                </div>
            </div>
        </div>

        <!-- Global Staff Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="window.location.href='/admin/users'">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-500 uppercase">Global Staff</p>
                <h4 class="text-3xl font-bold text-purple-700 drop-shadow-sm font-mono" x-text="stats.staff ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.staff > 0 }"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Total System Users</span>
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
                            <i class="fas fa-hospital-alt text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                Hospital Network
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Manage medical centers and administrative offices</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="fetchBranches()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <button @click="openAddModal()"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            New Branch
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        <button @click="fetchBranches()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            title="Refresh">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- View Content --}}
            <div class="relative min-h-[400px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">
                            <tr>
                                <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('branch_id')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-hospital text-xs"></i>
                                        </div>
                                        <span>Branch Profile</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'branch_id'"></i>
                                        <i class="fas fa-sort-up ml-1 text-indigo-500" x-show="sortField === 'branch_id' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-indigo-500" x-show="sortField === 'branch_id' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('location')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shadow-sm border border-amber-200 transition-all">
                                            <i class="fas fa-map-marker-alt text-xs"></i>
                                        </div>
                                        <span>Location Info</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'location'"></i>
                                        <i class="fas fa-sort-up ml-1 text-amber-500" x-show="sortField === 'location' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-amber-500" x-show="sortField === 'location' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center " >
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-users text-xs"></i>
                                        </div>
                                        <span>Staff</span>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('is_active')">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-toggle-on text-xs"></i>
                                        </div>
                                        <span>Status</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'is_active'"></i>
                                        <i class="fas fa-sort-up ml-1 text-indigo-500" x-show="sortField === 'is_active' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-indigo-500" x-show="sortField === 'is_active' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('action')">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 shadow-sm border border-slate-200 transition-all">
                                            <i class="fas fa-cogs text-xs"></i>
                                        </div>
                                        <span>Actions</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'action'"></i>
                                        <i class="fas fa-sort-up ml-1 text-slate-500" x-show="sortField === 'action' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-slate-500" x-show="sortField === 'action' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="branch in branches" :key="branch.id">
                                <tr class="hover:bg-indigo-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-11 w-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-indigo-400 to-indigo-600 text-white text-xl font-bold shadow-sm group-hover:scale-105 transition-transform">
                                                <i class="fas fa-hospital text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="branch.name"></p>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-tighter" x-text="branch.type"></span>
                                                    <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="branch.office?.name || 'Level 1'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-map-marker-alt text-slate-300"></i>
                                            <span class="text-xs font-bold" x-text="branch.location || 'Headquarters'"></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-100">
                                            <span x-text="branch.users?.length || 0"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button @click="toggleBranchStatus(branch)"
                                                class="relative inline-flex items-center h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                :class="branch.is_active ? 'bg-emerald-500' : 'bg-slate-200'">
                                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                                                  :class="branch.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                        </button>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1 w-full max-w-[120px] mx-auto">
                                            <a :href="`/admin/branches/${branch.id}`" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-search-plus"></i> View
                                            </a>
                                            <button @click="editBranch(branch)" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="Modify">
                                                <i class="fas fa-edit"></i> Modify
                                            </button>
                                            <button @click="confirmArchive(branch)" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-rose-600 hover:text-rose-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="Archive">
                                                <i class="fas fa-trash-alt"></i> Archive
                                            </button>
                                        </div>
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
                        <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing Network...</span>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="!loading && branches.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-8 border-4 border-white shadow-xl">
                        <i class="fas fa-hospital text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 mb-2">No Branches Found</h3>
                    <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The network appears empty for current criteria. Perhaps adjust your filters?</p>
                    <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Filters</button>
                </div>
            </div>

            {{-- Premium Pagination --}}
            <div x-show="!loading && branches.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
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
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Network</p>
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
                                <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchBranches()"
                                    placeholder="Search branches..."
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-tags text-purple-500"></i> Segment Type
                            </label>
                            <select x-model="filterType" @change="fetchBranches()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Types</option>
                                <option value="CMO">CMO</option>
                                <option value="RMO">RMO</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-shield-virus text-emerald-500"></i> Status
                            </label>
                            <select x-model="filterStatus" @change="fetchBranches()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                <option value="">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-ol text-blue-500"></i> Display Rows
                            </label>
                            <div class="relative group">
                                <select x-model="pagination.per_page" @change="fetchBranches()"
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-xs shadow-inner appearance-none cursor-pointer group-hover:border-slate-200">
                                    <option value="10">10 Rows per page</option>
                                    <option value="15">15 Rows per page</option>
                                    <option value="25">25 Rows per page</option>
                                    <option value="50">50 Rows per page</option>
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

    <!-- Modals -->
    @include('admin.branches.modals.add-edit-branch-modal')
    @include('admin.branches.modals.delete-branch-modal')

</div>

<script>
function branchManagement(availableOffices = []) {
    return {
        // State
        availableOffices: availableOffices,
        branches: [],
        stats: {},
        loading: false,
        sortField: 'id',
        sortDirection: 'desc',
        saving: false,
        deleting: false,

        // Filters
        searchQuery: '',
        filterType: '',
        filterStatus: '',
        filterOffice: '',
        showSidebar: false,

        // Modals
        showBranchModal: false,
        showDeleteModal: false,
        editingBranch: false,
        branchToDelete: null,

        // Form
        branchForm: {
            id: null,
            name: '',
            type: 'CMO',
            location: '',
            office_id: '',
            is_active: true
        },

        // Pagination
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 10,
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
            this.fetchBranches();
        },

        async fetchStats() {
            try {
                const response = await fetch('/admin/branches/stats');
                this.stats = await response.json();
            } catch (error) {
                console.error('Stats error:', error);
            }
        },

        async fetchBranches() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    sort: this.sortField,
                    direction: this.sortDirection,
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    search: this.searchQuery,
                    type: this.filterType,
                    active: this.filterStatus === 'active' ? '1' : '',
                    inactive: this.filterStatus === 'inactive' ? '1' : '',
                    office_id: this.filterOffice
                });

                const response = await fetch(`/admin/branches/data?${params}`);
                const data = await response.json();

                this.branches = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total,
                    per_page: data.per_page,
                    from: data.from,
                    to: data.to
                };
            } catch (error) {
                window.showNotification('Error loading branch data', 'error');
            } finally {
                this.loading = false;
            }
        },

        applyFilters() {
            this.pagination.current_page = 1;
            this.fetchBranches();
        },

        clearFilters() {
            this.searchQuery = '';
            this.filterType = '';
            this.filterStatus = '';
            this.filterOffice = '';
            this.applyFilters();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchBranches();
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

        openAddModal() {
            this.editingBranch = false;
            this.branchForm = {
                id: null,
                name: '',
                type: 'CMO',
                location: '',
                office_id: '',
                is_active: true
            };
            this.showBranchModal = true;
        },

        async editBranch(branch) {
            this.editingBranch = true;
            this.branchForm = { ...branch };
            this.showBranchModal = true;
        },

        closeBranchModal() {
            this.showBranchModal = false;
        },

        async saveBranch() {
            this.saving = true;
            try {
                const method = this.editingBranch ? 'PUT' : 'POST';
                const url = this.editingBranch ? `/admin/branches/${this.branchForm.id}` : '/admin/branches';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.branchForm)
                });

                const result = await response.json();
                if (result.success) {
                    window.showNotification(result.message, 'success');
                    this.closeBranchModal();
                    this.fetchBranches();
                    this.fetchStats();
                } else {
                    window.showNotification(result.message || 'Operation failed', 'error');
                }
            } catch (error) {
                window.showNotification('Network error occurred', 'error');
            } finally {
                this.saving = false;
            }
        },

        async toggleBranchStatus(branch) {
            try {
                const response = await fetch(`/admin/branches/${branch.id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    branch.is_active = !branch.is_active;
                    this.fetchStats();
                    window.showNotification('Status updated', 'success');
                }
            } catch (error) {
                window.showNotification('Update failed', 'error');
            }
        },

        confirmArchive(branch) {
            this.branchToDelete = branch;
            this.showDeleteModal = true;
        },

        async deleteBranch() {
            this.deleting = true;
            try {
                const response = await fetch(`/admin/branches/${this.branchToDelete.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.success || response.status === 200) {
                    window.showNotification('Branch archived successfully', 'success');
                    this.showDeleteModal = false;
                    this.fetchBranches();
                    this.fetchStats();
                }
            } catch (error) {
                window.showNotification('Archive failed', 'error');
            } finally {
                this.deleting = false;
            }
        }
    }
}
</script>
@endsection