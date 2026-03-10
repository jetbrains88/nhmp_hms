@extends('layouts.app')

@section('title', 'System Audit Trail - NHMP HMS')
@section('page-title', 'Security & Audit Logs')
@section('breadcrumb', 'Administration / Audit Logs')

@section('content')
<div x-data="auditManagement({{ json_encode($entityTypes) }}, {{ json_encode($actions) }})" x-init="init()" class="space-y-6">

<!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

        <!-- Total Trails Card — Blue like Total Users -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="clearFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-fingerprint text-2xl drop-shadow-md text-blue-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">Total Trails</p>
                <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono" x-text="stats.total ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                    <span class="text-xs text-blue-700 font-medium">All Recorded Events</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to clear filters & view all</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creations Card — Emerald -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="filterAction = 'created'; applyFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-plus-circle text-2xl drop-shadow-md text-emerald-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-emerald-600 uppercase">Creations</p>
                <h4 class="block antialiased text-3xl font-bold text-emerald-800 drop-shadow-md font-mono" x-text="stats.created ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600" :class="{ 'animate-pulse': stats.created > 0 }"></span>
                    <span class="text-xs text-emerald-700 font-medium">New Records Created</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to filter creation events</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Updates Card — Purple like Administrators -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="filterAction = 'updated'; applyFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-edit text-2xl drop-shadow-md text-purple-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">Updates</p>
                <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono" x-text="stats.updated ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.updated > 0 }"></span>
                    <span class="text-xs text-purple-700 font-medium">Record Modifications</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to filter update events</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deletions Card — Rose -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="filterAction = 'deleted'; applyFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-orange-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-trash-alt text-2xl drop-shadow-md text-rose-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">Deletions</p>
                <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono" x-text="stats.deleted ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600" :class="{ 'animate-pulse': stats.deleted > 0 }"></span>
                    <span class="text-xs text-rose-700 font-medium">Deleted Records</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to filter deletion events</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main List -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <!-- Header & Filters -->
        <div class="bg-gradient-to-r from-slate-50 to-indigo-50 p-8 border-b border-indigo-100">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                        System Audit Trail
                        <span class="text-lg font-bold text-slate-400 font-mono">(<span x-text="pagination.total"></span>)</span>
                    </h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Monitor and track all critical actions within the system</p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    <!-- Show Per Page -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-600">Show:</span>
                        <select x-model="pagination.per_page" @change="fetchLogs()"
                                class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <a :href="`/admin/audit-logs/export?${getQueryParams()}`"
                       class="flex items-center gap-2 px-5 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all font-bold text-sm shadow-lg shadow-emerald-100">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>

                    <button @click="clearFilters()"
                            class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all font-bold text-sm shadow-md shadow-blue-200">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>

                    <button @click="showAdvancedFilters = !showAdvancedFilters"
                            :class="showAdvancedFilters
                                ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 border-green-500 shadow-green-200'
                                : 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 border-orange-500 shadow-orange-200'"
                            class="flex items-center gap-2 px-4 py-2 text-white border rounded-xl transition-all font-bold text-sm shadow-md">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (Collapsible) -->
            <div x-show="showAdvancedFilters" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-8 bg-white p-6 rounded-2xl bg-gradient-to-r from-indigo-50/50 to-purple-50/50 border border-indigo-100 shadow-inner">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="relative md:col-span-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Search Records</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="applyFilters()" 
                                   placeholder="User, IP, Entity..." 
                                   class="pl-9 w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 text-sm">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Entity Type</label>
                        <select x-model="filterEntity" @change="applyFilters()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 text-sm appearance-none">
                            <option value="">All Entities</option>
                            <template x-for="type in entityTypes" :key="type">
                                <option :value="type" x-text="type.split('\\').pop()"></option>
                            </template>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Action Type</label>
                        <select x-model="filterAction" @change="applyFilters()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 text-sm appearance-none">
                            <option value="">All Actions</option>
                            <template x-for="action in actions" :key="action">
                                <option :value="action" x-text="action.charAt(0).toUpperCase() + action.slice(1)"></option>
                            </template>
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Performed By</label>
                        <select x-model="filterUserId" @change="applyFilters()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 text-sm appearance-none">
                            <option value="">All Users</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Branch</label>
                        <select x-model="filterBranchId" @change="applyFilters()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700 text-sm appearance-none">
                            <option value="">All Branches</option>
                            @foreach(\App\Models\Branch::orderBy('name')->get() as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Second filter row: Date range + Per page -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Date Range</label>
                        <div class="flex gap-2">
                            <input type="date" x-model="dateFrom" @change="applyFilters()"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700">
                            <div class="flex items-center text-slate-300">
                                <i class="fas fa-arrow-right text-xs"></i>
                            </div>
                            <input type="date" x-model="dateTo" @change="applyFilters()"
                                   class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Show Per Page</label>
                        <select x-model="pagination.per_page" @change="fetchLogs()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 outline-none font-bold text-slate-700 text-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters Summary -->
                <div x-show="searchQuery || filterEntity || filterAction || filterUserId || filterBranchId || dateFrom || dateTo"
                     class="flex flex-wrap items-center gap-2 mt-6 pt-4 border-t border-indigo-100">
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
        </div>

        <!-- Table Area -->
        <div class="overflow-x-auto min-h-[500px]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-600"></i>
                                Timestamp
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-600"></i>
                                Performed By
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-cube text-gray-600"></i>
                                Target Entity
                            </div>
                        </th>
                        <th class="px-8 py-5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-tag text-gray-600"></i>
                                Action
                            </div>
                        </th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-code-branch text-gray-600"></i>
                                Branch
                            </div>
                        </th>
                        <th class="px-8 py-5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end gap-2">
                                <i class="fas fa-search-plus text-gray-600"></i>
                                Details
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50" x-show="!loading">
                    <template x-for="log in logs" :key="log.id">
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="text-sm font-black text-gray-600" x-text="formatDate(log.created_at)"></div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter" x-text="formatTime(log.created_at)"></div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-black text-xs shadow-sm border border-indigo-100"
                                         x-text="getInitials(log.user?.name || 'SY')"></div>
                                    <div>
                                        <div class="text-sm font-black text-navy-800" x-text="log.user?.name || 'System Process'"></div>
                                        <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase" x-text="log.user ? `ID: ${log.user.id}` : 'Automated Task'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="p-2 bg-slate-50 rounded-xl text-slate-400 border border-slate-100">
                                        <i class="fas fa-cube text-xs"></i>
                                    </span>
                                    <div>
                                        <div class="text-sm font-black text-gray-600" x-text="log.entity_type.split('\\').pop()"></div>
                                        <div class="text-[10px] font-black text-indigo-400 tracking-wider" x-text="`REF ID: #${log.entity_id}`"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border"
                                      :class="getActionClass(log.action)"
                                      x-text="log.action"></span>
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-sm font-bold text-gray-600 italic" x-text="log.branch?.name || 'System Core'"></td>
                            <td class="px-8 py-5 text-right">
                                <button @click="viewDetails(log)"
                                        class="inline-flex items-center justify-center h-10 w-10 bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 rounded-xl transition-all hover:shadow-lg">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>

                <!-- Loading State -->
                <tbody x-show="loading">
                    <tr>
                        <td colspan="6" class="py-32 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-6"></div>
                                <p class="text-slate-500 font-black tracking-widest uppercase text-xs">Parsing security trails...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody x-show="!loading && logs.length === 0">
                    <tr>
                        <td colspan="6" class="py-32 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-24 w-24 rounded-3xl bg-slate-50 flex items-center justify-center mb-6">
                                    <i class="fas fa-fingerprint text-4xl text-slate-200"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-800">Clear Trails</h3>
                                <p class="text-slate-500 max-w-xs mx-auto text-sm font-medium mt-2">No activity logs found matching your current filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.last_page > 1" class="px-8 py-8 bg-slate-50/50 border-t border-slate-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">
                    Showing <span class="text-slate-800" x-text="pagination.from"></span> - <span class="text-slate-800" x-text="pagination.to"></span> of <span class="text-slate-800" x-text="pagination.total"></span> trails
                </p>
                <nav class="flex items-center gap-2">
                    <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="p-2.5 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 disabled:opacity-30 transition-all">
                        <i class="fas fa-angle-double-left text-xs"></i>
                    </button>
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-600 hover:bg-slate-50 disabled:opacity-30 transition-all">
                        PREVIOUS
                    </button>
                    <div class="px-5 py-2.5 bg-indigo-600 rounded-xl text-xs font-black text-white shadow-lg shadow-indigo-100">
                        PAGE <span x-text="pagination.current_page"></span>
                    </div>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-600 hover:bg-slate-50 disabled:opacity-30 transition-all">
                        NEXT
                    </button>
                    <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                            class="p-2.5 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 disabled:opacity-30 transition-all">
                        <i class="fas fa-angle-double-right text-xs"></i>
                    </button>
                </nav>
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
        showAdvancedFilters: false,
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

        init() {
            this.fetchStats();
            this.fetchLogs();
        },

        async fetchStats() {
            try {
                const response = await fetch('/admin/audit-logs/stats');
                this.stats = await response.json();
            } catch (error) {
                console.error('Stats error:', error);
            }
        },

        async fetchLogs() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
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
            this.pagination.current_page = page;
            this.fetchLogs();
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
                'created': 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'updated': 'bg-sky-50 text-sky-600 border-sky-100',
                'deleted': 'bg-rose-50 text-rose-600 border-rose-100'
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
@endsection
