{{-- resources/views/pharmacy/inventory/show.blade.php --}}
@extends('layouts.app')

@section('title', $medicine->name)
@section('page-title', 'Medicine Details')
@section('breadcrumb', $medicine->name)

@section('content')
    <div class="space-y-6">
        <!-- Medicine Header -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-xl">
                                    <i class="fas fa-pills text-white text-3xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900">{{ $medicine->name }}</h1>
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    {{ $medicine->code }}
                                </span>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                                    {{ $medicine->category->name }}
                                </span>
                                    @if($medicine->requires_prescription)
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                                    Prescription Required
                                </span>
                                    @endif
                                    @if($medicine->schedule)
                                        <span
                                            class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                    Schedule {{ $medicine->schedule }}
                                </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:text-right">
                        <div
                            class="text-5xl font-bold {{ $medicine->stock <= $medicine->reorder_level ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $medicine->stock }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">units in stock</div>
                        <div class="mt-4 flex lg:justify-end gap-3">
                            <button onclick="showUpdateStockModal({{ $medicine->id }}, '{{ $medicine->name }}')"
                                    class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-edit mr-2"></i>Update Stock
                            </button>
                            <a href="{{ route('pharmacy.inventory.edit', $medicine) }}"
                               class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-cog mr-2"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Status Bar -->
            <div class="p-8 border-b border-slate-50">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Inventory Status</span>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ $medicine->stock_status == 'out_of_stock' ? 'bg-rose-100 text-rose-700' :
                               ($medicine->stock_status == 'low_stock' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ ucfirst(str_replace('_', ' ', $medicine->stock_status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reorder Threshold</span>
                        <div class="text-lg font-black text-slate-700 mt-1">{{ $medicine->reorder_level }} <span class="text-xs">Units</span></div>
                    </div>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                    @php
                        $percentage = $medicine->reorder_level > 0
                            ? min(100, ($medicine->stock / ($medicine->reorder_level * 2 || 10)) * 100)
                            : 100;
                        $color = $medicine->stock == 0 ? 'from-rose-500 to-rose-600' :
                                ($medicine->stock <= $medicine->reorder_level ? 'from-amber-400 to-amber-600' : 'from-emerald-400 to-emerald-600');
                    @endphp
                    <div class="h-full bg-gradient-to-r {{ $color }} transition-all duration-700" style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            <!-- Details Grid -->
                    </div>
                @endif
                @if($medicine->expiry_date)
                    <div class="space-y-1">
                        <div class="text-sm text-gray-500">Expiry Date</div>
                        <div
                            class="font-medium {{ $medicine->isExpired() ? 'text-red-600': ($medicine->isAboutToExpire() ? 'text-amber-600' : 'text-green-900') }}">
                            {{ $medicine->expiry_date->format('d M Y') }}
                            @if($medicine->isExpired())
                                <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                    Expired
                                </span>
                            @elseif($medicine->isAboutToExpire())
                                <span class="ml-2 text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                    Expiring soon
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                @if($medicine->manufacture_date)
                    <div class="space-y-1">
                        <div class="text-sm text-gray-500">Manufacture Date</div>
                        <div class="font-medium text-gray-900">{{ $medicine->manufacture_date->format('d M Y') }}</div>
                    </div>
                @endif
                @if($medicine->batch_number)
                    <div class="space-y-1">
                        <div class="text-sm text-gray-500">Batch Number</div>
                        <div class="font-medium text-gray-900">{{ $medicine->batch_number }}</div>
                    </div>
                @endif
                @if($medicine->storage_conditions)
                    <div class="space-y-1">
                        <div class="text-sm text-gray-500">Storage Conditions</div>
                        <div class="font-medium text-gray-900">{{ $medicine->storage_conditions }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
            <!-- Tab Headers -->
            <div class="border-b border-gray-100">
                <nav class="flex -mb-px">
                    <button id="inventory-tab"
                            class="tab-button active py-4 px-6 font-medium text-gray-700 border-b-2 border-blue-500">
                        Inventory History
                    </button>
                    <button id="dispense-tab"
                            class="tab-button py-4 px-6 font-medium text-gray-500 hover:text-gray-700">
                        Dispense History
                    </button>
                    <button id="details-tab"
                            class="tab-button py-4 px-6 font-medium text-gray-500 hover:text-gray-700">
                        Additional Details
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Inventory History Tab -->
                <div id="inventory-content" class="tab-content active">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Log entry Date
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Transaction Type
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Quantity
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Previous
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    New Stock
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Responsible
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Notes
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                            @forelse($inventoryLogs as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                        {{ $log->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-wider rounded
                                            {{ $log->type == 'add' ? 'bg-emerald-100 text-emerald-800' :
                                               ($log->type == 'remove' ? 'bg-rose-100 text-rose-800' :
                                               ($log->type == 'dispense' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600')) }}">
                                            {{ ucfirst($log->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="text-sm font-black {{ $log->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-slate-400">
                                        {{ $log->previous_stock }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-base font-black text-slate-800">
                                        {{ $log->new_stock }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                        {{ $log->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-500 max-w-xs truncate italic">
                                        {{ $log->notes }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center text-slate-300">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-history text-2xl"></i>
                                        </div>
                                        <p class="font-black uppercase tracking-widest text-xs">No activity logs recorded</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($inventoryLogs->hasPages())
                        <div class="mt-6">
                            {{ $inventoryLogs->links() }}
                        </div>
                    @endif
                </div>

                <!-- Dispense History Tab -->
                <div id="dispense-content" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Dispense Date
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Patient Identity
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    EMRN
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Qty
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Prescriber
                                </th>
                                <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Dispenser
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                            @forelse($dispenseHistory as $prescription)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                        {{ $prescription->dispensed_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-black text-slate-800">
                                        {{ $prescription->diagnosis->visit->patient->name }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-[10px] font-black text-indigo-500 uppercase font-mono">
                                        {{ $prescription->diagnosis->visit->patient->emrn }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg border border-emerald-100">
                                            {{ $prescription->dispensed_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                        {{ $prescription->prescriber->name ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-slate-600">
                                        {{ $prescription->dispenser->name ?? 'Unknown' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center text-slate-300">
                                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-pills text-2xl"></i>
                                        </div>
                                        <p class="font-black uppercase tracking-widest text-xs">No dispense history found</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Additional Details Tab -->
                <div id="details-content" class="tab-content hidden">
                    <div class="space-y-6">
                        @if($medicine->description)
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Description</h4>
                                <div class="prose max-w-none">
                                    {{ $medicine->description }}
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-xl p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Inventory Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Last Restocked</span>
                                        <span class="font-medium">
                                        {{ $medicine->last_restocked_at ? $medicine->last_restocked_at->diffForHumans() : 'Never' }}
                                    </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Inventory Value</span>
                                        <span class="font-medium">
                                        {{ number_format($medicine->stock * $medicine->cost_price, 2) }}
                                    </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status</span>
                                        <span
                                            class="font-medium {{ $medicine->is_active ? 'text-emerald-600' : 'text-gray-600' }}">
                                        {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <h4 class="font-semibold text-gray-900 mb-4">Quick Actions</h4>
                                <div class="space-y-3">
                                    <button onclick="showUpdateStockModal({{ $medicine->id }}, '{{ $medicine->name }}')"
                                            class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg font-medium hover:shadow-lg transition-all text-center">
                                        <i class="fas fa-box mr-2"></i>Update Stock
                                    </button>
                                    <a href="{{ route('pharmacy.inventory.edit', $medicine) }}"
                                       class="block px-4 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg font-medium hover:shadow-lg transition-all text-center">
                                        <i class="fas fa-edit mr-2"></i>Edit Medicine
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include update stock modal from inventory index -->
    @include('pharmacy.inventory.partials.update-stock-modal')
@endsection

@push('scripts')
    <script>
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                // Update active tab
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'bg-white', 'text-indigo-600', 'shadow-sm', 'border', 'border-slate-200');
                    btn.classList.add('text-slate-400', 'hover:text-indigo-600');
                });

                this.classList.add('active', 'bg-white', 'text-indigo-600', 'shadow-sm', 'border', 'border-slate-200');
                this.classList.remove('text-slate-400', 'hover:text-indigo-600');

                // Show active content
                const tabId = this.id.replace('-tab', '-content');
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });

                document.getElementById(tabId).classList.remove('hidden');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Include update stock modal functionality
        function showUpdateStockModal(medicineId, medicineName) {
            currentMedicineId = medicineId;
            document.getElementById('modalMedicineName').textContent = medicineName;
            document.getElementById('updateStockForm').action = `/pharmacy/inventory/${medicineId}/update-stock`;
            document.getElementById('updateStockModal').classList.remove('hidden');
            document.getElementById('updateStockModal').classList.add('flex');

            fetch(`/api/pharmacy/medicines/${medicineId}/stock`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('currentStock').textContent = data.stock;
                });
        }

        function closeUpdateStockModal() {
            document.getElementById('updateStockModal').classList.add('hidden');
            document.getElementById('updateStockModal').classList.remove('flex');
        }
    </script>
@endpush
