@extends('layouts.app')

@section('title', 'Permissions Management - NHMP HMS')
@section('page-title', 'Permissions Management')
@section('breadcrumb', 'Administration / Permissions')

@section('content')
<div x-data="permissionManagement()" x-init="init()" x-cloak class="space-y-8 relative">

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
        title="Open Permission Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">Security Filters</span>
    </button>

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">
        <!-- Total Permissions Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-shield-alt text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Total Permissions</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600 animate-pulse"></span>
                    <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">System-wide Access Points</span>
                </div>
            </div>
        </div>

        <!-- Active Permissions Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterStatus = 'active'; searchPermissions()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-circle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Active</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Currently Functional</span>
                </div>
            </div>
        </div>

        <!-- Inactive Permissions Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="filterStatus = 'inactive'; searchPermissions()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-pink-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-ban text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Inactive</p>
                <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.inactive">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-200 pt-2 text-rose-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Disabled Access</span>
                </div>
            </div>
        </div>

        <!-- Groups Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-fuchsia-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="viewMode = 'grid'">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-fuchsia-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-fuchsia-500 uppercase">Module Groups</p>
                <h4 class="text-3xl font-bold text-fuchsia-700 drop-shadow-sm font-mono" x-text="stats.groups">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-fuchsia-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-fuchsia-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Logical Categories</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Permission Vault Table --}}
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col">
            
            {{-- Panel Header with Light Gradient (Matches User Management) --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                            <i class="fas fa-shield-alt text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                Permission Vault
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Control and access management system</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        {{-- Rows Selector moved here --}}
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="searchPermissions()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
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
                            Add Permission
                        </button>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchPermissions()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
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
                     class="bg-indigo-600 px-6 py-3 flex items-center justify-between text-white sticky top-[68px] z-10 shadow-2xl">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                            <span x-text="selectedIds.length"></span> Access Points Selected
                        </span>
                        <div class="flex items-center gap-2">
                            <button @click="bulkStatus(true)" class="px-3 py-1.5 bg-white/10 hover:bg-white text-white hover:text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Authorize All</button>
                            <button @click="bulkStatus(false)" class="px-3 py-1.5 bg-white/10 hover:bg-white text-white hover:text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Lock Selection</button>
                            <button @click="bulkDelete()" class="px-3 py-1.5 bg-rose-500/80 hover:bg-rose-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Purge Security</button>
                        </div>
                    </div>
                    <button @click="selectedIds = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2">
                        Dismiss <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- View Content --}}
                <div class="relative min-h-[400px]">
                    {{-- Table View --}}
                    <div x-show="viewMode === 'table'" x-transition>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">

                            <tr>
                                <th class="px-5 py-5 w-10">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected()"
                                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all shadow-sm">
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                            <i class="fas fa-fingerprint text-xs"></i>
                                        </div>
                                        <button @click="sortBy('display_name')" class="flex items-center gap-1.5 hover:text-navy-800 transition-colors group">
                                            Access Point
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('display_name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                            <i class="fas fa-cubes text-xs"></i>
                                        </div>
                                        <button @click="sortBy('group')" class="flex items-center gap-1.5 hover:text-navy-800 transition-colors group">
                                            Module
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('group')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5">
                                    <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                            <i class="fas fa-toggle-on text-xs"></i>
                                        </div>
                                        <button @click="sortBy('is_active')" class="flex items-center gap-1.5 hover:text-navy-800 transition-colors group">
                                            Status
                                            <i class="fas text-[10px] transition-transform duration-300" :class="getSortIcon('is_active')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                            <i class="fas fa-bolt text-xs"></i>
                                        </div>
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="permission in permissions" :key="permission.id">
                                <tr class="hover:bg-blue-50/40 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :value="permission.id" x-model="selectedIds"
                                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform"
                                                 :class="permission.is_active ? 'bg-indigo-600 text-white' : 'bg-maroon-600 text-white'">
                                                <i class="fas fa-shield-alt text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="permission.display_name"></p>
                                                <p class="text-[10px] font-mono text-gray-400" x-text="permission.name"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold"
                                            :class="getGroupClass(permission.group)">
                                            <i class="fas mr-2" :class="getGroupIcon(permission.group)"></i>
                                            <span x-text="formatGroupName(permission.group)"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] text-gray-700 font-bold uppercase tracking-wider transition-all">
                                            <i class="fas" :class="permission.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                            <span x-text="permission.is_active ? 'Authorized' : 'Locked'"></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <button @click="openViewModal(permission)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button @click="editPermission(permission)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="Edit Controller">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button @click="toggleStatus(permission)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas" :class="permission.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                                <span x-text="permission.is_active ? 'Deactivate' : 'Activate'"></span>
                                            </button>
                                            <button @click="confirmDelete(permission)" class="w-full flex items-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Grid View --}}
            <div x-show="viewMode === 'grid'" x-transition class="p-6">
                <div class="space-y-12">
                    <template x-for="(groupPerms, groupName) in groupedPermissions" :key="groupName">
                        <div>
                            <div class="flex items-center gap-4 mb-6 sticky top-0 z-10 bg-white py-2 border-b border-slate-50">
                                <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100 shadow-sm">
                                    <i :class="getGroupIcon(groupName)" class="text-lg"></i>
                                </div>
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-[0.2em]" x-text="formatGroupName(groupName)"></h3>
                                <div class="flex-1 border-b border-dashed border-slate-200"></div>
                                <span class="bg-indigo-600 text-white px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20" x-text="groupPerms.length + ' points'"></span>
                            </div>
                            <div class="grid grid-cols-1 gap-4 transition-all duration-300" :class="showSidebar ? 'md:grid-cols-2 xl:grid-cols-3' : 'md:grid-cols-2 xl:grid-cols-2'">
                                <template x-for="p in groupPerms" :key="p.id">
                                    <div class="bg-white border-2 border-indigo-500 rounded-3xl p-5 hover:border-frusia-500 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all group relative overflow-hidden"
                                        :class="selectedIds.includes(p.id) ? 'border-indigo-500 bg-indigo-50/30' : ''">
                                        
                                        <div class="absolute top-0 right-0 p-3">
                                            <input type="checkbox" :value="p.id" x-model="selectedIds" class="w-5 h-5 rounded border-indigo-500 text-indigo-600 focus:ring-frusia-500">
                                        </div>

                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white transition-all shadow-inner"
                                            :class="p.is_active ? 'bg-indigo-600' : 'bg-maroon-600'">
                                                <i class="fas fa-shield-alt text-lg"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-black text-navy-800 text-sm leading-tight mb-1" x-text="p.display_name"></h4>
                                                <span class="text-[9px] font-mono text-gray-400 uppercase tracking-tighter" x-text="p.name"></span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider transition-all"
                                                      :class="p.is_active ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
                                                    <i class="fas" :class="p.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                                    <span x-text="p.is_active ? 'Authorized' : 'Locked'"></span>
                                                </span>
                                            </div>
                                            <div class="flex gap-1">
                                                <button @click="openViewModal(p)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-sky-600 text-white hover:bg-sky-500 hover:text-white transition-all shadow-sm" title="View"><i class="fas fa-eye text-[10px]"></i></button>
                                                <button @click="editPermission(p)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-600 text-white hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Edit"><i class="fas fa-edit text-[10px]"></i></button>
                                                <button @click="toggleStatus(p)" class="w-8 h-8 flex items-center justify-center rounded-lg transition-all shadow-sm" :class="p.is_active ? 'bg-amber-600 text-white hover:bg-amber-500 hover:text-white' : 'bg-emerald-600 text-white hover:bg-emerald-500 hover:text-white'" :title="p.is_active ? 'Deactivate' : 'Activate'"><i class="fas text-[10px]" :class="p.is_active ? 'fa-ban' : 'fa-check-circle'"></i></button>
                                                <button @click="confirmDelete(p)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-600 text-white hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Delete"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Loading State --}}
            <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-lg"></div>
                    <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing Vault...</span>
                </div>
            </div>

            {{-- Empty State --}}
            <div x-show="!loading && permissions.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-8 border-4 border-white shadow-xl">
                    <i class="fas fa-ghost text-4xl text-slate-300"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">No Access Points Found</h3>
                <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
            </div>
        </div>
        {{-- /end: View Content (relative min-h-[400px]) --}}

        {{-- Premium Pagination --}}
        <div x-show="!loading && permissions.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Displaying <span class="text-slate-900" x-text="pagination.from"></span> - <span class="text-slate-900" x-text="pagination.to"></span> 
                        <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span> 
                        Source: <span class="text-indigo-600" x-text="pagination.total"></span> Entries
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- First Button --}}
                    <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                        class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                        <i class="fas fa-angles-left text-[10px]"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">First</span>
                    </button>

                    {{-- Previous Button --}}
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

                    {{-- Next Button --}}
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                        <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Next</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>

                    {{-- Last Button --}}
                    <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 disabled:pointer-events-none transition-all group">
                        <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Last</span>
                        <i class="fas fa-angles-right text-[10px]"></i>
                    </button>
                </div>
                {{-- /end: pagination buttons --}}
            </div>
            {{-- /end: pagination flex row --}}
        </div>
        {{-- /end: Premium Pagination --}}

        </div>
        {{-- /end: Permission Vault white card --}}
        </div>
        {{-- /end: lg:col-span-9 Left Column --}}

        {{-- Right Column - Security Filters Sidebar --}}
        <div class="lg:col-span-3 lg:sticky lg:top-0 lg:max-h-[calc(100vh-140px)] lg:overflow-y-auto scrollbar-hide pb-2" style="scrollbar-width: none;" x-show="showSidebar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
            
            {{-- Unified Filter Card --}}
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">
                
                {{-- Master Header --}}
                <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Permission Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Access Points</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm" title="Hide Filters">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="overflow-y-auto scrollbar-hide flex-1 space-y-5 p-5" style="scrollbar-width: none;">
                    
                    {{-- Dynamic View Controller & Active Intelligence --}}
                    <div class="space-y-4">
                        <div class="flex items-center bg-slate-100 p-1.5 rounded-xl shadow-inner border border-slate-200/50">
                    <button @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 px-4 py-2 text-center rounded-lg text-[10px] font-black transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                        <i class="fas fa-table"></i> Table
                    </button>
                    <button @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 px-4 py-2 text-center rounded-lg text-[10px] font-black transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                </div>

                    <div x-show="hasActiveFilters()" class="space-y-2 pt-4 border-t border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Intelligence:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <template x-if="searchQuery">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                <i class="fas fa-search opacity-50"></i>
                                <span x-text="searchQuery"></span>
                                <button @click="searchQuery=''; searchPermissions()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                            </span>
                        </template>
                        <template x-if="filterGroup">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-600 border border-purple-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                <i class="fas fa-cubes opacity-50"></i>
                                <span x-text="formatGroupName(filterGroup)"></span>
                                <button @click="filterGroup=''; searchPermissions()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                            </span>
                        </template>
                        <template x-if="filterStatus">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                <i class="fas fa-check-circle opacity-50"></i>
                                <span x-text="filterStatus.toUpperCase()"></span>
                                <button @click="filterStatus=''; searchPermissions()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                            </span>
                        </template>
                        <template x-if="filterDateFrom || filterDateTo">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 border border-amber-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                <i class="fas fa-calendar-alt opacity-50"></i>
                                <span x-text="getDateRangeText()"></span>
                                <button @click="filterDateFrom=''; filterDateTo=''; searchPermissions()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                            </span>
                        </template>
                        </div>
                    </div>

                    <div class="border-b border-dashed border-slate-200"></div>
                    {{-- Search Module --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-search text-indigo-500"></i> Localize Point
                        </label>
                        <div class="relative group">
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchPermissions()"
                                placeholder="Search Display Name or Slug..."
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                            <i class="fas fa-fingerprint absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>

                    {{-- Module Filter --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-cubes text-purple-500"></i> Segment Module
                        </label>
                        <select x-model="filterGroup" @change="searchPermissions()"
                            class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="">All Module Clusters</option>
                            <template x-for="group in availableGroups" :key="group">
                                <option :value="group" x-text="formatGroupName(group)"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Status Toggle --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-shield-virus text-emerald-500"></i> Auth Status
                        </label>
                        <div class="grid grid-cols-1 gap-2">
                            <button @click="filterStatus = ''; searchPermissions()" 
                                :class="filterStatus === '' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Global Data</span>
                                <i class="fas fa-globe-americas transition-opacity" :class="filterStatus === '' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filterStatus = 'active'; searchPermissions()" 
                                :class="filterStatus === 'active' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Authorized Only</span>
                                <i class="fas fa-check-circle transition-opacity" :class="filterStatus === 'active' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                            <button @click="filterStatus = 'inactive'; searchPermissions()" 
                                :class="filterStatus === 'inactive' ? 'bg-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100 border-transparent'"
                                class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between">
                                <span>Locked Vaults</span>
                                <i class="fas fa-lock transition-opacity" :class="filterStatus === 'inactive' ? 'opacity-100' : 'opacity-40'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Date Refinement --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-amber-500"></i> Temporal Range
                        </label>
                        <div class="space-y-2">
                            <input type="date" x-model="filterDateFrom" @change="searchPermissions()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 outline-none text-xs font-bold text-slate-600 transition-all shadow-inner">
                            <input type="date" x-model="filterDateTo" @change="searchPermissions()"
                                class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-500 outline-none text-xs font-bold text-slate-600 transition-all shadow-inner">
                        </div>
                    </div>

                    {{-- Pagination Rows --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-list-ol text-blue-500"></i> Display Rows
                        </label>
                        <div class="relative group">
                            <select x-model="pagination.per_page" @change="searchPermissions()"
                                class="w-full pl-4 pr-10 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-xs shadow-inner appearance-none cursor-pointer group-hover:border-slate-200">
                                <option value="10">10 Rows per page</option>
                                <option value="15">15 Rows per page</option>
                                <option value="25">25 Rows per page</option>
                                <option value="50">50 Rows per page</option>
                                <option value="100">100 Rows per page</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
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
        {{-- /end: Right Column filters --}}

    </div>
    {{-- /end: mt-8 grid --}}

    {{-- ═══════════════════════════════════════════════
         MODALS (Floating Premium Style)
    ═══════════════════════════════════════════════ --}}
    
    <!-- Permission Logic Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeAddModal"></div>
            
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg z-10 overflow-hidden relative border border-slate-100" x-transition.scale>
                <div class="bg-gradient-to-br from-indigo-700 to-indigo-900 p-8 text-white relative">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center shadow-xl border border-white/20">
                                <i class="fas fa-key text-xl text-white animate-bounce-slow"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight" x-text="editing ? 'Modify Security Key' : 'Forge New Security Key'"></h3>
                                <p class="text-indigo-300 text-[10px] font-black uppercase tracking-widest mt-1">Configure access parameters</p>
                            </div>
                        </div>
                        <button @click="closeAddModal" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <form @submit.prevent="savePermission" class="p-8 space-y-8">
                    <div class="space-y-6">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">Operation Label</label>
                            <input type="text" x-model="form.display_name" required placeholder="e.g. Delete High Critical Data"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-700 shadow-inner">
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">System Identifier (Unique)</label>
                            <div class="relative">
                                <input type="text" x-model="form.name" required placeholder="e.g. purge_patient_records"
                                    class="w-full pl-5 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-mono text-xs text-slate-600 shadow-inner">
                                <i class="fas fa-code absolute right-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">Target Module Group</label>
                            <div class="relative">
                                <input type="text" x-model="form.group" required placeholder="e.g. patient_management"
                                    class="w-full pl-5 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-700 shadow-inner">
                                <i class="fas fa-cube absolute right-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="closeAddModal"
                            class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 transition-all">Abort</button>
                        <button type="submit" :disabled="saving"
                            class="flex-[2] py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                            <span x-show="!saving" x-text="editing ? 'Commit Changes' : 'Execute Creation'"></span>
                            <span x-show="saving" class="flex items-center gap-2">
                                <i class="fas fa-circle-notch animate-spin"></i>
                                Linking...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Security Purge Confirmation -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl" @click="showDeleteModal = false"></div>
            
            <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full z-10 text-center border border-slate-100 shadow-2xl relative overflow-hidden" x-transition.scale>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl"></div>
                
                <div class="w-24 h-24 bg-rose-100 rounded-3xl flex items-center justify-center mx-auto mb-8 rotate-12 group hover:rotate-0 transition-transform duration-500">
                    <i class="fas fa-trash-alt text-4xl text-rose-600 drop-shadow-lg"></i>
                </div>
                
                <h3 class="text-2xl font-black text-slate-800 mb-4 tracking-tight">Vault Purge Request</h3>
                <p class="text-slate-500 text-sm font-medium leading-relaxed mb-10">
                    Are you certain about deleting <span class="text-rose-600 font-black text-lg" x-text="permissionToDelete?.display_name"></span>? This action is irreversible.
                </p>
                
                <div class="grid grid-cols-2 gap-4">
                    <button @click="showDeleteModal = false"
                        class="px-6 py-4 bg-slate-100 text-slate-700 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">Abort</button>
                    <button @click="confirmDeleteAction" :disabled="deleting"
                        class="px-6 py-4 bg-rose-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-rose-500/40 hover:scale-105 transition-all flex items-center justify-center gap-2">
                        <span x-show="!deleting">Execute Purge</span>
                        <i x-show="deleting" class="fas fa-circle-notch animate-spin"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto px-4 py-6" x-transition.opacity style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeViewModal"></div>
            
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg z-10 overflow-hidden relative border border-slate-100" x-transition.scale>
                <div class="bg-gradient-to-br from-sky-500 to-sky-400 p-8 text-white relative">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center shadow-xl border border-white/20">
                                <i class="fas fa-eye text-xl text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black tracking-tight" x-text="dataToView?.display_name || 'Details'"></h3>
                                <p class="text-slate-300 text-[10px] font-black uppercase tracking-widest mt-1">Permission Data Record</p>
                            </div>
                        </div>
                        <button @click="closeViewModal" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-white/10 transition-colors cursor-pointer">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Identifier</p>
                            <p class="font-mono text-sm text-slate-800 font-bold" x-text="dataToView?.name"></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Module</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                      :class="getGroupClass(dataToView?.group)">
                                    <i class="fas mr-1.5" :class="getGroupIcon(dataToView?.group)"></i>
                                    <span x-text="formatGroupName(dataToView?.group)"></span>
                                </span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Auth Status</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="dataToView?.is_active ? 'text-white bg-emerald-500 border border-emerald-200' : 'text-white bg-rose-500 border border-rose-200'">
                                    <i class="fas" :class="dataToView?.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                    <span x-text="dataToView?.is_active ? 'Authorized' : 'Locked'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button @click="closeViewModal" class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all shadow-sm cursor-pointer">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function permissionManagement() {
        return {
            // Core UI State
            viewMode: 'table',
            loading: false,
            saving: false,
            deleting: false,
            editing: false,
            showAddModal: false,
            showDeleteModal: false,
            showViewModal: false,
            showAdvancedFilters: false,
            showSidebar: false,
            
            // Data & Filtering
            permissions: [],
            groupedPermissions: {},
            stats: { total: 0, active: 0, inactive: 0, groups: 0 },
            searchQuery: '',
            filterGroup: '',
            filterStatus: '',
            filterDateFrom: '',
            filterDateTo: '',
            sortField: 'display_name',
            sortDirection: 'asc',
            availableGroups: [],
            
            // Selection Logic
            selectedIds: [],
            permissionToDelete: null,
            dataToView: null,
            
            pagination: {
                current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0
            },
            
            form: { id: null, name: '', display_name: '', group: '' },
            
            async init() {
                await this.fetchPermissions();
                await this.fetchStats();
            },
            
            async fetchPermissions() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        page: this.pagination.current_page,
                        search: this.searchQuery,
                        group: this.filterGroup,
                        status: this.filterStatus,
                        date_from: this.filterDateFrom,
                        date_to: this.filterDateTo,
                        sort: this.sortField,
                        direction: this.sortDirection,
                        per_page: this.pagination.per_page
                    });
                    
                    const response = await fetch(`/admin/permissions/data?${params}`);
                    const result = await response.json();
                    
                    this.permissions = result.data;
                    this.pagination = {
                        current_page: result.current_page,
                        last_page: result.last_page,
                        per_page: result.per_page,
                        total: result.total,
                        from: result.from,
                        to: result.to
                    };
                    
                    // Always refresh grouped permissions for grid view
                    this.updateGroupedPermissions();
                } catch (e) {
                    this.showToast('Logic synchronization failed', 'error');
                } finally {
                    this.loading = false;
                }
            },

            updateGroupedPermissions() {
                const grouped = {};
                this.permissions.forEach(p => {
                    if (!grouped[p.group]) grouped[p.group] = [];
                    grouped[p.group].push(p);
                });
                this.groupedPermissions = grouped;
            },
            
            async fetchStats() {
                try {
                    const response = await fetch('/admin/permissions/stats');
                    const data = await response.json();
                    this.stats = {
                        total: data.total,
                        active: data.active,
                        inactive: data.inactive,
                        groups: data.groups
                    };
                    this.availableGroups = data.available_groups || [];
                } catch (e) {}
            },
            
            searchPermissions() {
                this.pagination.current_page = 1;
                this.fetchPermissions();
            },
            
            sortBy(field) {
                if (this.sortField === field) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortDirection = 'asc';
                }
                this.fetchPermissions();
            },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort text-slate-200';
                return this.sortDirection === 'asc' ? 'fa-sort-up text-indigo-600' : 'fa-sort-down text-indigo-600';
            },
            
            clearFilters() {
                this.searchQuery = '';
                this.filterGroup = '';
                this.filterStatus = '';
                this.filterDateFrom = '';
                this.filterDateTo = '';
                this.pagination.current_page = 1;
                this.fetchPermissions();
            },

            hasActiveFilters() {
                return this.searchQuery !== '' || 
                       this.filterGroup !== '' || 
                       this.filterStatus !== '' || 
                       this.filterDateFrom !== '' || 
                       this.filterDateTo !== '';
            },

            getActiveFilterCount() {
                let count = 0;
                if (this.searchQuery) count++;
                if (this.filterGroup) count++;
                if (this.filterStatus) count++;
                if (this.filterDateFrom || this.filterDateTo) count++;
                return count;
            },

            getDateRangeText() {
                if (this.filterDateFrom && this.filterDateTo) {
                    return `${this.filterDateFrom} to ${this.filterDateTo}`;
                }
                return this.filterDateFrom || this.filterDateTo || '';
            },

            applyQuickFilter(period) {
                const now = new Date();
                let start = new Date();
                
                switch(period) {
                    case 'today':
                        start = now;
                        break;
                    case 'week':
                        start.setDate(now.getDate() - 7);
                        break;
                    case 'month':
                        start.setMonth(now.getMonth() - 1);
                        break;
                }

                this.filterDateFrom = start.toISOString().split('T')[0];
                this.filterDateTo = now.toISOString().split('T')[0];
                this.searchPermissions();
            },
            
            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.pagination.current_page = page;
                    this.selectedIds = [];
                    this.fetchPermissions();
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
            
            // Selection Helpers
            toggleSelectAll(e) {
                if (e.target.checked) {
                    this.selectedIds = this.permissions.map(p => p.id);
                } else {
                    this.selectedIds = [];
                }
            },

            isAllSelected() {
                return this.permissions.length > 0 && this.selectedIds.length === this.permissions.length;
            },

            // CRUD & Actions
            openViewModal(permission) {
                this.dataToView = permission;
                this.showViewModal = true;
            },
            
            closeViewModal() {
                this.showViewModal = false;
                setTimeout(() => { this.dataToView = null; }, 300);
            },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, name: '', display_name: '', group: '' };
                this.showAddModal = true;
            },
            
            editPermission(permission) {
                this.editing = true;
                this.form = { ...permission };
                this.showAddModal = true;
            },
            
            closeAddModal() {
                this.showAddModal = false;
            },
            
            async savePermission() {
                this.saving = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const url = this.editing ? `/admin/permissions/${this.form.id}` : '/admin/permissions';
                    const method = this.editing ? 'PUT' : 'POST';
                    
                    const response = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify(this.form)
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.showAddModal = false;
                        this.fetchPermissions();
                        this.fetchStats();
                    } else {
                        this.showToast(result.message || 'Error saving key', 'error');
                    }
                } catch (e) {
                    this.showToast('Network disruption', 'error');
                } finally {
                    this.saving = false;
                }
            },
            
            async toggleStatus(permission) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/admin/permissions/${permission.id}/toggle-status`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        permission.is_active = result.is_active;
                        this.fetchStats();
                    }
                } catch (e) {}
            },
            
            confirmDelete(permission) {
                this.permissionToDelete = permission;
                this.showDeleteModal = true;
            },
            
            async confirmDeleteAction() {
                this.deleting = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/admin/permissions/${this.permissionToDelete.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.showDeleteModal = false;
                        this.fetchPermissions();
                        this.fetchStats();
                    }
                } catch (e) {
                    this.showToast('Purge failed', 'error');
                } finally {
                    this.deleting = false;
                }
            },

            // Bulk Logic
            async bulkStatus(status) {
                if (this.selectedIds.length === 0) return;
                
                this.loading = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch('/admin/permissions/bulk-status', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': csrfToken, 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({ ids: this.selectedIds, is_active: status })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.selectedIds = [];
                        this.fetchPermissions();
                        this.fetchStats();
                    }
                } catch (e) {
                    this.showToast('Bulk update failed', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async bulkDelete() {
                if (this.selectedIds.length === 0) return;
                if (!confirm(`Are you sure you want to purge ${this.selectedIds.length} permissions? This cannot be undone.`)) return;

                this.loading = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch('/admin/permissions/bulk-destroy', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': csrfToken, 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify({ ids: this.selectedIds })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.selectedIds = [];
                        this.fetchPermissions();
                        this.fetchStats();
                    }
                } catch (e) {
                    this.showToast('Mass purge failed', 'error');
                } finally {
                    this.loading = false;
                }
            },
            
            getGroupClass(group) {
                if (!group) return 'bg-gray-50 text-gray-700 border border-gray-200';
                const lowerGroup = group.toLowerCase();
                const colors = {
                    'medicine': 'text-gray-600',
                    'permission': 'text-gray-600',
                    'lab': 'text-gray-600',
                    'patient': 'text-gray-600',
                    'user': 'text-gray-600',
                    'branch': 'text-gray-600',
                    'reports': 'text-gray-600',
                    'office': 'text-gray-600',
                    'api': 'text-gray-600',
                    'designations': 'text-gray-600 ',
                    'consultation': 'text-gray-600 ',
                    'diagnosis': 'text-gray-600 ',
                    'alert': 'text-gray-600 ',
                    'visits': 'text-gray-600 ',
                    'vitals': 'text-gray-600 ',
                    'dashboard': 'text-gray-600 ',
                    'role': 'text-gray-600 ',
                    'audit': 'text-gray-600 ',
                    'prescription': 'text-gray-600 ',
                    'setting': 'text-gray-600 ',
                    'notification': 'text-gray-600 '
                };
                for (const key in colors) {
                    if (lowerGroup.includes(key)) return colors[key];
                }
                return 'bg-gray-50 text-gray-700 border border-gray-100';
            },

            getGroupIcon(group) {
                if (!group) return 'fas fa-cube';
                const lowerGroup = group.toLowerCase();
                const icons = {
                    'pharmacy': 'fas fa-pills',
                    'permission' : 'fas fa-fingerprint',
                    'reception': 'fas fa-id-badge',
                    'laboratory': 'fas fa-microscope',
                    'doctor': 'fas fa-user-md',
                    'patient': 'fas fa-user-injured',
                    'inventory': 'fas fa-boxes',
                    'user': 'fas fa-users-cog',
                    'admin': 'fas fa-user-shield',
                    'branch': 'fas fa-code-branch',
                    'reports': 'fas fa-chart-line',
                    'settings': 'fas fa-cog',
                    'appointment': 'fas fa-calendar-alt',
                    'prescription': 'fas fa-file-prescription',
                    'office': 'fas fa-building',
                    'api': 'fas fa-gears',
                    'designations': 'fas fa-user-tag',
                    'nurse': 'fas fa-user-nurse',
                    'consultation': 'fas fa-stethoscope',
                    'diagnosis': 'fas fa-notes-medical',
                    'lab': 'fas fa-flask',
                    'alert': 'fas fa-bell',
                    'medicine': 'fas fa-pills',
                    'visits': 'fas fa-hospital',
                    'vitals': 'fas fa-heartbeat',
                    'dashboard': 'fas fa-gauge-high',
                    'role': 'fas fa-user-lock',
                    'audit': 'fas fa-clipboard-list',
                    'notification': 'fas fa-bell'
                };
                
                for (const key in icons) {
                    if (lowerGroup.includes(key)) return icons[key];
                }
                
                return 'fas fa-cube';
            },

            formatGroupName(group) {
                if (!group) return 'General';
                return group.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            },
            
            showToast(message, type = 'info') {
                if (window.showNotification) window.showNotification(message, type);
                else alert(message);
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .animate-bounce-slow {
        animation: bounce 3s infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
        50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
    }
</style>
@endpush