@extends('layouts.app')

@section('title', 'User Management - NHMP HMS')
@section('page-title', 'User Management')
@section('breadcrumb', 'Administration / Users')

@section('content')
    <div x-data="userManagement({{ json_encode($roles) }}, {{ json_encode($branches) }})" x-init="init()" class="space-y-6">

        <!-- Futuristic Floating Filter Toggle -->
        <button @click="showSidebar = true"
            x-show="!showSidebar"
            x-transition:enter="transition ease-out duration-500 delay-100"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-indigo-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(79,70,229,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(79,70,229,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-400/50 group cursor-pointer"
            title="Open User Filters">
            <div class="relative">
                <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
                <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
            </div>
            <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-indigo-50">User Filters</span>
        </button>

        <!-- Stats Cards - Vibrant Premium Style -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4">

            <!-- Total Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                 @click.throttle.500ms="setFilter('all')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/30 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="block antialiased font-sans text-xs font-bold tracking-wider text-sky-500 uppercase">Total Users</p>
                    <h4 class="block antialiased text-3xl font-bold text-sky-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-sky-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                        <span class="text-[10px] text-sky-700 font-bold uppercase tracking-tight">All Registered Users</span>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/20 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                 @click.throttle.500ms="setFilter('active')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/30 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-check text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="block antialiased font-sans text-xs font-bold tracking-wider text-teal-500 uppercase">Active Users</p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-700 drop-shadow-sm font-mono" x-text="stats.active ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600" :class="{ 'animate-pulse': stats.active > 0 }"></span>
                        <span class="text-[10px] text-teal-700 font-bold uppercase tracking-tight">Currently Active</span>
                    </div>
                </div>
            </div>

            <!-- Administrators Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/20 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                 @click.throttle.500ms="setFilter('admins')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/30 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-shield text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="block antialiased font-sans text-xs font-bold tracking-wider text-purple-500 uppercase">Administrators</p>
                    <h4 class="block antialiased text-3xl font-bold text-purple-700 drop-shadow-sm font-mono" x-text="stats.admins ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.admins > 0 }"></span>
                        <span class="text-[10px] uppercase font-bold text-purple-700 tracking-tight">System Admins</span>
                    </div>
                </div>
            </div>

            <!-- Inactive Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/20 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                 @click.throttle.500ms="setFilter('inactive')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-orange-400 shadow-lg shadow-rose-900/30 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="block antialiased font-sans text-xs font-bold tracking-wider text-rose-500 uppercase">Inactive Users</p>
                    <h4 class="block antialiased text-3xl font-bold text-rose-700 drop-shadow-sm font-mono" x-text="stats.inactive ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600" :class="{ 'animate-pulse': stats.inactive > 0 }"></span>
                        <span class="text-[10px] uppercase font-bold text-rose-700 tracking-tight">Inactive Accounts</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTROL PANEL -->
        <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column - Table -->
            <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col min-h-[500px]">

                    <!-- Panel Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-indigo-100/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm transition-transform hover:scale-105 duration-300">
                                    <i class="fas fa-users-cog text-2xl text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                                        User Management
                                        <span class="text-lg font-normal text-gray-600">
                                            (<span x-text="pagination.total"></span> records)
                                        </span>
                                    </h2>
                                    <p class="text-gray-600 text-sm font-medium mt-1">Manage system users, roles, and permissions</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                    <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase">Rows</span>
                                    <select x-model="pagination.per_page" @change="fetchUsers()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <button @click="openAddModal()"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/40 transition-all active:scale-95 group">
                                    <i class="fas fa-user-plus group-hover:-translate-y-1 transition-transform duration-300"></i>
                                    Add User
                                </button>

                                <button @click="showSidebar = !showSidebar"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                    :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                    <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                                </button>

                                <button @click="clearFilters(); fetchUsers()" 
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm"
                                    title="Refresh">
                                    <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Active Filters Summary -->
                        <div x-show="searchQuery || filterStatus || filters.role || registrationFrom || registrationTo"
                             class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-indigo-100" style="padding-left: 1.5rem; padding-right: 1.5rem; padding-bottom: 1rem;">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2">Active filters:</span>
                            
                            <template x-if="searchQuery">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                                    <i class="fas fa-search text-[10px]"></i>
                                    <span x-text="searchQuery"></span>
                                    <button @click="searchQuery = ''; applyFilters()" class="hover:text-blue-900"><i class="fas fa-times"></i></button>
                                </span>
                            </template>

                            <template x-if="filterStatus">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100">
                                    <i class="fas" :class="filterStatus === 'active' ? 'fa-user-check' : 'fa-user-clock'"></i>
                                    <span x-text="filterStatus === 'active' ? 'Active' : 'Inactive'"></span>
                                    <button @click="filterStatus = ''; applyFilters()" class="hover:text-emerald-900"><i class="fas fa-times"></i></button>
                                </span>
                            </template>

                            <template x-if="filters.role">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-purple-50 text-purple-700 rounded-lg text-xs font-bold border border-purple-100">
                                    <i class="fas fa-user-tag text-[10px]"></i>
                                    <span x-text="getRoleNameById(filters.role)"></span>
                                    <button @click="filters.role = ''; applyFilters()" class="hover:text-purple-900"><i class="fas fa-times"></i></button>
                                </span>
                            </template>

                            <template x-if="registrationFrom || registrationTo">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100">
                                    <i class="fas fa-calendar text-[10px]"></i>
                                    <span x-text="`${registrationFrom || '...'} to ${registrationTo || '...'}`"></span>
                                    <button @click="registrationFrom = ''; registrationTo = ''; applyFilters()" class="hover:text-amber-900"><i class="fas fa-times"></i></button>
                                </span>
                            </template>

                            <button @click="clearFilters()" class="text-[10px] font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest ml-auto transition-colors">
                                Clear All
                            </button>
                        </div>
                    </div>

                    <!-- View Content (Table Area) -->
                    <div class="relative min-h-[400px]">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100 border-b-2 border-indigo-200/50">
                                    <tr>
                                        <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('name')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-200 transition-all">
                                            <i class="fas fa-id-card text-xs"></i>
                                        </div>
                                        <span>User Information</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'name'"></i>
                                        <i class="fas fa-sort-up ml-1 text-indigo-500" x-show="sortField === 'name' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-indigo-500" x-show="sortField === 'name' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                        <th class="px-5 py-5 text-left cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('is_active')">
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shadow-sm border border-rose-200 transition-all">
                                            <i class="fas fa-envelope text-xs"></i>
                                        </div>
                                        <span>Email & Status</span>
                                        <i class="fas fa-sort ml-1 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300" x-show="sortField !== 'is_active'"></i>
                                        <i class="fas fa-sort-up ml-1 text-rose-500" x-show="sortField === 'is_active' && sortDirection === 'asc'"></i>
                                        <i class="fas fa-sort-down ml-1 text-rose-500" x-show="sortField === 'is_active' && sortDirection === 'desc'"></i>
                                    </div>
                                </th>
                                        <th class="px-5 py-5 text-left " >
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-200 transition-all">
                                            <i class="fas fa-shield-alt text-xs"></i>
                                        </div>
                                        <span>Roles & Permissions</span>
                                    </div>
                                </th>
                                        <th class="px-5 py-5 text-left " >
                                    <div class="flex items-center justify-start gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center text-sky-500 shadow-sm border border-sky-200 transition-all">
                                            <i class="fas fa-clock text-xs"></i>
                                        </div>
                                        <span>Activity</span>
                                    </div>
                                </th>
                                        <th class="px-5 py-5 text-right cursor-pointer group hover:bg-slate-50 transition-colors" @click="sortBy('action')">
                                    <div class="flex items-center justify-end gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
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
                                <tbody class="divide-y divide-slate-50" x-show="!loading && users && users.length > 0">
                                    <template x-for="user in users" :key="user.id">
                                        <tr class="hover:bg-indigo-50/40 transition-colors group">
                                            <!-- User Info -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-4">
                                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-sm border border-indigo-100 group-hover:scale-105 transition-transform"
                                                         x-text="getInitials(user.name)"></div>
                                                    <div>
                                                        <div class="text-sm font-extrabold text-navy-800 truncate max-w-[200px]"  x-text="user.name"></div>
                                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1 mt-0.5">
                                                            <i class="fas fa-id-badge text-indigo-400"></i>
                                                            <span x-text="`ID: ${user.id}`"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Email & Status -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-2 group/email relative cursor-default">
                                                        <span class="w-6 h-6 flex items-center justify-center bg-slate-50 border border-slate-100 rounded-md text-slate-400 group-hover/email:text-indigo-600 group-hover/email:border-indigo-200 transition-colors">
                                                            <i class="fas fa-envelope text-[10px]"></i>
                                                        </span>
                                                        <span class="text-xs font-bold text-gray-600 group-hover/email:text-indigo-600 transition-colors truncate max-w-[150px]"  x-text="user.email" :title="user.email"></span>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-6 h-6 flex items-center justify-center rounded-md"
                                                              :class="user.is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100'">
                                                            <i class="fas text-[10px]" :class="user.is_active ? 'fa-check-circle' : 'fa-ban'"></i>
                                                        </span>
                                                        <span class="text-[10px] font-black uppercase tracking-widest"
                                                              :class="user.is_active ? 'text-emerald-600' : 'text-rose-600'"
                                                              x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Roles -->
                                            <td class="px-5 py-4 whitespace-wrap text-sm font-medium">
                                                <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                                    <template x-for="role in user.roles" :key="role.id">
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-slate-50 border border-slate-200 text-slate-700 text-[10px] font-bold shadow-sm" :title="role.display_name || role.name">
                                                            <i class="fas text-[9px]" :class="getRoleIcon(role.name)"></i>
                                                            <span x-text="role.display_name || role.name"></span>
                                                        </span>
                                                    </template>
                                                    <span x-show="!user.roles || user.roles.length === 0" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                        <i class="fas fa-user-slash mr-1"></i> No Roles
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Activity -->
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="flex flex-col gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-6 h-6 flex items-center justify-center bg-blue-50 border border-blue-100 rounded-md text-blue-500">
                                                            <i class="fas fa-calendar-plus text-[10px]"></i>
                                                        </span>
                                                        <span class="text-xs font-bold text-gray-600" x-text="user.created_at ? formatDate(user.created_at) : 'N/A'" title="Registration Date"></span>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-6 h-6 flex items-center justify-center bg-slate-50 border border-slate-100 rounded-md"
                                                              :class="(user.last_login_at && user.last_login_at !== 'Invalid Date' && user.last_login_at !== null) ? 'text-indigo-500' : 'text-slate-400'">
                                                            <i class="fas fa-history text-[10px]"></i>
                                                        </span>
                                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500" x-text="getLastLoginText(user.last_login_at)" :title="getLastLoginTitle(user.last_login_at)"></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-5 py-4 text-right">
                                                <div class="flex items-center justify-end gap-1 flex-wrap w-[120px] ml-auto">
                                                    <a :href="`/admin/users/${user.uuid}`" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-colors shadow-sm tooltip" title="View User">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </a>
                                                    
                                                    <button @click="editUser(user)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-colors shadow-sm tooltip" title="Edit User">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    
                                                    <button @click="openResetPasswordModal(user)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-amber-600 hover:border-amber-200 hover:bg-amber-50 transition-colors shadow-sm tooltip" title="Reset Password">
                                                        <i class="fas fa-key text-xs"></i>
                                                    </button>
                                                    
                                                    <button @click="openPermissionsModal(user)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-colors shadow-sm tooltip" title="View Permissions">
                                                        <i class="fas fa-shield-alt text-xs"></i>
                                                    </button>
                                                    
                                                    <button @click="toggleUserStatus(user)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 transition-colors shadow-sm tooltip"
                                                            :class="user.is_active ? 'text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50' : 'text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50'"
                                                            :title="user.is_active ? 'Deactivate User' : 'Activate User'">
                                                        <i class="fas text-[10px]" :class="user.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                                    </button>

                                                    <button @click="confirmDelete(user)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition-colors shadow-sm tooltip" title="Delete User">
                                                        <i class="fas fa-trash text-[10px]"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all rounded-b-3xl">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-lg"></div>
                                <span class="text-xs font-black text-indigo-600 uppercase tracking-[0.3em] animate-pulse">Syncing Records...</span>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div x-show="!loading && (!users || users.length === 0)" class="flex flex-col items-center justify-center py-24 text-center">
                            <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mb-8 border-4 border-white shadow-xl">
                                <i class="fas fa-users-slash text-4xl text-slate-300"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 mb-2">No users found</h3>
                            <p class="text-slate-500 max-w-sm mx-auto mb-8 font-medium">Try adjusting your filter parameters to narrow the scope.</p>
                            <button @click="openAddModal()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/40 hover:scale-105 transition-all">
                                <i class="fas fa-user-plus mr-2"></i> Add First User
                            </button>
                        </div>
                    </div>

                    <!-- Premium Pagination -->
                    <div x-show="!loading && users && users.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
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

            <!-- Right Column - Security Filters Sidebar -->
            <div class="lg:col-span-3 lg:sticky lg:top-0 lg:max-h-[calc(100vh-140px)] lg:overflow-y-auto scrollbar-hide pb-2" style="scrollbar-width: none;" x-show="showSidebar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4">
                
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col min-h-0">
                    <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="fas fa-filter text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 text-base tracking-tight">User Filters</h2>
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">Refine Results</p>
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
                                    <i class="fas fa-search text-indigo-500"></i> Search Term
                                </label>
                                <div class="relative group">
                                    <input type="text" x-model="searchQuery" @input.debounce.500ms="searchUsers()"
                                        placeholder="Name, email..."
                                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner group-hover:border-slate-200">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-emerald-500"></i> Status
                                </label>
                                <select x-model="filterStatus" @change="applyFilters()"
                                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-user-tag text-purple-500"></i> Role
                                </label>
                                <select x-model="filters.role" @change="applyFilters()"
                                    class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-600 text-sm shadow-inner appearance-none cursor-pointer">
                                    <option value="">All Roles</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-3 border-t border-slate-100 pt-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-amber-500"></i> Registration Window
                                </label>
                                <div class="flex flex-col gap-2">
                                    <input type="date" x-model="registrationFrom" @change="applyFilters()"
                                        class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-600 text-xs shadow-inner" placeholder="From">
                                    <div class="text-center text-slate-400 text-xs font-black uppercase">to</div>
                                    <input type="date" x-model="registrationTo" @change="applyFilters()"
                                        class="w-full px-3 py-2 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-600 text-xs shadow-inner" placeholder="To">
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

        <!-- Include Modals -->
        @include('admin.users.modals.add-edit-user-modal')
        @include('admin.users.modals.delete-user-modal')
        @include('admin.users.modals.reset-password-modal')
        @include('admin.users.modals.view-permissions-modal')
        @include('admin.users.modals.deactivate-confirmation-modal')

    </div>

    <script>
        // Debounce utility function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const context = this;
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function userManagement(availableRoles = [], availableBranches = []) {
            return {
                // State
                availableRoles: availableRoles,
                availableBranches: availableBranches,
                users: [],
                loading: false,
                saving: false,
                deleting: false,
                showPassword: false,
                showUserModal: false,
                showDeleteModal: false,
                editingUser: false,
                userToDelete: null,
                currentRequest: null,
                showUserActions: null,

                // Modal states
                showResetPasswordModal: false,
                showPermissionsModal: false,
                showDeactivateModal: false,

                // Selected user for modals
                selectedUser: null,

                // Reset password specific
                resetPasswordMethod: 'auto',
                resetPassword: '',
                resetPasswordConfirmation: '',
                showResetPassword: false,
                sendEmailNotification: true,
                resettingPassword: false,

                // Permissions specific
                loadingPermissions: false,
                groupedPermissions: {},

                // Deactivate specific
                deactivateAction: 'deactivate',
                processingDeactivate: false,

                // UI states
                changePassword: false,
                showSidebar: false,
                filterStatus: '',
                _componentInitialized: false,

                // Forms
                userForm: {
                    id: null,
                    uuid: null,
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_ids: [],
                    branch_ids: [],
                    primary_branch_id: null,
                    is_active: true
                },

                // Search & Filters
                searchQuery: '',
                filters: {
                    active: false,
                    inactive: false,
                    role: ''
                },
                registrationFrom: '',
                registrationTo: '',

                // Sorting
                sortField: 'name',
                sortDirection: 'asc',

                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDirection = 'asc';
                    }
                    this.fetchUsers();
                },

                // Pagination
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },

                // Stats
                stats: {
                    total: 0,
                    active: 0,
                    inactive: 0,
                    admins: 0
                },

                // Initialize
                async init() {
                    this._componentInitialized = true;
                    await this.fetchUsers();
                    await this.fetchStats();
                    
                    // Check for edit_uuid in URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const editUuid = urlParams.get('edit_uuid');
                    if (editUuid) {
                        // Clear the parameter from URL without refreshing
                        const newUrl = window.location.pathname;
                        window.history.replaceState({}, '', newUrl);
                        
                        // Use the users from the current list or wait if needed
                        // But since we just fetched users, we can try to find it
                        const userToEdit = this.users.find(u => u.uuid === editUuid);
                        if (userToEdit) {
                            this.editUser(userToEdit);
                        } else {
                            // Fallback: fetch specific user if not in the current page
                            try {
                                const response = await fetch(`/admin/users/${editUuid}/edit`, {
                                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                                });
                                if (response.ok) {
                                    const userData = await response.json();
                                    this.editUser(userData.user);
                                }
                            } catch (e) {
                                console.error('Error fetching user for auto-edit:', e);
                            }
                        }
                    }
                    
                    console.log('UserManagement component initialized');
                },

                // Fetch users from server
                async fetchUsers() {
                    if (this.loading) return;

                    if (this.currentRequest) {
                        this.currentRequest.abort();
                    }

                    this.loading = true;
                    try {
                        const controller = new AbortController();
                        this.currentRequest = controller;

                        // Convert status filter to active/inactive flags
                        if (this.filterStatus === 'active') {
                            this.filters.active = true;
                            this.filters.inactive = false;
                        } else if (this.filterStatus === 'inactive') {
                            this.filters.active = false;
                            this.filters.inactive = true;
                        } else {
                            this.filters.active = false;
                            this.filters.inactive = false;
                        }

                        const params = new URLSearchParams({
                            page: this.pagination.current_page,
                            search: this.searchQuery,
                            filterStatus: this.filterStatus,
                            role: this.filters.role,
                            start_date: this.registrationFrom,
                            end_date: this.registrationTo,
                            sort: this.sortField,
                            direction: this.sortDirection,
                            per_page: this.pagination.per_page,
                            _: Date.now()
                        });

                        const response = await fetch(`/admin/users/data?${params}`, {
                            signal: controller.signal,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        this.users = data.data || [];

                        this.pagination = {
                            current_page: data.current_page || 1,
                            last_page: data.last_page || 1,
                            per_page: data.per_page || 10,
                            total: data.total || 0,
                            from: data.from || 0,
                            to: data.to || 0
                        };

                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error('Error fetching users:', error);
                            this.showToast('Failed to load users', 'error');
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                // Fetch stats
                async fetchStats() {
                    try {
                        const params = new URLSearchParams({
                            search: this.searchQuery,
                            filterStatus: this.filterStatus,
                            role: this.filters.role,
                            start_date: this.registrationFrom,
                            end_date: this.registrationTo,
                            _: Date.now()
                        });

                        const response = await fetch(`/admin/users/stats?${params}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            this.stats = await response.json();
                        }
                    } catch (error) {
                        console.error('Error fetching stats:', error);
                    }
                },


                // Search users
                searchUsers() {
                    this.pagination.current_page = 1;
                    this.fetchUsers();
                },

                // Set quick filter
                setFilter(type) {
                    if (this.loading) return;

                    this.resetFilters(false);

                    switch (type) {
                        case 'active':
                            this.filterStatus = 'active';
                            break;
                        case 'inactive':
                            this.filterStatus = 'inactive';
                            break;
                        case 'admins':
                            const adminRole = this.availableRoles.find(r => r.name === 'admin');
                            if (adminRole) {
                                this.filters.role = adminRole.id;
                            }
                            break;
                        case 'all':
                            this.filterStatus = '';
                            break;
                    }
                    this.debouncedApplyFilters();
                },

                debouncedApplyFilters: debounce(function() {
                    this.applyFilters();
                }, 300),

                // Update applyFilters to check loading state
                applyFilters() {
                    if (this.loading) return;
                    this.pagination.current_page = 1;
                    this.fetchUsers();
                    this.fetchStats();
                },

                // Reset all filters
                resetFilters(fetch = true) {
                    this.filters = {
                        active: false,
                        inactive: false,
                        role: ''
                    };
                    this.filterStatus = '';
                    this.searchQuery = '';
                    this.registrationFrom = '';
                    this.registrationTo = '';
                    this.pagination.current_page = 1;

                    if (fetch) {
                        this.fetchUsers();
                        this.fetchStats();
                    }
                },

                // Clear filters (from button)
                clearFilters() {
                    this.resetFilters(true);
                },

                // Pagination methods
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.pagination.current_page = page;
                        this.fetchUsers();
                    }
                },

                // Get page range for pagination
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

                // NEW: Get role name by ID
                getRoleNameById(roleId) {
                    if (!roleId) return '';
                    const role = this.availableRoles.find(r => r.id == roleId);
                    return role ? (role.display_name || role.name) : 'Unknown Role';
                },

                // Open add user modal
                openAddModal() {
                    this.editingUser = false;
                    this.resetUserForm();
                    this.showUserModal = true;
                },

                // Open reset password modal
                openResetPasswordModal(user) {
                    console.log('openResetPasswordModal called with user:', user);

                    this.selectedUser = user;
                    this.resetPasswordMethod = 'auto';
                    this.resetPassword = '';
                    this.resetPasswordConfirmation = '';
                    this.sendEmailNotification = true;
                    this.showUserActions = null;
                    this.showResetPasswordModal = true;
                },

                closeResetPasswordModal() {
                    this.showResetPasswordModal = false;
                    this.selectedUser = null;
                    this.resetPassword = '';
                    this.resetPasswordConfirmation = '';
                },

                async confirmResetPassword() {
                    this.resettingPassword = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const data = {
                            method: this.resetPasswordMethod,
                            send_email: this.sendEmailNotification,
                            _token: csrfToken
                        };

                        if (this.resetPasswordMethod === 'manual') {
                            if (this.resetPassword !== this.resetPasswordConfirmation) {
                                this.showToast('Passwords do not match', 'error');
                                this.resettingPassword = false;
                                return;
                            }
                            if (this.resetPassword.length < 8) {
                                this.showToast('Password must be at least 8 characters', 'error');
                                this.resettingPassword = false;
                                return;
                            }
                            data.password = this.resetPassword;
                            data.password_confirmation = this.resetPasswordConfirmation;
                        }

                        const response = await fetch(`/admin/users/${this.selectedUser.uuid}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast(result.message, 'success');
                            this.closeResetPasswordModal();
                        } else {
                            this.showToast(result.message || 'Failed to reset password', 'error');
                        }
                    } catch (error) {
                        console.error('Error resetting password:', error);
                        this.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.resettingPassword = false;
                    }
                },

                // View Permissions methods
                openPermissionsModal(user) {
                    console.log('openPermissionsModal called with user:', user);

                    this.selectedUser = user;
                    this.showPermissionsModal = true;
                    this.loadingPermissions = true;
                    this.showUserActions = null;

                    fetch(`/admin/users/${user.uuid}/permissions`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.groupedPermissions = this.groupPermissionsByModule(data.permissions || []);
                            this.loadingPermissions = false;
                        })
                        .catch(error => {
                            console.error('Error loading permissions:', error);
                            this.showToast('Error loading permissions', 'error');
                            this.loadingPermissions = false;
                        });
                },

                closePermissionsModal() {
                    this.showPermissionsModal = false;
                    this.selectedUser = null;
                    this.groupedPermissions = {};
                },

                groupPermissionsByModule(permissions) {
                    return permissions.reduce((groups, permission) => {
                        let module = 'general';
                        if (permission.name && permission.name.includes('_')) {
                            const parts = permission.name.split('_');
                            if (parts.length > 1) {
                                module = parts.slice(1).join('_') || parts[1] || 'general';
                            }
                        }

                        if (!groups[module]) {
                            groups[module] = [];
                        }
                        groups[module].push(permission);
                        return groups;
                    }, {});
                },

                // Deactivate/Activate methods
                toggleUserStatus(user) {
                    console.log('toggleUserStatus called with user:', user);

                    this.selectedUser = user;
                    this.deactivateAction = user.is_active ? 'deactivate' : 'activate';
                    this.showDeactivateModal = true;
                },

                closeDeactivateModal() {
                    this.showDeactivateModal = false;
                    this.selectedUser = null;
                    this.deactivateAction = 'deactivate';
                },

                async confirmDeactivateAction() {
                    this.processingDeactivate = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch(`/admin/users/${this.selectedUser.uuid}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast(result.message, 'success');
                            this.closeDeactivateModal();
                            await this.fetchUsers();
                            await this.fetchStats();
                        } else {
                            this.showToast(result.message || 'Failed to update user status', 'error');
                        }
                    } catch (error) {
                        console.error('Error toggling user status:', error);
                        this.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.processingDeactivate = false;
                    }
                },

                // Edit user
                async editUser(user) {
                    this.editingUser = true;
                    this.changePassword = false;

                    try {
                        const response = await fetch(`/admin/users/${user.uuid}/edit`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const userData = await response.json();
                        const fetchedUser = userData.user;

                        this.userForm = {
                            id: fetchedUser.id,
                            uuid: fetchedUser.uuid,
                            name: fetchedUser.name,
                            email: fetchedUser.email,
                            password: '',
                            password_confirmation: '',
                            role_ids: Array.isArray(userData.role_ids) ? userData.role_ids : [],
                            branch_ids: Array.isArray(userData.branch_ids) ? userData.branch_ids : [],
                            primary_branch_id: userData.primary_branch_id,
                            is_active: fetchedUser.is_active
                        };

                        this.showUserModal = true;
                    } catch (error) {
                        console.error('Error fetching user details:', error);
                        this.showToast('Failed to load user details', 'error');
                    }
                },

                // Toggle user actions dropdown
                toggleUserActions(userId) {
                    this.showUserActions = this.showUserActions === userId ? null : userId;
                },

                // Save user
                async saveUser() {
                    this.saving = true;
                    try {
                        if (!this.userForm.role_ids || this.userForm.role_ids.length === 0) {
                            this.showToast('Please select at least one role', 'warning');
                            this.saving = false;
                            return;
                        }

                        const url = this.editingUser ? `/admin/users/${this.userForm.uuid}` : '/admin/users';
                        const method = this.editingUser ? 'PUT' : 'POST';

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const data = {
                            name: this.userForm.name,
                            email: this.userForm.email,
                            is_active: this.userForm.is_active ? 1 : 0,
                            roles: this.userForm.role_ids,
                            branches: this.userForm.branch_ids,
                            primary_branch: this.userForm.primary_branch_id,
                            _token: csrfToken
                        };

                        if (!this.editingUser || this.userForm.password) {
                            data.password = this.userForm.password;
                            data.password_confirmation = this.userForm.password_confirmation;
                        }

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast(result.message, 'success');
                            this.closeUserModal();
                            await this.fetchUsers();
                            await this.fetchStats();
                        } else {
                            if (result.errors) {
                                const errorMessages = Object.values(result.errors).flat().join('\n');
                                this.showToast('Validation Error:\n' + errorMessages, 'error');
                            } else {
                                this.showToast(result.message || 'An error occurred', 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error saving user:', error);
                        this.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.saving = false;
                    }
                },

                // Delete user
                confirmDelete(user) {
                    this.userToDelete = user;
                    this.showDeleteModal = true;
                },

                async confirmDeleteAction() {
                    this.deleting = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch(`/admin/users/${this.userToDelete.uuid}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showToast('User Deleted Successfully!', 'success');
                            this.closeDeleteModal();
                            await this.fetchUsers();
                            await this.fetchStats();
                        } else {
                            this.showToast(data.message || 'Failed to delete user', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting user:', error);
                        this.showToast('Network error. Please try again.', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                // Helper methods
                resetUserForm() {
                    this.userForm = {
                        id: null,
                        uuid: null,
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        role_ids: [],
                        branch_ids: [],
                        primary_branch_id: null,
                        is_active: true
                    };
                },

                getInitials(name) {
                    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
                },

                getAvatarColor(name) {
                    const colors = [
                        'bg-gradient-to-br from-blue-400 to-blue-600',
                        'bg-gradient-to-br from-green-400 to-green-600',
                        'bg-gradient-to-br from-purple-400 to-purple-600',
                        'bg-gradient-to-br from-pink-400 to-pink-600',
                        'bg-gradient-to-br from-yellow-400 to-yellow-600',
                        'bg-gradient-to-br from-indigo-400 to-indigo-600',
                        'bg-gradient-to-br from-red-400 to-red-600',
                        'bg-gradient-to-br from-teal-400 to-teal-600'
                    ];
                    const index = name.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length;
                    return colors[index];
                },

                getRoleBadgeClass(roleName) {
                    const roleMap = {
                        'super_admin': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-purple-600 to-indigo-800 border border-purple-400',
                        'admin': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-indigo-400 to-indigo-600 border border-indigo-200',
                        'doctor': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-sky-400 to-sky-600  border border-blue-200',
                        'nurse': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-pink-400 to-pink-600  border border-pink-200',
                        'pharmacy': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-jade-400 to-teal-600  border border-green-200',
                        'lab': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-cyan-400 to-cyan-600  border border-cyan-200',
                        'receptionist': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-amber-400 to-amber-600  border border-amber-200',
                        'reception': 'text-xs font-bold text-white shadow-lg bg-gradient-to-br from-amber-400 to-amber-600  border border-amber-200'
                    };
                    return roleMap[roleName?.toLowerCase()] || 'bg-gray-50 text-gray-700 border border-gray-200';
                },

                getRoleIconClass(roleName) {
                    const roleMap = {
                        'super_admin': 'fas fa-user-shield',
                        'admin': 'fas fa-shield-alt',
                        'doctor': 'fas fa-stethoscope',
                        'nurse': 'fas fa-user-nurse',
                        'pharmacy': 'fas fa-pills',
                        'lab': 'fas fa-flask',
                        'reception': 'fas fa-user-plus',
                        'receptionist': 'fas fa-user-plus'
                    };
                    return roleMap[roleName?.toLowerCase()] || 'fas fa-user-circle';
                },

                // Role icon mapping
                getRoleIcon(roleName) {
                    const role = (roleName || '').toLowerCase();
                    const iconMap = {
                        'super_admin': 'fa-user-shield text-gray-600',
                        'admin': 'fa-shield-alt text-gray-600',
                        'administrator': 'fa-shield-alt text-gray-600',
                        'doctor': 'fas fa-stethoscope text-gray-600',
                        'pharmacy': 'fa-pills text-gray-600',
                        'pharmacist': 'fa-pills text-gray-600',
                        'reception': 'fas fa-user-plus text-gray-600',
                        'receptionist': 'fas fa-user-plus text-gray-600',
                        'nurse': 'fa-user-nurse text-gray-600',
                        'lab': 'fa-flask text-gray-600',
                        'technician': 'fa-flask text-gray-600',
                        'lab technician': 'fa-flask text-gray-600'
                    };

                    // Find matching icon or return default
                    for (let key in iconMap) {
                        if (role.includes(key)) {
                            return iconMap[key];
                        }
                    }
                    return 'fa-user text-gray-600';
                },

                // Last login helper methods
                getLastLoginText(lastLogin) {
                    if (!lastLogin || lastLogin === 'Invalid Date' || lastLogin === null) {
                        return 'Never';
                    }
                    return this.formatDate(lastLogin);
                },

                getLastLoginTitle(lastLogin) {
                    if (!lastLogin || lastLogin === 'Invalid Date' || lastLogin === null) {
                        return 'User has never logged in';
                    }
                    return `Last login on ${this.formatDate(lastLogin)}`;
                },

                getLastLoginTooltip(lastLogin) {
                    if (!lastLogin || lastLogin === 'Invalid Date' || lastLogin === null) {
                        return 'Never logged in';
                    }
                    return 'Last Login';
                },

                // Enhanced date formatter with validation
                formatDate(dateString) {
                    if (!dateString || dateString === 'Invalid Date' || dateString === null) {
                        return 'N/A';
                    }

                    try {
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) {
                            return 'N/A';
                        }

                        // Format as needed - adjust based on your requirements
                        return date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    } catch (e) {
                        return 'N/A';
                    }
                },

                // Show toast notification
                showToast(message, type = 'info') {
                    if (window.showNotification) {
                        window.showNotification(message, type);
                    } else if (window.toastr) {
                        window.toastr[type](message);
                    } else {
                        alert(message);
                    }
                },

                // Modal controls
                closeUserModal() {
                    this.showUserModal = false;
                    this.editingUser = false;
                    this.resetUserForm();
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.userToDelete = null;
                },

                closeDeactivateModal() {
                    this.showDeactivateModal = false;
                    this.selectedUser = null;
                },

                closeResetPasswordModal() {
                    this.showResetPasswordModal = false;
                    this.selectedUser = null;
                },

                closePermissionsModal() {
                    this.showPermissionsModal = false;
                    this.selectedUser = null;
                }
            };
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
