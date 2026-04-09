@extends('layouts.app')

@section('title', 'Add Inventory Stock')
@section('page-title', 'Inventory Management')
@section('breadcrumb', 'Pharmacy / Inventory / Add Stock')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in" x-data="{ medicineId: '{{ $selectedMedicine->id ?? '' }}' }">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ $selectedMedicine ? route('pharmacy.medicines.show', $selectedMedicine) : route('pharmacy.inventory') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-100 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-slate-800 leading-none">Add Inventory Stock</h2>
            <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mt-2">Register a new batch or restock existing medicine</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 font-bold text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pharmacy.inventory.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Medicine Selection -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full translate-x-16 -translate-y-16 opacity-50"></div>
            
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Medicine Identification
            </h3>
            
            <div class="space-y-2 relative z-10">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Select Medicine *</label>
                @if($selectedMedicine)
                    <div class="flex items-center gap-4 p-5 bg-blue-50/50 border border-blue-100 rounded-3xl group transition-all">
                        <div class="w-12 h-12 rounded-3xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-200/50">
                            <i class="fas fa-pills text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800">{{ $selectedMedicine->name }}</h4>
                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">{{ $selectedMedicine->generic_name }}</p>
                        </div>
                        <input type="hidden" name="medicine_id" value="{{ $selectedMedicine->id }}">
                    </div>
                @else
                    <select name="medicine_id" x-model="medicineId" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700 appearance-none">
                        <option value="">Choose medicine...</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{ $medicine->name }} ({{ $medicine->generic_name }})
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('medicine_id') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Batch Details -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Batch Specifications
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Batch Number *</label>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}" required placeholder="e.g. B24-409" 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700 placeholder:font-medium">
                    @error('batch_number') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Expiry Date *</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                    @error('expiry_date') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Quantity to Add *</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" required min="1" placeholder="0" 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                    @error('quantity') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">RC number</label>
                    <input type="text" name="rc_number" value="{{ old('rc_number') }}" placeholder="e.g. RC-2024-X" 
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700 placeholder:font-medium">
                </div>
            </div>
        </div>

        <!-- Pricing Details -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Commercial Value
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Unit Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-sm">Rs.</span>
                        <input type="number" name="unit_price" value="{{ old('unit_price') }}" required min="0" step="0.01" placeholder="0.00" 
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Retail Sale Price *</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-sm">Rs.</span>
                        <input type="number" name="sale_price" value="{{ old('sale_price') }}" required min="0" step="0.01" placeholder="0.00" 
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                    </div>
                </div>
            </div>

            <div class="mt-8 space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Batch Notes</label>
                <textarea name="notes" rows="3" placeholder="Storage instructions, special handling, or supplier notes..." 
                    class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-medium text-slate-700 placeholder:text-slate-400">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pb-10">
            <a href="{{ route('pharmacy.inventory') }}" class="px-8 py-4 bg-slate-100 text-slate-500 rounded-3xl font-bold hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-3xl font-black shadow-lg shadow-blue-200/50 hover:shadow-xl transition-all">
                Authorize Stock Entry
            </button>
        </div>
    </form>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
