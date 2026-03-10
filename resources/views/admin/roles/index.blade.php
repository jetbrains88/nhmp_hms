@extends('layouts.app')

@section('title', 'Role Management - NHMP HMS')
@section('page-title', 'Role Management')
@section('breadcrumb', 'Administration / Roles')

@section('content')
    <div x-data="roleManagement({{ json_encode($permissions) }})" x-init="init()" class="space-y-6">

        <!-- Light Themed Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

            <!-- Total Roles Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="clearFilters()" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-tag text-2xl drop-shadow-md text-blue-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                        Total Roles
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono"
                        x-text="stats.total ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"
                            :class="{ 'animate-pulse': stats.total > 0 }"></span>
                        <span class="text-xs text-blue-700 font-medium">System Roles</span>
                    </div>
                </div>
            </div>

            <!-- Roles with Permissions Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-shield-alt text-2xl drop-shadow-md text-emerald-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">
                        Configured Roles
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono"
                        x-text="stats.with_permissions ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600"
                            :class="{ 'animate-pulse': stats.with_permissions > 0 }"></span>
                        <span class="text-xs text-teal-700 font-medium">Roles with permissions</span>
                    </div>
                </div>
            </div>

            <!-- System Roles Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-cogs text-2xl drop-shadow-md text-purple-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                        System Roles
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono"
                        x-text="stats.system_roles ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"
                            :class="{ 'animate-pulse': stats.system_roles > 0 }"></span>
                        <span class="text-xs text-purple-700 font-medium">Built-in roles</span>
                    </div>
                </div>
            </div>

            <!-- Permission Groups Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-orange-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-layer-group text-2xl drop-shadow-md text-rose-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">
                        Perm Groups
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono">
                        {{ count($permissions) }}
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"
                            :class="{ 'animate-pulse': true }"></span>
                        <span class="text-xs text-rose-700 font-medium">Permission categories</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Roles List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                            <i class="fas fa-user-shield text-blue-600"></i>
                            Role Management
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Manage system roles and their permissions
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="pagination.per_page" @change="fetchRoles()"
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
                            <!-- Add Role Button -->
                            <a href="{{ route('admin.roles.create') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-plus"></i>
                                Add Role
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

                    <!-- First Row - Search -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchRoles()"
                                placeholder="Search roles by name, display name..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>
                    </div>

                    <!-- Clear All Filters Button -->
                    <div x-show="searchQuery" class="flex justify-end mt-4">
                        <button @click="clearFilters()"
                            class="flex items-center justify-center text-white px-4 py-2
                   text-center bg-gradient-to-r from-rose-500 to-rose-600
                   rounded-lg font-medium hover:from-rose-600 hover:to-rose-700
                   transition-all gap-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-filter-circle-xmark"></i>
                            Clear All Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <tr>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-gray-600"></i>
                                    ID & Name
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-gray-600"></i>
                                    Permissions
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-gray-600"></i>
                                    Created At
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cogs text-gray-600"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && roles && roles.length > 0">
                        <template x-for="role in roles" :key="role.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- Role Info Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-lg flex items-center justify-center text-white shadow-md bg-gradient-to-tr from-indigo-500 to-purple-400">
                                                <i class="fas" :class="getRoleIcon(role.name)"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-navy-800" x-text="role.display_name"></div>
                                            <div class="text-xs text-gray-400 font-mono" x-text="role.name"></div>
                                            <div class="text-xs text-gray-400 mt-1">ID: #<span x-text="role.id"></span></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Permissions Column -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        <template x-for="permission in role.permissions.slice(0, 5)" :key="permission.id">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                                <span x-text="permission.display_name"></span>
                                            </span>
                                        </template>
                                        <template x-if="role.permissions.length > 5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                +<span x-text="role.permissions.length - 5"></span> more
                                            </span>
                                        </template>
                                        <template x-if="role.permissions.length === 0">
                                            <span class="text-gray-400 text-xs italic">No permissions assigned</span>
                                        </template>
                                    </div>
                                </td>

                                <!-- Activity Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt mr-2 text-cyan-500"></i>
                                        <span x-text="formatDate(role.created_at)"></span>
                                    </div>
                                </td>

                                <!-- Actions Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 w-fit">
                                        <a :href="'{{ route('admin.roles.edit', 'role_id') }}'.replace('role_id', role.id)"
                                            class="inline-flex items-center justify-center px-3 py-1.5 text-gray-600 hover:text-gray-900 border border-gray-100 bg-white rounded-lg transition-colors w-full"
                                            title="View Permissions">
                                            <i class="fas fa-shield-alt mr-1.5 text-xs"></i>
                                            Permissions
                                        </a>

                                        <a :href="'{{ route('admin.roles.edit', 'role_id') }}'.replace('role_id', role.id)"
                                            class="inline-flex items-center justify-center px-3 py-1.5 text-gray-600 hover:text-gray-900 border border-gray-100 bg-white rounded-lg transition-colors w-full"
                                            title="Edit Role">
                                            <i class="fas fa-edit mr-1.5 text-xs"></i>
                                            Edit
                                        </a>

                                        <template x-if="role.id > 5">
                                            <div class="flex flex-col space-y-2">
                                                <button @click="toggleRoleStatus(role)"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-gray-600 hover:text-gray-900 border border-gray-100 bg-white rounded-lg transition-colors w-full">
                                                    <i class="fas mr-1.5 text-xs" :class="role.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                                    <span x-text="role.is_active ? 'Deactivate' : 'Activate'"></span>
                                                </button>

                                                <button @click="confirmDelete(role)"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 text-gray-600 hover:text-gray-900 border border-gray-100 bg-white rounded-lg transition-colors w-full"
                                                    title="Delete Role">
                                                    <i class="fas fa-trash-alt mr-1.5 text-xs"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </template>
                                        
                                        <template x-if="role.id <= 5">
                                            <span class="text-gray-400 text-[10px] italic text-center px-2 py-1 bg-gray-50 rounded italic border border-gray-100">System Protected</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Loading State -->
                    <tbody x-show="loading">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                                    <p class="text-gray-600">Loading roles...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!roles || roles.length === 0)">
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 mb-4 text-gray-300">
                                        <i class="fas fa-user-tag text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No roles found</h3>
                                    <a href="{{ route('admin.roles.create') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                        <i class="fas fa-plus"></i>
                                        Add First Role
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && roles && roles.length > 0" class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> results
                    </div>
                    <nav class="flex items-center space-x-2">
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 text-sm">First</button>
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 text-sm">Prev</button>
                        <template x-for="page in getPageRange()" :key="page">
                            <button @click="page !== '...' && changePage(page)"
                                :class="page === pagination.current_page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                                :disabled="page === '...'"
                                class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 text-sm">Next</button>
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 text-sm">Last</button>
                    </nav>
                </div>
            </div>
        </div>




        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDeleteModal = false"></div>
                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Delete Role</h3>
                            <p class="text-sm text-gray-500">Are you sure you want to delete this role? This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="showDeleteModal = false" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button @click="confirmDeleteAction()" :disabled="deleting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                            <span>Delete Role</span>
                            <i x-show="deleting" class="fas fa-spinner fa-spin ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

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
                saving: false,
                deleting: false,
                showDeleteModal: false,
                showAdvancedFilters: false,
                roleToDelete: null,
                searchQuery: '',
                groupedPermissions: groupedPermissions,
                
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },
                
                stats: {
                    total: 0,
                    with_permissions: 0,
                    without_permissions: 0,
                    system_roles: 0
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
                            sort: 'name',
                            direction: 'asc'
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
                        this.showToast('Error fetching roles', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchStats() {
                    try {
                        const response = await fetch('/admin/roles/stats', {
                            headers: { 'Accept': 'application/json' }
                        });
                        this.stats = await response.json();
                    } catch (e) {
                        console.error('Error fetching stats:', e);
                    }
                },

                searchRoles() {
                    this.pagination.current_page = 1;
                    this.fetchRoles();
                },

                clearFilters() {
                    this.searchQuery = '';
                    this.pagination.current_page = 1;
                    this.fetchRoles();
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
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
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

                formatDate(dateString) {
                    if (!dateString || dateString === 'Invalid Date' || dateString === null) {
                        return 'N/A';
                    }
                    try {
                        const date = new Date(dateString);
                        if (isNaN(date.getTime())) return 'N/A';
                        return date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    } catch (e) {
                        return 'N/A';
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

                formatGroupName(group) {
                    if (!group) return 'Default';
                    return group.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                },

                isGroupSelected(group) {
                    const groupPerms = this.groupedPermissions[group];
                    if (!groupPerms || groupPerms.length === 0) return false;
                    return groupPerms.every(p => this.roleForm.permissions.includes(p.id));
                },

                toggleGroup(group, selected) {
                    const groupPerms = this.groupedPermissions[group].map(p => p.id);
                    if (selected) {
                        this.roleForm.permissions = [...new Set([...this.roleForm.permissions, ...groupPerms])];
                    } else {
                        this.roleForm.permissions = this.roleForm.permissions.filter(id => !groupPerms.includes(id));
                    }
                },

                selectAllPermissions() {
                    const allPerms = Object.values(this.groupedPermissions).flat().map(p => p.id);
                    this.roleForm.permissions = allPerms;
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

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
