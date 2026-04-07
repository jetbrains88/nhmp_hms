@extends('layouts.app')

@section('title', 'Adjust Stock')

@section('content')
<div class="space-y-6">
    <!-- Header Content -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                <i class="fas fa-sliders text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 leading-tight">Adjust Stock</h1>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mt-1">Batch: <span class="text-amber-600">{{ $batch->batch_number }}</span> - {{ $batch->medicine->name }}</p>
            </div>
        </div>
        <a href="{{ route('pharmacy.inventory.batch', $batch->id) }}" class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-50 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to Batch
        </a>
    </div>

    <!-- Main Form Section -->
    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
        <form action="{{ route('pharmacy.inventory.adjust', $batch->id) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Adjustment Details</h3>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Current Quantity</label>
                        <input type="text" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-slate-500 font-mono font-bold cursor-not-allowed" value="{{ $batch->remaining_quantity }} {{ $batch->medicine->unit }}" disabled>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">New Exact Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" name="new_quantity" class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-700 font-bold transition-all focus:ring-4 focus:ring-amber-500/10" min="0" placeholder="Enter the final stock level" required>
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-2">Reasoning</h3>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Reason for Adjustment <span class="text-rose-500">*</span></label>
                        <select name="reason" class="w-full bg-white border border-slate-200 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-700 font-bold transition-all focus:ring-4 focus:ring-amber-500/10 appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-[right_1rem_center] bg-no-repeat" required>
                            <option value="">Select Reason...</option>
                            <option value="Damage">Damage / Spoilage</option>
                            <option value="Expiration">Expiration</option>
                            <option value="Count Discrepancy">Count Discrepancy (Correction)</option>
                            <option value="Loss">Loss / Theft</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 flex items-start gap-3">
                        <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                        <p class="text-xs text-amber-800 font-medium">This adjustment will be recorded in the audit logs. Changing quantities dynamically updates global stock values. Ensure proper physical verification is made before confirming.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('pharmacy.inventory.batch', $batch->id) }}" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-2xl font-bold hover:bg-slate-50 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-amber-500/30 transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Record Adjustment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
