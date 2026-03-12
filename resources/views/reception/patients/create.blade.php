@extends('layouts.app')

@section('title', 'Register New Patient - HMS')
@section('page-title', 'Register New Patient')
@section('breadcrumb', 'Patients / Register')

@section('content')
<!-- Include duplicate-patient-modal -->
@include('reception.partials.duplicate-patient-modal')

<!-- Include existing-patient-vitals-modal -->
@include('reception.partials.existing-patient-vitals-modal')

<div x-data="receptionDashboard()" x-init="init()">

    <!-- Welcome & Stats Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Welcome Card - Updated with soft medical theme -->
    <div class="lg:col-span-2 relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group p-6">
        <div class="absolute -top-6 left-6 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-hospital-user text-2xl drop-shadow-md text-white"></i>
        </div>
        
        <div class="pt-8">
            <h2 class="text-2xl font-bold text-blue-800 mb-1">Reception Desk</h2>
            <p class="text-blue-600 mb-4">Admit and process patient visits</p>
            
            <div class="flex items-center gap-4 mt-4">
                <div class="bg-gradient-to-br from-blue-100 to-cyan-100 p-4 rounded-xl border border-blue-200">
                    <i class="fas fa-ambulance text-2xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-600">Ready to admit Patients</p>
                    <p class="text-xl font-bold text-blue-800">Patient Care</p>
                </div>
            </div>
            
            <div class="mt-4 border-t border-blue-200 pt-3">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-xs text-blue-700 font-medium">Currently accepting new patients</span>
