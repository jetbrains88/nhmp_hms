@extends('layouts.app')

@section('title', 'Consultation Queue')
@section('page-title', 'Consultation Queue')
@section('breadcrumb', 'Doctor / Consultations')

@section('content')
    <div x-data="consultationManagement()" x-init="init()" class="space-y-6">
        <!-- Stats Cards - Floating Icon Design -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

            <!-- Total Consultations Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('all')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-check text-2xl drop-shadow-md text-blue-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                        Total Consultations
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono"
                        x-text="stats.total"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-xs text-blue-700 font-medium">All Consultations</span>
                    </div>
                </div>
            </div>

            <!-- Completed Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('completed')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-check-circle text-2xl drop-shadow-md text-emerald-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">
                        Completed
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono"
                        x-text="stats.completed"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600"></span>
                        <span class="text-xs text-teal-700 font-medium">Successfully Closed</span>
                    </div>
                </div>
            </div>

            <!-- Waiting Card - Using Amber/Yellow theme -->
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl shadow-lg shadow-amber-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('waiting')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-200 shadow-lg shadow-amber-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-2xl drop-shadow-md text-amber-600"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-amber-500 uppercase">
                        Waiting
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-amber-800 drop-shadow-md font-mono"
                        x-text="stats.waiting"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-500 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                        <span class="text-xs text-amber-700 font-medium">In Queue</span>
                    </div>
                </div>
            </div>

            <!-- In Progress Card - Using Violet/Purple theme -->
            <div class="relative flex flex-col bg-gradient-to-br from-violet-50 to-purple-50 rounded-2xl shadow-lg shadow-violet-500/30 border hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('in_progress')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-violet-500 to-purple-200 shadow-lg shadow-violet-900/40 border group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-stethoscope text-2xl drop-shadow-md text-violet-600"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-violet-500 uppercase">
                        In Progress
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-violet-800 drop-shadow-md font-mono"
                        x-text="stats.in_progress"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-violet-500 pt-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-xs text-violet-700"></i>
                        <span class="text-xs text-violet-700 font-medium">Currently Consulting</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Enhanced Consultations List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-navy-800 flex items-center gap-3">
                            <i class="fas fa-notes-medical text-blue-600"></i>
                            Patient Consultations
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Manage patient consultations and diagnoses
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="pagination.per_page" @change="fetchConsultations()"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button @click="fetchConsultations()"
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
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">

                    <!-- First Row - 3 items: Search, Status, Patient Type -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchConsultations()"
                                placeholder="Search patient name, EMRN, or phone..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select x-model="filterStatus" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Status</option>
                                <option value="waiting">Waiting</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <!-- Patient Type Filter -->
                        <div>
                            <select x-model="filters.patient_type" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                <option value="">All Patients</option>
                                <option value="1">NHMP Only</option>
                                <option value="0">General Only</option>
                            </select>
                        </div>
                    </div>

                    <!-- Second Row - 3 items: Date From, Date To, Clear Filters Button -->
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

                        <!-- Clear Filters Button -->
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

            <!-- Consultations Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user-injured text-blue-500"></i>
                                    Patient Information
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-purple-500"></i>
                                    EMRN
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-stethoscope text-green-500"></i>
                                    Visit Type
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-orange-500"></i>
                                    Time
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-red-500"></i>
                                    Status
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cogs text-orange-500"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100"
                        x-show="!loading && consultations && consultations.length > 0">
                        <template x-for="consultation in consultations" :key="consultation.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- Patient Info Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full flex items-center bg-gradient-to-br from-blue-400 to-blue-600 justify-center text-white text-lg font-bold shadow-lg">
                                                <i class="fas fa-user-injured text-white"></i>
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-xl font-bold text-navy-800 truncate"
                                                    x-text="consultation.patient.name"></p>
                                                <template x-if="consultation.patient.is_nhmp">
                                                    <span
                                                        class="px-2 py-0.5 bg-green-50 text-green-500 text-xs font-bold rounded">NHMP</span>
                                                </template>
                                            </div>

                                            <!-- Patient details -->
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <!-- Gender -->
                                                <div class="flex items-center text-xs"
                                                    :class="consultation.patient.gender === 'male' ? 'text-gray-700' :
                                                        consultation.patient.gender === 'female' ? 'text-gray-700' :
                                                        'text-gray-700'">
                                                    <i class="fas mr-1"
                                                        :class="consultation.patient.gender === 'male' ? 'fa-mars' :
                                                            consultation.patient.gender === 'female' ? 'fa-venus' :
                                                            'fa-genderless'"></i>
                                                    <span x-text="consultation.patient.gender"></span>

                                                </div>
                                                <!-- Age -->
                                                <div class="flex items-center text-xs text-gray-700">
                                                    <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                                    <span x-text="consultation.patient.age_formatted"></span>
                                                </div>
                                                <!-- Blood Group -->
                                                <div class="flex items-center text-xs px-2 py-0.5 rounded-full text-gray-700"
                                                    x-show="consultation.patient.blood_group">
                                                    <i class="fas fa-tint mr-1"></i>
                                                    <span x-text="consultation.patient.blood_group"></span>
                                                </div>
                                                <!-- Contact -->
                                                <div class="mt-1 text-xs font-mono text-gray-400">
                                                    <i class="fas fa-phone-alt text-gray-400 mr-1"></i>
                                                    <span x-text="consultation.patient.phone"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- EMRN Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="ml-2 text-sm font-mono text-gray-700"
                                            x-text="consultation.patient.emrn"></span>
                                    </div>
                                </td>

                                <!-- Visit Type Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-gray-700 rounded-md text-xs font-bold"
                                            >
                                            <i class="fas mr-1.5" :class="getVisitTypeIcon(consultation.visit_type)"></i>
                                            Type
                                        </span>
                                        <span class="ml-2 text-sm font-mono text-gray-700"
                                            x-text="formatVisitType(consultation.visit_type)"></span>
                                    </div>
                                </td>

                                <!-- Time Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold text-gray-700 ">
                                                <i class="fas fa-clock mr-1.5"></i>
                                                Time
                                            </span>
                                            <span class="ml-2 text-xs font-mono text-gray-600"
                                                x-text="formatTime(consultation.created_at)"></span>
                                        </div>
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold text-gray-700 ">
                                                <i class="fas fa-calendar mr-1.5"></i>
                                                Date
                                            </span>
                                            <span class="ml-2 text-xs font-mono text-gray-600"
                                                x-text="formatDate(consultation.created_at)"></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Column -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold"
                                        :class="getStatusClass(consultation.status)">
                                        <i class="fas mr-2" :class="getStatusIcon(consultation.status)"></i>
                                        <span x-text="formatStatus(consultation.status)"></span>
                                    </span>
                                </td>

                                <!-- Actions Column - Light Theme -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[140px]">

                                        <!-- Start Button (for waiting status) -->
                                        <template x-if="consultation.status === 'waiting'">
                                            <button @click="startConsultation(consultation.id)"
                                                class="group relative transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                title="Start this consultation">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-700">
                                                    <i class="fas fa-play-circle"></i>
                                                </span>
                                                <span class="text-sm">Start</span>
                                            </button>
                                        </template>

                                        <!-- Continue Button (for in_progress status) -->
                                        <template x-if="consultation.status === 'in_progress'">
                                            <a :href="`/doctor/consultancy/${consultation.id}`"
                                                class="group relative transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                title="Continue this consultation">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-700">
                                                    <i class="fas fa-notes-medical"></i>
                                                </span>
                                                <span class="text-sm">Continue</span>
                                            </a>
                                        </template>

                                        <!-- View Button (for completed status) -->
                                        <template x-if="consultation.status === 'completed'">
                                            <div class="flex flex-col space-y-1">
                                                <a :href="`/doctor/consultancy/${consultation.id}`"
                                                    class="group relative transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                    title="View consultation details">
                                                    <span class="inline-flex items-center mr-2 w-4 text-gray-700">
                                                        <i class="fas fa-eye"></i>
                                                    </span>
                                                    <span class="text-sm">View</span>
                                                </a>
                                                <button @click="printConsultation(consultation.id)"
                                                    class="group relative transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                    title="Print prescription">
                                                    <span class="inline-flex items-center mr-2 w-4 text-gray-700">
                                                        <i class="fas fa-print"></i>
                                                    </span>
                                                    <span class="text-sm">Print</span>
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Cancel Button (for waiting and in_progress status) -->
                                        <template x-if="['waiting', 'in_progress'].includes(consultation.status)">
                                            <button @click="cancelConsultation(consultation.id)"
                                                class="group relative transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-900"
                                                title="Cancel this consultation">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-700">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                                <span class="text-sm">Cancel</span>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Loading State -->
                    <tbody x-show="loading">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4">
                                    </div>
                                    <p class="text-gray-600">Loading consultations...</p>
                                    <p class="text-sm text-gray-400 mt-1">Please wait while we fetch the records</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!consultations || consultations.length === 0)">
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 mb-4 text-gray-300">
                                        <i class="fas fa-calendar-times text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No consultations found</h3>
                                    <p class="text-gray-500 max-w-md mb-4">
                                        <span
                                            x-show="searchQuery || filterStatus || filters.patient_type || dateFrom || dateTo">
                                            Try adjusting your filters or search terms
                                        </span>
                                        <span
                                            x-show="!searchQuery && !filterStatus && !filters.patient_type && !dateFrom && !dateTo">
                                            No consultations scheduled.
                                        </span>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && consultations && consultations.length > 0 && pagination && pagination.last_page > 1"
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
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            First
                        </button>

                        <!-- Previous Page -->
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <template x-if="getPageRange && typeof getPageRange === 'function'">
                            <template x-for="page in getPageRange()" :key="page + Math.random()">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ?
                                        'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-blue-600' :
                                        'border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    :disabled="page === '...'"
                                    class="px-3 py-1.5 rounded-lg border min-w-[40px] text-sm font-medium">
                                    <span x-text="page"></span>
                                </button>
                            </template>
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

        <!-- Cancel Cnsultation Modal -->
        <div x-show="showCancelModal" x-cloak class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50"
            @keydown.escape.window="closeCancelModal()">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4" @click.away="closeCancelModal()">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Cancel Consultation</h3>
                            <button @click="closeCancelModal()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-gray-600 mb-4">Are you sure you want to cancel this consultation?</p>
                        <textarea x-model="cancelReason" placeholder="Reason for cancellation (optional)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4" rows="3"></textarea>
                        <div class="flex justify-end space-x-3">
                            <button @click="closeCancelModal()" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                                No, Keep It
                            </button>
                            <button @click="confirmCancel()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Yes, Cancel
                            </button>
                        </div>
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



        function consultationManagement() {
            return {
                // State
                consultations: [],
                loading: false,
                showAdvancedFilters: false,
                filterStatus: "{{ $filters['status'] ?? '' }}",

                // Cancel modal state
                showCancelModal: false,
                currentVisitId: null,
                cancelReason: '',

                // Search & Filters
                searchQuery: "{{ $filters['search'] ?? '' }}",
                filters: {
                    patient_type: "{{ $filters['is_nhmp'] ?? '' }}",
                },
                dateFrom: "{{ $filters['date'] ?? '' }}",
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
                    waiting: {{ $totalWaiting }},
                    in_progress: 0,
                    completed: 0
                },

                // Initialize
                async init() {
                    await this.fetchConsultations();
                    await this.fetchStats();
                    console.log('ConsultationManagement component initialized');
                },

                // Fetch consultations from server
                async fetchConsultations() {
                    if (this.loading) return;

                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            page: this.pagination.current_page || 1,
                            search: this.searchQuery || '',
                            status: this.filterStatus || '',
                            is_nhmp: this.filters.patient_type || '',
                            date: this.dateFrom || '',
                            end_date: this.dateTo || '',
                            per_page: this.pagination.per_page || 10,
                            _: Date.now()
                        });

                        const response = await fetch(`/doctor/consultancy/data?${params}`);

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        this.consultations = data.data || [];

                        this.pagination = {
                            current_page: data.current_page || 1,
                            last_page: data.last_page || 1,
                            per_page: data.per_page || 10,
                            total: data.total || 0,
                            from: data.from || 0,
                            to: data.to || 0
                        };

                    } catch (error) {
                        console.error('Error fetching consultations:', error);
                        this.showToast('Failed to load consultations', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Fetch stats
                async fetchStats() {
                    try {
                        const response = await fetch('/doctor/consultancy/stats');
                        if (response.ok) {
                            this.stats = await response.json();
                        }
                    } catch (error) {
                        console.error('Error fetching stats:', error);
                    }
                },

                // Search consultations
                searchConsultations() {
                    this.pagination.current_page = 1;
                    this.fetchConsultations();
                },

                // Set quick filter
                setFilter(type) {
                    if (this.loading) return;

                    if (type === 'all') {
                        this.filterStatus = '';
                    } else {
                        this.filterStatus = type;
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
                    this.fetchConsultations();
                },

                // Reset all filters
                resetFilters(fetch = true) {
                    this.filters = {
                        patient_type: ''
                    };
                    this.filterStatus = '';
                    this.searchQuery = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.pagination.current_page = 1;

                    if (fetch) {
                        this.fetchConsultations();
                    }
                },

                // Clear filters (from button)
                clearFilters() {
                    this.resetFilters(true);
                    this.showToast('Filters cleared', 'info');
                },

                // Pagination methods
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.pagination.current_page = page;
                        this.fetchConsultations();
                    }
                },

                // Get page range for pagination
                // Get page range for pagination
                getPageRange() {
                    // Add null check for pagination
                    if (!this.pagination || !this.pagination.last_page) {
                        return [1];
                    }

                    const current = this.pagination.current_page || 1;
                    const last = this.pagination.last_page || 1;
                    const delta = 2;
                    const range = [];
                    const rangeWithDots = [];

                    // If there's only one page, just return [1]
                    if (last <= 1) {
                        return [1];
                    }

                    // Calculate the range of pages to show
                    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                        range.push(i);
                    }

                    // Add first page
                    if (current - delta > 2) {
                        rangeWithDots.push(1, '...');
                    } else if (current - delta > 1) {
                        rangeWithDots.push(1);
                    } else {
                        rangeWithDots.push(1);
                    }

                    // Add middle pages
                    for (let i of range) {
                        rangeWithDots.push(i);
                    }

                    // Add last page
                    if (current + delta < last - 1) {
                        rangeWithDots.push('...', last);
                    } else if (current + delta < last) {
                        rangeWithDots.push(last);
                    } else if (last > 1) {
                        rangeWithDots.push(last);
                    }

                    return rangeWithDots;
                },

                // Cancel consultation methods
                cancelConsultation(visitId) {
                    console.log('Cancelling consultation with ID:', visitId);

                    if (!visitId || visitId === 'null' || visitId === null || visitId === undefined) {
                        this.showToast('Invalid consultation ID', 'error');
                        return;
                    }

                    this.currentVisitId = visitId;
                    this.showCancelModal = true;
                },

                async confirmCancel() {
                    if (!this.currentVisitId) {
                        this.showToast('Invalid consultation ID', 'error');
                        this.closeCancelModal();
                        return;
                    }

                    try {
                        const response = await fetch(`/doctor/consultancy/${this.currentVisitId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                reason: this.cancelReason
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('Consultation cancelled successfully', 'success');
                            this.closeCancelModal();
                            await this.fetchConsultations(); // Refresh the list
                            await this.fetchStats(); // Refresh stats
                        } else {
                            this.showToast(data.message || 'Failed to cancel consultation', 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showToast('Failed to cancel consultation', 'error');
                    }
                },

                closeCancelModal() {
                    this.showCancelModal = false;
                    this.cancelReason = '';
                    this.currentVisitId = null;
                },

                // Start consultation
                startConsultation(visitId) {
                    fetch(`/doctor/consultancy/${visitId}/start`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.showToast('Consultation started successfully', 'success');
                                setTimeout(() => {
                                    window.location.href = `/doctor/consultancy/${visitId}`;
                                }, 1000);
                            } else {
                                this.showToast(data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.showToast('Failed to start consultation', 'error');
                        });
                },

                // Print prescription for completed consultation
                async printConsultation(visitId) {
                    try {
                        const resp = await fetch(`/doctor/consultancy/${visitId}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await resp.json();
                        
                        // Find the first diagnosis with a prescription
                        let printUrl = null;
                        if (data.visit && data.visit.diagnoses && data.visit.diagnoses.length > 0) {
                            const diag = data.visit.diagnoses[0];
                            if (diag && diag.prescriptions && diag.prescriptions.length > 0) {
                                const prescId = diag.prescriptions[0].id;
                                printUrl = `/print/prescription/${prescId}`;
                            }
                        }

                        if (printUrl) {
                            window.open(printUrl, '_blank');
                        } else {
                            this.showToast('No prescription found for this consultation.', 'info');
                        }
                    } catch (e) {
                        console.error('Error opening print:', e);
                        this.showToast('Could not fetch prescription data.', 'error');
                    }
                },

                // Helper methods
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

                getVisitTypeClass(type) {
                    const typeMap = {
                        'routine': 'bg-blue-50 text-md-blue',
                        'emergency': 'bg-pink-50 text-md-rose',
                        'followup': 'bg-green-50 text-md-green'
                    };
                    return typeMap[type] || 'bg-gray-50 text-gray-700';
                },

                getVisitTypeIcon(type) {
                    const typeMap = {
                        'routine': 'fa-calendar-check',
                        'emergency': 'fa-ambulance',
                        'followup': 'fa-rotate-left'
                    };
                    return typeMap[type] || 'fa-stethoscope';
                },

                formatVisitType(type) {
                    return type ? type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A';
                },

                getStatusClass(status) {
                    const statusMap = {
                        'waiting': 'text-gray-700',
                        'in_progress': 'text-gray-700',
                        'completed': ' text-gray-700',
                        'cancelled': ' text-gray-700'
                    };
                    return statusMap[status] || 'text-gray-700';
                },

                getStatusIcon(status) {
                    const statusMap = {
                        'waiting': 'fa-clock',
                        'in_progress': 'fa-stethoscope',
                        'completed': 'fa-check-circle',
                        'cancelled': 'fa-times-circle'
                    };
                    return statusMap[status] || 'fa-question-circle';
                },

                formatStatus(status) {
                    return status ? status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A';
                },

                formatTime(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
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
            };
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection

