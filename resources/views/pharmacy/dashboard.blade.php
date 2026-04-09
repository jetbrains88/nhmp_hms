@extends('layouts.app')

@section('title', 'Pharmacy Dashboard - NHMP HMS')
@section('page-title', 'Pharmacy Dashboard')
@section('page-description', 'Real-time overview of pharmaceutical operations and inventory')

@section('content')
<div class="space-y-10 pb-12">
    {{-- ═══════════════════════════════════════════════
         STATS OVERVIEW - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4 pt-8">
        
        <!-- Pending Prescriptions Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl shadow-lg shadow-orange-500/10 border border-orange-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-6 h-14 w-14 grid place-items-center rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 shadow-lg shadow-orange-900/20 border border-orange-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-prescription text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-6 text-right pt-6">
                <p class="text-[10px] font-black tracking-widest text-orange-500 uppercase opacity-70">Pending Orders</p>
                <h4 class="text-4xl font-black text-orange-700 drop-shadow-sm font-mono mt-1">{{ $stats['pending_prescriptions'] }}</h4>
            </div>
            <div class="mx-6 mb-6 border-t border-orange-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <span class="text-[10px] text-orange-700 font-black uppercase tracking-tight">Requires Attention</span>
                </div>
                <a href="{{ route('pharmacy.prescriptions.index') }}" class="text-[10px] font-black text-orange-600 uppercase hover:underline">View All</a>
            </div>
        </div>

        <!-- Dispensed Today Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-6 h-14 w-14 grid place-items-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-pills text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-6 text-right pt-6">
                <p class="text-[10px] font-black tracking-widest text-emerald-500 uppercase opacity-70">Dispensed Today</p>
                <h4 class="text-4xl font-black text-emerald-700 drop-shadow-sm font-mono mt-1">{{ $stats['dispensed_today'] }}</h4>
            </div>
            <div class="mx-6 mb-6 border-t border-emerald-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-emerald-700 font-black">
                    <i class="fas fa-check-circle text-xs"></i>
                    <span class="text-[10px] uppercase tracking-tight">Operational Today</span>
                </div>
                <a href="{{ route('pharmacy.dispense.history') }}" class="text-[10px] font-black text-emerald-600 uppercase hover:underline">History</a>
            </div>
        </div>

        <!-- Inventory Value Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-6 h-14 w-14 grid place-items-center rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-wallet text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-6 text-right pt-6">
                <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Inventory Value</p>
                <h4 class="text-2xl font-black text-blue-700 drop-shadow-sm font-mono mt-1">Rs. {{ number_format($stats['total_stock_value'], 0) }}</h4>
            </div>
            <div class="mx-6 mb-6 border-t border-blue-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-blue-700 font-black uppercase tracking-tight">Across {{ $stats['total_medicines'] }} Items</span>
                </div>
                <a href="{{ route('pharmacy.inventory') }}" class="text-[10px] font-black text-blue-600 uppercase hover:underline">Audit</a>
            </div>
        </div>

        <!-- Low Stock Alerts Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-pink-50 rounded-3xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-6 h-14 w-14 grid place-items-center rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-600 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-exclamation-triangle text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-6 text-right pt-6">
                <p class="text-[10px] font-black tracking-widest text-rose-500 uppercase opacity-70">Critical Alerts</p>
                <h4 class="text-4xl font-black text-rose-700 drop-shadow-sm font-mono mt-1">{{ $stats['low_stock_items'] }}</h4>
            </div>
            <div class="mx-6 mb-6 border-t border-rose-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                    <span class="text-[10px] text-rose-700 font-black uppercase tracking-tight">Stock Warnings</span>
                </div>
                <a href="{{ route('pharmacy.alerts.index') }}" class="text-[10px] font-black text-rose-600 uppercase hover:underline">Refill</a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN DASHBOARD GRID
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Side: Recent Activity --}}
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-white p-8 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <span class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                                <i class="fas fa-history"></i>
                            </span>
                            Recent Dispensations
                        </h2>
                        <p class="text-gray-500 text-sm font-medium mt-1">Real-time log of the last 10 fulfilled prescriptions</p>
                    </div>
                    <a href="{{ route('pharmacy.dispense.history') }}" class="px-6 py-3 bg-white border-2 border-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                        View Full Logs
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Patient / EMRN</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Medicine</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentDispenses as $prescription)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-emerald-100/50 flex items-center justify-center text-emerald-600 font-bold shadow-sm transition-transform group-hover:scale-110">
                                                {{ strtoupper(substr($prescription->diagnosis->visit->patient->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $prescription->diagnosis->visit->patient->name }}</div>
                                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">EMRN: {{ $prescription->diagnosis->visit->patient->emrn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-indigo-600">{{ $prescription->medicine->name }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $prescription->dosage }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-black font-mono">
                                            {{ $prescription->dispensed_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="text-xs font-bold text-slate-800">{{ $prescription->updated_at->diffForHumans() }}</div>
                                        <div class="text-[10px] font-medium text-slate-400">{{ $prescription->updated_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                                <i class="fas fa-history text-4xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-400">No recent transactions</h3>
                                                <p class="text-slate-300 text-sm">Activities will appear here once dispensed.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <a href="{{ route('pharmacy.prescriptions.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2.5rem] p-8 shadow-xl hover:shadow-indigo-500/40 transition-all duration-500 transform hover:-translate-y-2">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-white backdrop-blur-md shadow-inner">
                            <i class="fas fa-prescription-bottle-alt text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white leading-tight">Dispense Order</h3>
                            <p class="text-indigo-100 text-sm mt-2 opacity-80 font-medium">Process pending patient prescriptions globally.</p>
                        </div>
                        <div class="flex items-center gap-2 text-white font-black text-[10px] uppercase tracking-widest">
                            Go To Queue <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('pharmacy.inventory') }}" class="group relative overflow-hidden bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[2.5rem] p-8 shadow-xl hover:shadow-emerald-500/40 transition-all duration-500 transform hover:-translate-y-2">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-white backdrop-blur-md shadow-inner">
                            <i class="fas fa-boxes text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white leading-tight">Stock Management</h3>
                            <p class="text-emerald-100 text-sm mt-2 opacity-80 font-medium">Audit batches, categories and medicine forms.</p>
                        </div>
                        <div class="flex items-center gap-2 text-white font-black text-[10px] uppercase tracking-widest">
                            Open Warehouse <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Right Side: Intelligence & Alerts --}}
        <div class="lg:col-span-4 space-y-8">
            {{-- Low Stock Intelligence --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gradient-to-br from-rose-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-600 shadow-sm border border-rose-50">
                            <i class="fas fa-sensor-alert"></i>
                        </div>
                        <h3 class="text-base font-black text-gray-900 tracking-tight flex items-center gap-2 uppercase">
                            Low Stock
                            <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full">{{ count($alerts['low_stock']) }}</span>
                        </h3>
                    </div>
                </div>
                <div class="p-2 space-y-1 max-h-[400px] overflow-y-auto">
                    @forelse($alerts['low_stock'] as $medicine)
                        <div class="p-4 rounded-3xl hover:bg-rose-50 transition-all group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 group-hover:bg-rose-600 group-hover:text-white transition-colors shadow-inner">
                                <i class="fas fa-pills text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-bold text-slate-800 text-sm truncate uppercase tracking-tight">{{ $medicine->name }}</h4>
                                    <span class="text-xs font-black text-rose-600 font-mono">{{ $medicine->stock }}</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-1">
                                    <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: {{ max(10, min(100, ($medicine->stock / $medicine->reorder_level) * 100)) }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    <span>Current Level</span>
                                    <span>Req: {{ $medicine->reorder_level }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 font-bold uppercase text-xs tracking-widest">
                            <i class="fas fa-check-double text-emerald-500 mb-2"></i><br>
                            All Stocked
                        </div>
                    @endforelse
                </div>
                @if(count($alerts['low_stock']) > 0)
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="{{ route('pharmacy.alerts.index') }}" class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.2em] hover:text-indigo-800 transition-colors">See Refill Strategy <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                @endif
            </div>

            {{-- Expiry Watch --}}
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-gradient-to-br from-amber-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-600 shadow-sm border border-amber-50">
                            <i class="fas fa- hourglass-half"></i>
                        </div>
                        <h3 class="text-base font-black text-gray-900 tracking-tight flex items-center gap-2 uppercase">
                            Expiry Watch
                        </h3>
                    </div>
                </div>
                <div class="p-2 space-y-1">
                    @forelse($alerts['expiring_soon'] as $batch)
                        <div class="p-4 rounded-3xl hover:bg-amber-50 transition-all group flex items-start gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-inner">
                                <i class="fas fa-calendar-times text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate uppercase tracking-tight">{{ $batch->name }}</h4>
                                <div class="flex items-center justify-between mt-1">
                                    <div class="text-[10px] bg-white border border-amber-100 text-amber-700 px-2 py-0.5 rounded-lg font-bold">
                                        {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d M, Y') }}
                                    </div>
                                    <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest">{{ \Carbon\Carbon::parse($batch->expiry_date)->diffForHumans() }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-2 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    <i class="fas fa-layer-group text-xs"></i> Batch: {{ $batch->batch_number }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 font-bold uppercase text-xs tracking-widest">
                            <i class="fas fa-shield-check text-indigo-500 mb-2"></i><br>
                            Safety Guaranteed
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

