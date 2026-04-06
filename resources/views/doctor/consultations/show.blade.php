@extends('layouts.app')

@section('title', 'Consultation')
@section('page-title', 'Consultation - ' . $visit->patient->name)
@section('page-description', 'Patient consultation and diagnosis')

@section('content')
    <div class="flex-1 flex gap-4 overflow-hidden h-full min-h-[600px]"
         x-data="consultationWorkspace()">

        <!-- ========================================== -->
        <!-- MAIN WORKSPACE: CONSULTATION DATA          -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col gap-4 h-full overflow-hidden relative">

            <!-- Loading Overlay -->
            <div x-show="isLoading" x-cloak
                 class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center rounded-2xl">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                <p class="text-gray-600 font-bold animate-pulse">Loading Patient Data...</p>
            </div>

            <!-- Sticky Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100 shrink-0">
                        <i class="fas fa-user-injured text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 leading-tight"
                            x-text="currentVisit?.patient?.name || '{{ $visit->patient->name }}'"></h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded"
                                  x-text="currentVisit?.patient?.emrn || '{{ $visit->patient->emrn }}'"></span>
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded"
                                  x-text="currentVisit?.patient?.age_formatted || '{{ $visit->patient->age_formatted }}'"></span>
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded"
                                  x-text="currentVisit?.patient?.gender || '{{ $visit->patient->gender }}'"></span>
                            <template x-if="currentVisit?.patient?.is_nhmp || {{ $visit->patient->is_nhmp ? 'true' : 'false' }}">
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded flex items-center gap-1">
                                    <i class="fas fa-shield-alt text-[10px]"></i> NHMP
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <div class="hidden md:flex flex-col items-end">
                        <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Visit Time</div>
                        <div class="text-sm font-bold text-gray-700">
                            <span x-text="currentVisit?.created_at
                                ? new Date(currentVisit.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                                : '{{ $visit->created_at->format('h:i A') }}'"></span>
                        </div>
                    </div>
                    <div class="h-10 w-px bg-gray-100"></div>
                    <div class="flex items-center gap-2">
                        <button @click="openHistoryModal()"
                                class="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 font-bold rounded-xl hover:bg-gray-100 transition-colors border border-gray-200">
                            <i class="fas fa-history text-blue-500"></i>
                            <span class="hidden sm:inline">Medical History</span>
                        </button>

                        <div class="flex items-center gap-2">
                            <template x-if="(currentVisit?.status === 'in_progress') || ('{{ $visit->status }}' === 'in_progress')">
                                <button onclick="window.location.href='/doctor/consultancy'"
                                        class="flex items-center gap-2 px-4 py-2 bg-white text-gray-600 font-bold rounded-xl hover:bg-gray-50 transition-colors border border-gray-200 shadow-sm">
                                    <i class="fas fa-save text-gray-400"></i>
                                    <span class="hidden sm:inline">Save Draft</span>
                                </button>
                            </template>

                            <template x-if="['in_progress', 'completed'].includes(currentVisit?.status || '{{ $visit->status }}')">
                                <button @click="printCurrentPrescription()"
                                        class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 font-bold rounded-xl hover:bg-indigo-100 transition-colors border border-indigo-200 shadow-sm">
                                    <i class="fas fa-print text-indigo-500"></i>
                                    <span class="hidden sm:inline">Print Rx</span>
                                </button>
                            </template>

                            <template x-if="(currentVisit?.status === 'in_progress') || ('{{ $visit->status }}' === 'in_progress')">
                                <button @click="completeModalOpen = true"
                                        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-emerald-200 transition-all border-b-4 border-emerald-700 active:border-b-0 active:translate-y-1">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Complete & Print</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end sticky header --}}

            <!-- Main Workspace: Tabs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-100 bg-gray-50/50 p-1">
                    <button @click="activeTab = 'vitals'"
                            :class="activeTab === 'vitals' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent">
                        <i class="fas fa-heartbeat"></i> Vitals
                    </button>
                    <button @click="activeTab = 'notes'"
                            :class="activeTab === 'notes' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent">
                        <i class="fas fa-file-medical"></i> Notes & Diagnosis
                    </button>
                    <button @click="activeTab = 'prescriptions'"
                            :class="activeTab === 'prescriptions' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent relative">
                        <i class="fas fa-pills"></i> Prescriptions
                        <span x-show="prescriptionCount > 0"
                              x-text="prescriptionCount"
                              class="absolute top-2 right-4 w-5 h-5 bg-blue-600 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white">
                        </span>
                    </button>
                    <button @click="activeTab = 'labs'"
                            :class="activeTab === 'labs' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent">
                        <i class="fas fa-microscope"></i> Lab Orders
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="flex-1 overflow-y-auto p-6">

                    <!-- ===== Vitals Tab ===== -->
                    <div x-show="activeTab === 'vitals'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Current Vital Signs</h3>
                            <button @click="vitalsModalOpen = true"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                                <i class="fas fa-plus"></i> Record New Vitals
                            </button>
                        </div>

                        <!-- Static vitals from server on first load -->
                        <div x-show="!currentVisit || !currentVisit.latest_vital">
                            @if ($visit->latestVital)
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 text-xs">
                                    @php
                                        $temp = $visit->latestVital->temperature ?? null;
                                        $tempColor = 'gray';
                                        if ($temp) { $tempColor = ($temp > 37.5 || $temp < 36.0) ? 'red' : 'emerald'; }
                                        $sys = $visit->latestVital->blood_pressure_systolic ?? null;
                                        $dia = $visit->latestVital->blood_pressure_diastolic ?? null;
                                        $bpColor = 'gray';
                                        if ($sys && $dia) {
                                            if ($sys > 130 || $sys < 90 || $dia > 85 || $dia < 60) $bpColor = 'red';
                                            elseif ($sys >= 120 && $sys <= 129 && $dia < 80) $bpColor = 'amber';
                                            else $bpColor = 'emerald';
                                        }
                                        $pulse = $visit->latestVital->pulse ?? null;
                                        $pulseColor = 'gray';
                                        if ($pulse) { $pulseColor = ($pulse > 100 || $pulse < 60) ? 'amber' : 'emerald'; }
                                        $spo2 = $visit->latestVital->oxygen_saturation ?? null;
                                        $spo2Color = 'gray';
                                        if ($spo2) { $spo2Color = $spo2 < 95 ? 'amber' : 'indigo'; }
                                    @endphp
                                    <div class="bg-{{ $tempColor }}-50 p-2 rounded-xl text-center border border-{{ $tempColor }}-200">
                                        <span class="font-bold text-lg text-{{ $tempColor }}-700 flex items-center justify-center gap-1">{{ $temp ?? '--' }}°F <i class="fas fa-thermometer-half text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-{{ $tempColor }}-500 mt-1">Temp</div>
                                    </div>
                                    <div class="bg-{{ $bpColor }}-50 p-2 rounded-xl text-center border border-{{ $bpColor }}-200">
                                        <span class="font-bold text-lg text-{{ $bpColor }}-700 flex items-center justify-center gap-1">{{ $sys ?? '--' }}/{{ $dia ?? '--' }} <i class="fas fa-heart text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-{{ $bpColor }}-500 mt-1">BP</div>
                                    </div>
                                    <div class="bg-{{ $pulseColor }}-50 p-2 rounded-xl text-center border border-{{ $pulseColor }}-200">
                                        <span class="font-bold text-lg text-{{ $pulseColor }}-700 flex items-center justify-center gap-1">{{ $pulse ?? '--' }} <i class="fas fa-heartbeat text-sm opacity-60 animate-pulse"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-{{ $pulseColor }}-500 mt-1">Pulse</div>
                                    </div>
                                    <div class="bg-emerald-50 p-2 rounded-xl text-center border border-emerald-200">
                                        <span class="font-bold text-lg text-emerald-700 flex items-center justify-center gap-1">{{ $visit->latestVital->respiratory_rate ?? '--' }} <i class="fas fa-lungs text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 mt-1">Resp</div>
                                    </div>
                                    <div class="bg-{{ $spo2Color }}-50 p-2 rounded-xl text-center border border-{{ $spo2Color }}-200">
                                        <span class="font-bold text-lg text-{{ $spo2Color }}-700 flex items-center justify-center gap-1">{{ $spo2 ?? '--' }}% <i class="fas fa-wind text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-{{ $spo2Color }}-500 mt-1">SpO₂</div>
                                    </div>
                                    <div class="bg-purple-50 p-2 rounded-xl text-center border border-purple-200">
                                        <span class="font-bold text-lg text-purple-700 flex items-center justify-center gap-1">{{ $visit->latestVital->weight ?? '--' }}kg <i class="fas fa-weight text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-purple-500 mt-1">Weight</div>
                                    </div>
                                    <div class="bg-teal-50 p-2 rounded-xl text-center border border-teal-200">
                                        <span class="font-bold text-lg text-teal-700 flex items-center justify-center gap-1">{{ $visit->latestVital->bmi ?? '--' }} <i class="fas fa-ruler-vertical text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-teal-500 mt-1">BMI</div>
                                    </div>
                                    <div class="bg-orange-50 p-2 rounded-xl text-center border border-orange-200">
                                        <span class="font-bold text-lg text-orange-700 flex items-center justify-center gap-1">{{ $visit->latestVital->blood_glucose ?? '--' }} <i class="fas fa-tint text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-orange-500 mt-1">Glucose</div>
                                    </div>
                                    <div class="bg-slate-50 p-2 rounded-xl text-center border border-slate-200">
                                        <span class="font-bold text-lg text-slate-700 flex items-center justify-center gap-1">{{ $visit->latestVital->pain_scale ?? '0' }}/10 <i class="fas fa-face-grimace text-sm opacity-60"></i></span>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mt-1">Pain</div>
                                    </div>
                                </div>
                                @if($visit->latestVital->notes)
                                    <div class="mt-6 p-4 bg-gray-50 rounded-2xl border border-gray-100 text-sm text-gray-600 italic">
                                        <strong>Nurse's Notes:</strong> {{ $visit->latestVital->notes }}
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                    <div class="p-4 bg-white rounded-2xl shadow-sm mb-4">
                                        <i class="fas fa-heartbeat text-gray-300 text-4xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No vitals recorded yet for this visit.</p>
                                    <button @click="vitalsModalOpen = true" class="mt-4 text-blue-600 font-bold hover:underline">Record Now</button>
                                </div>
                            @endif
                        </div>

                        <!-- Dynamic vitals after AJAX load -->
                        <div x-show="currentVisit && currentVisit.latest_vital">
                            <template x-if="currentVisit && currentVisit.latest_vital">
                                <div class="mt-2 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 text-xs">
                                    <template x-for="[label, value, unit, color, icon] in vitalCards()" :key="label">
                                        <div :class="`bg-${color}-50 border-${color}-200`" class="p-2 rounded-xl text-center border">
                                            <span :class="`text-${color}-700`" class="font-bold text-lg flex items-center justify-center gap-1">
                                                <span x-text="value + (unit || '')"></span>
                                                <i :class="`fas ${icon} text-sm opacity-60`"></i>
                                            </span>
                                            <div :class="`text-${color}-500`" class="text-[10px] font-bold uppercase tracking-wider mt-1" x-text="label"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="currentVisit && !currentVisit.latest_vital">
                                <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                    <i class="fas fa-heartbeat text-gray-300 text-4xl mb-4"></i>
                                    <p class="text-gray-500 font-medium">No vitals recorded yet.</p>
                                    <button @click="vitalsModalOpen = true" class="mt-4 text-blue-600 font-bold hover:underline">Record Now</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ===== Notes & Diagnosis Tab ===== -->
                    <div x-show="activeTab === 'notes'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left: Input Form -->
                            <div class="lg:col-span-2 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Primary Diagnosis <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="diagnosis.text"
                                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium text-gray-900"
                                               placeholder="e.g. Acute Viral Infection">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Severity <span class="text-rose-500">*</span></label>
                                        <select x-model="diagnosis.severity"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-bold text-gray-900">
                                            <option value="mild">Mild</option>
                                            <option value="moderate">Moderate</option>
                                            <option value="severe">Severe</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Illness Tags</label>
                                    <select multiple x-model="diagnosis.illness_tag_ids"
                                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium text-gray-900 min-h-[100px]">
                                        @foreach($illnessTags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }} ({{ $tag->category }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple tags.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Refer To Medical Specialty</label>
                                        <select multiple x-model="diagnosis.medical_specialty_ids"
                                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium text-gray-900 min-h-[100px]">
                                            @foreach($medicalSpecialties as $specialty)
                                                <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">General Medical Advice / Lifestyle</label>
                                        <textarea x-model="diagnosis.medical_advice" rows="4"
                                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium text-gray-900"
                                                  placeholder="e.g. Bed rest, drink more water..."></textarea>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Clinical Notes & Observations</label>
                                    <textarea x-model="diagnosis.notes" rows="4"
                                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-medium text-gray-900"
                                              placeholder="Describe symptoms, duration, findings..."></textarea>
                                </div>

                                <div class="flex flex-wrap gap-4">
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition-all">
                                        <input type="checkbox" x-model="diagnosis.is_urgent" class="w-5 h-5 rounded text-rose-600 focus:ring-rose-500">
                                        <span class="text-sm font-bold text-gray-700 uppercase tracking-wide">Mark as Urgent</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition-all">
                                        <input type="checkbox" x-model="diagnosis.is_chronic" class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm font-bold text-gray-700 uppercase tracking-wide">Chronic Condition</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-100 transition-all">
                                        <input type="checkbox" x-model="diagnosis.has_prescription" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm font-bold text-gray-700 uppercase tracking-wide">Prescription Required</span>
                                    </label>
                                </div>

                                <button @click="saveDiagnosis()" :disabled="saving"
                                        class="w-full lg:w-auto px-8 py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center justify-center gap-3">
                                    <i class="fas fa-save" x-show="!saving"></i>
                                    <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                                    <span x-text="saving ? 'Saving...' : 'Save Clinical Record'"></span>
                                </button>
                            </div>

                            <!-- Right: Previous Diagnoses -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-history text-xs"></i> Session History
                                </h4>
                                <div class="space-y-3">
                                    <template x-if="currentVisit?.diagnoses && currentVisit.diagnoses.length > 0">
                                        <div>
                                            <template x-for="diag in currentVisit.diagnoses" :key="diag.id">
                                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 relative group transition-all hover:bg-white hover:shadow-md hover:border-blue-100 mb-3">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md"
                                                                      :class="{
                                                                          'bg-rose-100 text-rose-700': diag.severity === 'critical',
                                                                          'bg-orange-100 text-orange-700': diag.severity === 'severe',
                                                                          'bg-amber-100 text-amber-700': diag.severity === 'moderate',
                                                                          'bg-gray-200 text-gray-700': diag.severity === 'mild'
                                                                      }" x-text="diag.severity"></span>
                                                                <template x-if="diag.is_urgent">
                                                                    <span class="text-[10px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                                        <i class="fas fa-exclamation-triangle"></i> Urgent
                                                                    </span>
                                                                </template>
                                                                <template x-if="diag.is_chronic">
                                                                    <span class="text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                                        <i class="fas fa-sync"></i> Chronic
                                                                    </span>
                                                                </template>
                                                            </div>
                                                            <h4 class="font-bold text-gray-900 text-base" x-text="diag.diagnosis"></h4>
                                                        </div>
                                                    </div>
                                                    <template x-if="diag.doctor_notes">
                                                        <div class="mt-2 text-sm text-gray-600 bg-white p-3 rounded-xl border border-gray-100 font-medium" x-text="diag.doctor_notes"></div>
                                                    </template>
                                                    <div class="mt-3 flex items-center justify-between text-xs text-gray-400 font-bold border-t border-gray-100 pt-3">
                                                        <div class="flex items-center gap-1">
                                                            <i class="fas fa-clock text-gray-300"></i>
                                                            <span x-text="new Date(diag.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                                        </div>
                                                        <template x-if="diag.has_prescription">
                                                            <div class="flex items-center gap-1 text-blue-500">
                                                                <i class="fas fa-pills px-1"></i> Rx Required
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!currentVisit?.diagnoses || currentVisit.diagnoses.length === 0">
                                        <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">No diagnoses recorded</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== Prescriptions Tab ===== -->
                    <div x-show="activeTab === 'prescriptions'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Medicines & Prescriptions</h3>
                            <span id="prescriptionCount" class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"
                                  x-text="prescriptionCount + ' prescribed'"></span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Left: Add form -->
                            <div class="lg:col-span-1 bg-gray-50 rounded-2xl border border-gray-200 p-5">
                                <form id="addPrescriptionForm" class="space-y-4">
                                    <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                                    <input type="hidden" name="diagnosis_id" id="active_diagnosis_id" value="{{ $visit->diagnoses->last() ? $visit->diagnoses->last()->id : '' }}">
                                    <input type="hidden" name="patient_id" value="{{ $visit->patient_id }}">

                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Select Medicine</label>
                                        <select name="medicine_id" id="medicineSelect"
                                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-sm">
                                            <option value="">Choose medicine...</option>
                                            @foreach ($medicines as $medicine)
                                                <option value="{{ $medicine->id }}" data-stock="{{ $medicine->stock }}">
                                                    {{ $medicine->name }} ({{ $medicine->strength }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="medicineStock" class="mt-2 text-xs font-bold"></div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Abbreviation</label>
                                            <select id="abbrevSelect" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-blue-500" onchange="applyAbbreviation(this)">
                                                <option value="">Optional template...</option>
                                                @foreach ($prescriptionAbbreviations as $abbr)
                                                    <option value="{{ $abbr->id }}" 
                                                            data-days="{{ $abbr->default_days }}"
                                                            data-morning="{{ $abbr->morning }}"
                                                            data-evening="{{ $abbr->evening }}"
                                                            data-night="{{ $abbr->night }}">
                                                        {{ $abbr->abbreviation }} ({{ $abbr->meaning }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Dosage / Strength</label>
                                            <input type="text" name="dosage" id="prescrDosage"
                                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold"
                                                placeholder="e.g. 500mg, 1 tablet">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Schedule <span class="text-gray-400 font-normal">(tablets per dose)</span></label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-black text-blue-500 uppercase mb-1.5">Morning</label>
                                                <input type="number" name="morning" id="prescrMorning" min="0" value="0"
                                                       class="w-full px-3 py-2.5 bg-white border border-blue-200 rounded-xl text-sm font-bold text-center"
                                                       oninput="updatePrescQty()">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-amber-500 uppercase mb-1.5">Evening</label>
                                                <input type="number" name="evening" id="prescrEvening" min="0" value="0"
                                                       class="w-full px-3 py-2.5 bg-white border border-amber-200 rounded-xl text-sm font-bold text-center"
                                                       oninput="updatePrescQty()">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-indigo-500 uppercase mb-1.5">Night</label>
                                                <input type="number" name="night" id="prescrNight" min="0" value="0"
                                                       class="w-full px-3 py-2.5 bg-white border border-indigo-200 rounded-xl text-sm font-bold text-center"
                                                       oninput="updatePrescQty()">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Days</label>
                                            <input type="number" name="days" id="prescrDays" min="1"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold"
                                                   placeholder="e.g. 5" oninput="updatePrescQty()">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Qty. <span class="text-blue-400 font-normal">(auto)</span></label>
                                            <input type="number" name="quantity" id="prescrQty"
                                                   class="w-full px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl text-sm font-bold text-blue-700"
                                                   value="0" readonly>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-gray-400 uppercase mb-2">Clinical Instructions</label>
                                        <textarea name="instructions" rows="2"
                                                  class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium"
                                                  placeholder="Take after food, avoid cold water etc."></textarea>
                                    </div>

                                    <button type="submit"
                                            class="w-full py-4 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                                        <i class="fas fa-plus"></i> Add to Prescription
                                    </button>
                                </form>
                            </div>

                            <!-- Right: Prescriptions List -->
                            <div class="lg:col-span-2">
                                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-1 min-h-[300px]">
                                    <div id="prescriptionsList" class="divide-y divide-gray-100">
                                        <template x-if="currentVisit?.diagnoses && currentVisit.diagnoses.some(d => d.prescriptions?.length > 0)">
                                            <div>
                                                <template x-for="diag in currentVisit.diagnoses" :key="diag.id">
                                                    <template x-if="diag.prescriptions && diag.prescriptions.length > 0">
                                                        <template x-for="pres in diag.prescriptions" :key="pres.id">
                                                            <div class="p-4 hover:bg-white transition-colors group">
                                                                <div class="flex items-start justify-between">
                                                                    <div class="flex items-start gap-4">
                                                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                                                                            <i class="fas fa-pills"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="flex items-center gap-2 mb-1">
                                                                                <h4 class="font-bold text-gray-900" x-text="pres.medicine?.name || 'Unknown Medicine'"></h4>
                                                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200"
                                                                                      x-text="pres.medicine?.strength || ''"></span>
                                                                            </div>
                                                                            <div class="flex items-center gap-3 text-sm font-medium text-gray-600 mt-2">
                                                                                <div class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                                                                    <i class="fas fa-clock text-blue-400 text-xs"></i>
                                                                                    <span x-text="(pres.morning || 0) + '-' + (pres.evening || 0) + '-' + (pres.night || 0)"></span>
                                                                                </div>
                                                                                <div class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                                                                    <i class="fas fa-calendar-alt text-emerald-400 text-xs"></i>
                                                                                    <span x-text="pres.days + ' days'"></span>
                                                                                </div>
                                                                                <div class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                                                                    <i class="fas fa-box text-amber-400 text-xs"></i>
                                                                                    <span x-text="'Qty: ' + pres.quantity"></span>
                                                                                </div>
                                                                            </div>
                                                                            <template x-if="pres.instructions">
                                                                                <div class="mt-3 text-sm text-gray-500 bg-amber-50/50 p-2.5 rounded-xl border border-amber-100 flex items-start gap-2">
                                                                                    <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                                                                                    <span class="italic" x-text="pres.instructions"></span>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!currentVisit?.diagnoses || !currentVisit.diagnoses.some(d => d.prescriptions?.length > 0)">
                                            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                                                <i class="fas fa-prescription text-4xl mb-3 opacity-20"></i>
                                                <p class="text-xs font-black uppercase tracking-widest">No medications prescribed</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== Lab Orders Tab ===== -->
                    <div x-show="activeTab === 'labs'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Lab Investigations</h3>
                            <button @click="labModalOpen = true"
                                    class="flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200">
                                <i class="fas fa-flask"></i> New Lab Order
                            </button>
                        </div>

                        <!-- Lab orders list — iterates order.items which each have lab_test_type -->
                        <div>
                            <template x-if="currentVisit?.labOrders && currentVisit.labOrders.length > 0">
                                <div class="space-y-4">
                                    <template x-for="order in currentVisit.labOrders" :key="order.id">
                                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                                            <!-- Order header -->
                                            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/60">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                                                        <i class="fas fa-flask text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest"
                                                              x-text="'Lab #' + (order.lab_number || order.id)"></span>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <!-- Priority badge -->
                                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full"
                                                                  :class="order.priority === 'urgent'
                                                                      ? 'bg-red-100 text-red-700'
                                                                      : 'bg-blue-100 text-blue-600'"
                                                                  x-text="order.priority || 'Normal'"></span>
                                                            <!-- Status badge -->
                                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full border"
                                                                  :class="{
                                                                      'bg-amber-50 text-amber-700 border-amber-200': order.status === 'pending',
                                                                      'bg-blue-50 text-blue-700 border-blue-200': order.status === 'in_progress',
                                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': order.status === 'completed',
                                                                      'bg-gray-50 text-gray-500 border-gray-200': order.status === 'cancelled'
                                                                  }"
                                                                  x-text="(order.status || 'pending').replace('_', ' ')"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-gray-400"
                                                          x-text="order.created_at ? new Date(order.created_at).toLocaleDateString() : ''"></span>
                                                    <a :href="'/doctor/lab-orders/' + order.id" target="_blank"
                                                       class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 hover:bg-purple-100 transition-colors"
                                                       title="View full order">
                                                        <i class="fas fa-external-link-alt text-xs"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Test items grid -->
                                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                <template x-if="!order.items || order.items.length === 0">
                                                    <div class="col-span-3 text-center text-sm text-gray-400 py-4">No test items found.</div>
                                                </template>
                                                <template x-for="item in (order.items || [])" :key="item.id">
                                                    <div class="flex items-center gap-3 p-3 rounded-xl border"
                                                         :class="{
                                                             'bg-emerald-50 border-emerald-200': item.status === 'completed',
                                                             'bg-blue-50 border-blue-200': item.status === 'in_progress',
                                                             'bg-gray-50 border-gray-200': !item.status || item.status === 'pending',
                                                             'bg-red-50 border-red-200': item.status === 'cancelled'
                                                         }">
                                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                                                             :class="{
                                                                 'bg-emerald-100 text-emerald-600': item.status === 'completed',
                                                                 'bg-blue-100 text-blue-600': item.status === 'in_progress',
                                                                 'bg-gray-100 text-gray-500': !item.status || item.status === 'pending',
                                                             }">
                                                            <i class="fas text-xs"
                                                               :class="item.status === 'completed' ? 'fa-check-circle' : 'fa-vial'"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-bold text-gray-900 truncate"
                                                               x-text="item.lab_test_type?.name || 'Unknown Test'"></p>
                                                            <p class="text-[10px] font-bold uppercase tracking-wider mt-0.5"
                                                               :class="{
                                                                   'text-emerald-600': item.status === 'completed',
                                                                   'text-blue-500': item.status === 'in_progress',
                                                                   'text-gray-400': !item.status || item.status === 'pending'
                                                               }"
                                                               x-text="(item.status || 'Pending').replace('_', ' ')"></p>
                                                        </div>
                                                        <!-- View result if completed -->
                                                        <template x-if="item.status === 'completed'">
                                                            <button @click="openLabResultModal(order)"
                                                                    class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center hover:bg-emerald-200 transition-colors"
                                                                    title="View result">
                                                                <i class="fas fa-eye text-xs"></i>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Notes if any -->
                                            <template x-if="order.comments">
                                                <div class="px-5 pb-3">
                                                    <p class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 italic"
                                                       x-text="'Note: ' + order.comments"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!currentVisit?.labOrders || currentVisit.labOrders.length === 0">
                                <div class="flex flex-col items-center justify-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                                        <i class="fas fa-microscope text-gray-300 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-bold mb-1">No lab orders placed yet.</p>
                                    <p class="text-gray-400 text-sm mb-4">Order diagnostic tests for this patient</p>
                                    <button @click="labModalOpen = true"
                                            class="px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-flask mr-2"></i>Order Diagnostic Tests
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
            {{-- End Main Workspace Tabs --}}
        </div>
        {{-- End main content col --}}

        <!-- ========================================== -->
        <!-- RIGHT SIDEBAR: COMBINED TABBED QUEUE       -->
        <!-- ========================================== -->
        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-4 overflow-hidden h-full">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex flex-col overflow-hidden flex-1"
                 x-data="{ queueTab: 'waiting' }">
                <!-- Tab Headers -->
                <div class="flex border-b border-gray-100 bg-gray-50/50 p-1 flex-shrink-0">
                    <button @click="queueTab = 'waiting'"
                            :class="queueTab === 'waiting' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent">
                        <i class="fas fa-users"></i>
                        Waiting
                        <span class="ml-1 bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $waitingQueue->count() }}</span>
                    </button>
                    <button @click="queueTab = 'in_progress'"
                            :class="queueTab === 'in_progress' ? 'bg-white text-blue-600 shadow-sm border-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'"
                            class="flex-1 py-3 text-sm font-bold rounded-xl transition-all flex items-center justify-center gap-2 border border-transparent">
                        <i class="fas fa-user-clock"></i>
                        In Progress
                        <span class="ml-1 bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">{{ $inProgressQueue->count() }}</span>
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="flex-1 overflow-y-auto p-3 space-y-2">

                    <!-- Waiting Panel -->
                    <div x-show="queueTab === 'waiting'"
                         x-transition:enter="transition ease-out duration-200">
                        @forelse($waitingQueue as $queueVisit)
                            <button @click="startConsultation({{ $queueVisit->id }})"
                                    class="w-full text-left p-4 rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all bg-white mb-2 group">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                                        {{ strtoupper(substr($queueVisit->patient->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-900 truncate">{{ $queueVisit->patient->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span>{{ $queueVisit->patient->emrn }}</span>
                                            <span>•</span>
                                            <span>{{ $queueVisit->created_at->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                        Start →
                                    </span>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-10 text-gray-400 text-sm">
                                <i class="fas fa-user-check text-3xl mb-2 opacity-50 block"></i>
                                <p>No patients waiting</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- In Progress Panel -->
                    <div x-show="queueTab === 'in_progress'"
                         x-transition:enter="transition ease-out duration-200">
                        @forelse($inProgressQueue as $queueVisit)
                            <button @click="loadPatient({{ $queueVisit->id }})"
                                    class="w-full text-left p-4 rounded-xl border border-gray-200 hover:border-amber-300 hover:shadow-md transition-all bg-white mb-2 group"
                                    :class="{ 'ring-2 ring-blue-500 border-blue-500': activeVisitId === {{ $queueVisit->id }} }">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold flex-shrink-0">
                                        {{ strtoupper(substr($queueVisit->patient->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-gray-900 truncate">{{ $queueVisit->patient->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span>{{ $queueVisit->patient->emrn }}</span>
                                            <span>•</span>
                                            <span>{{ $queueVisit->created_at->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                        Resume →
                                    </span>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-10 text-gray-400 text-sm">
                                <i class="fas fa-spinner text-3xl mb-2 opacity-50 block"></i>
                                <p>No active consultations</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
        {{-- End RIGHT SIDEBAR --}}

    {{-- End outer flex row columns - x-data wrapper (consultationWorkspace) stays open until after modals --}}

    <!-- ========================================== -->
    <!-- MODALS (inside x-data scope)               -->
    <!-- ========================================== -->

    <!-- Vitals Modal -->
    <div x-show="vitalsModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="vitalsModalOpen = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden border border-gray-100"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-blue-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 leading-tight">Record Vital Signs</h3>
                        <p class="text-xs font-bold text-blue-600 uppercase tracking-widest">Session #{{ $visit->consultancy_no ?? $visit->id }}</p>
                    </div>
                </div>
                <button @click="vitalsModalOpen = false" class="w-8 h-8 rounded-full hover:bg-white flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>

            <form id="vitalsForm" @submit.prevent="submitVitals" class="p-6 space-y-6">
                <input type="hidden" name="visit_id" value="{{ $visit->id }}">
                <input type="hidden" name="patient_id" value="{{ $visit->patient_id }}">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Core Vitals -->
                    <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-200">
                        <h4 class="col-span-2 text-xs font-black text-gray-500 uppercase tracking-widest border-b pb-2">Core Vitals</h4>
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Temp (°F)</label>
                                <input type="number" step="0.1" name="temperature" min="95" max="110"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl font-bold focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="36.5">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Pulse (BPM)</label>
                                <input type="number" name="pulse" min="20" max="300"
                                       class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl font-bold" placeholder="72">
                            </div>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">BP Sys (mmHg)</label>
                                <input type="number" name="blood_pressure_systolic" min="40" max="300"
                                       placeholder="120" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">BP Dia (mmHg)</label>
                                <input type="number" name="blood_pressure_diastolic" min="30" max="200"
                                       placeholder="80" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl font-bold">
                            </div>
                        </div>
                    </div>

                    <!-- Respiratory -->
                    <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                        <h4 class="col-span-2 text-xs font-black text-blue-500 uppercase tracking-widest border-b border-blue-100 pb-2">Respiratory</h4>
                        <div>
                            <label class="block text-xs font-black text-blue-400 uppercase tracking-widest mb-1">Resp. Rate (RPM)</label>
                            <input type="number" name="respiratory_rate" min="5" max="100"
                                   class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-blue-400 uppercase tracking-widest mb-1">SpO₂ (%)</label>
                            <input type="number" step="0.1" name="oxygen_saturation" min="0" max="100"
                                   class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-blue-400 uppercase tracking-widest mb-1">O₂ Device</label>
                            <input type="text" name="oxygen_device" placeholder="e.g. Nasal Cannula"
                                   class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-medium text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-blue-400 uppercase tracking-widest mb-1">O₂ Flow (L/min)</label>
                            <input type="number" step="0.1" name="oxygen_flow_rate" min="0" max="15"
                                   class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl font-bold">
                        </div>
                    </div>

                    <!-- Anthropometrics -->
                    <div class="md:col-span-2 grid grid-cols-3 gap-4 bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                        <h4 class="col-span-3 text-xs font-black text-emerald-500 uppercase tracking-widest border-b border-emerald-100 pb-2">Anthropometrics</h4>
                        <div>
                            <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest mb-1">Height (cm)</label>
                            <input type="number" step="0.1" x-model="vitalsForm.height" name="height" min="30" max="250"
                                   class="w-full px-3 py-2 bg-white border border-emerald-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest mb-1">Weight (kg)</label>
                            <input type="number" step="0.1" x-model="vitalsForm.weight" name="weight" min="1" max="300"
                                   class="w-full px-3 py-2 bg-white border border-emerald-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-emerald-400 uppercase tracking-widest mb-1">BMI</label>
                            <input type="number" step="0.1" :value="calculateBMI()" readonly name="bmi"
                                   class="w-full px-3 py-2 bg-emerald-100/50 border border-emerald-200 rounded-xl font-bold text-emerald-700 pointer-events-none">
                        </div>
                    </div>

                    <!-- Other Clinical -->
                    <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-amber-50/50 p-4 rounded-2xl border border-amber-100">
                        <h4 class="col-span-2 text-xs font-black text-amber-500 uppercase tracking-widest border-b border-amber-100 pb-2">Other Clinical</h4>
                        <div>
                            <label class="block text-xs font-black text-amber-400 uppercase tracking-widest mb-1">Pain Scale (0-10)</label>
                            <input type="number" min="0" max="10" name="pain_scale"
                                   class="w-full px-3 py-2 bg-white border border-amber-200 rounded-xl font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-amber-400 uppercase tracking-widest mb-1">Blood Glucose</label>
                            <input type="number" step="0.1" name="blood_glucose"
                                   class="w-full px-3 py-2 bg-white border border-amber-200 rounded-xl font-bold">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-span-1 md:col-span-4 mt-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Observation Notes</label>
                        <textarea name="notes" rows="2"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-medium focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                    </div>
                </div>

                <button type="submit" :disabled="saving"
                        class="w-full py-4 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                    <i class="fas fa-check-circle" x-show="!saving"></i>
                    <span x-text="saving ? 'Saving...' : 'Save Vital Signs'"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Lab Order Modal -->
    <div x-show="labModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="labModalOpen = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-purple-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 leading-tight">Diagnostic Investigation Request</h3>
                        <p class="text-xs font-bold text-purple-600 uppercase tracking-widest">Patient: {{ $visit->patient->name }}</p>
                    </div>
                </div>
                <button @click="labModalOpen = false" class="w-8 h-8 rounded-full hover:bg-white flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Available Tests</h4>
                        <div class="relative mb-4">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" x-model="labSearch"
                                   class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-100 transition-all font-medium"
                                   placeholder="Search tests (e.g. CBC, Lipid Profile)...">
                        </div>
                        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                            @foreach($labTestTypes as $test)
                                <div x-show="!labSearch || '{{ strtolower($test->name) }}'.includes(labSearch.toLowerCase())">
                                    <button @click="toggleLabTest({{ $test->id }}, '{{ addslashes($test->name) }}', {{ $test->price ?? 0 }})"
                                            :class="selectedTests.find(t => t.id === {{ $test->id }}) ? 'border-purple-500 bg-purple-50' : 'border-gray-100 bg-white hover:border-purple-200'"
                                            class="w-full p-4 border rounded-2xl flex items-center justify-between group transition-all">
                                        <div class="text-left">
                                            <div class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">{{ $test->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $test->category ?? 'General' }}</div>
                                        </div>
                                        <i :class="selectedTests.find(t => t.id === {{ $test->id }}) ? 'fas fa-check-circle text-purple-500' : 'fas fa-plus-circle text-gray-200 group-hover:text-purple-300'"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-6 border border-gray-200 flex flex-col">
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Selected Investigations</h4>
                        <div class="flex-1 space-y-3 mb-6">
                            <template x-if="selectedTests.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-gray-400 py-10">
                                    <i class="fas fa-flask text-3xl mb-3 opacity-20"></i>
                                    <p class="text-[10px] font-bold uppercase tracking-widest">No tests selected</p>
                                </div>
                            </template>
                            <template x-for="test in selectedTests" :key="test.id">
                                <div class="bg-white p-3 rounded-xl border border-gray-100 flex items-center justify-between shadow-sm">
                                    <div class="font-bold text-sm text-gray-800" x-text="test.name"></div>
                                    <button @click="toggleLabTest(test.id)" class="text-rose-500 hover:bg-rose-50 w-6 h-6 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="pt-4 border-t border-gray-200 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Priority</label>
                                    <select x-model="labPriority" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold">
                                        <option value="normal">Normal</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Clinical Indication</label>
                                    <textarea x-model="labNotes" rows="1" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium" placeholder="E.g. Persistent fever..."></textarea>
                                </div>
                            </div>
                            <button @click="submitLabOrder()" :disabled="selectedTests.length === 0 || savingLabs"
                                    class="w-full py-4 bg-purple-600 text-white font-black rounded-xl hover:bg-purple-700 shadow-xl shadow-purple-200 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane" x-show="!savingLabs"></i>
                                <i class="fas fa-spinner fa-spin" x-show="savingLabs"></i>
                                <span x-text="savingLabs ? 'Placing Order...' : 'Place Lab Order'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Modal -->
    <div x-show="historyModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="historyModalOpen = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col border border-gray-100"
             x-transition:enter="transition ease-out duration-300">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-600">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 leading-tight">Electronic Medical Record</h3>
                        <p class="text-sm font-bold text-gray-500">{{ $visit->patient->name }} | Age: {{ $visit->patient->age }} | Sex: {{ ucfirst($visit->patient->gender) }}</p>
                    </div>
                </div>
                <button @click="historyModalOpen = false" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-gray-400"></i>
                </button>
            </div>

            <div class="flex-1 overflow-hidden flex">
                <div class="w-64 border-r border-gray-100 p-4 space-y-1">
                    <button @click="activeHistoryTab = 'visits'" :class="activeHistoryTab === 'visits' ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full text-left p-4 rounded-xl font-bold flex items-center justify-between transition-colors">
                        Past Visits <i class="fas fa-chevron-right text-xs" x-show="activeHistoryTab === 'visits'"></i>
                    </button>
                    <button @click="activeHistoryTab = 'medications'" :class="activeHistoryTab === 'medications' ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full text-left p-4 rounded-xl font-bold flex items-center justify-between transition-colors">
                        Medications <i class="fas fa-chevron-right text-xs" x-show="activeHistoryTab === 'medications'"></i>
                    </button>
                    <button @click="activeHistoryTab = 'labs'" :class="activeHistoryTab === 'labs' ? 'bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full text-left p-4 rounded-xl font-bold flex items-center justify-between transition-colors">
                        Lab Results <i class="fas fa-chevron-right text-xs" x-show="activeHistoryTab === 'labs'"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-8 bg-gray-50/50">
                    <template x-if="isLoadingHistory">
                        <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                            <i class="fas fa-spinner fa-spin text-3xl mb-4"></i>
                            <p class="text-sm font-bold uppercase tracking-widest">Loading history...</p>
                        </div>
                    </template>

                    <template x-if="!isLoadingHistory">
                        <div>
                            <div x-show="activeHistoryTab === 'visits'" class="space-y-4">
                                <h4 class="text-lg font-black text-gray-900 mb-4">Consultation History</h4>
                                <template x-if="!historyData.visits || historyData.visits.length === 0">
                                    <div class="text-center py-10 text-gray-400 font-medium">No previous visits found.</div>
                                </template>
                                <template x-for="pastVisit in historyData.visits" :key="pastVisit.id">
                                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm mb-4 cursor-pointer hover:border-blue-200 transition-colors"
                                         @click="loadPatient(pastVisit.id); historyModalOpen = false;">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-gray-900" x-text="'Visit #' + pastVisit.consultancy_no"></div>
                                            <div class="text-xs font-bold text-gray-400" x-text="new Date(pastVisit.created_at).toLocaleDateString()"></div>
                                        </div>
                                        <template x-if="pastVisit.diagnoses && pastVisit.diagnoses.length > 0">
                                            <div>
                                                <div class="text-sm text-gray-600 mt-2">
                                                    <strong>Diagnosis:</strong> <span x-text="pastVisit.diagnoses[0].diagnosis"></span>
                                                </div>
                                                <template x-if="pastVisit.diagnoses[0].medical_advice">
                                                    <div class="text-sm text-gray-600 mt-1">
                                                        <strong>Medical Advice:</strong> <span x-text="pastVisit.diagnoses[0].medical_advice"></span>
                                                    </div>
                                                </template>
                                                <template x-if="pastVisit.diagnoses[0].illness_tags && pastVisit.diagnoses[0].illness_tags.length > 0">
                                                    <div class="mt-2 flex flex-wrap gap-1">
                                                        <template x-for="tag in pastVisit.diagnoses[0].illness_tags" :key="tag.id">
                                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded border bg-gray-100 text-gray-600 border-gray-200"
                                                                  x-text="tag.name"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="pastVisit.diagnoses[0].external_specialists && pastVisit.diagnoses[0].external_specialists.length > 0">
                                                    <div class="mt-2">
                                                        <strong class="text-xs text-gray-500 uppercase tracking-widest">Referred To:</strong>
                                                        <div class="flex flex-col gap-1 mt-1">
                                                            <template x-for="spec in pastVisit.diagnoses[0].external_specialists" :key="spec.id">
                                                                <span class="text-xs text-gray-700 font-medium"><i class="fas fa-stethoscope text-gray-400 mr-1"></i> <span x-text="spec.name"></span> (<span x-text="spec.specialty"></span>)</span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div x-show="activeHistoryTab === 'medications'" class="space-y-4">
                                <h4 class="text-lg font-black text-gray-900 mb-4">Prescription History</h4>
                                <template x-if="!historyData.prescriptions || historyData.prescriptions.length === 0">
                                    <div class="text-center py-10 text-gray-400 font-medium">No previous prescriptions found.</div>
                                </template>
                                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                                    <div class="divide-y divide-gray-100">
                                        <template x-for="pres in historyData.prescriptions" :key="pres.id">
                                            <div class="p-4 flex flex-col">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <div class="font-bold text-gray-900 mb-1" x-text="pres.medicine?.name"></div>
                                                        <div class="text-xs text-gray-500" x-text="(pres.dosage || '') + ' for ' + (pres.days || '') + ' days'"></div>
                                                    </div>
                                                    <div class="text-xs font-bold px-2 py-1 bg-gray-50 text-gray-500 rounded-lg border border-gray-200"
                                                         x-text="new Date(pres.created_at).toLocaleDateString()"></div>
                                                </div>
                                                <template x-if="pres.dispensations && pres.dispensations.length > 0">
                                                    <div class="mt-3 pl-3 border-l-2 border-emerald-200 space-y-1">
                                                        <template x-for="disp in pres.dispensations" :key="disp.id">
                                                            <div class="text-xs">
                                                                <span class="text-emerald-600 font-bold"><i class="fas fa-check-circle"></i> Dispensed</span>
                                                                <span class="text-gray-500 font-medium ml-1" x-text="disp.dispensed_quantity + ' units'"></span>
                                                                <template x-if="disp.is_alternative && disp.alternative_medicine">
                                                                    <span class="text-amber-600 font-bold ml-2">
                                                                        <i class="fas fa-exchange-alt"></i> Substituted with <span x-text="disp.alternative_medicine.name"></span>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeHistoryTab === 'labs'" class="space-y-4">
                                <h4 class="text-lg font-black text-gray-900 mb-4">Laboratory Records</h4>
                                <template x-if="!historyData.labs || historyData.labs.length === 0">
                                    <div class="text-center py-10 text-gray-400 font-medium">No previous lab results found.</div>
                                </template>
                                <div class="grid grid-cols-1 gap-4">
                                    <template x-for="lab in historyData.labs" :key="lab.id">
                                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center">
                                            <div>
                                                <div class="font-bold text-gray-900" x-text="lab.items?.[0]?.lab_test_type?.name || 'Lab Test'"></div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Status: <span class="capitalize font-medium"
                                                                  :class="{
                                                                      'text-emerald-600': lab.status === 'completed',
                                                                      'text-amber-600': lab.status === 'in_progress',
                                                                      'text-gray-500': !lab.status || lab.status === 'pending'
                                                                  }"
                                                                  x-text="lab.status ? lab.status.replace('_', ' ') : 'pending'"></span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-bold text-gray-400 mb-2" x-text="new Date(lab.created_at).toLocaleDateString()"></div>
                                                <button @click="openLabResultModal(lab)"
                                                        class="text-xs font-bold text-purple-600 hover:text-purple-700 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100 transition-colors">
                                                    View Result
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Consultation Confirmation Modal -->
    <div x-show="completeModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="completeModalOpen = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 text-center border border-gray-100"
             x-transition:enter="transition ease-out duration-300">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Finalize Consultation?</h3>
            <p class="text-gray-500 font-medium mb-8">This will mark the patient's visit as complete and generate the final prescription.</p>
            <div class="flex flex-col gap-3">
                <button @click="finalizeVisit()" :disabled="saving"
                        class="w-full py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                    <span x-text="saving ? 'Finalizing...' : 'Yes, Complete Visit'"></span>
                </button>
                <button @click="completeModalOpen = false" class="w-full py-4 bg-gray-100 text-gray-600 font-black rounded-2xl hover:bg-gray-200 transition-all">
                    Not Yet, Keep Editing
                </button>
            </div>
        </div>
    </div>

    <!-- Lab Result Detail Modal -->
    <div x-show="labResultModalOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="labResultModalOpen = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div>
                    <h3 class="text-lg font-black text-gray-900" x-text="selectedLab?.items?.[0]?.lab_test_type?.name || 'Lab Test Result'"></h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="'Lab No: ' + (selectedLab?.lab_number || 'N/A')"></p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold capitalize"
                          :class="{
                              'bg-emerald-100 text-emerald-700': selectedLab?.status === 'completed',
                              'bg-amber-100 text-amber-700': selectedLab?.status === 'in_progress',
                              'bg-gray-100 text-gray-500': !selectedLab?.status || selectedLab?.status === 'pending'
                          }"
                          x-text="selectedLab?.status ? selectedLab.status.replace('_', ' ') : 'Pending'"></span>
                    <button @click="labResultModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                        <i class="fas fa-times text-gray-500 text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <template x-if="selectedLab?.status !== 'completed'">
                    <div class="text-center py-10">
                        <i class="fas fa-flask text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">Results not yet available.</p>
                    </div>
                </template>
                <template x-if="selectedLab?.status === 'completed'">
                    <div class="space-y-4">
                        <template x-if="!selectedLab?.items || selectedLab.items.length === 0">
                            <div class="text-center py-6 text-gray-400">No result details recorded.</div>
                        </template>
                        <template x-for="item in (selectedLab?.items || [])" :key="item.id">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-gray-900 text-sm" x-text="item.lab_test_type?.name || 'Test'"></div>
                                </div>
                                <template x-if="item.lab_results && item.lab_results.length > 0">
                                    <div class="space-y-1 mt-2">
                                        <template x-for="result in item.lab_results" :key="result.id">
                                            <div class="flex justify-between text-xs text-gray-600">
                                                <span x-text="result.parameter_name || 'Result'"></span>
                                                <span class="font-bold" x-text="result.value + ' ' + (result.unit || '')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="p-4 border-t border-gray-100 flex justify-between items-center">
                <a :href="'/doctor/lab-orders/' + selectedLab?.id" target="_blank"
                   class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <i class="fas fa-external-link-alt"></i> Full Report
                </a>
                <button @click="labResultModalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-6 left-6 z-[200] flex flex-col gap-2 pointer-events-none"></div>

    </div>
    {{-- End x-data="consultationWorkspace()" wrapper --}}

    @push('scripts')
    <script>
        // ============================================
        // Global Notification Helper
        // ============================================
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-emerald-600',
                error: 'bg-rose-600',
                warning: 'bg-amber-500',
                info: 'bg-blue-600',
            };
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle',
            };
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl text-white font-bold text-sm max-w-sm ${colors[type] || colors.info} transform transition-all duration-300 -translate-y-4 opacity-0`;
            toast.innerHTML = `<i class="fas ${icons[type] || icons.info} mt-0.5 flex-shrink-0"></i><span>${message}</span>`;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('-translate-y-4', 'opacity-0');
            });
            setTimeout(() => {
                toast.classList.add('opacity-0', '-translate-y-4');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ============================================
        // Main Alpine Component
        // ============================================
        function consultationWorkspace() {
            return {
                activeVisitId: {{ $visit->id ?? 'null' }},
                currentVisit: null,
                isLoading: false,
                saving: false,
                activeTab: 'notes',
                vitalsModalOpen: false,
                labModalOpen: false,
                historyModalOpen: false,
                completeModalOpen: false,
                labResultModalOpen: false,
                selectedLab: null,
                savingLabs: false,
                isLoadingHistory: false,
                activeHistoryTab: 'visits',
                prescriptionCount: {{ $visit->prescriptions->count() }},

                historyData: { visits: [], prescriptions: [], labs: [] },
                vitalsForm: { height: '', weight: '' },

                labSearch: '',
                labNotes: '',
                labPriority: 'normal',
                selectedTests: [],

                diagnosis: {
                    text: '',
                    notes: '',
                    severity: 'moderate',
                    is_urgent: false,
                    is_chronic: false,
                    has_prescription: true,
                    illness_tag_ids: [],
                    medical_specialty_ids: [],
                    medical_advice: ''
                },

                calculateBMI() {
                    const h = parseFloat(this.vitalsForm.height) / 100;
                    const w = parseFloat(this.vitalsForm.weight);
                    if (h > 0 && w > 0) return (w / (h * h)).toFixed(1);
                    return '';
                },

                vitalCards() {
                    const v = this.currentVisit?.latest_vital;
                    if (!v) return [];
                    const cards = [];
                    // [label, value, unit, color, icon]
                    if (v.temperature != null) cards.push(['Temp', v.temperature, '°F', (v.temperature > 99.5 || v.temperature < 96.8) ? 'red' : 'emerald', 'fa-thermometer-half']);
                    if (v.blood_pressure_systolic != null) cards.push(['BP', `${v.blood_pressure_systolic}/${v.blood_pressure_diastolic}`, '', (v.blood_pressure_systolic > 130 || v.blood_pressure_systolic < 90) ? 'red' : 'emerald', 'fa-heart']);
                    if (v.pulse != null) cards.push(['Pulse', v.pulse, ' bpm', (v.pulse > 100 || v.pulse < 60) ? 'amber' : 'emerald', 'fa-heartbeat']);
                    if (v.respiratory_rate != null) cards.push(['Resp', v.respiratory_rate, '/min', 'emerald', 'fa-lungs']);
                    if (v.oxygen_saturation != null) cards.push(['SpO₂', v.oxygen_saturation, '%', v.oxygen_saturation < 95 ? 'amber' : 'indigo', 'fa-wind']);
                    if (v.weight != null) cards.push(['Weight', v.weight, 'kg', 'purple', 'fa-weight']);
                    if (v.bmi != null) cards.push(['BMI', v.bmi, '', 'teal', 'fa-ruler-vertical']);
                    if (v.blood_glucose != null) cards.push(['Glucose', v.blood_glucose, ' mg/dL', 'orange', 'fa-tint']);
                    if (v.pain_scale != null) cards.push(['Pain', `${v.pain_scale}/10`, '', 'slate', 'fa-face-grimace']);
                    return cards;
                },

                init() {
                    if (this.activeVisitId) {
                        this.loadPatient(this.activeVisitId);
                    }
                    // Medicine stock display
                    const medSelect = document.getElementById('medicineSelect');
                    if (medSelect) {
                        medSelect.addEventListener('change', function() {
                            const opt = this.options[this.selectedIndex];
                            const stock = opt?.dataset?.stock;
                            const el = document.getElementById('medicineStock');
                            if (el && stock !== undefined) {
                                el.textContent = `Stock: ${stock} units`;
                                el.className = `mt-2 text-xs font-bold ${parseInt(stock) > 0 ? 'text-emerald-600' : 'text-rose-500'}`;
                            } else if (el) {
                                el.textContent = '';
                            }
                        });
                    }
                },

                async startConsultation(visitId) {
                    try {
                        const response = await fetch(`/doctor/consultancy/${visitId}/start`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            showNotification('Consultation started', 'success');
                            window.location.href = `/doctor/consultancy/${visitId}`;
                        } else {
                            showNotification(data.message || 'Failed to start consultation', 'error');
                        }
                    } catch (e) {
                        showNotification('Network error starting consultation', 'error');
                    }
                },

                async openHistoryModal() {
                    this.historyModalOpen = true;
                    const patientId = this.currentVisit?.patient_id || {{ $visit->patient_id }};
                    this.isLoadingHistory = true;
                    try {
                        const response = await fetch(`/doctor/patient/${patientId}/history-json`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        if (!response.ok) throw new Error('Request failed');
                        const data = await response.json();
                        if (data.success) {
                            this.historyData = data.data || { visits: [], prescriptions: [], labs: [] };
                        } else {
                            showNotification('Could not load history', 'warning');
                        }
                    } catch (e) {
                        showNotification('Failed to load patient history', 'error');
                    } finally {
                        this.isLoadingHistory = false;
                    }
                },

                openLabResultModal(lab) {
                    this.selectedLab = lab;
                    this.labResultModalOpen = true;
                },

                async loadPatient(visitId) {
                    if (!visitId) return;
                    this.isLoading = true;
                    this.activeVisitId = visitId;

                    try {
                        const response = await fetch(`/doctor/consultancy/${visitId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        if (!response.ok) throw new Error(`HTTP ${response.status}`);
                        const data = await response.json();

                        if (data.success) {
                            this.currentVisit = data.visit;

                            // Normalize medicine names from select options as fallback
                            const medSelect = document.getElementById('medicineSelect');
                            const medMap = {};
                            if (medSelect) {
                                Array.from(medSelect.options).forEach(opt => {
                                    if (opt.value) {
                                        const text = opt.textContent.trim();
                                        const strengthMatch = text.match(/\(([^)]+)\)$/);
                                        medMap[opt.value] = {
                                            name: text.replace(/\s*\([^)]+\)$/, '').trim(),
                                            strength: strengthMatch ? strengthMatch[1] : ''
                                        };
                                    }
                                });
                            }
                            // Patch prescriptions with medicine data if missing
                            (this.currentVisit.diagnoses || []).forEach(d => {
                                (d.prescriptions || []).forEach(p => {
                                    if (!p.medicine || !p.medicine.name) {
                                        const lookup = medMap[p.medicine_id] || medMap[String(p.medicine_id)];
                                        if (lookup) {
                                            p.medicine = { name: lookup.name, strength: lookup.strength };
                                        } else {
                                            p.medicine = { name: p.medicine_name || 'Unknown Medicine', strength: p.dosage || '' };
                                        }
                                    }
                                });
                            });

                            // Count prescriptions
                            let pCount = 0;
                            (this.currentVisit.diagnoses || []).forEach(d => {
                                pCount += (d.prescriptions || []).length;
                            });
                            this.prescriptionCount = pCount;

                            // Update diagnosis_id hidden field
                            const latestDiag = (this.currentVisit.diagnoses || [])[0] || null;
                            const diagIdEl = document.getElementById('active_diagnosis_id');
                            if (diagIdEl && latestDiag) diagIdEl.value = latestDiag.id;

                            if (latestDiag) {
                                this.diagnosis.text = latestDiag.diagnosis || '';
                                this.diagnosis.notes = latestDiag.doctor_notes || '';
                                this.diagnosis.severity = latestDiag.severity || 'moderate';
                                this.diagnosis.is_urgent = !!latestDiag.is_urgent;
                                this.diagnosis.is_chronic = !!latestDiag.is_chronic;
                                this.diagnosis.has_prescription = latestDiag.has_prescription !== false;
                                this.diagnosis.medical_advice = latestDiag.medical_advice || '';
                                this.diagnosis.illness_tag_ids = (latestDiag.illness_tags || []).map(t => String(t.id));
                                this.diagnosis.medical_specialty_ids = (latestDiag.medical_specialties || []).map(s => String(s.id));
                            } else {
                                this.diagnosis = { text: '', notes: '', severity: 'moderate', is_urgent: false, is_chronic: false, has_prescription: true, medical_advice: '', illness_tag_ids: [], medical_specialty_ids: [] };
                            }

                            window.history.pushState({}, '', `/doctor/consultancy/${visitId}`);
                        } else {
                            showNotification(data.message || 'Failed to load patient', 'error');
                        }
                    } catch (e) {
                        showNotification('Error loading patient data', 'error');
                        console.error(e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                toggleLabTest(id, name, price) {
                    const index = this.selectedTests.findIndex(t => t.id === id);
                    if (index > -1) {
                        this.selectedTests.splice(index, 1);
                    } else {
                        this.selectedTests.push({ id, name, price });
                    }
                },

                async saveDiagnosis() {
                    if (!this.diagnosis.text.trim()) {
                        showNotification('Primary diagnosis is required', 'warning');
                        return;
                    }
                    this.saving = true;
                    try {
                        const response = await fetch('/doctor/diagnoses', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                visit_id: this.activeVisitId,
                                patient_id: this.currentVisit?.patient_id || {{ $visit->patient_id }},
                                diagnosis: this.diagnosis.text,
                                doctor_notes: this.diagnosis.notes,
                                severity: this.diagnosis.severity,
                                is_urgent: this.diagnosis.is_urgent ? 1 : 0,
                                is_chronic: this.diagnosis.is_chronic ? 1 : 0,
                                has_prescription: this.diagnosis.has_prescription ? 1 : 0,
                                medical_advice: this.diagnosis.medical_advice,
                                illness_tag_ids: this.diagnosis.illness_tag_ids,
                                medical_specialty_ids: this.diagnosis.medical_specialty_ids
                            })
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            showNotification('Clinical record saved successfully', 'success');
                            if (data.diagnosis?.id) {
                                const diagIdEl = document.getElementById('active_diagnosis_id');
                                if (diagIdEl) diagIdEl.value = data.diagnosis.id;
                            }
                            await this.loadPatient(this.activeVisitId);
                            if (this.diagnosis.has_prescription) {
                                this.activeTab = 'prescriptions';
                            }
                        } else if (response.status === 422) {
                            const errors = Object.values(data.errors || {}).flat().join(', ');
                            showNotification('Validation error: ' + errors, 'error');
                        } else {
                            showNotification(data.message || 'Failed to save diagnosis', 'error');
                        }
                    } catch (e) {
                        showNotification('Failed to save diagnosis', 'error');
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },

                async submitLabOrder() {
                    if (this.selectedTests.length === 0) return;
                    this.savingLabs = true;
                    try {
                        const response = await fetch('/doctor/lab-orders', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                visit_id: this.activeVisitId,
                                patient_id: this.currentVisit?.patient_id || {{ $visit->patient_id }},
                                test_type_ids: this.selectedTests.map(t => t.id),
                                priority: this.labPriority,
                                comments: this.labNotes
                            })
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            const count = this.selectedTests.length;
                            showNotification(`${count} lab order${count > 1 ? 's' : ''} placed successfully!`, 'success');
                            this.labModalOpen = false;
                            this.selectedTests = [];
                            this.labNotes = '';
                            this.labPriority = 'normal';
                            await this.loadPatient(this.activeVisitId);
                            this.activeTab = 'labs';
                        } else if (response.status === 422) {
                            const errors = Object.values(data.errors || {}).flat().join(', ');
                            showNotification('Validation error: ' + errors, 'error');
                        } else {
                            showNotification(data.message || 'Failed to place lab order', 'error');
                        }
                    } catch (e) {
                        showNotification('Failed to place lab order. Please try again.', 'error');
                        console.error(e);
                    } finally {
                        this.savingLabs = false;
                    }
                },

                async finalizeVisit() {
                    this.saving = true;
                    try {
                        const response = await fetch(`/doctor/consultancy/${this.activeVisitId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.completeModalOpen = false;
                            showNotification('Consultation completed! Pharmacy has been notified.', 'success');
                            if (data.print_url) {
                                setTimeout(() => window.open(data.print_url, '_blank'), 500);
                            }
                            setTimeout(() => { window.location.href = '/doctor/consultancy'; }, 1500);
                        } else {
                            showNotification(data.message || 'Failed to complete consultation', 'error');
                        }
                    } catch (e) {
                        showNotification('Failed to complete consultation. Please try again.', 'error');
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },

                async submitVitals() {
                    this.saving = true;
                    try {
                        const form = document.getElementById('vitalsForm');
                        const get = (name) => form.querySelector(`[name="${name}"]`)?.value || null;

                        const payload = {
                            visit_id: {{ $visit->id }},
                            patient_id: {{ $visit->patient_id }},
                            temperature: get('temperature'),
                            pulse: get('pulse'),
                            blood_pressure_systolic: get('blood_pressure_systolic'),
                            blood_pressure_diastolic: get('blood_pressure_diastolic'),
                            respiratory_rate: get('respiratory_rate'),
                            oxygen_saturation: get('oxygen_saturation'),
                            oxygen_device: get('oxygen_device'),
                            oxygen_flow_rate: get('oxygen_flow_rate'),
                            pain_scale: get('pain_scale'),
                            height: this.vitalsForm.height || null,
                            weight: this.vitalsForm.weight || null,
                            bmi: this.calculateBMI() || null,
                            blood_glucose: get('blood_glucose'),
                            notes: get('notes'),
                        };

                        // Remove empty/null values
                        Object.keys(payload).forEach(k => {
                            if (payload[k] === '' || payload[k] === null || payload[k] === undefined) {
                                if (k !== 'visit_id' && k !== 'patient_id') delete payload[k];
                            }
                        });

                        const response = await fetch('/doctor/vitals/record', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            showNotification('Vital signs recorded successfully!', 'success');
                            this.vitalsModalOpen = false;
                            form.reset();
                            this.vitalsForm = { height: '', weight: '' };
                            await this.loadPatient(this.activeVisitId);
                            this.activeTab = 'vitals';
                        } else if (response.status === 422) {
                            const errors = Object.values(data.errors || {}).flat();
                            showNotification('Please check: ' + errors.slice(0, 3).join(', '), 'warning');
                        } else {
                            showNotification(data.message || 'Error saving vitals', 'error');
                        }
                    } catch (e) {
                        showNotification('Network error saving vitals', 'error');
                        console.error(e);
                    } finally {
                        this.saving = false;
                    }
                },

                async printCurrentPrescription() {
                    const diags = this.currentVisit?.diagnoses || [];
                    for (const diag of diags) {
                        if (diag.prescriptions && diag.prescriptions.length > 0) {
                            const prescId = diag.prescriptions[0].id;
                            window.open(`/print/prescription/${prescId}`, '_blank');
                            return;
                        }
                    }
                    showNotification('No prescription found for this visit. Add a prescription first.', 'warning');
                }
            };
        }

        // ============================================
        // Prescription Form Handler
        // ============================================
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('addPrescriptionForm');
            if (!form) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const diagId = document.getElementById('active_diagnosis_id')?.value;
                if (!diagId) {
                    showNotification('Please save a diagnosis first before adding prescriptions.', 'warning');
                    return;
                }

                const medicine = form.querySelector('[name="medicine_id"]')?.value;
                const dosage = document.getElementById('prescrDosage')?.value;
                const morning = parseInt(document.getElementById('prescrMorning')?.value) || 0;
                const evening = parseInt(document.getElementById('prescrEvening')?.value) || 0;
                const night = parseInt(document.getElementById('prescrNight')?.value) || 0;
                const days = parseInt(document.getElementById('prescrDays')?.value) || 0;

                if (!medicine) { showNotification('Please select a medicine.', 'warning'); return; }
                if (!dosage) { showNotification('Please enter dosage.', 'warning'); return; }
                if (days < 1) { showNotification('Please enter number of days (minimum 1).', 'warning'); return; }
                if (morning + evening + night === 0) { showNotification('Please enter at least one dose time.', 'warning'); return; }

                const formData = new FormData(form);
                formData.set('quantity', (morning + evening + night) * days);

                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                // Store selected medicine name for immediate display
                const medSelectEl = document.getElementById('medicineSelect');
                const selectedOptText = medSelectEl?.options[medSelectEl.selectedIndex]?.textContent?.trim() || '';

                try {
                    const response = await fetch('/doctor/prescriptions', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await response.json();

                    if (response.ok && data.success) {
                        showNotification('Medication added to prescription!', 'success');
                        form.reset();
                        ['prescrMorning','prescrEvening','prescrNight','prescrQty'].forEach(id => {
                            const el = document.getElementById(id);
                            if (el) el.value = 0;
                        });
                        const abbrevEl = document.getElementById('abbrevSelect');
                        if (abbrevEl) abbrevEl.value = '';
                        // Refresh visit data via Alpine
                        document.addEventListener('alpine:initialized', () => {}, { once: true });
                        const workspace = Alpine.$data(document.querySelector('[x-data="consultationWorkspace()"]'));
                        if (workspace) await workspace.loadPatient(workspace.activeVisitId);
                    } else if (response.status === 422) {
                        const errors = Object.values(data.errors || {}).flat().join(', ');
                        showNotification('Validation error: ' + errors, 'error');
                    } else {
                        showNotification(data.message || 'Failed to add medication', 'error');
                    }
                } catch (err) {
                    showNotification('Network error adding medicine', 'error');
                    console.error(err);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add to Prescription';
                }
            });
        });

        function applyAbbreviation(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;
            
            if(opt.dataset.days) document.getElementById('prescrDays').value = opt.dataset.days;
            if(opt.dataset.morning) document.getElementById('prescrMorning').value = opt.dataset.morning;
            if(opt.dataset.evening) document.getElementById('prescrEvening').value = opt.dataset.evening;
            if(opt.dataset.night) document.getElementById('prescrNight').value = opt.dataset.night;
            
            updatePrescQty();
        }

        function updatePrescQty() {
            const m = parseInt(document.getElementById('prescrMorning')?.value) || 0;
            const e = parseInt(document.getElementById('prescrEvening')?.value) || 0;
            const n = parseInt(document.getElementById('prescrNight')?.value) || 0;
            const d = parseInt(document.getElementById('prescrDays')?.value) || 0;
            const el = document.getElementById('prescrQty');
            if (el) el.value = (m + e + n) * d;
        }
    </script>
    @endpush
@endsection