(NHMP-HMS STARTED)
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Stats Card - Updated with amber/orange theme -->
    <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/30 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group p-6">
        <div class="absolute -top-6 left-6 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-300 shadow-lg shadow-amber-900/40 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-chart-line text-2xl drop-shadow-md text-white"></i>
        </div>
        
        <div class="pt-8">
            <h3 class="text-xl font-bold text-amber-800 mb-4">Today's Stats</h3>
            
            <div class="space-y-4">
                <!-- <div class="flex justify-between items-center border-b border-amber-100 pb-2">
                    <span class="text-amber-700 font-medium">Total Patients</span>
                    <span class="font-bold text-amber-900 text-lg">{{ $totalPatients }}</span>
                </div> -->
                <div class="flex justify-between items-center border-b border-amber-100 pb-2">
                    <span class="text-amber-700 font-medium">Today's Registrations</span>
                    <span class="font-bold text-amber-900 text-lg">{{ $todayPatients }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-amber-100 pb-2">
                    <span class="text-amber-700 font-medium">Waiting Patients</span>
                    <span class="font-bold text-amber-900 text-lg">{{ $waitingPatients }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-amber-700 font-medium">In Progress</span>
                    <span class="font-bold text-amber-900 text-lg" x-text="inProgressCount">0</span>
                </div>
            </div>
            
            <div class="mt-4 border-t border-amber-200 pt-3">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-600"></span>
                    <span class="text-xs text-amber-700 font-medium">Updated in real-time</span>
                </div>
            </div>
        </div>
    </div>    
</div>
    (NHMP-HMS STARTED)
    <div class="mt-8 grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Registration Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Form Tabs (Now only New Patient) -->
                <div class="flex border-b">
                    <button type="button" @click="activeTab = 'new'"
                        :class="activeTab === 'new' ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500'"
                        class="tab-btn flex-1 py-4 font-bold">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            New Patient
                        </span>
                    </button>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <!-- New Patient Form -->
                    <form action="{{ route('reception.patients.store') }}" method="POST" id="newPatientForm"
                        x-show="activeTab === 'new'" @submit.prevent="submitNewPatientForm">
                        @csrf
                        <input type="hidden" name="visit_type" x-model="visitType">

                        <!-- Visit Type Selection -->
                        <div class="mb-6 p-4 border-2 border-dashed border-indigo-200 rounded-lg bg-indigo-50/30">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-stethoscope mr-2 text-indigo-600"></i>
                                Visit Type *
                            </h3>
                            <div class="grid md:grid-cols-3 gap-3">
                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                                    <input type="radio" name="visit_type" value="routine" required
                                        class="w-4 h-4 text-indigo-600 visit-type-radio" x-model="visitType"
                                        @change="clearVisitTypeErrors">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">Routine</span>
                                        <p class="text-xs text-gray-500">Regular checkup or consultation</p>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-red-50 has-[:checked]:border-red-500">
                                    <input type="radio" name="visit_type" value="emergency" required
                                        class="w-4 h-4 text-red-600 visit-type-radio" x-model="visitType"
                                        @change="clearVisitTypeErrors">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">Emergency</span>
                                        <p class="text-xs text-gray-500">Urgent medical attention needed</p>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-green-50 has-[:checked]:border-green-500">
                                    <input type="radio" name="visit_type" value="followup" required
                                        class="w-4 h-4 text-green-600 visit-type-radio" x-model="visitType"
                                        @change="clearVisitTypeErrors">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">Follow-up</span>
                                        <p class="text-xs text-gray-500">Post-treatment review</p>
                                    </div>
                                </label>
                            </div>
                            <div id="visit-type-error" class="text-red-500 text-sm mt-2 hidden">
                                Please select a visit type
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="section-card mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">1</span>
                                    <h3 class="font-bold text-gray-800">Basic Information</h3>
                                </div>
                                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Required</span>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                    <input type="text" name="name" required x-model="newPatient.name"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="John Doe">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CNIC</label>
                                    <input type="text" name="cnic" x-model="newPatient.cnic"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="00000-0000000-0" oninput="formatCNIC(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number
                                        *</label>
                                    <input type="tel" name="phone" required x-model="newPatient.phone"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="03001234567" oninput="validatePhone(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Emergency
                                        Contact</label>
                                    <input type="tel" name="emergency_contact"
                                        x-model="newPatient.emergency_contact"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="03009876543"
                                        oninput="validateEmergencyContact(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth
                                        *</label>
                                    <input type="date" name="dob" required x-model="newPatient.dob"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        max="{{ date('Y-m-d') }}"
                                        onchange="validateDOB(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                                    <select name="gender" required x-model="newPatient.gender"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea name="address" rows="2" x-model="newPatient.address"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="House #, Street, City, Province"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                                    <select name="blood_group" x-model="newPatient.blood_group"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        <option value="">Select Blood Group</option>
                                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                                        <option value="{{ $group }}">{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <div class="section-card mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">2</span>
                                    <h3 class="font-bold text-gray-800">Medical Information</h3>
                                </div>
                                <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full">Important</span>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Known
                                        Allergies</label>
                                    <textarea name="allergies" rows="2" x-model="newPatient.allergies"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="List any allergies (e.g., Penicillin, Peanuts)"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chronic
                                        Conditions</label>
                                    <textarea name="chronic_conditions" rows="2"
                                        x-model="newPatient.chronic_conditions"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="e.g., Diabetes, Hypertension, Asthma"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Medical
                                        History</label>
                                    <textarea name="medical_history" rows="2" x-model="newPatient.medical_history"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="Previous surgeries, treatments, family history"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Category Selection -->
                        <div class="mb-6 p-4 border-2 border-dashed border-blue-200 rounded-lg bg-blue-50/30">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-id-card mr-2 text-blue-600"></i>
                                Patient Category *
                            </h3>
                            <div class="grid md:grid-cols-3 gap-3">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                    <input type="radio" name="category" value="private" required
                                           class="w-4 h-4 text-blue-600" x-model="newPatient.category"
                                           @change="newPatient.is_nhmp = false; newPatient.is_dependent = false">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">Private</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                    <input type="radio" name="category" value="nhmp" required
                                           class="w-4 h-4 text-blue-600" x-model="newPatient.category"
                                           @change="newPatient.is_nhmp = true; newPatient.is_dependent = false; newPatient.relationship = 'self'">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">NHMP Staff</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-white transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                                    <input type="radio" name="category" value="dependent" required
                                           class="w-4 h-4 text-blue-600" x-model="newPatient.category"
                                           @change="newPatient.is_nhmp = true; newPatient.is_dependent = true">
                                    <div class="ml-3">
                                        <span class="font-medium text-gray-700">Dependent</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Hidden inputs for backend compatibility -->
                            <input type="hidden" name="is_nhmp" :value="newPatient.is_nhmp ? '1' : '0'">

                            <!-- NHMP & Dependent Fields -->
                            <div x-show="newPatient.is_nhmp" x-transition class="mt-6 space-y-6 pt-6 border-t border-blue-100">
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div x-show="newPatient.is_dependent">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Relation with Staff *</label>
                                        <select name="relationship" x-model="newPatient.relationship"
                                                :required="newPatient.is_dependent"
                                                :disabled="!newPatient.is_dependent"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Relation</option>
                                            <option value="spouse">Spouse</option>
                                            <option value="child">Child</option>
                                            <option value="parent">Parent</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div x-show="newPatient.is_dependent">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Staff Member Search (Optional)</label>
                                        <div class="relative">
                                            <input type="text" x-model="staffSearchQuery" @input.debounce.300ms="searchStaff"
                                                   placeholder="Search by name, emrn..."
                                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <div x-show="staffSearchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                <template x-for="staff in staffSearchResults" :key="staff.id">
                                                    <div @click="selectStaff(staff)" class="p-2 hover:bg-blue-50 cursor-pointer text-sm">
                                                        <span x-text="staff.name"></span> (<span x-text="staff.emrn"></span>)
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <input type="hidden" name="parent_id" x-model="newPatient.parent_id">
                                    </div>
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation
                                            *</label>
                                        <select name="designation_id" x-model="newPatient.designation_id"
                                            :required="newPatient.is_nhmp"
                                            :disabled="!newPatient.is_nhmp"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Designation</option>
                                            @foreach ($designations as $designation)
                                            <option
                                                value="{{ $designation->id }}">{{ $designation->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-1">Rank/Grade</label>
                                        <input type="text" name="rank" x-model="newPatient.rank"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                            placeholder="e.g., BPS-17">
                                    </div>
                                </div>

                                <!-- Office Type Toggle -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Office Type</label>
                                    <div class="flex space-x-2">
                                        <button type="button"
                                            @click="newPatient.officeType = 'office'"
                                            :class="newPatient.officeType === 'office' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-4 py-2 rounded-lg font-medium transition-colors">
                                            <i class="fas fa-building mr-2"></i>Office
                                        </button>
                                        <button type="button"
                                            @click="newPatient.officeType = 'hierarchical'"
                                            :class="newPatient.officeType === 'hierarchical' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="px-4 py-2 rounded-lg font-medium transition-colors">
                                            <i class="fas fa-sitemap mr-2"></i>Hierarchical
                                        </button>
                                    </div>
                                </div>

                                <!-- Office Selection -->
                                <div x-show="newPatient.officeType === 'office'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Office *</label>
                                    <select name="office_id" x-model="newPatient.office_id"
                                        :required="newPatient.is_nhmp && newPatient.officeType === 'office'"
                                        :disabled="!(newPatient.is_nhmp && newPatient.officeType === 'office')"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                        <option value="">Select Office</option>
                                        @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hierarchical Office Selection -->
                                <div x-show="newPatient.officeType === 'hierarchical'" x-transition
                                    class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Region *</label>
                                        <select x-model="newPatient.selectedRegion" @change="loadZones('new')"
                                            :required="newPatient.is_nhmp && newPatient.officeType === 'hierarchical'"
                                            :disabled="!(newPatient.is_nhmp && newPatient.officeType === 'hierarchical')"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Region</option>
                                            @foreach ($regions as $region)
                                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="newPatient.selectedRegion">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                                        <select x-model="newPatient.selectedZone" @change="loadSectors('new')"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Zone</option>
                                            <template x-for="zone in newPatient.zones" :key="zone.id">
                                                <option :value="zone.id" x-text="zone.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div x-show="newPatient.selectedZone">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                                        <select x-model="newPatient.selectedSector" @change="loadPLHQs('new')"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Sector</option>
                                            <template x-for="sector in newPatient.sectors" :key="sector.id">
                                                <option :value="sector.id" x-text="sector.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div x-show="newPatient.selectedSector">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">PLHQ *</label>
                                        <select x-model="newPatient.selectedPLHQ" @change="loadBeats('new')"
                                            :required="newPatient.is_nhmp && newPatient.officeType === 'hierarchical' && newPatient.selectedSector"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select PLHQ</option>
                                            <template x-for="plhq in newPatient.plhqs" :key="plhq.id">
                                                <option :value="plhq.id" x-text="plhq.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div x-show="newPatient.selectedPLHQ">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Beat</label>
                                        <select x-model="newPatient.selectedBeat" name="beat_id"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                            <option value="">Select Beat</option>
                                            <template x-for="beat in newPatient.beats" :key="beat.id">
                                                <option :value="beat.id" x-text="beat.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Hidden input for hierarchical office ID -->
                                    <input type="hidden" name="hierarchical_office_id"
                                        x-model="newPatient.hierarchicalOfficeId">
                                </div>
                            </div>
                        </div>

                        <!-- Vital Signs -->
                        <div class="section-card">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center font-bold">3</span>
                                    <h3 class="font-bold text-gray-800">Vital Signs</h3>
                                </div>
                                <span class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1 rounded-full">Required</span>
                            </div>

                            <div class="space-y-6">
                                <!-- Temperature & Pulse -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Temperature
                                            (°F)</label>
                                        <input type="number" step="0.1" name="vitals[temperature]" required
                                            x-model="newPatient.vitals.temperature"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pulse
                                            (BPM)</label>
                                        <input type="number" name="vitals[pulse]" required
                                            x-model="newPatient.vitals.pulse"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>

                                <!-- Blood Pressure -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Pressure
                                        (mmHg)</label>
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <input type="number" name="vitals[blood_pressure_systolic]" required
                                                x-model="newPatient.vitals.blood_pressure_systolic"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                                placeholder="120">
                                            <span class="text-xs text-gray-500 mt-1 block">Systolic</span>
                                        </div>
                                        <div>
                                            <input type="number" name="vitals[blood_pressure_diastolic]" required
                                                x-model="newPatient.vitals.blood_pressure_diastolic"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                                placeholder="80">
                                            <span class="text-xs text-gray-500 mt-1 block">Diastolic</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Oxygen & Respiratory -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Oxygen
                                            Saturation (%)</label>
                                        <input type="number" name="vitals[oxygen_saturation]" required
                                            x-model="newPatient.vitals.oxygen_saturation"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Respiratory
                                            Rate</label>
                                        <input type="number" name="vitals[respiratory_rate]" required
                                            x-model="newPatient.vitals.respiratory_rate"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>

                                <!-- Weight & Height -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight
                                            (kg)</label>
                                        <input type="number" step="0.1" name="vitals[weight]"
                                            x-model="newPatient.vitals.weight"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Height
                                            (cm)</label>
                                        <input type="number" step="0.1" name="vitals[height]"
                                            x-model="newPatient.vitals.height"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>

                                <!-- Pain Scale & Blood Glucose -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pain Scale
                                            (0-10)</label>
                                        <input type="range" name="vitals[pain_scale]" min="0" max="10"
                                            x-model="newPatient.vitals.pain_scale"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                        <div class="text-center text-sm text-gray-600 mt-1">
                                            <span x-text="newPatient.vitals.pain_scale || '0'"></span>/10
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Glucose
                                            (mg/dL)</label>
                                        <input type="number" step="0.1" name="vitals[blood_glucose]"
                                            x-model="newPatient.vitals.blood_glucose"
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>

                                <!-- Clinical Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Clinical
                                        Notes</label>
                                    <textarea name="vitals[notes]" rows="3" x-model="newPatient.vitals.notes"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                        placeholder="Any additional observations or remarks..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" :disabled="isSubmitting"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-spinner fa-spin" x-show="isSubmitting"></i>
                                <span
                                    x-text="isSubmitting ? 'Processing...' : 'Complete Registration & Generate Visit'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div> <!-- This closes the main grid -->
        </div> <!-- This closes the receptionDashboard div -->

        <!-- Right Column - Quick Search & Patient Lists -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Patient Search -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Quick Patient Search
                </h2>
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="performSearch"
                        placeholder="Search by name, EMRN, phone, or CNIC..."
                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                    <div class="absolute left-4 top-3.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Search Results -->
                <div x-show="searchResults.length > 0" class="mt-3 space-y-2 max-h-60 overflow-y-auto">
                    <template x-for="patient in searchResults" :key="patient.id">
                        <div @click="selectExistingPatient(patient)"
                            class="p-3 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-800" x-text="patient.name"></div>
                                    <div class="text-sm text-gray-600"
                                        x-text="`${patient.emrn} • ${patient.phone}`"></div>
                                </div>
                                <span
                                    class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Select</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="searchQuery && searchResults.length === 0" class="mt-3 text-center py-4">
                    <p class="text-gray-600">No patients found</p>
                </div>
            </div>

            <!-- Waiting Patients -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="font-bold text-gray-800 text-xl flex items-center gap-2">
                        <i class="fas fa-clock text-orange-500"></i>
                        Waiting Patients
                        <span
                            class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded-full ml-2">
                            {{ $waitingPatients }}
                        </span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Click to update status/vitals</p>
                </div>

                <div class="overflow-y-auto max-h-[450px]" id="waitingPatientsList">
                    @include('reception.partials.waiting-patients', ['waitingPatientsList' => $waitingPatientsList])
                </div>
            </div>

            <!-- In Progress Patients -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="font-bold text-gray-800 text-xl flex items-center gap-2">
                        <i class="fas fa-user-md text-blue-500"></i>
                        In Progress
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full ml-2"
                            x-text="inProgressCount">0</span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Currently being examined</p>
                </div>

                <div class="overflow-y-auto max-h-[450px]" id="inProgressPatientsList">
                    <div class="p-8 text-center" x-show="inProgressCount === 0">
                        <div class="w-16 h-16 mx-auto mb-4 text-gray-300">
                            <i class="fas fa-user-md text-4xl"></i>
                        </div>
                        <p class="text-gray-500">No patients in progress</p>
                    </div>
                    <template x-for="patient in inProgressPatients" :key="patient.id">
                        <div
                            class="p-4 hover:bg-blue-50 transition-colors cursor-pointer border-b border-gray-100"
                            @click="openPatientModal(patient)">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user-md text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-gray-800 truncate" x-text="patient.name"></h4>
                                        <span
                                            class="text-xs font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded"
                                            x-text="patient.queue_token"></span>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-sm text-gray-600" x-text="patient.phone"></span>
                                        <span class="text-xs px-2 py-0.5 rounded-full"
                                            :class="patient.gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'"
                                            x-text="patient.gender"></span>
                                    </div>
                                    <div class="mt-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            In Progress
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500"
                                            x-text="patient.waiting_time"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Update Modal (for waiting/in-progress patients) -->
    <div x-show="showPatientModal" x-transition
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Update Patient Status & Vitals</h3>
                <button @click="showPatientModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">
                    &times;
                </button>
            </div>

            <form @submit.prevent="updatePatientStatus" class="p-6">
                @csrf
                <input type="hidden" x-model="modalData.visit_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient Info -->
                    <div>
                        <div class="bg-blue-50 p-4 rounded-lg mb-6">
                            <h4 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
                                <i class="fas fa-user text-blue-600"></i>
                                Patient Information
                            </h4>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm text-gray-600">Name:</span>
                                    <span class="font-bold ml-2" x-text="modalData.patient_name"></span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Token:</span>
                                    <span class="font-bold text-blue-600 ml-2"
                                        x-text="modalData.queue_token"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Update -->
                        <div class="mb-6">
                            <h4 class="font-bold text-gray-800 mb-3">Update Status</h4>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="waiting" x-model="modalData.status"
                                        class="sr-only peer">
                                    <div
                                        class="p-3 text-center rounded-lg border-2 border-gray-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                        <i class="fas fa-clock text-yellow-600 text-lg mb-1"></i>
                                        <div class="text-sm font-medium">Waiting</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="in_progress"
                                        x-model="modalData.status"
                                        class="sr-only peer">
                                    <div
                                        class="p-3 text-center rounded-lg border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                        <i class="fas fa-user-md text-blue-600 text-lg mb-1"></i>
                                        <div class="text-sm font-medium">In Progress</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="completed"
                                        x-model="modalData.status"
                                        class="sr-only peer">
                                    <div
                                        class="p-3 text-center rounded-lg border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50">
                                        <i class="fas fa-check-circle text-green-600 text-lg mb-1"></i>
                                        <div class="text-sm font-medium">Completed</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Vitals Update -->
                    <div>
                        <h4 class="font-bold text-gray-800 mb-3">Update Vitals</h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Temperature
                                        (°F)</label>
                                    <input type="number" step="0.1" x-model="modalData.temperature"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pulse
                                        (BPM)</label>
                                    <input type="number" x-model="modalData.pulse"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Oxygen
                                        Saturation
                                        (%)</label>
                                    <input type="number" step="0.1" x-model="modalData.oxygen_saturation"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Glucose
                                        (mg/dL)</label>
                                    <input type="number" x-model="modalData.blood_glucose"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Blood
                                    Pressure</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="number" x-model="modalData.blood_pressure_systolic"
                                            placeholder="Systolic"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <span class="text-xs text-gray-500 mt-1 block">Systolic</span>
                                    </div>
                                    <div>
                                        <input type="number" x-model="modalData.blood_pressure_diastolic"
                                            placeholder="Diastolic"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <span class="text-xs text-gray-500 mt-1 block">Diastolic</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Notes</label>
                                <textarea x-model="modalData.notes" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Any additional observations..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" @click="showPatientModal = false"
                        class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isUpdating"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i>
                        <span x-text="isUpdating ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // First, define the function globally before Alpine initializes
    function receptionDashboard() {
        return {
            // State
            activeTab: 'new',
            isSubmitting: false,
            isUpdating: false,
            showPatientModal: false,
            visitType: 'routine',

            // New patient form data
            newPatient: {
                name: '',
                cnic: '',
                phone: '',
                emergency_contact: '',
                dob: '',
                gender: '',
                address: '',
                blood_group: '',
                allergies: '',
                chronic_conditions: '',
                medical_history: '',
                is_nhmp: false,
                officeType: 'office',
                designation_id: '',
                rank: '',
                office_id: '',
                selectedRegion: '',
                selectedZone: '',
                selectedSector: '',
                selectedPLHQ: '',
                selectedBeat: '',
                hierarchicalOfficeId: '',
                zones: [],
                sectors: [],
                plhqs: [],
                beats: [],
                vitals: {
                    temperature: '97.7',
                    pulse: '72',
                    blood_pressure_systolic: '120',
                    blood_pressure_diastolic: '80',
                    oxygen_saturation: '98',
                    respiratory_rate: '16',
                    weight: '',
                    height: '',
                    pain_scale: '0',
                    blood_glucose: '',
                    notes: ''
                }
            },

            // Search
            searchQuery: '',
            searchResults: [],
            selectedExistingPatient: {
                name: '',
                emrn: '',
                phone: '',
                cnic: '',
                id: ''
            },

            // Patient lists
            inProgressPatients: [],
            inProgressCount: 0,

            // Modal data
            modalData: {
                visit_id: '',
                patient_name: '',
                queue_token: '',
                status: 'waiting',
                temperature: '',
                pulse: '',
                oxygen_saturation: '',
                blood_glucose: '',
                blood_pressure_systolic: '',
                blood_pressure_diastolic: '',
                notes: ''
            },

            // Initialize
            init() {
                console.log('Dashboard initialized');
                this.loadInProgressPatients();

                // Store reference for modal functions
                window.alpineReceptionData = this;

                // Add event listeners for waiting patients
                setTimeout(() => this.addWaitingPatientEventListeners(), 100);
            },

            // Add event listeners for waiting patients
            addWaitingPatientEventListeners() {
                const waitingPatients = document.querySelectorAll('.waiting-patient-item');
                waitingPatients.forEach(patient => {
                    patient.addEventListener('click', (e) => {
                        e.stopPropagation();

                        const visitId = patient.getAttribute('data-visit-id');
                        const patientName = patient.getAttribute('data-patient-name');
                        const token = patient.getAttribute('data-token');

                        this.modalData = {
                            visit_id: visitId,
                            patient_name: patientName,
                            queue_token: token,
                            status: 'waiting',
                            temperature: '',
                            pulse: '',
                            oxygen_saturation: '',
                            blood_glucose: '',
                            blood_pressure_systolic: '',
                            blood_pressure_diastolic: '',
                            notes: ''
                        };

                        this.showPatientModal = true;
                    });
                });
            },

            // Form validation
            clearVisitTypeErrors() {
                const errorEl = document.getElementById('visit-type-error');
                if (errorEl) errorEl.classList.add('hidden');
            },

            // NHMP Fields
            toggleNHMPFields() {
                if (!this.newPatient.is_nhmp) {
                    this.resetNHMPFields();
                }
            },

            resetNHMPFields() {
                this.newPatient.designation_id = '';
                this.newPatient.rank = '';
                this.newPatient.office_id = '';
                this.newPatient.selectedRegion = '';
                this.newPatient.selectedZone = '';
                this.newPatient.selectedSector = '';
                this.newPatient.selectedPLHQ = '';
                this.newPatient.selectedBeat = '';
                this.newPatient.hierarchicalOfficeId = '';
                this.newPatient.zones = [];
                this.newPatient.sectors = [];
                this.newPatient.plhqs = [];
                this.newPatient.beats = [];
            },

            // Office hierarchy loading
            async loadZones(type = 'new') {
                const region = type === 'new' ? this.newPatient.selectedRegion : this.selectedRegion;
                if (!region) {
                    if (type === 'new') {
                        this.newPatient.zones = [];
                        this.newPatient.selectedZone = '';
                        this.newPatient.selectedSector = '';
                        this.newPatient.selectedPLHQ = '';
                        this.newPatient.selectedBeat = '';
                    }
                    return;
                }

                try {
                    const response = await fetch(`/api/offices/region/${region}/zones`);
                    if (response.ok) {
                        const zones = await response.json();
                        if (type === 'new') {
                            this.newPatient.zones = zones;
                            this.newPatient.selectedZone = '';
                            this.newPatient.selectedSector = '';
                            this.newPatient.selectedPLHQ = '';
                            this.newPatient.selectedBeat = '';
                        }
                    }
                } catch (error) {
                    console.error('Error loading zones:', error);
                    window.showNotification('Error loading zones', 'error');
                }
            },

            async loadSectors(type = 'new') {
                const zone = type === 'new' ? this.newPatient.selectedZone : this.selectedZone;
                if (!zone) {
                    if (type === 'new') {
                        this.newPatient.sectors = [];
                        this.newPatient.selectedSector = '';
                        this.newPatient.selectedPLHQ = '';
                        this.newPatient.selectedBeat = '';
                    }
                    return;
                }

                try {
                    const response = await fetch(`/api/offices/zone/${zone}/sectors`);
                    if (response.ok) {
                        const sectors = await response.json();
                        if (type === 'new') {
                            this.newPatient.sectors = sectors;
                            this.newPatient.selectedSector = '';
                            this.newPatient.selectedPLHQ = '';
                            this.newPatient.selectedBeat = '';
                        }
                    }
                } catch (error) {
                    console.error('Error loading sectors:', error);
                    window.showNotification('Error loading sectors', 'error');
                }
            },

            async loadPLHQs(type = 'new') {
                const sector = type === 'new' ? this.newPatient.selectedSector : this.selectedSector;
                if (!sector) {
                    if (type === 'new') {
                        this.newPatient.plhqs = [];
                        this.newPatient.selectedPLHQ = '';
                        this.newPatient.selectedBeat = '';
                    }
                    return;
                }

                try {
                    const response = await fetch(`/api/offices/sector/${sector}/plhqs`);
                    if (response.ok) {
                        const plhqs = await response.json();
                        if (type === 'new') {
                            this.newPatient.plhqs = plhqs;
                            this.newPatient.selectedPLHQ = '';
                            this.newPatient.selectedBeat = '';
                        }
                    }
                } catch (error) {
                    console.error('Error loading PLHQs:', error);
                    window.showNotification('Error loading PLHQs', 'error');
                }
            },

            async loadBeats(type = 'new') {
                const plhq = type === 'new' ? this.newPatient.selectedPLHQ : this.selectedPLHQ;
                if (!plhq) {
                    if (type === 'new') {
                        this.newPatient.beats = [];
                        this.newPatient.selectedBeat = '';
                    }
                    return;
                }

                try {
                    const response = await fetch(`/api/offices/plhq/${plhq}/beats`);
                    if (response.ok) {
                        const beats = await response.json();
                        if (type === 'new') {
                            this.newPatient.beats = beats;
                            this.newPatient.selectedBeat = '';
                            this.newPatient.hierarchicalOfficeId = plhq;
                        }
                    }
                } catch (error) {
                    console.error('Error loading beats:', error);
                    window.showNotification('Error loading beats', 'error');
                }
            },

            // Patient search
            async performSearch() {
                if (this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }

                try {
                    const response = await fetch(`{{ route("reception.quick-search") }}?search=${encodeURIComponent(this.searchQuery)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.searchResults = await response.json();
                    }
                } catch (error) {
                    console.error('Search error:', error);
                    this.searchResults = [];
                    window.showNotification('Search service unavailable', 'error');
                }
            },

            selectExistingPatient(patient) {
                this.selectedExistingPatient = patient;
                this.searchResults = [];
                this.searchQuery = '';

                setTimeout(() => {
                    if (window.showExistingPatientVitalsModal) {
                        window.showExistingPatientVitalsModal(patient);
                    }
                }, 100);
            },

            clearSelectedPatient() {
                this.selectedExistingPatient = {
                    name: '',
                    emrn: '',
                    phone: '',
                    cnic: '',
                    id: ''
                };
                window.showNotification('Patient selection cleared', 'info');
            },

            // Patient modal
            async openPatientModal(patientData) {
                try {
                    const baseUrl = window.location.origin;
                    const url = `${baseUrl}/reception/visits/${patientData.id}/vitals`;

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.modalData = {
                                visit_id: patientData.id,
                                patient_name: data.visit.patient_name,
                                queue_token: patientData.queue_token,
                                status: data.visit.status,
                                temperature: data.vitals?.temperature || '',
                                pulse: data.vitals?.pulse || '',
                                oxygen_saturation: data.vitals?.oxygen_saturation || '',
                                blood_glucose: data.vitals?.blood_glucose || '',
                                blood_pressure_systolic: data.vitals?.blood_pressure_systolic || '',
                                blood_pressure_diastolic: data.vitals?.blood_pressure_diastolic || '',
                                notes: data.vitals?.notes || ''
                            };
                        }
                    }

                    this.showPatientModal = true;
                } catch (error) {
                    console.error('Error loading patient data:', error);
                    showError(error, 'Error!')
                    window.showNotification('Error loading patient data', 'error');
                }
            },

            async updatePatientStatus() {
                this.isUpdating = true;

                try {
                    const formData = new FormData();
                    Object.keys(this.modalData).forEach(key => {
                        if (this.modalData[key] !== null && this.modalData[key] !== undefined) {
                            formData.append(key, this.modalData[key]);
                        }
                    });

                    const baseUrl = window.location.origin;
                    const url = `${baseUrl}/reception/visits/${this.modalData.visit_id}/update-status`;

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            window.showNotification('Patient updated successfully!', 'success');
                            this.showPatientModal = false;
                            
                            // Reload both lists to show updated state
                            await this.loadInProgressPatients();
                            // If there was a function for waiting list, call it too
                            if (typeof this.loadWaitingPatients === 'function') {
                                await this.loadWaitingPatients();
                            }
                        } else {
                            window.showNotification(data.message || 'Error updating patient', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Update error:', error);
                    window.showNotification('Network error', 'error');
                } finally {
                    this.isUpdating = false;
                }
            },

            // New patient form submission
            async submitNewPatientForm(event) {
                event.preventDefault();

                if (!this.visitType) {
                    document.getElementById('visit-type-error').classList.remove('hidden');
                    return;
                }

                const form = event.target;
                const formData = new FormData(form);
                formData.append('visit_type', this.visitType);

                this.isSubmitting = true;

                try {
                    // Check for duplicate patient
                    const phone = formData.get('phone');
                    const cnic = formData.get('cnic');

                    if (phone || cnic) {
                        const existingPatient = await this.checkDuplicatePatient(phone, cnic);
                        if (existingPatient) {
                            if (window.showDuplicatePatientModal) {
                                window.showDuplicatePatientModal(existingPatient);
                            }
                            this.isSubmitting = false;
                            return;
                        }
                    }

                    // Submit the form
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        const result = await response.json();
                        if (result.success) {
                            window.showNotification(result.message || 'Patient registered successfully!', 'success');
                            this.resetNewPatientForm();

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            if (result.errors) {
                                Object.keys(result.errors).forEach(field => {
                                    window.showNotification(result.errors[field][0], 'error');
                                });
                            } else {
                                window.showNotification(result.message || 'An error occurred', 'error');
                            }
                        }
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    window.showNotification('Network error', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            },

            // Reset new patient form
            resetNewPatientForm() {
                this.newPatient = {
                    name: '',
                    cnic: '',
                    phone: '',
                    emergency_contact: '',
                    dob: '',
                    gender: '',
                    address: '',
                    blood_group: '',
                    allergies: '',
                    chronic_conditions: '',
                    medical_history: '',
                    category: 'private',
                    is_nhmp: false,
                    is_dependent: false,
                    relationship: '',
                    parent_id: '',
                    officeType: 'office',
                    designation_id: '',
                    rank: '',
                    office_id: '',
                    selectedRegion: '',
                    selectedZone: '',
                    selectedSector: '',
                    selectedPLHQ: '',
                    selectedBeat: '',
                    hierarchicalOfficeId: '',
                    zones: [],
                    sectors: [],
                    plhqs: [],
                    beats: [],
                    vitals: {
                        temperature: '97.7',
                        pulse: '72',
                        blood_pressure_systolic: '120',
                        blood_pressure_diastolic: '80',
                        oxygen_saturation: '98',
                        respiratory_rate: '16',
                        weight: '',
                        height: '',
                        pain_scale: '0',
                        blood_glucose: '',
                        notes: ''
                    }
                };
                this.visitType = 'routine';
            },

            // Check for duplicate patient
            async checkDuplicatePatient(phone, cnic = null) {
                try {
                    const cleanPhone = phone ? phone.replace(/[^\d]/g, '') : null;
                    const cleanCnic = cnic ? cnic.replace(/[^\d-]/g, '') : null;

                    if (!cleanPhone && !cleanCnic) return null;

                    const response = await fetch('{{ route("reception.check-patient-exists") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            phone: cleanPhone,
                            cnic: cleanCnic
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        return data.exists ? data.patient : null;
                    }
                    return null;
                } catch (error) {
                    console.error('Error checking duplicate:', error);
                    return null;
                }
            },

            // Staff search for dependents
            staffSearchQuery: '',
            staffSearchResults: [],
            async searchStaff() {
                if (this.staffSearchQuery.length < 3) {
                    this.staffSearchResults = [];
                    return;
                }
                try {
                    const response = await fetch(`{{ route('reception.quick-search') }}?q=${this.staffSearchQuery}`);
                    if (response.ok) {
                        this.staffSearchResults = await response.json();
                    }
                } catch (error) {
                    console.error('Staff search error:', error);
                }
            },
            selectStaff(staff) {
                this.newPatient.parent_id = staff.id;
                this.staffSearchQuery = staff.name;
                this.staffSearchResults = [];
                window.showNotification(`Selected staff: ${staff.name}`, 'info');
            },

            // Patient lists
            async loadInProgressPatients() {
                try {
                    const response = await fetch('{{ route("reception.visits.in-progress") }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.inProgressPatients = data.patients || [];
                            this.inProgressCount = data.count || 0;
                        }
                    }
                } catch (error) {
                    console.error('Error loading in-progress patients:', error);
                }
            }
        };
    }

    // Add utility functions that were previously defined separately
    function formatCNIC(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value.length > 0) {
            value = value.substring(0, 13);
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            }
            if (value.length > 13) {
                value = value.substring(0, 13) + '-' + value.substring(13);
            }
        }
        input.value = value;
    }

    function validatePhone(input) {
        const value = input.value.replace(/[^0-9]/g, '');

        if (value && (value.length !== 11 || !value.startsWith('03'))) {
            input.setCustomValidity('Phone must be 11 digits starting with 03');
        } else {
            input.setCustomValidity('');
        }
    }

    function validateDOB(input) {
        const birthDate = new Date(input.value);
        const today = new Date();

        if (birthDate > today) {
            input.setCustomValidity('Date of birth cannot be in the future');
        } else {
            input.setCustomValidity('');
        }
    }

    function validateEmergencyContact(input) {
        const phone = input.value.replace(/[^0-9]/g, '');

        if (phone && !phone.match(/^03\d{9}$/)) {
            input.setCustomValidity('Emergency contact must be 11 digits starting with 03');
        } else {
            input.setCustomValidity('');
        }
    }

    // Global functions for duplicate modal
    window.showDuplicatePatientModal = function(patient) {
        document.getElementById('duplicate-patient-name').textContent = patient.name;
        document.getElementById('duplicate-patient-emrn').textContent = patient.emrn;
        document.getElementById('duplicate-patient-phone').textContent = patient.phone;
        document.getElementById('duplicate-patient-cnic').textContent = patient.cnic || 'N/A';
        
        // Store patient data for buttons
        window.currentDuplicatePatient = patient;
        
        document.getElementById('duplicate-patient-modal').classList.remove('hidden');
    };

    window.closeDuplicateModal = function() {
        document.getElementById('duplicate-patient-modal').classList.add('hidden');
    };

    window.useExistingPatient = function() {
        const patient = window.currentDuplicatePatient;
        if (!patient) return;
        
        window.closeDuplicateModal();
        
        // Open the vitals modal for this patient to start a visit
        if (window.showExistingPatientVitalsModal) {
            window.showExistingPatientVitalsModal(patient);
        } else {
            // Find the Alpine component and call its method as fallback
            const dashboardEl = document.querySelector('[x-data="receptionDashboard()"]');
            if (dashboardEl && dashboardEl.__x) {
                dashboardEl.__x.$data.openPatientModal(patient);
            }
        }
    };

    window.viewPatientHistory = function() {
        const patient = window.currentDuplicatePatient;
        if (!patient) return;
        
        window.location.href = `/reception/patients/${patient.id}/history`;
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize validation for existing inputs
        document.querySelectorAll('input[name="phone"]').forEach(validatePhone);
        document.querySelectorAll('input[name="emergency_contact"]').forEach(validateEmergencyContact);
    });
</script>
<style>
    /* Enhanced styling */
    [x-cloak] {
        display: none !important;
    }

    /* Gradient text */
    .gradient-text {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Smooth transitions */
    .transition-all {
        transition: all 0.3s ease;
    }

    /* Better table styling */
    table tbody tr {
        transition: all 0.2s ease;
    }

    table tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Modal backdrop fix */
    .fixed.inset-0 {
        backdrop-filter: blur(2px);
    }

    /* Ensure modals are on top */
    .z-50 {
        z-index: 9999 !important;
    }
</style>
@endpush
@endsection