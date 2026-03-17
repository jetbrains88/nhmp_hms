@extends('layouts.app')

@section('title', 'Visit Details - ' . $visit->queue_token)
@section('page-title', 'Visit Details')
@section('breadcrumb', 'Reception / Visits / ' . $visit->queue_token)

@section('content')
<div class="space-y-6">
    {{-- Top Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('reception.queue') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
            <i class="fas fa-arrow-left"></i> Back to Queue
        </a>
        <div class="flex gap-3">
            <a href="{{ route('reception.visits.print-token', $visit) }}" target="_blank"
               class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-all shadow-sm flex items-center gap-2">
                <i class="fas fa-print"></i> Re-print Token
            </a>
            @if(!in_array($visit->status, ['completed', 'cancelled']))
                <button onclick="window.alpineReceptionData?.openPatientModal({
                    id: {{ $visit->id }},
                    name: '{{ $visit->patient->name }}',
                    queue_token: '{{ $visit->queue_token }}'
                })"
                class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-edit"></i> Update Status/Vitals
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Patient & Visit Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Patient Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl shadow-inner">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold">{{ $visit->patient->name }}</h2>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-blue-100 text-sm">
                                <span><i class="fas fa-id-badge mr-1"></i>{{ $visit->patient->emrn }}</span>
                                <span><i class="fas fa-phone mr-1"></i>{{ $visit->patient->phone }}</span>
                                <span><i class="fas fa-venus-mars mr-1"></i>{{ ucfirst($visit->patient->gender) }}</span>
                                <span><i class="fas fa-birthday-cake mr-1"></i>{{ $visit->patient->dob ? \Carbon\Carbon::parse($visit->patient->dob)->age . ' years' : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs uppercase tracking-wider font-bold opacity-75">Token Number</span>
                            <div class="text-3xl font-mono font-bold">{{ $visit->queue_token }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-4">Visit Information</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Visit Type</dt>
                                <dd class="font-bold text-gray-800">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $visit->visit_type === 'emergency' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($visit->visit_type) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status</dt>
                                <dd class="font-bold">
                                    <span class="px-2 py-0.5 rounded text-xs {{ 
                                        $visit->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                        ($visit->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Registered At</dt>
                                <dd class="text-gray-800 font-medium">{{ $visit->created_at->format('d M Y, h:i A') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Assigned Doctor</dt>
                                <dd class="text-gray-800 font-bold text-blue-600">
                                    {{ $visit->doctor ? $visit->doctor->name : 'Wait-listing' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-xs uppercase tracking-wider font-bold text-gray-400 mb-4">Primary Complaint</h3>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 italic text-gray-700">
                            {{ $visit->complaint ?? 'No complaint recorded.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vitals History --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-heartbeat text-red-500"></i> Recorded Vitals
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/80 text-xs font-bold text-gray-400 uppercase">
                                <th class="px-6 py-3">Time</th>
                                <th class="px-6 py-3">Temp</th>
                                <th class="px-6 py-3">BP</th>
                                <th class="px-6 py-3">Pulse</th>
                                <th class="px-6 py-3">SpO2</th>
                                <th class="px-6 py-3">BGL</th>
                                <th class="px-6 py-3">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($visit->vitals as $vital)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                        {{ $vital->created_at->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold {{ $vital->temperature >= 38 ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $vital->temperature ?? '--' }}°
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 font-medium">
                                        {{ $vital->blood_pressure_systolic ?? '--' }}/{{ $vital->blood_pressure_diastolic ?? '--' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $vital->pulse ?? '--' }} bpm</td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold {{ $vital->oxygen_saturation <= 94 ? 'text-orange-600' : 'text-gray-800' }}">
                                            {{ $vital->oxygen_saturation ?? '--' }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $vital->blood_glucose ?? '--' }} mg/dL</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ $vital->recorder->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                        No vitals recorded for this visit yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Timeline & Summary --}}
        <div class="space-y-6">
            {{-- Visit Timeline --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-stream text-indigo-500"></i> Visit Timeline
                </h3>
                <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center">
                            <i class="fas fa-check text-[10px] text-blue-600"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Registration Complete</p>
                        <p class="text-xs text-gray-500">{{ $visit->created_at->diffForHumans() }}</p>
                    </div>
                    
                    @if($visit->status !== 'waiting')
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-yellow-100 border-2 border-white flex items-center justify-center">
                            <i class="fas fa-bolt text-[10px] text-yellow-600"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Consultation Started</p>
                        <p class="text-xs text-gray-500">
                            {{ $visit->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    @endif

                    @if($visit->status === 'completed')
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-green-100 border-2 border-white flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-[10px] text-green-600"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-800">Visit Finalized</p>
                        <p class="text-xs text-gray-500">
                            {{ $visit->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-amber-500"></i> Reception Notes
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $visit->notes ?? 'No additional notes recorded by reception.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
