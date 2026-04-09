@extends('layouts.app')

@section('title', 'Prescription Details - NHMP HMS')
@section('page-title', 'Prescription Details')
@section('breadcrumb', 'Prescription Details')

@section('content')
<div class="space-y-10 pb-12">
    <!-- Header Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-gradient-to-tr from-indigo-600 to-blue-500 rounded-3xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i class="fas fa-file-prescription text-3xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Prescription #{{ $prescription->prescription_number }}</h2>
                    <p class="text-slate-500 font-medium mt-1">Prescribed by <span class="text-indigo-600 font-bold">Dr. {{ $prescription->prescribedBy->name ?? 'Unknown' }}</span> on {{ $prescription->created_at->format('d M, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'partially_dispensed' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
                    ];
                    $statusIcons = [
                        'pending' => 'fa-clock',
                        'partially_dispensed' => 'fa-spinner',
                        'completed' => 'fa-check-circle',
                        'cancelled' => 'fa-times-circle',
                    ];
                    $color = $statusColors[$prescription->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    $icon = $statusIcons[$prescription->status] ?? 'fa-info-circle';
                @endphp
                <span class="inline-flex items-center gap-2 px-6 py-2 rounded-2xl text-sm font-black uppercase tracking-widest border-2 {{ $color }} shadow-sm">
                    <i class="fas {{ $icon }}"></i>
                    {{ str_replace('_', ' ', $prescription->status) }}
                </span>
                <a href="{{ route('pharmacy.prescriptions.print', $prescription) }}" target="_blank" class="p-3 bg-white border-2 border-slate-100 text-slate-400 rounded-2xl hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
                    <i class="fas fa-print"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Patient Info Column -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center gap-4 bg-gradient-to-br from-indigo-50/50 to-white">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-indigo-200">
                        {{ strtoupper(substr($prescription->diagnosis->visit->patient->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 leading-tight">{{ $prescription->diagnosis->visit->patient->name }}</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">EMRN: {{ $prescription->diagnosis->visit->patient->emrn }}</p>
                    </div>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Age / Gender</p>
                            <p class="text-sm font-bold text-slate-700">{{ $prescription->diagnosis->visit->patient->age ?? 'N/A' }} / {{ $prescription->diagnosis->visit->patient->gender ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Blood Group</p>
                            <p class="text-sm font-bold text-slate-700">{{ $prescription->diagnosis->visit->patient->blood_group ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-slate-50 space-y-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Medical Notes</p>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 italic text-slate-600 text-sm">
                            {{ $prescription->diagnosis->clinical_notes ?? 'No clinical notes recorded.' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-500/20">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-6">Course Progress</h4>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-2xl font-black">{{ $prescription->dispensations->sum('quantity_dispensed') }} / {{ $prescription->quantity }}</span>
                            <span class="text-[10px] font-black uppercase opacity-60">Units Dispensed</span>
                        </div>
                        <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden">
                            <div class="bg-white h-full transition-all duration-1000 shadow-[0_0_10px_rgba(255,255,255,0.5)]" 
                                 style="width: {{ min(100, ($prescription->dispensations->sum('quantity_dispensed') / max(1, $prescription->quantity)) * 100) }}%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10">
                        <div>
                            <p class="text-[9px] font-black uppercase opacity-50">Duration</p>
                            <p class="font-bold">{{ $prescription->days }} Days</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase opacity-50">Dosage</p>
                            <p class="font-bold">{{ $prescription->dosage }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Prescription Content Column -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gradient-to-r from-emerald-50/30 to-white">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-50">
                            <i class="fas fa-pills"></i>
                        </span>
                        Medication Details
                    </h3>
                </div>
                <div class="p-10 space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Prescribed Medicine</h4>
                                <p class="text-2xl font-black text-indigo-600">{{ $prescription->medicine->name }}</p>
                                <p class="text-sm font-medium text-slate-500 mt-1">{{ $prescription->medicine->generic_name }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach(['Morning' => 'morning', 'Afternoon' => 'afternoon', 'Night' => 'night'] as $label => $key)
                                <div class="p-4 rounded-2xl border-2 {{ $prescription->$key ? 'bg-indigo-50 border-indigo-100 text-indigo-700' : 'bg-slate-50 border-slate-100 text-slate-300' }} text-center">
                                    <p class="text-[9px] font-black uppercase tracking-tight mb-1">{{ $label }}</p>
                                    <p class="text-lg font-black">{{ $prescription->$key ?: '0' }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Clinical Instructions</h4>
                                <p class="text-sm text-slate-700 font-medium leading-relaxed italic">
                                    "{{ $prescription->instructions ?: 'Take as directed by your physician.' }}"
                                </p>
                            </div>
                            @if($prescription->medicine->stock < ($prescription->quantity - $prescription->dispensations->sum('quantity_dispensed')))
                                <div class="flex items-center gap-4 p-4 bg-rose-50 border-2 border-rose-100 rounded-2xl animate-pulse">
                                    <div class="w-10 h-10 rounded-xl bg-rose-600 flex items-center justify-center text-white">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="text-xs font-black text-rose-700 uppercase tracking-tight">
                                        Insufficient Warehouse Stock! <br>Only {{ $prescription->medicine->stock }} units available.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($prescription->status !== 'completed' && $prescription->status !== 'cancelled')
                    <!-- Dispense Action Section -->
                    <div class="pt-10 border-t border-slate-100">
                        <h3 class="text-lg font-black text-slate-900 mb-8 uppercase tracking-widest">Dispense New Batch</h3>
                        <form x-data="{ 
                                submitting: false, 
                                quantity: {{ $prescription->quantity - $prescription->dispensations->sum('quantity_dispensed') }},
                                async submit(e) {
                                    e.preventDefault();
                                    this.submitting = true;
                                    const form = e.target;
                                    const formData = new FormData(form);
                                    
                                    try {
                                        const r = await fetch(form.action, {
                                            method: 'POST',
                                            body: formData,
                                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                        });
                                        const data = await r.json();
                                        if (data.success) {
                                            if (window.showSuccess) window.showSuccess(data.message);
                                            setTimeout(() => window.location.reload(), 1500);
                                        } else {
                                            if (window.showError) window.showError(data.message);
                                            this.submitting = false;
                                        }
                                    } catch (err) {
                                        if (window.showError) window.showError('Network error occurred.');
                                        this.submitting = false;
                                    }
                                }
                            }" 
                            @submit="submit" action="{{ route('pharmacy.prescriptions.dispense', $prescription) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @csrf
                            <div class="space-y-5">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">Select Medicine Batch *</label>
                                    <select name="medicine_batch_id" required class="w-full h-14 px-6 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-50 font-bold transition-all outline-none">
                                        <option value="">Select an available batch...</option>
                                        @foreach($prescription->medicine->batches->where('remaining_quantity', '>', 0) as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->batch_number }} (Exp: {{ \Carbon\Carbon::parse($batch->expiry_date)->format('M Y') }}) - {{ $batch->remaining_quantity }} in stock</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="group">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">Quantity to Dispense *</label>
                                    <div class="relative">
                                        <input type="number" name="quantity_dispensed" x-model="quantity" max="{{ $prescription->quantity - $prescription->dispensations->sum('quantity_dispensed') }}" required 
                                               class="w-full h-14 px-6 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-50 font-bold transition-all outline-none">
                                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">Units</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-5">
                                <div class="group">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-indigo-600 transition-colors">Internal Notes</label>
                                    <textarea name="notes" rows="3" class="w-full p-6 bg-slate-50 border-2 border-slate-100 rounded-3xl focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-50 font-medium transition-all outline-none italic text-sm" placeholder="Any specific notes about this dispensation..."></textarea>
                                </div>
                                <button type="submit" :disabled="submitting" 
                                        class="w-full h-14 bg-gradient-to-r from-emerald-600 to-teal-500 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-emerald-200 hover:shadow-emerald-300 hover:-translate-y-1 transition-all disabled:opacity-50 disabled:translate-y-0">
                                    <span x-show="!submitting" class="flex items-center justify-center gap-2">
                                        <i class="fas fa-hand-holding-medical"></i> Confirm Dispensation
                                    </span>
                                    <span x-show="submitting"><i class="fas fa-circle-notch fa-spin"></i> Processing...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dispensation History Table -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-indigo-50/20 to-white">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-50">
                            <i class="fas fa-history"></i>
                        </span>
                        Dispensation History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Dispensed By</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch Info</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($prescription->dispensations as $dispense)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                                {{ strtoupper(substr($dispense->dispensedBy->name, 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-slate-700 text-sm">{{ $dispense->dispensedBy->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-slate-900 text-sm">#{{ $dispense->medicineBatch->batch_number }}</div>
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-tight">Exp: {{ \Carbon\Carbon::parse($dispense->medicineBatch->expiry_date)->format('M Y') }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-black">{{ $dispense->quantity_dispensed }} Units</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="text-xs font-bold text-slate-800">{{ $dispense->created_at->format('d M, Y') }}</div>
                                        <div class="text-[10px] font-medium text-slate-400 uppercase">{{ $dispense->created_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center text-slate-300 italic font-medium">
                                        No dispensations recorded for this prescription yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection