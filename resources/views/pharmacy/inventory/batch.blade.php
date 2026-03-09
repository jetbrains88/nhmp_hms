@extends('layouts.app')

@section('title', 'Batch Details')

@section('content')
<div class="container-fluid max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Batch: {{ $batch->batch_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">Medicine: {{ $batch->medicine->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('pharmacy.inventory') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
            @if($batch->remaining_quantity > 0)
            <a href="{{ route('pharmacy.inventory.adjust-form', $batch->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-lg font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors shadow-sm">
                <i class="fas fa-sliders-h mr-2"></i>Adjust Stock
            </a>
            <a href="{{ route('pharmacy.inventory.transfer-form', $batch->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm">
                <i class="fas fa-exchange-alt mr-2"></i>Transfer
            </a>
            @endif
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-info-circle mr-2 text-purple-500"></i>Batch Information</h3>
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Stock Remaining</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($batch->remaining_quantity) }} <span class="text-sm font-normal text-gray-500">units</span></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                        @if($batch->remaining_quantity <= 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Out of Stock</span>
                        @elseif($batch->expiry_date < now())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Expiry Date</p>
                        <p class="text-gray-900 font-medium {{ $batch->expiry_date < now()->addMonths(3) ? 'text-red-600' : '' }}">
                            {{ Carbon\Carbon::parse($batch->expiry_date)->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Purchase Price</p>
                        <p class="text-gray-900">Rs {{ number_format($batch->unit_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Selling Price</p>
                        <p class="text-gray-900">Rs {{ number_format($batch->sale_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Value</p>
                        <p class="text-gray-900 font-bold">Rs {{ number_format($batch->remaining_quantity * $batch->unit_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-history mr-2 text-purple-500"></i>Activity Log</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($batch->inventoryLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->type == 'add')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-plus mr-1"></i> Add</span>
                                    @elseif($log->type == 'deduct' || $log->type == 'dispense')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-minus mr-1"></i> Deduct</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($log->type) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $log->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->user->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $log->notes ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No activity logs found for this batch.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
             <!-- Medicine Image Placeholder (using a generic pharma icon if none exists) -->
             <div class="bg-gray-50 h-48 flex items-center justify-center border-b border-gray-200">
                 <i class="fas fa-pills text-6xl text-gray-300"></i>
             </div>
             <div class="p-6">
                 <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $batch->medicine->name }}</h3>
                 <p class="text-sm text-gray-500 mb-4">{{ $batch->medicine->generic_name ?? $batch->medicine->category->name }}</p>
                 
                 <div class="space-y-3 pt-4 border-t border-gray-100">
                     <div class="flex justify-between items-center text-sm">
                         <span class="text-gray-500">Strength</span>
                         <span class="font-medium text-gray-900">{{ $batch->medicine->strength ?? '-' }}</span>
                     </div>
                     <div class="flex justify-between items-center text-sm">
                         <span class="text-gray-500">Form</span>
                         <span class="font-medium text-gray-900">{{ $batch->medicine->form ?? '-' }}</span>
                     </div>
                     <div class="flex justify-between items-center text-sm">
                         <span class="text-gray-500">Requires Rx</span>
                         <span class="font-medium text-gray-900">{{ $batch->medicine->requires_prescription ? 'Yes' : 'No' }}</span>
                     </div>
                     <div class="flex justify-between items-center text-sm">
                         <span class="text-gray-500">Code</span>
                         <span class="font-medium text-gray-900">{{ $batch->medicine->code }}</span>
                     </div>
                 </div>
                 
                 <div class="mt-6">
                     <a href="{{ route('pharmacy.inventory') }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                         View All Batches for Medicine
                     </a>
                 </div>
             </div>
            </div>
        </div>
    </div>
</div>
@endsection
