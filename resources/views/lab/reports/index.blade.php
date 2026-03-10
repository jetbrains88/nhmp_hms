@extends('layouts.app')

@section('title', 'Lab Reports - NHMP HMS')
@section('page-title', 'Laboratory Reports')
@section('breadcrumb', 'Lab Reports')

@section('content')
    <div x-data="labReportsIndex()" x-init="init()" class="space-y-6">

        <!-- Light Themed Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 gap-y-10 mt-8 p-4">

            <!-- Total Reports Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer text-sky-800"
                @click="setFilter('all')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-sky-500 to-blue-100 shadow-lg shadow-blue-900/40 text-sky-800 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-flask text-2xl drop-shadow-md text-sky-600"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-sky-500 uppercase">
                        Total Reports
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-sky-800 drop-shadow-md font-mono"
                        x-text="stats.total">154</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-sky-500 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-900 animate-pulse"></span>
                        <span class="text-xs text-sky-900 font-medium">System Active</span>
                    </div>
                </div>
            </div>


            <!-- Completed Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('completed')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-100 shadow-lg shadow-emerald-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-check-circle text-2xl drop-shadow-md text-emerald-600"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-500 uppercase">
                        Completed
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono"
                        x-text="stats.completed">61</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-500 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-800"></span>
                        <span class="text-xs text-teal-800 font-medium">Successfully Closed</span>
                    </div>
                </div>
            </div>

            <!-- Processing Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-violet-50 to-fuchsia-50 rounded-2xl shadow-lg shadow-violet-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('processing')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-violet-500 to-purple-100 shadow-lg shadow-violet-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-cogs text-2xl drop-shadow-md text-violet-600"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-fuchsia-500 uppercase">
                        Processing
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-fuchsia-800 drop-shadow-md font-mono"
                        x-text="stats.processing">49</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-fuchsia-500 pt-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-xs text-fuchsia-800"></i>
                        <span class="text-xs text-fuchsia-800 font-medium">In Progress...</span>
                    </div>
                </div>
            </div>

            <!-- Pending Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-lg shadow-amber-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('pending')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-yellow-600 to-amber-100 shadow-lg shadow-amber-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-2xl drop-shadow-md text-amber-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-amber-500 uppercase">
                        Pending
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-amber-800 drop-shadow-md font-mono"
                        x-text="stats.pending">30</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-orange-500 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 text-amber-800 rounded-full bg-amber-800 animate-pulse"></span>
                        <span class="text-xs text-amber-800 font-medium">Awaiting Action</span>
                    </div>
                </div>
            </div>

            <!-- Urgent Card - Using red instead of maroon -->
            <div class="relative flex flex-col bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-lg shadow-orange-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('urgent')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-red-500 to-orange-100 shadow-lg shadow-red-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-2xl drop-shadow-md text-red-600 animate-pulse"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-red-500 uppercase">
                        Urgent
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-red-800 drop-shadow-md font-mono"
                        x-text="stats.urgent">35</h4>
                </div>
                <div class="mx-4 mb-4 border-t border-red-500 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-800 animate-ping"></span>
                        <span class="text-xs text-red-800 font-medium">Attention Required</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Reports Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                            <i class="fas fa-flask text-blue-600"></i>
                            Laboratory Reports
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Manage all laboratory test reports and results
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="pagination.per_page" @change="fetchReports()"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button @click="clearFilters()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                            <button @click="showAdvancedFilters = !showAdvancedFilters"
                                :class="showAdvancedFilters ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' :
                                    'bg-gradient-to-r from-orange-500 to-orange-600 text-white'"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-filter"></i>
                                Filters
                            </button>
                            <a href="{{ route('lab.orders.create') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-plus"></i>
                                New Report
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) - Light Themed -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

                    <!-- First Row - Search, Status, Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchReports()"
                                placeholder="Search by patient name, lab #, or test..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select x-model="filterStatus" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Priority Filter -->
                        <div>
                            <select x-model="filters.priority" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Priority</option>
                                <option value="normal">Normal</option>
                                <option value="urgent">Urgent</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                    </div>

                    <!-- Second Row - Date From, Date To, Clear Button (3-column grid) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                            <input type="date" x-model="dateFrom" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" x-model="dateTo" @change="applyFilters"
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
                </div>
            </div>

            <!-- Reports Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                     <thead class="bg-gradient-to-r from-indigo-50 to-indigo-100">
                        <tr>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-vial text-blue-500"></i>
                                    Test Details
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-injured text-purple-500"></i>
                                    Patient
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-green-500"></i>
                                    Status & Priority
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-orange-500"></i>
                                    Date
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
                    <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && reports && reports.length > 0">
                        <template x-for="report in reports" :key="report.id">
                            <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                                <!-- Test Details Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-start space-x-4">
                                        <!-- Icon - Light themed -->
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-md border"
                                                :class="getTestIconColor(report.test_type?.name || 'general')">
                                                <i class="fas" :class="getTestIcon(report.test_type?.name)"></i>
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-xl font-bold text-navy-800 truncate"
                                                    x-text="report.test_type?.name || 'Mixed/Generic Report'"></p>
                                                <span class="px-2 py-0.5 text-xs font-bold rounded"
                                                    :class="getPriorityClass(report.priority)">
                                                    <span
                                                        x-text="report.priority ? report.priority.toUpperCase() : 'NORMAL'"></span>
                                                </span>
                                            </div>

                                            <!-- Lab Number -->
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <div class="flex items-center text-xs text-gray-600">
                                                    <i class="fas fa-hashtag mr-1 text-gray-400"></i>
                                                    <span x-text="'#' + report.lab_number"></span>
                                                </div>
                                                <template x-if="report.test_type?.department">
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-building mr-1 text-gray-400"></i>
                                                        <span x-text="report.test_type.department"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Patient Column (already light themed) -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[180px]">
                                        <!-- Patient Name -->
                                        <div class="group relative">
                                            <span class="transition-colors duration-200 text-left inline-flex items-center"
                                                :title="report.patient?.name ? `View details for ${report.patient.name}` :
                                                    'No patient name available'">
                                                <i
                                                    class="fas fa-user-injured mr-2 w-4 text-gray-600"></i>
                                                <span class="text-sm text-navy-800 font-bold"
                                                    x-text="report.patient?.name || 'N/A'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Patient Name</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CNIC/ID -->
                                        <div class="group relative">
                                            <span class="transition-colors duration-200 text-left inline-flex items-center"
                                                :title="report.patient?.cnic ? `ID: ${report.patient.cnic}` :
                                                    'No CNIC available'">
                                                <i
                                                    class="fas fa-id-card mr-2 w-4 text-gray-600"></i>
                                                <span class="text-sm text-gray-600"
                                                    x-text="report.patient?.cnic || 'N/A'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">CNIC Number</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Doctor Name -->
                                        <div class="group relative">
                                            <span class="transition-colors duration-200 text-left inline-flex items-center"
                                                :title="report.doctor?.name ? `Attending: ${report.doctor.name}` :
                                                    'No doctor assigned'">
                                                <i
                                                    class="fas fa-user-md mr-2 w-4 text-gray-600"></i>
                                                <span class="text-sm text-gray-600"
                                                    x-text="report.doctor?.name || 'N/A'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Attending Doctor</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status & Priority Column (already light themed) -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[140px]">
                                        <!-- Status -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full"
                                                :class="getStatusTextClass(report.status)">
                                                <span class="inline-flex items-center mr-2 w-4">
                                                    <i class="fas" :class="getStatusIcon(report.status)"></i>
                                                </span>
                                                <span class="text-sm" x-text="formatStatus(report.status)"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Current Status</span>
                                                    <span class="ml-1"
                                                        x-text="`(${formatStatus(report.status)})`"></span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Priority -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full"
                                                :class="getPriorityTextClass(report.priority)">
                                                <span class="inline-flex items-center mr-2 w-4">
                                                    <i class="fas" :class="getPriorityIcon(report.priority)"></i>
                                                </span>
                                                <span class="text-sm" x-text="formatPriority(report.priority)"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Priority Level</span>
                                                    <span class="ml-1"
                                                        x-text="`(${formatPriority(report.priority)})`"></span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date Column (already light themed) -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[160px]">
                                        <!-- Created Date -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-800">
                                                <span class="inline-flex items-center mr-2 w-4 text-blue-600">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </span>
                                                <span class="text-xs" x-text="formatDate(report.created_at)"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Created On</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Completed Date (conditional) -->
                                        <template x-if="report.results?.length">
                                            <div class="group relative">
                                                <span
                                                    class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-800">
                                                    <span class="inline-flex items-center mr-2 w-4 text-green-600">
                                                        <i class="fas fa-check-circle"></i>
                                                    </span>
                                                    <span class="text-xs"
                                                        x-text="formatDate(report.reporting_date)"></span>
                                                </span>
                                                <!-- Tooltip -->
                                                <div
                                                    class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                    <div
                                                        class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                        <span class="font-semibold">Completed On</span>
                                                        <div
                                                            class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Placeholder when no completion date -->
                                        <template x-if="!report.results?.length">
                                            <div class="group relative">
                                                <span
                                                    class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-400 cursor-not-allowed">
                                                    <span class="inline-flex items-center mr-2 w-4 text-gray-400">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                    <span class="text-xs">Pending completion</span>
                                                </span>
                                                <!-- Tooltip -->
                                                <div
                                                    class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                    <div
                                                        class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                        <span class="font-semibold">Not yet completed</span>
                                                        <div
                                                            class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <!-- Actions Column (already light themed) -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[120px]">
                                        <!-- View/Process Button -->
                                        <a :href="`/lab/reports/${report.id}`"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            :title="report.status === 'completed' ? 'View Report' : 'Process Report'">
                                            <i class="fas mr-2 w-4"
                                                :class="report.status === 'completed' ? 'fa-eye' : 'fa-flask'"></i>
                                            <span x-text="report.status === 'completed' ? 'View' : 'Process'"></span>
                                        </a>

                                        <!-- Edit Button -->
                                        <a :href="`/lab/reports/${report.id}/edit`"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Edit Report">
                                            <i class="fas fa-edit mr-2 w-4"></i>
                                            Edit
                                        </a>

                                        <!-- Print Button (conditional) -->
                                        <template x-if="report.status === 'completed'">
                                            <a :href="`/lab/reports/${report.id}/print`" target="_blank"
                                                class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                                title="Print Report">
                                                <i class="fas fa-print mr-2 w-4"></i>
                                                Print
                                            </a>
                                        </template>
                                        <template x-if="report.status !== 'completed'">
                                            <div class="text-gray-400 cursor-not-allowed text-left"
                                                title="Print unavailable">
                                                <i class="fas fa-print mr-2 w-4"></i>
                                                Print
                                            </div>
                                        </template>

                                        <!-- Delete Button -->
                                        <button @click="openDeleteModal(report)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Delete Report">
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
                                    <p class="text-gray-700">Loading lab reports...</p>
                                    <p class="text-sm text-gray-500 mt-1">Please wait while we fetch the records</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!reports || reports.length === 0)">
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 mb-4 text-gray-300">
                                        <i class="fas fa-flask text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No lab reports found</h3>
                                    <p class="text-gray-600 max-w-md mb-4">
                                        <span
                                            x-show="searchQuery || filterStatus || filters.priority || dateFrom || dateTo">
                                            Try adjusting your filters or search terms
                                        </span>
                                        <span
                                            x-show="!searchQuery && !filterStatus && !filters.priority && !dateFrom && !dateTo">
                                            No reports in the system. Create your first lab report.
                                        </span>
                                    </p>
                                    <a href="{{ route('lab.orders.create') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                        <i class="fas fa-plus"></i>
                                        Create First Report
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && reports && reports.length > 0 && pagination && pagination.last_page > 1"
                class="bg-white px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Pagination Info -->
                    <div class="text-sm text-gray-700">
                        Showing <span x-text="pagination.from || 0"></span> to
                        <span x-text="pagination.to || 0"></span> of
                        <span x-text="pagination.total || 0"></span> results
                    </div>

                    <!-- Pagination Controls -->
                    <nav class="flex items-center space-x-2">
                        <!-- First Page -->
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm bg-white">
                            First
                        </button>

                        <!-- Previous Page -->
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm bg-white">
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <template x-if="getPageRange && typeof getPageRange === 'function'">
                            <template x-for="page in getPageRange()" :key="page + Math.random()">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ?
                                        'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-blue-600' :
                                        'border-gray-300 text-gray-700 hover:bg-gray-50 bg-white'"
                                    :disabled="page === '...'"
                                    class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                                    <span x-text="page"></span>
                                </button>
                            </template>
                        </template>

                        <!-- Next Page -->
                        <button @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm bg-white">
                            Next
                        </button>

                        <!-- Last Page -->
                        <button @click="changePage(pagination.last_page)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm bg-white">
                            Last
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4"
            @keydown.escape.window="closeDeleteModal()">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.away="closeDeleteModal()">
                <div class="p-6 text-center">
                    <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Report</h3>
                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete report
                        <span class="font-bold text-maroon-700"
                            x-text="reportToDelete ? '#' + reportToDelete.lab_number : ''"></span>?
                        This action cannot be undone.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button @click="closeDeleteModal"
                            class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button @click="confirmDeleteAction" :disabled="deleting"
                            :class="deleting ? 'opacity-70 cursor-not-allowed' : ''"
                            class="px-6 py-2.5 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">
                            <span x-show="!deleting">Delete Report</span>
                            <span x-show="deleting">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

        document.addEventListener('alpine:init', () => {
            Alpine.data('labReportsIndex', () => ({
                // State
                reports: [],
                loading: false,
                showAdvancedFilters: false,
                filterStatus: '',
                deleting: false,
                showDeleteModal: false,
                reportToDelete: null,

                // Search & Filters
                searchQuery: '',
                filters: {
                    priority: ''
                },
                dateFrom: '',
                dateTo: '',

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
                    pending: 0,
                    processing: 0,
                    completed: 0,
                    urgent: 0
                },

                // Initialize
                async init() {
                    await this.fetchStats();
                    await this.fetchReports();
                    console.log('LabReportsIndex component initialized');
                },

                // Fetch reports from server
                async fetchReports() {
                    if (this.loading) return;

                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            page: this.pagination.current_page || 1,
                            search: this.searchQuery || '',
                            status: this.filterStatus || '',
                            priority: this.filters.priority || '',
                            date_from: this.dateFrom || '',
                            date_to: this.dateTo || '',
                            per_page: this.pagination.per_page || 10,
                            _: Date.now()
                        });

                        const response = await fetch(`/lab/reports/data?${params}`);

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();

                        // Handle different response formats
                        if (data.data) {
                            this.reports = data.data || [];
                            this.pagination = {
                                current_page: data.current_page || 1,
                                last_page: data.last_page || 1,
                                per_page: data.per_page || 10,
                                total: data.total || 0,
                                from: data.from || 0,
                                to: data.to || 0
                            };
                        } else if (Array.isArray(data)) {
                            this.reports = data;
                            this.pagination = {
                                current_page: 1,
                                last_page: 1,
                                per_page: data.length,
                                total: data.length,
                                from: 1,
                                to: data.length
                            };
                        } else {
                            this.reports = [];
                        }

                    } catch (error) {
                        console.error('Error fetching reports:', error);
                        this.showToast('Failed to load lab reports', 'error');
                    } finally {
                        // this.clearFilters()
                        this.loading = false;
                    }
                },

                // Fetch stats
                async fetchStats() {
                    try {
                        const response = await fetch('/lab/reports/statistics');
                        if (response.ok) {
                            const result = await response.json();
                            this.stats = result.data || this.stats;
                        }
                    } catch (error) {
                        console.error('Error fetching stats:', error);
                    }
                },

                // Search reports
                searchReports() {
                    this.pagination.current_page = 1;
                    this.fetchReports();
                },

                // Set quick filter
                setFilter(type) {
                    if (this.loading) return;

                    if (type === 'all') {
                        this.filterStatus = '';
                    } else if (type === 'urgent') {
                        this.filters.priority = 'urgent';
                        this.filterStatus = '';
                    } else {
                        this.filterStatus = type;
                        this.filters.priority = '';
                    }

                    this.applyFilters();
                },

                debouncedApplyFilters: debounce(function() {
                    this.applyFilters();
                }, 300),

                // Apply filters
                applyFilters() {
                    if (this.loading) return;
                    this.pagination.current_page = 1;
                    this.fetchReports();
                },

                // Reset all filters
                resetFilters(fetch = true) {
                    this.filters = {
                        priority: ''
                    };
                    this.filterStatus = '';
                    this.searchQuery = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.pagination.current_page = 1;

                    if (fetch) {
                        this.fetchReports();
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
                        this.fetchReports();
                    }
                },

                // Get page range for pagination
                getPageRange() {
                    if (!this.pagination || !this.pagination.last_page) {
                        return [1];
                    }

                    const current = this.pagination.current_page || 1;
                    const last = this.pagination.last_page || 1;
                    const delta = 2;
                    const range = [];
                    const rangeWithDots = [];

                    if (last <= 1) {
                        return [1];
                    }

                    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current +
                            delta); i++) {
                        range.push(i);
                    }

                    if (current - delta > 2) {
                        rangeWithDots.push(1, '...');
                    } else if (current - delta > 1) {
                        rangeWithDots.push(1);
                    } else {
                        rangeWithDots.push(1);
                    }

                    for (let i of range) {
                        rangeWithDots.push(i);
                    }

                    if (current + delta < last - 1) {
                        rangeWithDots.push('...', last);
                    } else if (current + delta < last) {
                        rangeWithDots.push(last);
                    } else if (last > 1) {
                        rangeWithDots.push(last);
                    }

                    return rangeWithDots;
                },

                // Modal methods
                openDeleteModal(report) {
                    this.reportToDelete = report;
                    this.showDeleteModal = true;
                },

                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.reportToDelete = null;
                },

                async confirmDeleteAction() {
                    if (!this.reportToDelete) return;

                    this.deleting = true;
                    try {
                        const response = await fetch(
                            `/lab/reports/${this.reportToDelete.id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('Report deleted successfully', 'success');
                            this.closeDeleteModal();
                            await this.fetchReports();
                            await this.fetchStats();
                        } else {
                            this.showToast(data.message || 'Failed to delete report', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting report:', error);
                        this.showToast('An error occurred while deleting the report', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                // Helper methods
                getTestIconColor(testName) {
                    const colors = [
                        'bg-gradient-to-br from-indigo-400 to-indigo-600',
                    ];
                    const index = (testName || '').split('').reduce((acc, char) => acc + char
                        .charCodeAt(0), 0) % colors.length;
                    return colors[index];
                },

                getTestIcon(testName) {
                    const testMap = {
                        'blood': 'fa-tint',
                        'urine': 'fa-tint',
                        'xray': 'fa-x-ray',
                        'ultrasound': 'fa-ultrasound',
                        'mri': 'fa-magnet',
                        'ct': 'fa-cube',
                        'covid': 'fa-virus'
                    };

                    const lowerName = (testName || '').toLowerCase();
                    for (let [key, icon] of Object.entries(testMap)) {
                        if (lowerName.includes(key)) {
                            return icon;
                        }
                    }
                    return 'fa-flask';
                },

                getStatusClass(status) {
                    const statusMap = {
                        'pending': 'text-white font-bold bg-gradient-to-br from-md-orange to-amber-600 border border-amber-200',
                        'processing': 'text-white font-bold bg-gradient-to-br from-md-blue to-sky-600 border border-sky-200',
                        'completed': 'text-white font-bold bg-gradient-to-br from-md-green to-green-600 border border-green-200',
                        'cancelled': 'text-white font-bold bg-gradient-to-br from-md-red to-maroon-600 border border-maroon-200'
                    };
                    return statusMap[status] || 'bg-gray-50 text-gray-700 border border-gray-200';
                },

                getStatusIcon(status) {
                    const statusMap = {
                        'pending': 'fa-clock',
                        'processing': 'fa-cogs',
                        'completed': 'fa-check-circle',
                        'cancelled': 'fa-times-circle'
                    };
                    return statusMap[status] || 'fa-question-circle';
                },

                getStatusTextClass(status) {
                    const statusMap = {
                        'pending': 'text-yellow-600',
                        'processing': 'text-blue-600',
                        'completed': 'text-green-600',
                        'cancelled': 'text-red-600'
                    };
                    return statusMap[status] || 'text-gray-600';
                },

                formatStatus(status) {
                    return status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';
                },

                getPriorityBadgeClass(priority) {
                    const priorityMap = {
                        'normal': 'text-white font-bold bg-gradient-to-br from-md-green to-green-600 border border-green-200',
                        'urgent': 'text-white font-bold bg-gradient-to-br from-md-orange to-orange-600 border border-orange-200',
                        'emergency': 'text-white font-bold bg-gradient-to-br from-md-red to-maroon-600 border border-maroon-200'
                    };
                    return priorityMap[priority?.toLowerCase()] || 'bg-gray-50 text-gray-700';
                },

                getPriorityIcon(priority) {
                    const priorityMap = {
                        'normal': 'fa-flag',
                        'urgent': 'fa-exclamation-triangle',
                        'emergency': 'fa-exclamation-circle'
                    };
                    return priorityMap[priority?.toLowerCase()] || 'fa-flag';
                },

                getPriorityClass(priority) {
                    const priorityMap = {
                        'normal': 'text-white font-bold bg-gradient-to-br from-md-green to-green-600 border border-green-200',
                        'urgent': 'text-white font-bold bg-gradient-to-br from-md-orange to-orange-600 border border-orange-200',
                        'emergency': 'text-white font-bold bg-gradient-to-br from-md-red to-maroon-600 border border-maroon-200 animated-pulse'
                    };
                    return priorityMap[priority?.toLowerCase()] || 'bg-gray-100 text-gray-700';
                },

                getPriorityTextClass(priority) {
                    const priorityMap = {
                        'normal': 'text-gray-600',
                        'urgent': 'text-orange-600',
                        'emergency': 'text-red-600 font-bold'
                    };
                    return priorityMap[priority?.toLowerCase()] || 'text-gray-600';
                },

                formatPriority(priority) {
                    if (!priority) return 'Normal';
                    return priority.charAt(0).toUpperCase() + priority.slice(1);
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
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
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
