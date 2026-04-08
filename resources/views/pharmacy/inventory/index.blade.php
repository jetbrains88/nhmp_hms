@extends('layouts.app')

@section('title', 'Medicine Inventory Management')
@section('page-title', 'Inventory Management')
@section('breadcrumb', 'Pharmacy / Inventory')

@section('content')
    <div x-data="inventoryManager()" x-init="init()" class="space-y-8 relative">

        <!-- Light Themed Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-4">

            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-2xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'All')">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-blue-400 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-boxes text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-blue-500 uppercase opacity-70">Total Batches</p>
                    <h4 class="text-3xl font-black text-blue-700 drop-shadow-sm font-mono" x-text="stats.total ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600" :class="{ 'animate-pulse': stats.total > 0 }"></span>
                        <span class="text-[9px] text-blue-700 font-black uppercase tracking-tight">Active Stock Batches</span>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-2xl shadow-lg shadow-amber-500/10 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'low')">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 shadow-lg shadow-amber-900/20 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-amber-600 uppercase opacity-70">Low Stock</p>
                    <h4 class="text-3xl font-black text-amber-700 drop-shadow-sm font-mono" x-text="stats.low_stock ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500" :class="{ 'animate-pulse': stats.low_stock > 0 }"></span>
                        <span class="text-[9px] text-amber-700 font-black uppercase tracking-tight">Needs Reordering</span>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50/50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'near_expiry')">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-400 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-hourglass-half text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-purple-600 uppercase opacity-70">Near Expiry</p>
                    <h4 class="text-3xl font-black text-purple-700 drop-shadow-sm font-mono" x-text="stats.near_expiry ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-500" :class="{ 'animate-pulse': stats.near_expiry > 0 }"></span>
                        <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">Requires Attention</span>
                    </div>
                </div>
            </div>

            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-red-50/50 rounded-2xl shadow-lg shadow-rose-500/10 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'out')">
                <div class="absolute -top-6 left-4 h-14 w-14 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-600 to-red-400 shadow-lg shadow-rose-900/20 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-times-circle text-xl text-white drop-shadow-md"></i>
                </div>
                <div class="p-4 text-right pt-4">
                    <p class="text-[10px] font-black tracking-widest text-rose-600 uppercase opacity-70">Out of Stock</p>
                    <h4 class="text-3xl font-black text-rose-700 drop-shadow-sm font-mono" x-text="stats.out_of_stock ?? 0"></h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-100 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500" :class="{ 'animate-pulse': stats.out_of_stock > 0 }"></span>
                        <span class="text-[9px] text-rose-700 font-black uppercase tracking-tight">Depleted Inventory</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Floating Filter Toggle --}}
        <button @click="showSidebar = true"
            x-show="!showSidebar"
            class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-white text-indigo-600 p-2.5 py-6 rounded-l-2xl shadow-[-10px_0_30px_-10px_rgba(99,102,241,0.2)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-indigo-100 group cursor-pointer"
            title="Open Filters">
            <i class="fas fa-sliders-h text-sm group-hover:rotate-90 transition-transform duration-500"></i>
            <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 text-indigo-400">Filters</span>
        </button>

        {{-- 12-Col Grid Layout --}}
        <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">

            {{-- Left Column - Table --}}
            <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col">

                    {{-- Panel Header --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-100/50">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-indigo-100 shadow-sm">
                                    <i class="fas fa-warehouse text-2xl text-indigo-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight">
                                        Inventory Management
                                        <span class="text-lg font-normal text-gray-500 ml-2">(<span x-text="pagination.total"></span> records)</span>
                                    </h2>
                                    <p class="text-gray-500 text-sm font-medium mt-1">Monitor medicine batches, expiry dates and stock levels.</p>
                                </div>
                            </div>
                            <div class="flex gap-3 items-center">
                                <div class="flex items-center gap-2 bg-white border border-indigo-100 rounded-xl px-3 py-1.5 shadow-sm">
                                    <span class="text-[9px] font-black text-slate-400 border-r border-slate-100 pr-2 uppercase font-mono">Row Density</span>
                                    <select x-model="filters.length" @change="fetchInventory()" class="bg-transparent text-indigo-600 text-[10px] font-black uppercase cursor-pointer outline-none focus:ring-0 border-none p-0 pr-4">
                                        <option value="15">15 Per Page</option>
                                        <option value="30">30 Per Page</option>
                                        <option value="50">50 Per Page</option>
                                        <option value="100">100 Per Page</option>
                                    </select>
                                </div>
                                <a href="{{ route('pharmacy.inventory.create') }}"
                                   class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg hover:scale-105 transition-all" title="Add Stock">
                                    <i class="fas fa-plus"></i> Add Stock
                                </a>
                                <button @click="fetchInventory()" :disabled="loading"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm group" title="Refresh">
                                    <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700" :class="loading ? 'animate-spin' : ''"></i>
                                </button>
                                <button @click="showSidebar = !showSidebar"
                                    class="w-10 h-10 flex items-center justify-center bg-white border border-indigo-100 text-indigo-600 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm relative"
                                    :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                    <i class="fas" :class="showSidebar ? 'fa-eye-slash' : 'fa-filter'"></i>
                                    <span x-show="hasActiveFilters()" class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 border-2 border-white rounded-full"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Batches Table --}}
                    <div class="overflow-x-auto relative min-h-[300px]">
                        {{-- Loading overlay --}}
                        <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-20 flex items-center justify-center rounded-3xl">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-inner"></div>
                                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest animate-pulse">Syncing Data...</p>
                            </div>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200" :class="density === 'condensed' ? 'condensed-table' : 'spacious-table'">
                            <thead class="bg-white border-b border-indigo-50">
                                <tr>
                                    <th class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                <i class="fas fa-pills text-[10px]"></i>
                                            </div>
                                            Medicine &amp; Batch
                                        </div>
                                    </th>
                                    <th class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                <i class="fas fa-tag text-[10px]"></i>
                                            </div>
                                            Category &amp; Form
                                        </div>
                                    </th>
                                    <th class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                <i class="fas fa-cubes text-[10px]"></i>
                                            </div>
                                            Stock Level
                                        </div>
                                    </th>
                                    <th class="px-5 py-4 border-b border-slate-50">
                                        <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                            </div>
                                            Expiration
                                        </div>
                                    </th>
                                    <th class="px-5 py-4 text-center border-b border-slate-50">
                                        <div class="flex items-center justify-center gap-2.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100">
                                                <i class="fas fa-bolt-lightning text-[10px]"></i>
                                            </div>
                                            Actions
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                    <tbody class="bg-white divide-y divide-gray-100"
                        x-show="!loading && medicines && medicines.length > 0">
                        <template x-for="batch in medicines" :key="batch.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- Medicine Info -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-base font-black text-navy-800 leading-tight"
                                            x-text="batch.medicine_name"></div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold"
                                                x-text="'#' + batch.batch_number"></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Form -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md text-[10px] w-fit"
                                            x-text="batch.category_name"></span>
                                        <span class="text-slate-500 font-bold text-[10px] uppercase tracking-wider px-1"
                                            x-text="batch.form"></span>
                                    </div>
                                </td>

                                <!-- Stock Level -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col min-w-[120px]">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <span class="text-xl font-black"
                                                :class="batch.stock <= batch.reorder_level ? 'text-rose-600' :
                                                    'text-emerald-600'"
                                                x-text="batch.stock"></span>
                                            <span
                                                class="text-[10px] text-slate-400 font-bold uppercase ml-1">Remaining</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full transition-all duration-500" :class="batch.stock_color"
                                                :style="'width: ' + batch.stock_percentage + '%'"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Expiry -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-slate-700"
                                                x-text="batch.expiry_date"></span>
                                            <template x-if="batch.is_about_to_expire">
                                                <i class="fas fa-exclamation-triangle text-amber-500 animate-pulse"
                                                    title="Near Expiry"></i>
                                            </template>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest"
                                            :class="batch.is_about_to_expire ? 'text-amber-600' : 'text-slate-400'"
                                            x-text="batch.is_about_to_expire ? 'Expiring Soon' : 'Valid Batch'"></span>
                                    </div>
                                </td>



                                <!-- Actions -->
                                <td class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a :href="batch.view_url"
                                            class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100"
                                            title="View History">
                                            <i class="fas fa-history text-[10px]"></i>
                                        </a>
                                        <button @click="showUpdateStockModal(batch.id, batch.medicine_name)"
                                            class="h-8 w-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-500 hover:text-white transition-all shadow-sm border border-amber-100"
                                            title="Adjust Stock">
                                            <i class="fas fa-sliders text-[10px]"></i>
                                        </button>
                                        <a :href="batch.edit_url"
                                            class="h-8 w-8 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-500 hover:text-white transition-all shadow-sm border border-indigo-100"
                                            title="Edit Batch">
                                            <i class="fas fa-edit text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Loading State -->
                    <tbody x-show="loading">
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-4 shadow-inner">
                                    </div>
                                    <p class="text-slate-600 font-black uppercase tracking-widest text-xs">Loading
                                        Inventory System...</p>
                                    <p class="text-[10px] text-slate-400 mt-1 font-bold">PLEASE WAIT WHILE WE SYNCHRONIZE
                                        BATCH DATA</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!medicines || medicines.length === 0)">
                        <tr>
                            <td colspan="5" class="px-6 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-24 h-24 mb-6 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 shadow-inner">
                                        <i class="fas fa-box-open text-5xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-400">Inventory Empty</h3>
                                    <p class="text-slate-300 mt-2 font-medium max-w-sm mx-auto">
                                        No active batches found matching your current filters. Start by adding new stock or
                                        adjust your search.
                                    </p>
                                    <a href="{{ route('pharmacy.inventory.create') }}"
                                        class="mt-8 inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-bold hover:shadow-lg transition-all shadow-blue-200/50">
                                        <i class="fas fa-plus-circle"></i>
                                        Add First Stock Batch
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && medicines && medicines.length > 0" class="p-6 bg-slate-50 border-t border-slate-100 rounded-b-3xl">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Displaying <span class="text-slate-900" x-text="pagination.from ?? 0"></span> - <span class="text-slate-900" x-text="pagination.to ?? 0"></span>
                        <span class="mx-2 overflow-hidden bg-slate-200 w-8 h-[2px] inline-block align-middle"></span>
                        Total: <span class="text-indigo-600" x-text="pagination.total"></span> Records
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-left text-[10px]"></i></button>
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-chevron-left text-[10px]"></i> <span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Prev</span></button>
                        <div class="flex items-center gap-1 px-1">
                            <template x-for="page in getPageRange()" :key="page">
                                <button @click="page !== '...' && changePage(page)"
                                    :class="page === pagination.current_page ? 'bg-indigo-600 text-white shadow-lg border-indigo-600 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-indigo-600'"
                                    x-text="page" class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center"></button>
                            </template>
                        </div>
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><span class="text-[8px] font-black uppercase tracking-widest hidden sm:inline">Next</span> <i class="fas fa-chevron-right text-[10px]"></i></button>
                        <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-indigo-600 hover:text-indigo-600 disabled:opacity-30 transition-all"><i class="fas fa-angles-right text-[10px]"></i></button>
                    </div>
                </div>
            </div>
        </div>{{-- /bg-white panel --}}
    </div>{{-- /left-col --}}

    {{-- Right Column - Sticky Sidebar --}}
    <div x-show="showSidebar" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-12"
         x-transition:enter-end="opacity-100 translate-x-0"
         class="lg:col-span-3 sticky top-8 max-h-[calc(100vh-100px)] overflow-y-auto custom-scrollbar pr-2 group/sidebar">
        
        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-50 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-600 shadow-sm bg-blue-50 border border-blue-100">
                        <i class="fas fa-filter text-sm"></i>
                    </div>
                    <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Filters</h2>
                </div>
                <button @click="showSidebar = false" class="text-slate-400 hover:text-blue-600 transition-colors"><i class="fas fa-times"></i></button>
            </div>

            <div class="p-6 space-y-6">
                {{-- Search Filter --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                        <i class="fas fa-search text-blue-500"></i> Search Inventory
                    </label>
                    <div class="relative group">
                        <input x-model.debounce.500ms="filters.search" @input="fetchInventory()" type="text" placeholder="Medicine, Batch No..." 
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl pl-10 pr-4 py-3 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-400 transition-all outline-none font-bold ring-0">
                        <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                </div>

                {{-- Status Category --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-shield-virus text-indigo-500"></i> Stock Status
                    </label>
                    <div class="grid grid-cols-1 gap-2">
                        <button @click="filters.stock_status = 'All'; fetchInventory()"
                            :class="filters.stock_status === 'All' ?
                                'bg-indigo-600 text-white shadow-lg shadow-indigo-200' :
                                'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                            <span>Global Data</span>
                            <i class="fas fa-globe-americas transition-opacity" :class="filters.stock_status === 'All' ? 'opacity-100' : 'opacity-40'"></i>
                        </button>
                        <button @click="filters.stock_status = 'low'; fetchInventory()"
                            :class="filters.stock_status === 'low' ?
                                'bg-amber-500 text-white shadow-lg shadow-amber-200' :
                                'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                            <span>Low Stock</span>
                            <i class="fas fa-exclamation-triangle transition-opacity" :class="filters.stock_status === 'low' ? 'opacity-100' : 'opacity-40'"></i>
                        </button>
                        <button @click="filters.stock_status = 'out'; fetchInventory()"
                            :class="filters.stock_status === 'out' ?
                                'bg-rose-500 text-white shadow-lg shadow-rose-200' :
                                'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                            <span>Out of Stock</span>
                            <i class="fas fa-times-circle transition-opacity" :class="filters.stock_status === 'out' ? 'opacity-100' : 'opacity-40'"></i>
                        </button>
                        <button @click="filters.stock_status = 'near_expiry'; fetchInventory()"
                            :class="filters.stock_status === 'near_expiry' ?
                                'bg-purple-500 text-white shadow-lg shadow-purple-200' :
                                'bg-slate-50 text-slate-600 hover:bg-slate-100 border-2 border-slate-100'"
                            class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all text-left flex items-center justify-between group">
                            <span>Expired / Near Expiry</span>
                            <i class="fas fa-calendar-times transition-opacity" :class="filters.stock_status === 'near_expiry' ? 'opacity-100' : 'opacity-40'"></i>
                        </button>
                    </div>
                </div>

                {{-- Category Select --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                        <i class="fas fa-layer-group text-blue-500"></i> Category Select
                    </label>
                    <div class="relative group">
                        <i class="fas fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"></i>
                        <select x-model="filters.category" @change="fetchInventory()" class="w-full px-4 py-3 pl-10 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs ring-0 text-slate-600 cursor-pointer appearance-none">
                            <option value="All">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Sort By Select --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Assortment</label>
                    <div class="relative group">
                        <i class="fas fa-sort-amount-down absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500"></i>
                        <select x-model="filters.sort_by" @change="fetchInventory()" class="w-full px-4 py-3 pl-10 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-400 focus:bg-white transition-all font-bold text-xs ring-0 text-slate-600 cursor-pointer appearance-none">
                            <option value="expiry_date">Expiry Date (Earliest)</option>
                            <option value="name">Medicine Name (A-Z)</option>
                            <option value="stock">Stock (Low to High)</option>
                            <option value="stock_desc">Stock (High to Low)</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none"></i>
                    </div>
                </div>

                {{-- Page Density Filter --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-compress-alt text-indigo-500"></i> Page Density
                    </label>
                    <div class="grid grid-cols-2 gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200/50">
                        <button type="button" @click="density = 'condensed'"
                            :class="density === 'condensed' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                            class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                            Condensed
                        </button>
                        <button type="button" @click="density = 'spacious'"
                            :class="density === 'spacious' ? 'bg-white text-indigo-600 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700'"
                            class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all">
                            Spacious
                        </button>
                    </div>
                </div>

                {{-- Records Per Page --}}
                <div class="space-y-3 pb-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-list-ol text-indigo-500"></i> Records Per Page
                    </label>
                    <div class="grid grid-cols-4 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                        <template x-for="limit in [10, 25, 50, 100]" :key="limit">
                            <button @click="filters.length = limit; fetchInventory()" 
                                :class="filters.length == limit ? 'bg-white text-indigo-600 shadow-sm border-0' : 'text-slate-400 hover:text-indigo-600 border-0'"
                                class="py-2 text-[9px] font-black uppercase tracking-widest rounded-lg transition-all" x-text="limit">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            
            <div class="p-6 pt-0 mt-auto flex flex-col gap-2">
                <button @click="clearFilters()" class="rose-reset-btn w-full py-5 text-white rounded-3xl text-[10px] font-black uppercase tracking-[0.3em] transition-all duration-300 flex items-center justify-center gap-3 active:scale-95">
                    <i class="fas fa-broom"></i> Reset Filters
                </button>
                <button @click="showSidebar = false" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all flex items-center justify-between px-6">
                    <span>Hide Panel</span>
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>
    </div>
</div>{{-- /12-Col Grid Layout --}}
</div>{{-- /space-y-8 --}}
@endsection

@push('scripts')
    <script>
        function inventoryManager() {
            return {
                // State
                medicines: [],
                loading: false,
                showSidebar: false,
                stats: {
                    total: 0,
                    low_stock: 0,
                    near_expiry: 0,
                    out_of_stock: 0
                },
                defaultFilters: {
                    category: 'All',
                    stock_status: 'All',
                    sort_by: 'expiry_date',
                    sort_direction: 'asc',
                    length: 10,
                    search: ''
                },
                filters: {
                    category: 'All',
                    stock_status: 'All',
                    sort_by: 'expiry_date',
                    sort_direction: 'asc',
                    length: 10,
                    search: ''
                },
                density: 'spacious',
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 0,
                    from: 0,
                    to: 0
                },

                // Initialize
                init() {
                    // Try to load filters from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    for (let key in this.filters) {
                        if (urlParams.has(key)) {
                            this.filters[key] = urlParams.get(key);
                        }
                    }
                    if (urlParams.has('page')) {
                        this.pagination.current_page = parseInt(urlParams.get('page'));
                    }

                    this.fetchInventory();
                },

                // Set a specific filter and fetch
                setFilter(key, value) {
                    this.filters[key] = value;
                    this.pagination.current_page = 1;
                    this.fetchInventory();
                },

                // Reset all filters
                clearFilters() {
                    this.filters = {
                        ...this.defaultFilters
                    };
                    this.pagination.current_page = 1;
                    this.fetchInventory();
                },

                // Check for active filters
                hasActiveFilters() {
                    return this.filters.search !== '' ||
                           this.filters.stock_status !== 'All' ||
                           this.filters.category !== 'All';
                },

                // Change page
                changePage(page) {
                    if (page < 1 || page > this.pagination.last_page) return;
                    this.pagination.current_page = page;
                    this.fetchInventory();
                },

                // Fetch inventory data
                async fetchInventory() {
                    this.loading = true;
                    try {
                        const queryParams = new URLSearchParams({
                            ...this.filters,
                            page: this.pagination.current_page
                        });

                        const response = await fetch(`/pharmacy/inventory/list?${queryParams}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Data fetch failed');

                        const result = await response.json();
                        if (result.success) {
                            this.medicines = result.data;
                            this.stats = result.stats;
                            this.pagination = {
                                ...this.pagination,
                                current_page: result.pagination.current_page,
                                last_page: result.pagination.last_page,
                                total: result.pagination.total,
                                from: (result.pagination.current_page - 1) * result.pagination.per_page + 1,
                                to: Math.min(result.pagination.current_page * result.pagination.per_page, result
                                    .pagination.total)
                            };

                            // Update URL
                            const newUrl = new URL(window.location.href);
                            for (let key in this.filters) {
                                if (this.filters[key] !== this.defaultFilters[key]) {
                                    newUrl.searchParams.set(key, this.filters[key]);
                                } else {
                                    newUrl.searchParams.delete(key);
                                }
                            }
                            if (this.pagination.current_page > 1) {
                                newUrl.searchParams.set('page', this.pagination.current_page);
                            } else {
                                newUrl.searchParams.delete('page');
                            }
                            window.history.pushState({}, '', newUrl);
                        }
                    } catch (error) {
                        console.error('Error fetching inventory:', error);
                        if (window.showNotification) showNotification('Error loading data', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // Pagination range helper
                getPageRange() {
                    const current = this.pagination.current_page;
                    const last = this.pagination.last_page;
                    const range = [];
                    const offset = 2;

                    for (let i = 1; i <= last; i++) {
                        if (i === 1 || i === last || (i >= current - offset && i <= current + offset)) {
                            range.push(i);
                        } else if (i === current - offset - 1 || i === current + offset + 1) {
                            range.push('...');
                        }
                    }
                    return range;
                },

                // Helper for status label
                getStockStatusLabel(status) {
                    const labels = {
                        'All': 'All Stock',
                        'low': 'Low Stock',
                        'out': 'Out of Stock',
                        'near_expiry': 'Near Expiry'
                    };
                    return labels[status] || status;
                },

                // Mock functions for missing global dependencies
                showUpdateStockModal(id, name) {
                    // This should be implemented or integrated with existing modal logic
                    console.log('Update stock for batch:', id, name);
                    // Example: window.dispatchEvent(new CustomEvent('show-stock-modal', { detail: { batchId: id, name: name } }));
                }
            }
        }
    </script>
@endpush
