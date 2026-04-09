@extends('layouts.app')

@section('title', 'Bulk Upload Inventory')
@section('page-title', 'Bulk Upload')
@section('breadcrumb', 'Pharmacy / Inventory / Bulk Upload')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="bulkUploadManager()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600">
                Bulk Upload Inventory
            </h1>
            <p class="text-slate-500 text-sm mt-1">Upload CSV or Excel files to bulk insert inventory batches.</p>
        </div>
        <a href="{{ route('pharmacy.inventory') }}"
            class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-bold shadow-sm hover:shadow hover:border-slate-300 transition-all cursor-pointer">
            <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
        </a>
    </div>

    <!-- Step 1: Upload File -->
    <div x-show="step === 1" class="transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center bg-gradient-to-br from-indigo-50/50 to-white relative overflow-hidden"
            @dragover.prevent="dragover = true"
            @dragleave.prevent="dragover = false"
            @drop.prevent="handleDrop($event)"
            :class="dragover ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200'">
            
            <div class="max-w-md mx-auto relative z-10">
                <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-sm">
                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Upload your CSV</h3>
                <p class="text-slate-500 mb-8 text-sm leading-relaxed">
                    Drag and drop your CSV file here, or click to browse. Ensure your CSV has columns: <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">medicine_name</span>, <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">batch_number</span>, <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">expiry_date</span>, <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">stock</span>, <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">unit_price</span>, <span class="font-mono bg-slate-100 text-slate-700 px-1 py-0.5 rounded text-xs">sale_price</span>
                </p>
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <label for="csvFileInput" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all cursor-pointer">
                        <i class="fas fa-folder-open"></i> Browse Files
                        <input type="file" id="csvFileInput" accept=".csv" class="hidden" @change="handleFileSelect($event)">
                    </label>
                    <a href="{{ route('pharmacy.inventory.bulk-upload-template') }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-bold shadow-sm hover:shadow hover:border-slate-300 transition-all cursor-pointer">
                        <i class="fas fa-download text-emerald-600"></i> Download Template
                    </a>
                </div>
            </div>
            
            <!-- Abstract Shapes Pattern -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-200/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-purple-200/30 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <div class="mt-6 bg-amber-50/80 border border-amber-200 rounded-xl p-4 flex gap-3 shadow-sm">
            <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">CSV Guidelines</h4>
                <ul class="text-amber-800/80 text-xs list-disc pl-4 space-y-1">
                    <li>Dates must be in standard format (e.g., YYYY-MM-DD).</li>
                    <li>The <span class="font-semibold">medicine_name</span> must exactly match an active medicine name in your branch.</li>
                    <li>Fields 'unit_price' and 'remarks' are optional.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Step 2: Validate and Edit Data -->
    <div x-show="step === 2" x-cloak class="transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-[75vh]">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        <i class="fas fa-table text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800">Preview & Validate Data</h2>
                        <p class="text-xs text-slate-500"><span x-text="parsedData.length"></span> rows imported. Correct any errors before submission.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="reset()" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="validateAndSubmit()"
                        :disabled="isSubmitting"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 transition-colors flex items-center gap-2">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="isSubmitting ? 'Uploading...' : 'Confirm Upload'"></span>
                    </button>
                </div>
            </div>

            <!-- Error Banner -->
            <div x-show="validationErrors.length > 0" class="bg-red-50 p-3 flex items-start gap-3 border-b border-red-100 text-sm">
                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                <div class="text-red-800">
                    <p class="font-bold">Errors found in validation:</p>
                    <ul class="list-disc pl-4 mt-1 space-y-0.5">
                        <template x-for="(error, index) in validationErrors" :key="index">
                            <li x-text="error" class="text-xs"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="flex-1 overflow-x-auto p-4 custom-scrollbar bg-slate-50/50">
                <table class="w-full text-left whitespace-nowrap text-sm border-collapse">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 rounded-tl-lg shadow-sm">Row</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 shadow-sm"><i class="fas fa-pills mr-1 text-slate-400"></i> Medicine Name (Required)</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 shadow-sm"><i class="fas fa-tag mr-1 text-slate-400"></i> Batch # (Required)</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 shadow-sm"><i class="fas fa-calendar-times mr-1 text-slate-400"></i> Expiry (Required)</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 shadow-sm"><i class="fas fa-cubes mr-1 text-slate-400"></i> Stock (Required)</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 shadow-sm"><i class="fas fa-money-bill-wave mr-1 text-slate-400"></i> Sale Price (Required)</th>
                            <th class="px-3 py-2 bg-white border border-slate-200 font-semibold text-slate-600 rounded-tr-lg shadow-sm">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in parsedData" :key="index">
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm text-center font-mono text-slate-400" x-text="index + 1"></td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm relative">
                                    <input type="text" x-model="row.medicine_name" @input="verifyName(row)" class="w-40 text-sm p-1 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent text-slate-800 font-medium" placeholder="e.g. Paracetamol 500mg">
                                    <template x-if="row.medicine_id">
                                        <i class="fas fa-check-circle text-emerald-500 absolute right-2 top-3 text-xs" title="Matched"></i>
                                    </template>
                                    <template x-if="!row.medicine_id && row.medicine_name">
                                        <i class="fas fa-times-circle text-red-500 absolute right-2 top-3 text-xs" title="Not Found"></i>
                                    </template>
                                </td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm">
                                    <input type="text" x-model="row.batch_number" class="w-full text-sm p-1 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent text-slate-800">
                                </td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm">
                                    <input type="date" x-model="row.expiry_date" class="w-32 text-sm p-1 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent text-slate-800">
                                </td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm">
                                    <input type="number" x-model="row.stock" class="w-20 text-sm p-1 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent text-slate-800 text-right">
                                </td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm">
                                    <div class="flex items-center">
                                        <span class="text-slate-400 text-xs px-1">Rs</span>
                                        <input type="number" step="0.01" x-model="row.sale_price" class="w-24 text-sm p-1 border-0 border-b border-transparent focus:border-indigo-500 focus:ring-0 bg-transparent text-slate-800 text-right">
                                    </div>
                                </td>
                                <td class="px-3 py-2 border border-slate-200 bg-white shadow-sm text-center">
                                    <button @click="removeRow(index)" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div class="p-4 flex justify-center">
                    <button @click="addRow()" class="px-4 py-2 border border-dashed border-slate-300 text-slate-500 rounded-xl text-sm font-bold hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Row Manually
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bulkUploadManager', () => ({
            step: 1,
            dragover: false,
            parsedData: [],
            isSubmitting: false,
            validationErrors: [],
            medicinesMap: @json($medicines->keyBy('name')->toArray()), // { "Paracetamol 500mg": {id: 1, name: "Paracetamol 500mg"} }
            
            handleDrop(e) {
                this.dragover = false;
                const file = e.dataTransfer.files[0];
                if (file) this.processFile(file);
            },
            
            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) this.processFile(file);
            },
            
            processFile(file) {
                Papa.parse(file, {
                    header: true,
                    skipEmptyLines: true,
                    complete: (results) => {
                        this.parsedData = results.data.map(row => {
                            // Trim keys to avoid issues with spacing
                            const cleanRow = {};
                            Object.keys(row).forEach(k => cleanRow[k.trim()] = (row[k] || '').trim());

                            const medicineName = cleanRow.medicine_name || '';
                            const matchedMed = this.medicinesMap[medicineName];

                            return {
                                medicine_name: medicineName,
                                medicine_id: matchedMed ? matchedMed.id : null,
                                batch_number: cleanRow.batch_number || '',
                                expiry_date: cleanRow.expiry_date || '',
                                stock: cleanRow.stock ? parseInt(cleanRow.stock) : 0,
                                sale_price: cleanRow.sale_price ? parseFloat(cleanRow.sale_price) : 0,
                                unit_price: cleanRow.unit_price ? parseFloat(cleanRow.unit_price) : 0,
                            };
                        });
                        this.step = 2;
                        this.validateData();
                    },
                    error: (err) => {
                        Swal.fire('Error Parsing CSV', err.message, 'error');
                    }
                });
            },
            
            verifyName(row) {
                const match = this.medicinesMap[row.medicine_name];
                row.medicine_id = match ? match.id : null;
                this.validateData();
            },
            
            addRow() {
                this.parsedData.push({
                    medicine_name: '',
                    medicine_id: null,
                    batch_number: '',
                    expiry_date: '',
                    stock: 0,
                    sale_price: 0,
                });
            },
            
            removeRow(index) {
                this.parsedData.splice(index, 1);
                this.validateData();
            },
            
            validateData() {
                this.validationErrors = [];
                let hasErrors = false;
                
                this.parsedData.forEach((row, i) => {
                    const rowNum = i + 1;
                    if (!row.medicine_name) this.validationErrors.push(`Row ${rowNum}: Medicine name is missing.`);
                    else if (!row.medicine_id) this.validationErrors.push(`Row ${rowNum}: '${row.medicine_name}' not found in database.`);

                    if (!row.batch_number) this.validationErrors.push(`Row ${rowNum}: Batch number is missing.`);
                    if (!row.expiry_date) this.validationErrors.push(`Row ${rowNum}: Expiry date is missing.`);
                    if (!row.stock || row.stock <= 0) this.validationErrors.push(`Row ${rowNum}: Stock must be greater than 0.`);
                    if (row.sale_price === null || row.sale_price === '' || row.sale_price < 0) this.validationErrors.push(`Row ${rowNum}: Invalid sale price.`);
                });
                
                return this.validationErrors.length === 0;
            },
            
            async validateAndSubmit() {
                if (!this.validateData()) return;

                if (this.parsedData.length === 0) {
                    alert('No data to import.');
                    return;
                }

                this.isSubmitting = true;

                try {
                    const response = await fetch("{{ route('pharmacy.inventory.bulk-upload') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ batches: this.parsedData })
                    });

                    // Handle non-OK responses (validation errors return 422)
                    if (!response.ok) {
                        const errData = await response.json().catch(() => ({}));
                        const msg = errData.message || (errData.errors ? Object.values(errData.errors).flat().join('\n') : 'Server error occurred.');
                        if (window.Swal) {
                            Swal.fire('Upload Failed', msg, 'error');
                        } else {
                            alert('Upload Failed: ' + msg);
                        }
                        return;
                    }

                    const result = await response.json();

                    if (result.success) {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('pharmacy.inventory') }}";
                            });
                        } else {
                            alert(result.message);
                            window.location.href = "{{ route('pharmacy.inventory') }}";
                        }
                    } else {
                        const msg = result.message || 'Unknown error occurred';
                        window.Swal ? Swal.fire('Upload Failed', msg, 'error') : alert('Upload Failed: ' + msg);
                    }
                } catch (error) {
                    console.error("Bulk Upload Error:", error);
                    window.Swal ? Swal.fire('Error', 'Failed to connect to server.', 'error') : alert('Error: Failed to connect to server.');
                } finally {
                    this.isSubmitting = false;
                }
            },

            
            reset() {
                this.step = 1;
                this.parsedData = [];
                this.validationErrors = [];
                document.getElementById('csvFileInput').value = '';
            }
        }));
    });
</script>
@endpush
