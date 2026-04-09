@extends('layouts.app')

@section('title', 'Pending Prescriptions')
@section('page-title', 'Pending Prescriptions')
@section('page-description', 'Review and dispense pending prescriptions')

@section('content')
    <div x-data="{ viewMode: 'grid' }" class="space-y-6">
        <!-- Header with Stats -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pending Prescriptions</h1>
                    <p class="text-gray-600 mt-1">Review and dispense medicines to patients</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Standardized View Toggle --}}
                    <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm h-11">
                        <button @click="viewMode = 'table'"
                            :class="viewMode === 'table' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300 text-sm font-bold group">
                            <i class="fas fa-list-ul" :class="viewMode === 'table' ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'"></i>
                            Table
                        </button>
                        <button @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-300 text-sm font-bold group">
                            <i class="fas fa-th-large" :class="viewMode === 'grid' ? 'text-white' : 'text-gray-400 group-hover:text-indigo-600'"></i>
                            Grid
                        </button>
                    </div>

                    <div class="relative">
                        <input type="text"
                               id="search-prescriptions"
                               placeholder="Search patients or medicines..."
                               class="pl-10 pr-4 py-2 border border-blue-100 bg-blue-50/30 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64 text-sm transition-all">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions Content -->
        <div>
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($prescriptions as $prescription)
                    <div
                        class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                        <!-- Prescription Header -->
                        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-lg group-hover:text-indigo-600 transition-colors">{{ $prescription->medicine->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-sm text-gray-600">{{ $prescription->medicine->strength }}</span>
                                        <span
                                            class="text-[10px] uppercase font-black px-2 py-1 rounded-full {{ $prescription->priority == 'urgent' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ ucfirst($prescription->priority) }}
                                </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-indigo-600">{{ $prescription->quantity }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">Quantity</div>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Information -->
                        <div class="p-6">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-xl flex items-center justify-center text-indigo-600 shadow-inner">
                                        <i class="fas fa-user-injured text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900">{{ $prescription->diagnosis->visit->patient->name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs font-black uppercase text-gray-500 tracking-tighter">EMRN: {{ $prescription->diagnosis->visit->patient->emrn }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-gray-100 text-gray-800 font-black uppercase">
                                    {{ ucfirst($prescription->diagnosis->visit->patient->gender) }}
                                </span>
                                    </div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-widest">
                                        <i class="fas fa-user-md mr-1 text-indigo-300"></i>
                                        {{ $prescription->prescriber->name ?? 'Unknown Doctor' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Prescription Details -->
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dosage</div>
                                        <div class="text-sm font-bold text-gray-800">{{ $prescription->dosage }}</div>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Frequency</div>
                                        <div class="text-sm font-bold text-gray-800">{{ $prescription->frequency }}</div>
                                    </div>
                                </div>

                                <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                                    <div class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Instructions</div>
                                    <div
                                        class="text-xs font-medium text-gray-800">{{ $prescription->instructions ?? 'No special instructions' }}</div>
                                </div>

                                <!-- Stock Information -->
                                <div
                                    class="{{ $prescription->medicine->stock < $prescription->quantity ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-emerald-50 border border-emerald-200 text-emerald-800' }} p-3 rounded-xl transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div
                                                class="text-[10px] font-black uppercase tracking-widest">
                                                Stock Status
                                            </div>
                                            <div
                                                class="text-xs font-bold">
                                                {{ $prescription->medicine->stock }} units available
                                            </div>
                                        </div>
                                        @if($prescription->medicine->stock < $prescription->quantity)
                                            <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                                        @else
                                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <button onclick="showDispenseModal({{ $prescription->id }})"
                                        class="flex-1 px-4 py-3 bg-gradient-to-r {{ $prescription->medicine->stock < $prescription->quantity ? 'from-gray-400 to-gray-500 cursor-not-allowed' : 'from-indigo-600 to-blue-600 hover:shadow-lg hover:shadow-indigo-500/30' }} text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all"
                                    {{ $prescription->medicine->stock < $prescription->quantity ? 'disabled' : '' }}>
                                    <i class="fas fa-pills mr-2"></i>
                                    Dispense Now
                                </button>
                                <a href="{{ route('pharmacy.prescriptions.show', $prescription) }}"
                                   class="w-12 h-12 flex items-center justify-center bg-white text-indigo-600 rounded-xl font-bold border border-indigo-100 hover:bg-indigo-50 transition-all shadow-sm group-hover:scale-105">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-12 text-center">
                            <i class="fas fa-prescription text-5xl text-gray-300 mb-6"></i>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">No Pending Prescriptions</h3>
                            <p class="text-gray-500">All prescriptions have been dispensed.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Table View -->
            <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Patient / EMRN</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Medicine</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qty</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Stock Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Priority</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($prescriptions as $prescription)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $prescription->diagnosis->visit->patient->name }}</div>
                                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">EMRN: {{ $prescription->diagnosis->visit->patient->emrn }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-indigo-600">{{ $prescription->medicine->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $prescription->medicine->strength }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-gray-700">
                                        {{ $prescription->quantity }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($prescription->medicine->stock < $prescription->quantity)
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-tight">Low Stock ({{ $prescription->medicine->stock }})</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-tight">Available ({{ $prescription->medicine->stock }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-gray-700">
                                        @if($prescription->priority == 'urgent')
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-black uppercase">Urgent</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-black uppercase">Normal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="showDispenseModal({{ $prescription->id }})" 
                                                class="h-9 px-4 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all disabled:opacity-50"
                                                {{ $prescription->medicine->stock < $prescription->quantity ? 'disabled' : '' }}>
                                                Dispense
                                            </button>
                                            <a href="{{ route('pharmacy.prescriptions.show', $prescription) }}" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($prescriptions->hasPages())
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>

    <!-- Dispense Modal -->
    <div id="dispenseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full animate-slide-in">
            <form id="dispenseForm" method="POST">
                @csrf
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Dispense Medicine</h3>
                    <p class="text-gray-600 mt-1" id="medicineName"></p>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Prescription Details -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="text-xs text-blue-700">Required Quantity</div>
                                <div class="font-bold text-blue-900 text-lg" id="requiredQuantity"></div>
                            </div>
                            <div>
                                <div class="text-xs text-blue-700">Available Stock</div>
                                <div class="font-bold text-blue-900 text-lg" id="availableStock"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Dispense Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity to Dispense</label>
                        <input type="number"
                               name="dispensed_quantity"
                               id="quantityDispensed"
                               min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <!-- Batch Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number (Optional)</label>
                        <input type="text"
                               name="batch_number"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dispense Notes (Optional)</label>
                        <textarea name="dispense_notes"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any special instructions..."></textarea>
                    </div>

                    <!-- Confirmation -->
                    <div class="flex items-start">
                        <input type="checkbox"
                               name="confirm_stock"
                               id="confirmStock"
                               class="mt-1 mr-3"
                               required>
                        <label for="confirmStock" class="text-sm text-gray-700">
                            I confirm that the stock is physically available and will be dispensed.
                        </label>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-100 flex gap-3">
                    <button type="button"
                            onclick="closeDispenseModal()"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all flex-1">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg font-bold hover:shadow-lg transition-all flex-1">
                        <i class="fas fa-pills mr-2"></i>
                        Confirm Dispense
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPrescriptionId = null;

        function showDispenseModal(prescriptionId) {
            const prescription = @json($prescriptions->items());
            const selected = prescription.find(p => p.id === prescriptionId);

            if (!selected) return;

            currentPrescriptionId = prescriptionId;
            document.getElementById('medicineName').textContent = selected.medicine.name;
            document.getElementById('requiredQuantity').textContent = selected.quantity;
            document.getElementById('availableStock').textContent = selected.medicine.stock;
            document.getElementById('quantityDispensed').value = selected.quantity;
            document.getElementById('quantityDispensed').max = selected.quantity;

            // Update form action
            document.getElementById('dispenseForm').action = `/pharmacy/dispense/${prescriptionId}`;

            // Show modal
            document.getElementById('dispenseModal').classList.remove('hidden');
            document.getElementById('dispenseModal').classList.add('flex');
        }

        function closeDispenseModal() {
            document.getElementById('dispenseModal').classList.add('hidden');
            document.getElementById('dispenseModal').classList.remove('flex');
            currentPrescriptionId = null;
        }

        // Close modal on outside click
        document.getElementById('dispenseModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeDispenseModal();
            }
        });

        // Search functionality
        document.getElementById('search-prescriptions')?.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.prescription-card');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    </script>
@endpush
