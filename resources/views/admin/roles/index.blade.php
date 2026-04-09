@extends('layouts.app')

@section('title', 'Role Management - NHMP HMS')
@section('page-title', 'Role Management')
@section('breadcrumb', 'Administration / Roles')

@section('content')
<div x-data="roleManagement({{ json_encode($permissions) }})" x-init="init()" x-cloak class="space-y-8 relative">

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
        title="Open Role Filters">
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
        <!-- Total Roles Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click="clearFilters()">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-tag text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-sky-500 uppercase">Total Roles</p>
                <h4 class="text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600 animate-pulse"></span>
                    <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">System Roles</span>
                </div>
            </div>
        </div>

        <!-- Configured Roles Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-shield-alt text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-teal-500 uppercase">Configured</p>
                <h4 class="text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.with_permissions ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                    <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Roles with permissions</span>
                </div>
            </div>
        </div>

        <!-- System Roles Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-cogs text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-purple-500 uppercase">System</p>
                <h4 class="text-3xl font-bold text-purple-700 drop-shadow-sm font-mono" x-text="stats.system_roles ?? 0">0</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-200 pt-2 text-purple-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Built-in roles</span>
                </div>
            </div>
        </div>

        <!-- Groups Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-orange-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-xs font-bold tracking-wider text-rose-500 uppercase">Perm Groups</p>
                <h4 class="text-3xl font-bold text-rose-700 drop-shadow-sm font-mono">{{ count($permissions) }}</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-rose-200 pt-2 text-rose-700">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                    <span class="text-[10px] font-bold uppercase tracking-tight">Permission Categories</span>
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
                            <i class="fas fa-user-shield text-2xl text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                Role Management
                                <span class="text-lg font-normal text-gray-600">
                                    (<span x-text="pagination.total"></span> records)
                                </span>
                            </h2>
                            <p class="text-gray-600 text-sm font-medium mt-1">Manage system roles and their permissions</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                            <select x-model="pagination.per_page" @change="fetchRoles()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-1 bg-white border border-indigo-100 rounded-xl p-1 shadow-sm">
                            <button @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-indigo-600'"
                                class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Table View">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:text-indigo-600'"
                                class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>

                        <a href="{{ route('admin.roles.create') }}"
                            class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/40 transition-all active:scale-95 group">
                            <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-500"></i>
                            Add Role
                        </a>
                        <button @click="showSidebar = !showSidebar"
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                            <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                        </button>
                        
                        <button @click="fetchRoles()" 
                            class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                            title="Refresh">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                        </button>
                    </div>
                </div>
            </div>

                <!-- View Content -->
                <div class="relative min-h-[400px]">
                    <!-- Table View -->
                    <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                            <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">
                                <tr>
                                    <th class="px-5 py-5 text-left " >
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-id-card text-xs"></i>
                                        </div>
                                        <span>ID & Name</span>
                                    </div>
                                </th>
                                    <th class="px-5 py-5 text-left " >
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-200 transition-all">
                                            <i class="fas fa-shield-alt text-xs"></i>
                                        </div>
                                        <span>Permissions</span>
                                    </div>
                                </th>
                                    <th class="px-5 py-5 text-center cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('action')">
                                    <div class="flex items-center justify-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-bolt text-xs"></i>
                                        </div>
                                        <span>Actions</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'action'"></i>
                                        <i class="fas fa-sort-up ml-1 text-indigo-500" x-show="sortField === 'action' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-indigo-500" x-show="sortField === 'action' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="role in roles" :key="role.id">
                                    <tr class="hover:bg-blue-50/40 transition-colors group">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm text-white bg-gradient-to-tr from-indigo-500 to-purple-400 group-hover:scale-110 transition-transform">
                                                    <i class="fas text-sm" :class="getRoleIcon(role.name)"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-navy-800" x-text="role.display_name"></p>
                                                    <p class="text-[10px] font-mono text-gray-400" x-text="role.name"></p>
                                                    <p class="text-[10px] font-mono text-gray-400">ID #<span x-text="role.id"></span></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex flex-wrap gap-1 max-w-md">
                                                <template x-for="permission in role.permissions.slice(0, 5)" :key="permission.id">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                        <i class="fas fa-check-circle mr-1 opacity-50"></i>
                                                        <span x-text="permission.display_name"></span>
                                                    </span>
                                                </template>
                                                <template x-if="role.permissions.length > 5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                                        +<span x-text="role.permissions.length - 5"></span> more
                                                    </span>
                                                </template>
                                                <template x-if="role.permissions.length === 0">
                                                    <span class="text-slate-400 text-[10px] font-bold italic">No permissions assigned</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            <div class="flex flex-col items-center justify-center gap-1 w-full max-w-[120px] mx-auto">
                                                <a :href="'{{ route('admin.roles.edit', 'role_id') }}'.replace('role_id', role.id)" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider" title="Edit Controller">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <template x-if="role.id > 5">
                                                    <button @click="toggleRoleStatus(role)" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-gray-600 hover:text-gray-900 transition-colors text-[10px] font-bold uppercase tracking-wider">
                                                        <i class="fas" :class="role.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                                        <span x-text="role.is_active ? 'Deactivate' : 'Activate'"></span>
                                                    </button>
                                                </template>
                                                <template x-if="role.id > 5">
                                                    <button @click="confirmDelete(role)" class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-rose-600 hover:text-rose-900 transition-colors text-[10px] font-bold uppercase tracking-wider">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </button>
                                                </template>
                                                <template x-if="role.id <= 5">
                                                    <span class="w-full text-slate-400 text-[9px] font-black uppercase tracking-widest bg-slate-50 border border-slate-100 rounded py-1 px-2">System Protected</span>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grid View -->
                <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <template x-for="role in roles" :key="role.id">
                            <div class="group relative bg-white border-2 border-slate-100 rounded-[2.5rem] p-6 hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 overflow-hidden">
                                <!-- Status Indicator -->
                                <div class="absolute top-6 right-6 flex items-center gap-2">
                                    <span class="flex h-2.5 w-2.5 rounded-full" :class="role.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest" :class="role.is_active ? 'text-emerald-600' : 'text-rose-600'" x-text="role.is_active ? 'Active' : 'Inactive'"></span>
                                </div>

                                <!-- Role ID -->
                                <div class="absolute top-6 left-6 text-[10px] font-black text-slate-300 uppercase tracking-widest" x-text="'ID: ' + role.id"></div>

                                <div class="flex flex-col items-center text-center mt-6">
                                    <!-- Icon -->
                                    <div class="relative group/icon cursor-pointer mb-5">
                                        <div class="absolute inset-0 bg-indigo-500 blur-2xl opacity-0 group-hover/icon:opacity-20 transition-opacity duration-500 rounded-full"></div>
                                        <div class="relative h-20 w-20 rounded-[2rem] bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white text-2xl shadow-xl shadow-indigo-500/20 group-hover:scale-105 group-hover:rotate-3 transition-all duration-500 border-4 border-white">
                                            <i class="fas" :class="getRoleIcon(role.name)"></i>
                                        </div>
                                    </div>

                                    <!-- Name & Meta -->
                                    <h3 class="text-xl font-black text-slate-800 leading-tight mb-1" x-text="role.display_name"></h3>
                                    <div class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-4" x-text="role.name"></div>

                                    <!-- Permission Count -->
                                    <div class="bg-slate-50 border border-slate-100 rounded-2xl px-4 py-2 mb-6">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Security Level:</span>
                                        <div class="flex items-center gap-2 justify-center mt-1">
                                            <i class="fas fa-shield-alt text-indigo-500 text-xs"></i>
                                            <span class="text-sm font-black text-slate-800" x-text="role.permissions.length + ' Nodes'"></span>
                                        </div>
                                    </div>

                                    <!-- Actions Footer -->
                                    <div class="w-full pt-6 border-t border-slate-50 flex items-center justify-center gap-2">
                                        <a :href="'{{ route('admin.roles.edit', 'role_id') }}'.replace('role_id', role.id)" class="px-5 py-2.5 bg-slate-50 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm flex items-center gap-2 group/btn">
                                            <i class="fas fa-edit group-hover/btn:scale-110 transition-transform"></i>
                                            Modify
                                        </a>
                                        <template x-if="role.id > 5">
                                            <button @click="confirmDelete(role)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-rose-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </template>
                                        <template x-if="role.id <= 5">
                                            <div class="px-3 h-10 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 text-[8px] font-black uppercase tracking-tighter">
                                                Protected
                                            </div>
                                        </template>
                                    </div>
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
                    <div x-show="!loading && roles.length === 0" class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-8 border-4 border-white shadow-xl">
                            <i class="fas fa-ghost text-4xl text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Roles Found</h3>
                        <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">The vault appears empty for current criteria. Perhaps adjust your security filters?</p>
                        <button @click="clearFilters()" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">Reset Security Clearance</button>
                    </div>
                </div>

            {{-- Premium Pagination --}}
            <div x-show="!loading && roles.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
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
                            <h2 class="font-black text-slate-800 text-base tracking-tight">Role Filters</h2>
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Access Points</p>
                        </div>
                    </div>
                    <button @click="showSidebar = false" class="w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm" title="Hide Filters">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </div>

                <div class="overflow-y-auto scrollbar-hide flex-1 space-y-5 p-5" style="scrollbar-width: none;">
                    
                    <div class="space-y-4">
                        <div x-show="searchQuery" class="space-y-2 pt-4 border-t border-slate-100">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Intelligence:</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <template x-if="searchQuery">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest group">
                                        <i class="fas fa-search opacity-50"></i>
                                        <span x-text="searchQuery"></span>
                                        <button @click="searchQuery=''; searchRoles()" class="hover:text-rose-600 transition-colors"><i class="fas fa-times-circle"></i></button>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div class="border-b border-dashed border-slate-200"></div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-search text-indigo-500"></i> Localize Point
                            </label>
                            <div class="relative group">
                                <input type="text" x-model="searchQuery" @input.debounce.500ms="searchRoles()"
                                    placeholder="Search roles by name..."
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-ol text-blue-500"></i> Display Rows
                            </label>
                            <div class="relative group">
                                <select x-model="pagination.per_page" @change="searchRoles()"
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

    <!-- Security Purge Confirmation -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl" @click="showDeleteModal = false"></div>
            
            <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full z-10 text-center border border-slate-100 shadow-2xl relative overflow-hidden" x-transition.scale>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl"></div>
                
                <div class="w-24 h-24 bg-rose-100 rounded-3xl flex items-center justify-center mx-auto mb-8 rotate-12 group hover:rotate-0 transition-transform duration-500">
                    <i class="fas fa-trash-alt text-4xl text-rose-600 drop-shadow-lg"></i>
                </div>
                
                <h3 class="text-2xl font-black text-slate-800 mb-4 tracking-tight">Role Purge Request</h3>
                <p class="text-slate-500 text-sm font-medium leading-relaxed mb-10">
                    Are you certain about deleting role <span class="text-rose-600 font-black text-lg" x-text="roleToDelete?.display_name"></span>? This action is irreversible.
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
</div>
@endsection

