@extends('layouts.app')

@section('title', 'Adjust Stock')

@section('content')
<div class="container-fluid max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Adjust Stock</h1>
            <p class="mt-1 text-sm text-gray-500">Batch: {{ $batch->batch_number }} - {{ $batch->medicine->name }}</p>
        </div>
        <a href="{{ route('pharmacy.inventory.batch', $batch->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>Back to Batch
        </a>
    </div>



    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('pharmacy.inventory.adjust', $batch->id) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Quantity</label>
                    <input type="text" class="form-control bg-gray-50" value="{{ $batch->remaining_quantity }}" disabled>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Quantity</label>
                    <input type="number" name="new_quantity" class="form-control" min="0" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Adjustment</label>
                    <select name="reason" class="form-control" required>
                        <option value="">Select Reason...</option>
                        <option value="Damage">Damage / Spoilage</option>
                        <option value="Expiration">Expiration</option>
                        <option value="Count Discrepancy">Count Discrepancy (Correction)</option>
                        <option value="Loss">Loss / Theft</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('pharmacy.inventory.batch', $batch->id) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>
@endsection
