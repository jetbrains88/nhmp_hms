@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-8" x-data="permissionManagement()" x-init="init()" x-cloak>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
            <p class="text-gray-500 mt-1">Configure and manage granular system access permissions.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openAddModal()"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all shadow-lg hover:shadow-xl font-bold transform hover:-translate-y-0.5">
                <i class="fas fa-plus"></i>
                <span>Add Permission</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Permissions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <i class="fas fa-shield-alt text-6xl text-blue-600"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Permissions</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.total">0</h3>
                <div class="mt-4 flex items-center text-sm text-blue-600 font-medium">
                    <span class="bg-blue-50 px-2 py-1 rounded-lg">System-wide</span>
                </div>
            </div>
        </div>

        <!-- Active Permissions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle text-6xl text-emerald-600"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.active">0</h3>
                <div class="mt-4 flex items-center text-sm text-emerald-600 font-medium">
                    <span class="bg-emerald-50 px-2 py-1 rounded-lg">Currently In Use</span>
                </div>
            </div>
        </div>

        <!-- Inactive Permissions -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <i class="fas fa-ban text-6xl text-rose-600"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Inactive</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.inactive">0</h3>
                <div class="mt-4 flex items-center text-sm text-rose-600 font-medium">
                    <span class="bg-rose-50 px-2 py-1 rounded-lg">Disabled</span>
                </div>
            </div>
        </div>

        <!-- Permission Groups -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                <i class="fas fa-layer-group text-6xl text-purple-600"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Groups</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2" x-text="stats.groups">0</h3>
                <div class="mt-4 flex items-center text-sm text-purple-600 font-medium">
                    <span class="bg-purple-50 px-2 py-1 rounded-lg">Module Categories</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Permissions List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- List Toolbar -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Search and Filters -->
                <div class="flex flex-1 flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-96 group">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"></i>
                        <input type="text" 
                            x-model="searchQuery"
                            @input.debounce.300ms="searchPermissions()"
                            placeholder="Search by name or group..." 
                            class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none shadow-sm">
                    </div>
                    
                    <button @click="showAdvancedFilters = !showAdvancedFilters" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all shadow-sm font-medium">
                        <i class="fas fa-filter text-blue-600"></i>
                        <span>Filters</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="showAdvancedFilters ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <button @click="clearFilters()" x-show="searchQuery || filterStatus || filterGroup"
                        class="text-sm text-red-600 font-medium hover:text-red-700 underline underline-offset-4 decoration-2">
                        Clear All
                    </button>
                </div>

                <div class="flex items-center gap-3 self-end lg:self-auto">
                    <button @click="fetchPermissions()" 
                        class="p-2.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all"
                        title="Refresh Data">
                        <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                    </button>
                </div>
            </div>

            <!-- Collapsible Advanced Filters -->
            <div x-show="showAdvancedFilters" x-collapse
                class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Group Filter</label>
                        <select x-model="filterGroup" @change="searchPermissions()"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            <option value="">All Groups</option>
                            <template x-for="group in availableGroups" :key="group">
                                <option :value="group" x-text="formatGroupName(group)"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <select x-model="filterStatus" @change="searchPermissions()"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto relative min-h-[400px]">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <button @click="sortBy('display_name')" class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                Permission Name
                                <i class="fas" :class="sortField === 'display_name' ? (sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-gray-300'"></i>
                            </button>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <button @click="sortBy('group')" class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                Group
                                <i class="fas" :class="sortField === 'group' ? (sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-gray-300'"></i>
                            </button>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <button @click="sortBy('is_active')" class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                Status
                                <i class="fas" :class="sortField === 'is_active' ? (sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort text-gray-300'"></i>
                            </button>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="permission in permissions" :key="permission.id">
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900" x-text="permission.display_name"></div>
                                        <div class="text-xs text-mono text-gray-400" x-text="permission.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold uppercase tracking-wider" x-text="formatGroupName(permission.group)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="permission.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                    class="px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="permission.is_active ? 'bg-emerald-500' : 'bg-red-500'"></span>
                                    <span x-text="permission.is_active ? 'Active' : 'Inactive'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="editPermission(permission)"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                        title="Edit Permission">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="toggleStatus(permission)"
                                        :class="permission.is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50'"
                                        class="p-2 rounded-lg transition-all"
                                        :title="permission.is_active ? 'Deactivate' : 'Activate'">
                                        <i class="fas" :class="permission.is_active ? 'fa-ban' : 'fa-check-circle'"></i>
                                    </button>
                                    <button @click="confirmDelete(permission)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        title="Delete Permission">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>

                <!-- Loading State -->
                <tbody x-show="loading">
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-10 h-10 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                                <span class="font-medium">Loading permissions...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody x-show="!loading && permissions.length === 0">
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-400">
                                <i class="fas fa-shield-alt text-6xl opacity-20"></i>
                                <div class="max-w-xs mx-auto">
                                    <p class="text-xl font-bold text-gray-900 mb-1">No Permissions Found</p>
                                    <p class="text-sm">We couldn't find any permissions matching your criteria.</p>
                                </div>
                                <button @click="clearFilters()" class="text-blue-600 font-bold hover:underline">Clear all filters</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && permissions.length > 0" class="p-6 bg-gray-50/50 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-500 font-medium">
                    Showing <span class="text-gray-900" x-text="pagination.from"></span> to 
                    <span class="text-gray-900" x-text="pagination.to"></span> of 
                    <span class="text-gray-900" x-text="pagination.total"></span> permissions
                </div>
                <div class="flex items-center gap-2">
                    <button @click="changePage(pagination.current_page - 1)" 
                        :disabled="pagination.current_page === 1"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="page in getPageRange()" :key="page">
                        <button @click="page !== '...' && changePage(page)"
                            :class="page === pagination.current_page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            :disabled="page === '...'"
                            class="w-10 h-10 rounded-lg border text-sm font-bold transition-all"
                            x-text="page">
                        </button>
                    </template>
                    <button @click="changePage(pagination.current_page + 1)" 
                        :disabled="pagination.current_page === pagination.last_page"
                        class="p-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" 
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity" @click="closeAddModal"></div>

            <div x-show="showAddModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form @submit.prevent="savePermission">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold text-white" x-text="editing ? 'Edit Permission' : 'Add New Permission'"></h3>
                            <button type="button" @click="closeAddModal" class="text-white/60 hover:text-white transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Display Name</label>
                            <input type="text" x-model="form.display_name" required
                                placeholder="e.g. View Dashboard"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">System Name (Unique Key)</label>
                            <input type="text" x-model="form.name" required
                                placeholder="e.g. view_dashboard"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                            <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">Must be lowercase with underscores</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Module Group</label>
                            <input type="text" x-model="form.group" required
                                placeholder="e.g. users_management"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 outline-none transition-all">
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" @click="closeAddModal"
                            class="px-6 py-2.5 text-gray-600 font-bold hover:bg-gray-200 rounded-xl transition-all">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving"
                            class="px-8 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                            <span x-show="!saving" x-text="editing ? 'Update' : 'Create'"></span>
                            <span x-show="saving" class="flex items-center gap-2">
                                <i class="fas fa-circle-notch animate-spin"></i>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" @click="showDeleteModal = false"
                class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"></div>

            <div x-show="showDeleteModal"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full p-8 text-center">
                
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trash-alt text-3xl text-red-600"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirm Delete</h3>
                <p class="text-gray-500 mb-8">Are you sure you want to delete <span class="font-bold text-gray-900" x-text="permissionToDelete?.display_name"></span>? This action cannot be undone.</p>
                
                <div class="grid grid-cols-2 gap-3">
                    <button @click="showDeleteModal = false"
                        class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition-all">
                        Cancel
                    </button>
                    <button @click="confirmDeleteAction" :disabled="deleting"
                        class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all flex items-center justify-center gap-2">
                        <span x-show="!deleting">Delete</span>
                        <i x-show="deleting" class="fas fa-circle-notch animate-spin"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function permissionManagement() {
        return {
            permissions: [],
            stats: { total: 0, active: 0, inactive: 0, groups: 0 },
            loading: false,
            saving: false,
            deleting: false,
            showAddModal: false,
            showAdvancedFilters: false,
            showDeleteModal: false,
            editing: false,
            searchQuery: '',
            filterGroup: '',
            filterStatus: '',
            sortField: 'name',
            sortDirection: 'asc',
            permissionToDelete: null,
            availableGroups: [],
            
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0,
                from: 0,
                to: 0
            },
            
            form: {
                id: null,
                name: '',
                display_name: '',
                group: ''
            },
            
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
                    
                    // Update available groups if empty
                    if (this.availableGroups.length === 0) {
                        const groups = [...new Set(this.permissions.map(p => p.group))];
                        this.availableGroups = groups;
                    }
                } catch (e) {
                    this.showToast('Failed to load permissions', 'error');
                } finally {
                    this.loading = false;
                }
            },
            
            async fetchStats() {
                try {
                    const response = await fetch('/admin/permissions/stats');
                    this.stats = await response.json();
                } catch (e) {
                    console.error('Error fetching stats:', e);
                }
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
            
            clearFilters() {
                this.searchQuery = '';
                this.filterGroup = '';
                this.filterStatus = '';
                this.pagination.current_page = 1;
                this.fetchPermissions();
            },
            
            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.pagination.current_page = page;
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
            
            openAddModal() {
                this.editing = false;
                this.form = { id: null, name: '', display_name: '', group: '' };
                this.showAddModal = true;
            },
            
            editPermission(permission) {
                this.editing = true;
                this.form = {
                    id: permission.id,
                    name: permission.name,
                    display_name: permission.display_name,
                    group: permission.group
                };
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
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.closeAddModal();
                        this.fetchPermissions();
                        this.fetchStats();
                    } else {
                        this.showToast(result.message || 'Error saving permission', 'error');
                    }
                } catch (e) {
                    this.showToast('Network error', 'error');
                } finally {
                    this.saving = false;
                }
            },
            
            async toggleStatus(permission) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(`/admin/permissions/${permission.id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        permission.is_active = result.is_active;
                        this.fetchStats();
                    }
                } catch (e) {
                    this.showToast('Network error', 'error');
                }
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
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.showToast(result.message, 'success');
                        this.showDeleteModal = false;
                        this.fetchPermissions();
                        this.fetchStats();
                    }
                } catch (e) {
                    this.showToast('Network error', 'error');
                } finally {
                    this.deleting = false;
                }
            },
            
            formatGroupName(group) {
                if (!group) return 'Default';
                return group.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
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

@section('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endsection