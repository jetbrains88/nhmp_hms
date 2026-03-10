@extends('layouts.app')

@section('title', 'Patient Management - NHMP HMS')
@section('page-title', 'Patient Management')
@section('breadcrumb', 'Patients')

@section('content')
    <div x-data="patientManagement()" x-init="init()" class="space-y-6">
        <!-- Stats Cards - Light Theme -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">
            <!-- Total Patients Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('all')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-2xl drop-shadow-md text-blue-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                        Total Patients
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono"
                        x-text="stats.total"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-xs text-blue-700 font-medium">All Registered Patients</span>
                    </div>
                </div>
            </div>

            <!-- Active Patients Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/30 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('active')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-300 shadow-lg shadow-emerald-900/40 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-check text-2xl drop-shadow-md text-emerald-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-teal-600 uppercase">
                        Active Patients
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-teal-800 drop-shadow-md font-mono"
                        x-text="stats.active"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-teal-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                        <span class="text-xs text-teal-700 font-medium">Currently Active</span>
                    </div>
                </div>
            </div>

            <!-- NHMP Staff Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('nhmp')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-shield text-2xl drop-shadow-md text-purple-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                        NHMP Staff
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono"
                        x-text="stats.nhmp"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                        <span class="text-xs text-purple-700 font-medium">NHMP Personnel</span>
                    </div>
                </div>
            </div>

            <!-- Today's Patients Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/30 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click.throttle.500ms="setFilter('today')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-300 shadow-lg shadow-amber-900/40 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-day text-2xl drop-shadow-md text-amber-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-amber-600 uppercase">
                        Today's Patients
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-amber-800 drop-shadow-md font-mono"
                        x-text="stats.today"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-200 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                        <span class="text-xs text-amber-700 font-medium" x-text="stats.waiting + ' waiting'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Patients List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                            <i class="fas fa-users text-blue-600"></i>
                            Patient Management
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Manage patient records, visits, and medical history
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="pagination.per_page" @change="fetchPatients()"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button @click="refreshPatients()"
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
                            <!-- Add Patient Button -->
                            <a href="{{ route('reception.patients.create') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-user-plus"></i>
                                Add Patient
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-200 border border-indigo-700 shadow-lg">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.500ms="searchPatients()"
                                placeholder="Search patients by name, EMRN, phone..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <!-- Gender Filter -->
                        <div>
                            <select x-model="filters.gender" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Blood Group Filter -->
                        <div>
                            <select x-model="filters.blood_group" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">All Blood Groups</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                                    <option value="{{ $group }}">{{ $group }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- NHMP Filter -->
                        <div>
                            <select x-model="filters.is_nhmp" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="">All Types</option>
                                <option value="1">NHMP Staff</option>
                                <option value="0">General Public</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date Range Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration From</label>
                            <input type="date" x-model="registrationFrom" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration To</label>
                            <input type="date" x-model="registrationTo" @change="applyFilters"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>

                        <!-- Clear All Filters Button -->
                        <div class="md:col-span-2 flex items-end">
                            <button @click="clearFilters()"
                                class="w-full flex items-center justify-center text-white py-2.5
                           text-center bg-gradient-to-r from-rose-500 to-rose-600
                           rounded-lg font-medium hover:from-rose-600 hover:to-rose-700
                           disabled:opacity-50 disabled:cursor-not-allowed transition-all
                           gap-2 shadow-md hover:shadow-lg">
                                <i class="fas fa-filter-circle-xmark"></i>
                                Clear All Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <tr>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-blue-500"></i>
                                    Patient Information
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-purple-500"></i>
                                    Identification
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-green-500"></i>
                                    Visit History
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
                    <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && patients && patients.length > 0">
                        <template x-for="patient in patients" :key="patient.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- Patient Info Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full flex items-center justify-center text-white text-lg font-bold shadow-lg"
                                                :class="patient.gender === 'male' ?
                                                    'bg-gradient-to-br from-blue-400 to-blue-600' :
                                                    patient.gender === 'female' ?
                                                    'bg-gradient-to-br from-pink-400 to-pink-600' :
                                                    'bg-gradient-to-br from-purple-400 to-purple-600'">
                                                <i class="fas fa-user-injured text-white"></i>
                                            </div>
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-xl font-bold truncate"
                                                    :class="patient.gender === 'male' ? 'text-blue-900' :
                                                        patient.gender === 'female' ? 'text-pink-900' :
                                                        'text-purple-900'"
                                                    x-text="patient.name"></p>
                                                <!-- Gender Badge -->
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                    :class="patient.gender === 'male' ? 'bg-blue-100 text-blue-700' :
                                                        'bg-pink-100 text-pink-700'">
                                                    <i class="fas"
                                                        :class="patient.gender === 'male' ? 'fa-mars' : 'fa-venus'"></i>
                                                </span>
                                            </div>

                                            <!-- Demographics -->
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <!-- Age -->
                                                <div class="flex items-center text-xs text-gray-600">
                                                    <i class="fas fa-birthday-cake mr-1 text-blue-500"></i>
                                                    <span x-text="patient.age || 'N/A'"></span> years
                                                </div>

                                                <!-- Gender -->
                                                <div class="flex items-center text-xs px-2 py-0.5 rounded-full"
                                                    :class="patient.gender === 'male' ? 'bg-blue-100 text-blue-800' :
                                                        patient.gender === 'female' ? 'bg-pink-100 text-pink-800' :
                                                        'bg-purple-100 text-purple-800'">
                                                    <i class="fas mr-1"
                                                        :class="patient.gender === 'male' ? 'fa-mars' :
                                                            patient.gender === 'female' ? 'fa-venus' :
                                                            'fa-genderless'"></i>
                                                    <span
                                                        x-text="patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'"></span>
                                                </div>

                                                <!-- Blood Group -->
                                                <div class="flex items-center text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-700"
                                                    x-show="patient.blood_group">
                                                    <i class="fas fa-tint mr-1"></i>
                                                    <span x-text="patient.blood_group"></span>
                                                </div>
                                            </div>

                                            <!-- Contact -->
                                            <div class="mt-1 text-xs text-gray-500">
                                                <i class="fas fa-phone-alt text-orange-500 mr-1"></i>
                                                <span x-text="patient.phone"></span>
                                            </div>

                                            <!-- Address -->
                                            <div class="mt-1 text-xs text-gray-500" x-show="patient.address">
                                                <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>
                                                <span x-text="patient.address" class="text-wrap truncate max-w-xs"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Identification Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[180px]">
                                        <!-- EMRN -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-900 hover:text-gray-700">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-id-badge"></i>
                                                </span>
                                                <span class="font-mono font-bold text-sm text-gray-600"
                                                    x-text="patient.emrn"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">EMRN Number</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CNIC -->
                                        <div class="group relative" x-show="patient.cnic">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-700 hover:text-gray-900">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-address-card"></i>
                                                </span>
                                                <span class="font-mono text-sm" x-text="patient.cnic"></span>
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

                                        <!-- NHMP Info -->
                                        <div class="group relative" x-show="patient.is_nhmp">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-700 hover:text-gray-900">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <!-- Conditional icon using a method -->
                                                    <i class="fas" :class="getNhmpIcon(patient.designation)"></i>
                                                </span>
                                                <span class="text-sm" x-text="patient.designation || 'NHMP Staff'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold"
                                                        x-text="patient.designation ? 'Designation' : 'Staff Type'"></span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Registration Date -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-600 hover:text-gray-800">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-calendar-plus"></i>
                                                </span>
                                                <span class="text-xs" x-text="formatDate(patient.created_at)"></span>
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
                                    </div>
                                </td>

                                <!-- Visit History Column -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2 min-w-[160px]">
                                        <!-- Last Visit -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-700 hover:text-gray-900">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-history"></i>
                                                </span>
                                                <span class="text-sm"
                                                    x-text="patient.last_visit_date || 'No visits'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Last Visit Date</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Visits -->
                                        <div class="group relative">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-700 hover:text-gray-900">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-600">
                                                    <i class="fas fa-calendar-check"></i>
                                                </span>
                                                <span class="text-sm" x-text="patient.total_visits || 0"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Total Number of Visits</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div class="group relative" x-show="patient.last_visit_status">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full"
                                                :class="patient.last_visit_status === 'completed' ?
                                                    'text-green-600 hover:text-green-800' :
                                                    patient.last_visit_status === 'in_progress' ?
                                                    'text-blue-600 hover:text-blue-800' :
                                                    patient.last_visit_status === 'waiting' ?
                                                    'text-orange-600 hover:text-orange-800' :
                                                    'text-gray-600 hover:text-gray-800'">
                                                <span class="inline-flex items-center mr-2 w-4"
                                                    :class="patient.last_visit_status === 'completed' ? 'text-green-600' :
                                                        patient.last_visit_status === 'in_progress' ? 'text-blue-600' :
                                                        patient.last_visit_status === 'waiting' ? 'text-orange-600' :
                                                        'text-gray-600'">
                                                    <i class="fas fa-circle"></i>
                                                </span>
                                                <span class="text-sm capitalize"
                                                    x-text="patient.last_visit_status ? patient.last_visit_status.replace('_', ' ') : 'N/A'"></span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">Last Visit Status</span>
                                                    <div
                                                        class="absolute bottom-0 left-4 transform translate-y-1/2 rotate-45 w-2 h-2 bg-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Placeholder when no status -->
                                        <div class="group relative" x-show="!patient.last_visit_status">
                                            <span
                                                class="transition-colors duration-200 text-left inline-flex items-center w-full text-gray-400 cursor-not-allowed">
                                                <span class="inline-flex items-center mr-2 w-4 text-gray-400">
                                                    <i class="fas fa-minus-circle"></i>
                                                </span>
                                                <span class="text-sm">No visit history</span>
                                            </span>
                                            <!-- Tooltip -->
                                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-10">
                                                <div
                                                    class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg whitespace-nowrap">
                                                    <span class="font-semibold">No visits recorded</span>
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
                                        <!-- View Details Button -->
                                        <button @click="viewPatientDetails(patient)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="View Patient Details">
                                            <i class="fas fa-eye mr-2 w-4"></i>
                                            View
                                        </button>

                                        <!-- Edit Button -->
                                        <button @click="editPatient(patient)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Edit Patient Details">
                                            <i class="fas fa-edit mr-2 w-4"></i>
                                            Edit
                                        </button>

                                        <!-- Medical History Button -->
                                        <button @click="viewMedicalHistory(patient)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="View Medical History">
                                            <i class="fas fa-file-medical mr-2 w-4"></i>
                                            Medical
                                        </button>

                                        <!-- Visit History Button -->
                                        <button @click="viewVisitHistory(patient)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="View Visit History">
                                            <i class="fas fa-calendar-check mr-2 w-4"></i>
                                            Visits
                                        </button>

                                        <!-- Delete Button -->
                                        <button @click="confirmDelete(patient)"
                                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200 text-left"
                                            title="Delete Patient">
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
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4">
                                    </div>
                                    <p class="text-gray-600">Loading patients...</p>
                                    <p class="text-sm text-gray-400 mt-1">Please wait while we fetch the records</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!patients || patients.length === 0)">
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 mb-4 text-gray-300">
                                        <i class="fas fa-users text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No patients found</h3>
                                    <p class="text-gray-500 max-w-md mb-4">
                                        <span
                                            x-show="searchQuery || filters.gender || filters.blood_group || registrationFrom || registrationTo">
                                            Try adjusting your filters or search terms
                                        </span>
                                        <span
                                            x-show="!searchQuery && !filters.gender && !filters.blood_group && !registrationFrom && !registrationTo">
                                            No patients in the system. Start by registering a new patient.
                                        </span>
                                    </p>
                                    <a href="{{ route('reception.patients.create') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-all shadow-md hover:shadow-lg font-medium">
                                        <i class="fas fa-plus-circle"></i>
                                        Register First Patient
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && patients && patients.length > 0" class="bg-white px-6 py-4 border-t border-gray-200">
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
        @include('reception.patients.modals.patient-details-modal')
        @include('reception.patients.modals.edit-patient-modal')
        @include('reception.patients.modals.medical-history-modal')
        @include('reception.patients.modals.visit-history-modal')
        @include('reception.patients.modals.delete-patient-modal')
        @include('reception.patients.modals.bulk-upload-modal')
    </div>

    <script>
        function patientManagement() {
            return {
                // State
                patients: [],
                loading: false,
                showAdvancedFilters: false,
                showDeleteModal: false,
                userToDelete: null,
                deleting: false,

                // Stats
                stats: {
                    total: {{ $totalPatients ?? 0 }},
                    active: {{ $activePatients ?? 0 }},
                    nhmp: {{ $nhmpPatients ?? 0 }},
                    today: {{ $todayPatients ?? 0 }},
                    waiting: {{ $waitingPatients ?? 0 }}
                },

                // Search & Filters
                searchQuery: '',
                filters: {
                    gender: '',
                    blood_group: '',
                    is_nhmp: ''
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

                // Current request for aborting
                currentRequest: null,

                // Initialize
                init() {
                    this.fetchPatients();
                },

                // Fetch patients from server
                async fetchPatients() {
                    if (this.loading) return;

                    if (this.currentRequest) {
                        this.currentRequest.abort();
                    }

                    this.loading = true;
                    try {
                        const controller = new AbortController();
                        this.currentRequest = controller;

                        const params = new URLSearchParams({
                            page: this.pagination.current_page,
                            search: this.searchQuery,
                            gender: this.filters.gender,
                            blood_group: this.filters.blood_group,
                            is_nhmp: this.filters.is_nhmp === '1' ? 'true' : this.filters.is_nhmp === '0' ?
                                'false' : '',
                            start_date: this.registrationFrom,
                            end_date: this.registrationTo,
                            sort_by: this.sortField,
                            sort_order: this.sortDirection,
                            per_page: this.pagination.per_page,
                            _: Date.now()
                        });

                        const response = await fetch(`{{ route('reception.patients.list') }}?${params}`, {
                            signal: controller.signal,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error(`Network response was not ok: ${response.status}`);
                        }

                        const data = await response.json();
                        this.patients = data.data || [];

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
                            console.error('Error fetching patients:', error);
                            this.showToast('Failed to load patients', 'error');
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                // Sort patients
                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDirection = 'asc';
                    }
                    this.fetchPatients();
                },

                // Search patients
                searchPatients() {
                    this.pagination.current_page = 1;
                    this.fetchPatients();
                },

                // Set quick filter
                setFilter(type) {
                    if (this.loading) return;

                    this.clearFilters(false);

                    switch (type) {
                        case 'active':
                            // You can add active filter logic here
                            break;
                        case 'nhmp':
                            this.filters.is_nhmp = '1';
                            break;
                        case 'today':
                            const today = new Date().toISOString().split('T')[0];
                            this.registrationFrom = today;
                            this.registrationTo = today;
                            break;
                        case 'all':
                            // Reset all filters
                            break;
                    }
                    this.applyFilters();
                },

                // Apply all filters
                applyFilters() {
                    if (this.loading) return;
                    this.pagination.current_page = 1;
                    this.fetchPatients();
                },

                // Reset all filters
                resetFilters(fetch = true) {
                    this.filters = {
                        gender: '',
                        blood_group: '',
                        is_nhmp: ''
                    };
                    this.searchQuery = '';
                    this.registrationFrom = '';
                    this.registrationTo = '';
                    this.pagination.current_page = 1;

                    if (fetch) {
                        this.fetchPatients();
                    }
                },

                // Clear filters
                clearFilters(fetch = true) {
                    this.resetFilters(fetch);
                },

                // Refresh patients
                refreshPatients() {
                    this.fetchPatients();
                    this.showToast('Patient list refreshed', 'info');
                },

                // Change page
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.pagination.current_page = page;
                        this.fetchPatients();
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
                    if (last > 1 && !range.includes(last)) range.push(last);

                    return range;
                },

                // Export patients
                exportPatients() {
                    const params = new URLSearchParams({
                        search: this.searchQuery,
                        gender: this.filters.gender,
                        blood_group: this.filters.blood_group,
                        is_nhmp: this.filters.is_nhmp,
                        start_date: this.registrationFrom,
                        end_date: this.registrationTo
                    });

                    window.open(`{{ route('reception.patients.export') }}?${params}`, '_blank');
                },

                // View patient details
                async viewPatientDetails(patient) {
                    try {
                        const response = await fetch(`/reception/patients/${patient.id}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.success) {
                                if (window.showPatientDetailsModal) {
                                    window.showPatientDetailsModal(data.patient);
                                } else {
                                    this.showToast('View details functionality', 'info');
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error loading patient details:', error);
                        this.showToast('Unable to load patient details', 'error');
                    }
                },

                // Edit patient
                editPatient(patient) {
                    if (window.showEditPatientModal) {
                        window.showEditPatientModal(patient);
                    } else {
                        this.showToast('Edit functionality', 'info');
                    }
                },

                // View medical history
                async viewMedicalHistory(patient) {
                    try {
                        const response = await fetch(`/reception/patients/${patient.id}/medical-history`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (data.success) {
                                if (window.showMedicalHistoryModal) {
                                    window.showMedicalHistoryModal(data.patient);
                                } else {
                                    this.showToast('Medical history functionality', 'info');
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error loading medical history:', error);
                        this.showToast('Unable to load medical history', 'error');
                    }
                },

                // View visit history
                async viewVisitHistory(patient) {
                    try {
                        const response = await fetch(`/reception/patients/${patient.id}/visit-history`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            if (window.showVisitHistoryModal) {
                                window.showVisitHistoryModal(data.visits || []);
                            } else {
                                this.showToast('Visit history functionality', 'info');
                            }
                        }
                    } catch (error) {
                        console.error('Error loading visit history:', error);
                        this.showToast('Unable to load visit history', 'error');
                    }
                },

                // Confirm delete
                confirmDelete(patient) {
                    this.userToDelete = patient;
                    this.showDeleteModal = true;
                },

                // Close delete modal
                closeDeleteModal() {
                    this.showDeleteModal = false;
                    this.userToDelete = null;
                },

                // Alias for delete modal button compatibility
                async confirmDeleteAction() {
                    return this.deletePatient();
                },

                // Delete patient
                async deletePatient() {
                    if (!this.userToDelete) return;

                    this.deleting = true;
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const response = await fetch(`/reception/patients/${this.userToDelete.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to delete patient');
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.showToast(data.message || 'Patient deleted successfully', 'success');
                            this.closeDeleteModal();
                            await this.fetchPatients();

                            if (data.stats) {
                                this.stats = data.stats;
                            }
                        } else {
                            this.showToast(data.message || 'Failed to delete patient', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting patient:', error);
                        this.showToast(error.message || 'Network error. Please try again.', 'error');
                    } finally {
                        this.deleting = false;
                    }
                },

                // Helper methods
                getAvatarColor(name, gender) {
                    const genderColors = {
                        'male': 'bg-gradient-to-br from-blue-400 to-blue-600',
                        'female': 'bg-gradient-to-br from-pink-400 to-pink-600',
                        'other': 'bg-gradient-to-br from-purple-400 to-purple-600'
                    };

                    if (gender && genderColors[gender]) {
                        return genderColors[gender];
                    }

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
                    const index = name ? name.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0) % colors.length :
                        0;
                    return colors[index];
                },

                formatDate(dateString) {
                    if (!dateString) return 'N/A';
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

                // Open bulk upload modal
                openBulkUploadModal() {
                    if (window.showBulkUploadModal) {
                        window.showBulkUploadModal();
                    } else {
                        this.showToast('Bulk upload functionality', 'info');
                    }
                },

                getNhmpIcon(designation) {
                    if (!designation) return 'fa-user-tie';

                    const uniformKeywords = ['inspector', 'constable', 'patrol', 'sub-inspector', 'asi', 'head constable',
                        'assistant sub-inspector'
                    ];
                    const lowerDesignation = designation.toLowerCase();

                    return uniformKeywords.some(keyword => lowerDesignation.includes(keyword)) ?
                        'fa-user-shield' :
                        'fa-user-tie';
                }
            };
        }
    </script>
@endsection