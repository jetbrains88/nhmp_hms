@extends('layouts.app')

@section('title', 'User Management - NHMP HMS')
@section('page-title', 'User Management')
@section('breadcrumb', 'Administration / Users')

@section('content')
    <div x-data="userManagement({{ json_encode($roles) }}, {{ json_encode($branches) }})" x-init="init()" class="space-y-6">

        <!-- Light Themed Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

            <!-- Total Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('all')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-2xl drop-shadow-md text-blue-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                        Total Users
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono"
                        x-text="stats.total ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"
                            :class="{ 'animate-pulse': stats.total > 0 }"></span>
                        <span class="text-xs text-blue-700 font-medium">All Registered Users</span>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                            <span class="font-semibold">Click to view all users</span>
                            <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('active')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-check text-2xl drop-shadow-md text-emerald-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">
                        Active Users
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono"
                        x-text="stats.active ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600"
                            :class="{ 'animate-pulse': stats.active > 0 }"></span>
                        <span class="text-xs text-teal-700 font-medium">Currently Active</span>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                            <span class="font-semibold">Click to view active users</span>
                            <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Administrators Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('admins')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-shield text-2xl drop-shadow-md text-purple-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                        Administrators
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono"
                        x-text="stats.admins ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"
                            :class="{ 'animate-pulse': stats.admins > 0 }"></span>
                        <span class="text-xs text-purple-700 font-medium">System Admins</span>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                            <span class="font-semibold">Click to view administrators</span>
                            <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Users Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('inactive')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-orange-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-2xl drop-shadow-md text-rose-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">
                        Inactive Users
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono"
                        x-text="stats.inactive ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"
                            :class="{ 'animate-pulse': stats.inactive > 0 }"></span>
                        <span class="text-xs text-rose-700 font-medium">Inactive Accounts</span>
                    </div>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                            <span class="font-semibold">Click to view inactive users</span>
                            <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Users List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                            <i class="fas fa-users-cog text-blue-600"></i>
                            User Management
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Manage system users, roles, and permissions
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="pagination.per_page" @change="fetchUsers()"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button @click="clearFilters()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                            <button @click="showAdvancedFilters = !showAdvancedFilters"
                                :class="showAdvancedFilters ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' :
                                    'bg-gradient-to-r from-orange-500 to-orange-600 text-white'"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-filter"></i>
                                Filters
                            </button>
                            <!-- Add User Button -->
                            <button @click="openAddModal()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-user-plus"></i>
                                Add User
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

                    <!-- First Row - Search, Status, Role (3 columns) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchUsers()"
                                placeholder="Search users by name, email..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select x-model="filterStatus" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <select x-model="filters.role" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Roles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Second Row - Date From, Date To, Clear Button -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <!-- Registration From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration From</label>
                            <input type="date" x-model="registrationFrom" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Registration To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration To</label>
                            <input type="date" x-model="registrationTo" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Clear All Filters Button -->
                        <div class="flex items-end">
                            <button @click="clearFilters()"
                                class="w-full flex items-center justify-center text-white py-2.5
                       text-center bg-gradient-to-r from-rose-500 to-rose-600
                       rounded-lg font-medium hover:from-rose-600 hover:to-rose-700
                       disabled:opacity-50 disabled:cursor-not-allowed transition-all
                       gap-2 shadow-md hover:shadow-lg h-[42px]">
                                <i class="fas fa-filter-circle-xmark"></i>
                                Clear All Filters
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Summary -->
                    <div x-show="searchQuery || filterStatus || filters.role || registrationFrom || registrationTo"
                        class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-purple-200">
                        <span class="text-xs font-medium text-gray-500">Active filters:</span>

                        <template x-if="searchQuery">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs">
                                <i class="fas fa-search"></i>
                                <span x-text="searchQuery"></span>
                                <button @click="searchQuery = ''; applyFilters()" class="ml-1 hover:text-blue-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <template x-if="filterStatus">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs">
                                <i class="fas"
                                    :class="filterStatus === 'active' ? 'fa-user-check' : 'fa-user-clock'"></i>
                                <span x-text="filterStatus === 'active' ? 'Active' : 'Inactive'"></span>
                                <button @click="filterStatus = ''; applyFilters()" class="ml-1 hover:text-green-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <template x-if="filters.role">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-md text-xs">
                                <i class="fas fa-user-tag"></i>
                                <span x-text="getRoleNameById(filters.role)"></span>
                                <button @click="filters.role = ''; applyFilters()" class="ml-1 hover:text-purple-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <template x-if="registrationFrom">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-700 rounded-md text-xs">
                                <i class="fas fa-calendar-start"></i>
                                <span x-text="registrationFrom"></span>
                                <button @click="registrationFrom = ''; applyFilters()" class="ml-1 hover:text-amber-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <template x-if="registrationTo">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-700 rounded-md text-xs">
                                <i class="fas fa-calendar-end"></i>
                                <span x-text="registrationTo"></span>
                                <button @click="registrationTo = ''; applyFilters()" class="ml-1 hover:text-amber-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <button @click="clearFilters()" class="text-xs text-rose-600 hover:text-rose-800 underline ml-2">
                            Clear all
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <tr>
                             <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-blue-500"></i>
                                    User Information
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-purple-500"></i>
                                    Email & Status
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-green-500"></i>
                                    Roles & Permissions
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-orange-500"></i>
                                    Activity
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cogs text-orange-500"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && users && users.length > 0">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- User Info Column (Name & Avatar) -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full flex bg-gradient-to-br from-indigo-400 to-indigo-600 items-center justify-center text-white text-lg font-bold shadow-lg">
                                                <span x-text="getInitials(user.name)"></span>
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-xl font-bold text-navy-800 truncate" x-text="user.name">
                                                </p>
                                            </div>

                                            <!-- ID and other info -->
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <i class="fas fa-id-badge mr-1 text-blue-500"></i>
                                                    <span>ID: <span x-text="user.id"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email & Status Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[160px]">
                                        <!-- Email -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                :title="`Email: ${user.email}`">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                                <span class="text-sm" x-text="user.email"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Email Address</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                :title="user.is_active ? 'Account is active' : 'Account is inactive'">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas"
                                                        :class="user.is_active ? 'fa-check-circle' : 'fa-clock'"></i>
                                                </span>
                                                <span class="text-sm"
                                                    x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Account Status</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Roles Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[200px]">
                                        <template x-for="role in user.roles" :key="role.id">
                                            <div class="group relative">
                                                <span
                                                    class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                    :title="`Role: ${role.display_name || role.name}`">
                                                    <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                        <i class="fas" :class="getRoleIcon(role.name)"></i>
                                                    </span>
                                                    <span class="text-sm">
                                                        <span x-text="role.display_name || role.name"></span>
                                                        <span class="text-xs text-gray-500 ml-1"
                                                            x-text="`(${role.name})`"
                                                            x-show="role.display_name && role.display_name !== role.name"></span>
                                                    </span>
                                                </span>
                                                <!-- Tooltip with role details -->
                                                <div
                                                    class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                    <div
                                                        class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                        <span class="font-semibold"
                                                            x-text="role.display_name || role.name"></span>
                                                        <div
                                                            class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- No roles fallback -->
                                        <div class="group relative" x-show="!user.roles || user.roles.length === 0">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-400 cursor-not-allowed"
                                                title="No roles assigned">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-400">
                                                    <i class="fas fa-user-slash"></i>
                                                </span>
                                                <span class="text-sm">No Role Assigned</span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">No roles assigned</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Activity Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[160px]">
                                        <!-- Registration Date -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                :title="user.created_at ? `Registered on ${formatDate(user.created_at)}` :
                                                    'Registration date not available'">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </span>
                                                <span class="text-sm"
                                                    x-text="user.created_at ? formatDate(user.created_at) : 'N/A'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Registration Date</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Last Login -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full"
                                                :class="user.last_login_at && user.last_login_at !== 'Invalid Date' && user
                                                    .last_login_at !== null ? 'text-gray-600 hover:text-gray-900' :
                                                    'text-gray-400 cursor-not-allowed'"
                                                :title="getLastLoginTitle(user.last_login_at)">
                                                <span class="inline-flex items-center mr-2 w-4"
                                                    :class="user.last_login_at && user.last_login_at !== 'Invalid Date' && user
                                                        .last_login_at !== null ? 'text-gray-600' : 'text-gray-400'">
                                                    <i class="fas fa-history"></i>
                                                </span>
                                                <span class="text-sm"
                                                    x-text="getLastLoginText(user.last_login_at)"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold"
                                                        x-text="getLastLoginTooltip(user.last_login_at)"></span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[120px]">
                                        <!-- View Button -->
                                        <a href="{{ url('/admin/users') }}/" :href="`{{ url('/admin/users') }}/${user.uuid}`"
                                           class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                           title="View User Details">
                                            <i class="fas fa-eye mr-2 w-4"></i>
                                            View
                                        </a>

                                        <!-- Edit Button -->
                                        <button @click="editUser(user)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Edit User">
                                            <i class="fas fa-edit mr-2 w-4"></i>
                                            Edit
                                        </button>

                                        <!-- Reset Password Button -->
                                        <button @click="openResetPasswordModal(user)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Reset Password">
                                            <i class="fas fa-key mr-2 w-4"></i>
                                            Reset
                                        </button>

                                        <!-- Status Toggle Button -->
                                        <button @click="toggleUserStatus(user)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            :title="user.is_active ? 'Deactivate User' : 'Activate User'">
                                            <i class="fas mr-2 w-4"
                                                :class="user.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                            <span x-text="user.is_active ? 'Deactivate' : 'Activate'"></span>
                                        </button>

                                        <!-- View Permissions Button -->
                                        <button @click="openPermissionsModal(user)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="View Permissions">
                                            <i class="fas fa-shield-alt mr-2 w-4"></i>
                                            Permissions
                                        </button>

                                        <!-- Delete Button -->
                                        <button @click="confirmDelete(user)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Delete User">
                                            <i class="fas fa-trash mr-2 w-4"></i>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Loading State -->
                    <tbody x-show="loading">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4">
                                    </div>
                                    <p class="text-gray-600">Loading users...</p>
                                    <p class="text-sm text-gray-400 mt-1">Please wait while we fetch the records</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!users || users.length === 0)">
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 mb-4 text-gray-300">
                                        <i class="fas fa-users-cog text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                    <p class="text-gray-500 max-w-md mb-4">
                                        <span
                                            x-show="searchQuery || filters.role || filterStatus || registrationFrom || registrationTo">
                                            Try adjusting your filters or search terms
                                        </span>
                                        <span
                                            x-show="!searchQuery && !filters.role && !filterStatus && !registrationFrom && !registrationTo">
                                            No users in the system. Start by adding a new user.
                                        </span>
                                    </p>
                                    <button @click="openAddModal()"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                        <i class="fas fa-user-plus"></i>
                                        Add First User
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && users && users.length > 0" class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-700">
                        Showing <span x-text="pagination.from"></span> to
                        <span x-text="pagination.to"></span> of
                        <span x-text="pagination.total"></span> results
                    </div>

                    <!-- Pagination Controls -->
                    <nav class="flex items-center space-x-2">
                        <!-- First Page -->
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            First
                        </button>

                        <!-- Previous Page -->
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <template x-for="page in getPageRange()" :key="page">
                            <button @click="page !== '...' && changePage(page)"
                                :class="page === pagination.current_page ?
                                    'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-blue-600' :
                                    'border-gray-300 text-gray-700 hover:bg-gray-50'"
                                :disabled="page === '...'"
                                class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                                <span x-text="page"></span>
                            </button>
                        </template>

                        <!-- Next Page -->
                        <button @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Next
                        </button>

                        <!-- Last Page -->
                        <button @click="changePage(pagination.last_page)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Last
                        </button>
                    </nav>
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
                showAdvancedFilters: false,
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
                            active: this.filters.active ? '1' : '0',
                            inactive: this.filters.inactive ? '1' : '0',
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
                        const response = await fetch('/admin/users/stats', {
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

                // Sort users
                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDirection = 'asc';
                    }
                    this.fetchUsers();
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
