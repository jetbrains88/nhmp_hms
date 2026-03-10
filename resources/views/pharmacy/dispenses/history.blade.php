@extends('layouts.app')

@section('title', 'Dispense History')
@section('page-title', 'Pharmacy Reports')
@section('breadcrumb', 'Dispense History')

@section('content')
    <div class="space-y-6">
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Dispense History</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="medicine_id" class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                    <select name="medicine_id" id="medicine_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Medicines</option>
                        @foreach($medicines as $medicine)
                            <option
                                value="{{ $medicine->id }}" {{ request('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                {{ $medicine->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg w-full">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-pills text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $dispenses->total() }}</div>
                        <div class="text-sm text-gray-600">Total Dispenses</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl p-6 border border-green-100">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gray-100 border border-gray-200 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            ${{ number_format($dispenses->sum('total_amount') ?? 0, 2) }}
                        </div>
                        <div class="text-sm text-gray-600">Total Revenue</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-user-md text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $dispenses->unique('prescriber_id')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Prescribers</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-100">
                <div class="flex items-center">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ $dispenses->unique('patient_id')->count() }}
                        </div>
                        <div class="text-sm text-gray-600">Patients</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispense History Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Dispense History</h3>
                <p class="text-gray-600 mt-1">Complete history of all dispensed medications</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                </div>
                                <span>Date & Time</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <span>Patient</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-pills text-xs"></i>
                                </div>
                                <span>Medicine</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-cubes text-xs"></i>
                                </div>
                                <span>Quantity</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-user-md text-xs"></i>
                                </div>
                                <span>Prescriber</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center gap-2.5 text-xs font-black text-gray-700 uppercase tracking-widest">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                                    <i class="fas fa-user-check text-xs"></i>
                                </div>
                                <span>Dispensed By</span>
                            </div>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dispenses as $dispense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium font-mono">
                                {{ $dispense->dispensed_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 border border-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 text-[10px] font-black uppercase">
                                        {{ strtoupper(substr($dispense->diagnosis->visit->patient->name, 0, 2)) }}
                                    </span>
                                    </div>
                                    <div class="text-sm font-bold text-navy-800">
                                        {{ $dispense->diagnosis->visit->patient->name }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-navy-800">{{ $dispense->medicine->name }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $dispense->batch_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">
                                {{ $dispense->dispensed_quantity }} {{ $dispense->medicine->unit }}
                            </td>
                            {{--                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">--}}
                            {{--                                ${{ number_format($dispense->total_amount, 2) }}--}}
                            {{--                            </td>--}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-navy-800">
                                    {{ $dispense->prescriber->name ?? 'Unknown' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-gray-100 border border-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-md text-gray-600 text-xs"></i>
                                    </div>
                                    <div class="text-sm font-bold text-navy-800">
                                        {{ $dispense->dispenser->name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400 text-5xl mb-4">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-600 mb-2">No Dispense History Found</h3>
                                <p class="text-gray-500">No medications have been dispensed yet.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($dispenses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $dispenses->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
