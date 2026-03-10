@extends('layouts.app')

@section('title', 'Branch Management - NHMP HMS')
@section('page-title', 'Branch Management')
@section('breadcrumb', 'Administration / Branches')

@section('content')
<div x-data="branchManagement({{ json_encode($offices) }})" x-init="init()" class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 gap-y-10 mt-8 p-4">

        <!-- Total Branches Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="clearFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-hospital text-2xl drop-shadow-md text-blue-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">Total Branches</p>
                <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono" x-text="stats.total ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                    <span class="text-xs text-blue-700 font-medium">All Hospital Branches</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to clear filters & view all</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Centers Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="filterStatus = 'active'; applyFilters()"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-check-double text-2xl drop-shadow-md text-emerald-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">Active Centers</p>
                <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono" x-text="stats.active ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-teal-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-teal-600" :class="{ 'animate-pulse': stats.active > 0 }"></span>
                    <span class="text-xs text-teal-700 font-medium">Currently Operating</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to view active branches</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Staff Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
             @click.throttle.500ms="window.location.href='/admin/users'"
             :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">
            <div class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-2xl drop-shadow-md text-purple-700"></i>
            </div>
            <div class="p-4 text-right pt-6">
                <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">Global Staff</p>
                <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono" x-text="stats.staff ?? 0"></h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600" :class="{ 'animate-pulse': stats.staff > 0 }"></span>
                    <span class="text-xs text-purple-700 font-medium">Total System Users</span>
                </div>
                <div class="absolute bottom-full left-4 mb-2 hidden group-hover:block z-10">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                        <span class="font-semibold">Click to go to User Management</span>
                        <div class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Branches List -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Header with Filters -->
        <div class="bg-gradient-to-r from-slate-50 to-indigo-50 p-6 border-b border-indigo-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                        <i class="fas fa-hospital-alt text-indigo-600"></i>
                        Hospital Network
                        <span class="text-lg font-normal text-gray-600">(<span x-text="pagination.total"></span> records)</span>
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Manage medical centers and administrative offices</p>
                </div>

                <div class="flex flex-wrap gap-3 items-center">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-slate-600">Show:</span>
                        <select x-model="pagination.per_page" @change="fetchBranches()"
                                class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button @click="clearFilters()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all text-sm font-bold shadow-md shadow-blue-200">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button @click="showAdvancedFilters = !showAdvancedFilters"
                                :class="showAdvancedFilters
                                    ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 border-green-500 shadow-green-200'
                                    : 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 border-orange-500 shadow-orange-200'"
                                class="flex items-center gap-2 px-4 py-2 text-white border rounded-xl transition-all text-sm font-bold shadow-md">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <button @click="openAddModal()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl transition-all text-sm font-bold shadow-md shadow-indigo-200">
                            <i class="fas fa-plus-circle"></i> New Branch
                        </button>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div x-show="showAdvancedFilters" x-transition class="mt-6 bg-white p-6 rounded-2xl border border-indigo-100 shadow-md grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchBranches()"
                           placeholder="Search branches..."
                           class="pl-10 w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <select x-model="filterType" @change="fetchBranches()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none">
                    <option value="">All Types</option>
                    <option value="CMO">CMO</option>
                    <option value="RMO">RMO</option>
                </select>
                <select x-model="filterStatus" @change="fetchBranches()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 outline-none">
                    <option value="">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
                <!-- Clear All Filters Button -->
                <div class="flex items-end">
                    <button @click="clearFilters()"
                            class="w-full flex items-center justify-center text-white py-2.5
                                   text-center bg-gradient-to-r from-rose-500 to-rose-600
                                   rounded-xl font-bold hover:from-rose-600 hover:to-rose-700
                                   disabled:opacity-50 disabled:cursor-not-allowed transition-all
                                   gap-2 shadow-md hover:shadow-lg h-[46px]">
                        <i class="fas fa-filter-circle-xmark"></i>
                        Clear All Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Branches Table -->
        <div class="overflow-x-auto min-h-[400px]">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-hospital text-gray-600"></i>
                                Branch Profile
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-gray-600"></i>
                                Location Info
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-users text-gray-600"></i>
                                Staff Count
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-toggle-on text-gray-600"></i>
                                Status
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end gap-2">
                                <i class="fas fa-cogs text-gray-600"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50" x-show="!loading">
                    <template x-for="branch in branches" :key="branch.id">
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-indigo-400 to-indigo-600 text-white text-xl font-bold shadow-sm group-hover:scale-105 transition-transform">
                                        <i class="fas fa-hospital text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-navy-800 text-base" x-text="branch.name"></div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-tighter" x-text="branch.type"></span>
                                            <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="branch.office?.name || 'Level 1'"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <i class="fas fa-map-marker-alt text-slate-300"></i>
                                    <span class="text-sm font-medium" x-text="branch.location || 'Headquarters'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-2.5 py-1 rounded-full border border-indigo-100" x-text="branch.users?.length || 0"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleBranchStatus(branch)"
                                        class="relative inline-flex items-center h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                        :class="branch.is_active ? 'bg-emerald-500' : 'bg-slate-200'">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                                          :class="branch.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex flex-col space-y-2 min-w-[120px]">
                                    <a :href="`/admin/branches/${branch.id}`"
                                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left text-sm font-bold" title="View Details">
                                        <i class="fas fa-search-plus mr-2 w-4"></i>
                                        View
                                    </a>
                                    <button @click="editBranch(branch)"
                                       class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left text-sm font-bold" title="Modify">
                                        <i class="fas fa-edit mr-2 w-4"></i>
                                        Modify
                                    </button>
                                    <button type="button" @click="confirmArchive(branch)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left text-sm font-bold" title="Archive">
                                        <i class="fas fa-trash-alt mr-2 w-4"></i>
                                        Archive
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>

                <!-- Loading State -->
                <tbody x-show="loading">
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mb-4"></div>
                                <p class="text-slate-500 font-bold">Fetching branches...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>

                <!-- Empty State -->
                <tbody x-show="!loading && branches.length === 0">
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center mb-4">
                                    <i class="fas fa-hospital text-4xl text-slate-200"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">No Branches Found</h3>
                                <p class="text-slate-500 text-sm max-w-xs mx-auto">Try adjusting your filters or add a new branch to the network.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div x-show="!loading && pagination.last_page > 1" class="px-8 py-6 bg-slate-50 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm font-bold text-slate-500">
                    Showing <span x-text="pagination.from"></span> - <span x-text="pagination.to"></span> of <span x-text="pagination.total"></span> records
                </p>
                <div class="flex gap-2">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold disabled:opacity-50">Previous</button>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold disabled:opacity-50">Next</button>
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
        saving: false,
        deleting: false,

        // Filters
        searchQuery: '',
        filterType: '',
        filterStatus: '',
        filterOffice: '',
        showAdvancedFilters: false,

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
            this.pagination.current_page = page;
            this.fetchBranches();
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