@push('scripts')
<script>
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function roleManagement(groupedPermissions = {}) {
        return {
            roles: [],
            loading: false,
            viewMode: 'table',
            sortField: 'id',
            sortDirection: 'desc',
            saving: false,
            deleting: false,
            showDeleteModal: false,
            showSidebar: false,
            roleToDelete: null,
            searchQuery: '',
            groupedPermissions: groupedPermissions,
            
            pagination: {
                current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0
            },
            
            stats: { total: 0, with_permissions: 0, without_permissions: 0, system_roles: 0 },

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

            async init() {
                await this.fetchRoles();
                await this.fetchStats();
            },

            async fetchRoles() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        page: this.pagination.current_page,
                        search: this.searchQuery,
                        per_page: this.pagination.per_page,
                        sort: this.sortField,
                        direction: this.sortDirection,
                        _: Date.now()
                    });
                    const response = await fetch(`/admin/roles/data?${params}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    this.roles = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to
                    };
                } catch (e) {
                    this.showToast('Logic synchronization failed', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async fetchStats() {
                try {
                    const params = new URLSearchParams({
                        search: this.searchQuery,
                        _: Date.now()
                    });
                    const response = await fetch(`/admin/roles/stats?${params}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    this.stats = await response.json();
                } catch (e) {}
            },

            applyFilters() {
                this.pagination.current_page = 1;
                this.fetchRoles();
                this.fetchStats();
            },

            searchRoles() {
                this.applyFilters();
            },

            clearFilters() {
                this.searchQuery = '';
                this.applyFilters();
            },

            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.pagination.current_page = page;
                    this.fetchRoles();
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

            confirmDelete(role) {
                this.roleToDelete = role;
                this.showDeleteModal = true;
            },

            async confirmDeleteAction() {
                this.deleting = true;
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/admin/roles/${this.roleToDelete.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.showDeleteModal = false;
                        this.fetchRoles();
                        this.fetchStats();
                    } else {
                        this.showToast(result.message || 'Error deleting role', 'error');
                    }
                } catch (e) {
                    this.showToast('Network error', 'error');
                } finally {
                    this.deleting = false;
                }
            },

            async toggleRoleStatus(role) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/admin/roles/${role.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        role.is_active = result.is_active;
                    } else {
                        this.showToast(result.message || 'Error updating status', 'error');
                    }
                } catch (e) {
                    this.showToast('Network error', 'error');
                }
            },

            getRoleIcon(name) {
                const role = (name || '').toLowerCase();
                const iconMap = {
                    'super_admin': 'fa-user-shield',
                    'admin': 'fa-shield-alt',
                    'doctor': 'fa-user-md',
                    'nurse': 'fa-user-nurse',
                    'pharmacy': 'fa-pills',
                    'lab': 'fa-vial',
                    'reception': 'fa-user-plus'
                };
                for (let key in iconMap) {
                    if (role.includes(key)) return iconMap[key];
                }
                return 'fa-user-tag';
            },

            showToast(message, type = 'info') {
                if (window.showNotification) {
                    window.showNotification(message, type);
                } else if (window.toastr) {
                    window.toastr[type](message);
                } else {
                    alert(message);
                }
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
