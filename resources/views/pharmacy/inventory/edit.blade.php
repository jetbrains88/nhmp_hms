@extends('layouts.app')

@section('title', 'Edit Medicine')
@section('page-title', 'Pharmacy Inventory')
@section('breadcrumb', 'Edit Medicine')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('pharmacy.inventory.show', $medicine) }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:border-blue-100 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-slate-800 leading-none">Edit Medicine Record</h2>
            <p class="text-slate-400 font-black uppercase tracking-widest text-[10px] mt-2">Update core specifications for {{ $medicine->name }}</p>
        </div>
    </div>

    <form action="{{ route('pharmacy.inventory.update', $medicine) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Primary Identification -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full translate-x-16 -translate-y-16 opacity-50"></div>
            
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Primary Identification
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div class="space-y-2">
                    <label for="name" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Medicine Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $medicine->name) }}" required
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                    @error('name') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label for="generic_name" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Generic Name</label>
                    <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                </div>
                <div class="space-y-2">
                    <label for="category_id" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Category *</label>
                    <div class="relative">
                        <select name="category_id" id="category_id" required
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700 appearance-none">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $medicine->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="manufacturer" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Manufacturer</label>
                    <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                </div>
            </div>
        </div>

        <!-- Inventory Control -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Inventory Control
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Locked Current Stock</label>
                    <div class="px-5 py-4 bg-slate-100 rounded-3xl font-black text-slate-400 border border-slate-200/50 flex items-center justify-between">
                        <span>{{ $medicine->stock }} Units</span>
                        <i class="fas fa-lock text-[10px]"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="unit" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Unit Type *</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $medicine->unit) }}" required placeholder="e.g. Tablets"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-slate-700">
                </div>
                <div class="space-y-2">
                    <label for="min_stock_level" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Reorder Level</label>
                    <input type="number" name="min_stock_level" id="min_stock_level" value="{{ old('min_stock_level', $medicine->min_stock_level) }}" min="0"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-slate-700">
                </div>
            </div>
        </div>

        <!-- Financial Parameters -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Financial Parameters
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="unit_price" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Base Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-sm">Rs.</span>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price', $medicine->unit_price) }}" required step="0.01" min="0"
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-slate-700">
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="selling_price" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Hospital Retail Price *</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-sm">Rs.</span>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $medicine->selling_price) }}" required step="0.01" min="0"
                            class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-slate-700">
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Specifications -->
        <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Extended Specifications
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label for="batch_number" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Default Batch</label>
                    <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number', $medicine->batch_number) }}"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                </div>
                <div class="space-y-2">
                    <label for="expiry_date" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Next Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $medicine->expiry_date) }}"
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-bold text-slate-700">
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="description" class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Clinical Description</label>
                <textarea name="description" id="description" rows="3" placeholder="Notes on dosage, side effects, or special handling..." 
                    class="w-full px-5 py-4 bg-slate-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 transition-all font-medium text-slate-700 placeholder:text-slate-400">{{ old('description', $medicine->description) }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pb-10">
            <a href="{{ route('pharmacy.inventory.show', $medicine) }}" 
               class="px-8 py-4 bg-slate-100 text-slate-500 rounded-3xl font-bold hover:bg-slate-200 transition-all">Cancel changes</a>
            <button type="submit" 
                    class="px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-3xl font-black shadow-lg shadow-blue-200/50 hover:shadow-xl transition-all">
                <i class="fas fa-save mr-2"></i> Commit Updates
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