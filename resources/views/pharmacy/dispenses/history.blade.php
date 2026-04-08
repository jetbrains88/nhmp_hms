@extends('layouts.app')

@section('title', 'Dispense History - NHMP HMS')
@section('page-title', 'Dispense History')
@section('breadcrumb', 'Pharmacy / Dispense History')

@section('content')
<div class="space-y-8 relative">

    {{-- ═══════════════════════════════════════════════
         STATS CARDS - Vibrant Premium Style
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-10 mt-4 pt-8">

        <!-- Total Dispenses Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-pills text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Total Dispenses</p>
                <h4 class="text-3xl font-black text-blue-700 drop-shadow-sm font-mono">{{ $dispenses->total() }}</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-blue-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[9px] text-blue-700 font-black uppercase tracking-tight">All Time Records</span>
                </div>
            </div>
        </div>

        <!-- Total Patients Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-users text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-emerald-500 uppercase opacity-70">Patients Served</p>
                <h4 class="text-3xl font-black text-emerald-700 drop-shadow-sm font-mono">{{ $dispenses->unique('patient_id')->count() }}</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-emerald-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                    <span class="text-[9px] text-emerald-700 font-black uppercase tracking-tight">Unique Patients</span>
                </div>
            </div>
        </div>

        <!-- Prescribers Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-md text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-purple-500 uppercase opacity-70">Prescribers</p>
                <h4 class="text-3xl font-black text-purple-700 drop-shadow-sm font-mono">{{ $dispenses->unique('prescriber_id')->count() }}</h4>
            </div>
            <div class="mx-4 mb-4 border-t border-purple-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600"></span>
                    <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">Active Doctors</span>
                </div>
            </div>
        </div>

        <!-- Filter Period Card -->
        <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group">
            <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-lg shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-calendar-alt text-xl text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-4">
                <p class="text-[10px] font-black tracking-widest text-amber-500 uppercase opacity-70">Current Filter</p>
                <h4 class="text-lg font-black text-amber-700 drop-shadow-sm leading-tight mt-1">
                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d') : 'All' }}
                    {{ request('date_to') ? '— ' . \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Time' }}
                </h4>
            </div>
            <div class="mx-4 mb-4 border-t border-amber-100 pt-2">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                    <span class="text-[9px] text-amber-700 font-black uppercase tracking-tight">Date Window</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MAIN CONTROL PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

        {{-- Left Column - Table --}}
        <div class="lg:col-span-9 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">

                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-blue-100 shadow-sm">
                                <i class="fas fa-history text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight flex items-center gap-3">
                                    Dispense History
                                    <span class="text-lg font-normal text-gray-500">
                                        ({{ $dispenses->total() }} records)
                                    </span>
                                </h2>
                                <p class="text-gray-500 text-sm font-medium mt-1">Complete log of all dispensed medications across all patients.</p>
                            </div>
                        </div>
                        <a href="{{ route('pharmacy.dispense.history') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-blue-100 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors shadow-sm" title="Reset Filters">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white border-b border-blue-50">
                            <tr>
                                <th class="px-5 py-4 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-calendar-alt text-[10px]"></i>
                                        </div>
                                        Date & Time
                                    </div>
                                </th>
                                <th class="px-5 py-4 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-user text-[10px]"></i>
                                        </div>
                                        Patient
                                    </div>
                                </th>
                                <th class="px-5 py-4 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-pills text-[10px]"></i>
                                        </div>
                                        Medicine / Batch
                                    </div>
                                </th>
                                <th class="px-5 py-4 text-center border-b border-slate-50">
                                    <div class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-cubes text-[10px]"></i>
                                        </div>
                                        Qty
                                    </div>
                                </th>
                                <th class="px-5 py-4 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-user-md text-[10px]"></i>
                                        </div>
                                        Prescriber
                                    </div>
                                </th>
                                <th class="px-5 py-4 border-b border-slate-50">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm border border-blue-100">
                                            <i class="fas fa-user-check text-[10px]"></i>
                                        </div>
                                        Dispensed By
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($dispenses as $dispense)
                                <tr class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-800">{{ $dispense->dispensed_at->format('M d, Y') }}</span>
                                            <span class="text-[10px] text-slate-400 font-bold font-mono">{{ $dispense->dispensed_at->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-700 font-black text-sm shrink-0">
                                                {{ strtoupper(substr($dispense->diagnosis->visit->patient->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-bold text-slate-800">{{ $dispense->diagnosis->visit->patient->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800">{{ $dispense->medicine->name }}</span>
                                            <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider w-fit mt-0.5">{{ $dispense->batch_number }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 border-b border-slate-50 text-center">
                                        <span class="text-base font-black text-indigo-700 font-mono">{{ $dispense->dispensed_quantity }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">{{ $dispense->medicine->unit }}</span>
                                    </td>
                                    <td class="px-5 py-4 border-b border-slate-50">
                                        <span class="text-sm font-bold text-slate-600">{{ $dispense->prescriber->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center">
                                                <i class="fas fa-user-check text-slate-500 text-[10px]"></i>
                                            </div>
                                            <span class="text-sm font-bold text-slate-600">{{ $dispense->dispenser->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-32 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 mb-6 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 shadow-inner">
                                                <i class="fas fa-history text-5xl"></i>
                                            </div>
                                            <h3 class="text-xl font-black text-slate-400">No Dispense History</h3>
                                            <p class="text-slate-400 mt-2 font-medium max-w-sm">No medications have been dispensed matching your filter criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($dispenses->hasPages())
                    <div class="p-6 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Showing {{ $dispenses->firstItem() }}–{{ $dispenses->lastItem() }} of <span class="text-blue-600">{{ $dispenses->total() }}</span> records
                            </div>
                            {{ $dispenses->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Sticky Filter Sidebar --}}
        <div class="lg:col-span-3 lg:sticky lg:top-8 lg:max-h-[calc(100vh-80px)] lg:overflow-y-auto pb-2" style="scrollbar-width: none;">
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                            <i class="fas fa-filter text-sm"></i>
                        </div>
                        <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Filters</h2>
                    </div>
                </div>

                <form method="GET" action="{{ route('pharmacy.dispense.history') }}" class="p-6 space-y-6">
                    {{-- Date From --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest" for="date_from">Date From</label>
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 transition-all font-bold text-xs text-slate-600 outline-none">
                        </div>
                    </div>

                    {{-- Date To --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest" for="date_to">Date To</label>
                        <div class="relative">
                            <i class="fas fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 transition-all font-bold text-xs text-slate-600 outline-none">
                        </div>
                    </div>

                    {{-- Medicine --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest" for="medicine_id">Medicine</label>
                        <div class="relative">
                            <i class="fas fa-pills absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <select id="medicine_id" name="medicine_id"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 transition-all font-bold text-xs text-slate-600 outline-none appearance-none">
                                <option value="">All Medicines</option>
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}" {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                        {{ $medicine->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 pt-4 border-t border-slate-100">
                        <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:shadow-lg transition-all flex items-center justify-between px-6">
                            <span>Apply Filters</span>
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('pharmacy.dispense.history') }}"
                            class="w-full py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all flex items-center justify-between px-6">
                            <span>Clear Filters</span>
                            <i class="fas fa-broom"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
