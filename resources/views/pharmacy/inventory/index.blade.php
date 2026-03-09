@extends('layouts.app')

@section('title', 'Medicine Inventory Management')
@section('page-title', 'Inventory Management')
@section('breadcrumb', 'Pharmacy / Inventory')

@section('content')
    <div x-data="inventoryManager()" x-init="init()" class="space-y-6">

        <!-- Light Themed Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 gap-y-10 mt-8 p-4">

            <!-- Total Batches Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/30 border border-blue-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'All')" :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-500 to-cyan-300 shadow-lg shadow-blue-900/40 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-boxes text-2xl drop-shadow-md text-blue-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-blue-600 uppercase">
                        Total Batches
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-blue-800 drop-shadow-md font-mono"
                        x-text="stats.total ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-blue-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"
                            :class="{ 'animate-pulse': stats.total > 0 }"></span>
                        <span class="text-xs text-blue-700 font-medium">Active Stock Batches</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg shadow-amber-500/30 border border-amber-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'low')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-amber-500 to-orange-300 shadow-lg shadow-amber-900/40 border border-amber-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-2xl drop-shadow-md text-amber-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-amber-600 uppercase">
                        Low Stock
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-amber-800 drop-shadow-md font-mono"
                        x-text="stats.low_stock ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-amber-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-600"
                            :class="{ 'animate-pulse': stats.low_stock > 0 }"></span>
                        <span class="text-xs text-amber-700 font-medium">Needs Reorder</span>
                    </div>
                </div>
            </div>

            <!-- Near Expiry Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/30 border border-purple-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'near_expiry')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-500 to-indigo-300 shadow-lg shadow-purple-900/40 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-clock text-2xl drop-shadow-md text-purple-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-purple-600 uppercase">
                        Near Expiry
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-purple-800 drop-shadow-md font-mono"
                        x-text="stats.near_expiry ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-purple-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-purple-600"
                            :class="{ 'animate-pulse': stats.near_expiry > 0 }"></span>
                        <span class="text-xs text-purple-700 font-medium">Expiring within 30 days</span>
                    </div>
                </div>
            </div>

            <!-- Out of Stock Card -->
            <div class="relative flex flex-col bg-gradient-to-br from-rose-50 to-orange-50 rounded-2xl shadow-lg shadow-rose-500/30 border border-rose-200 hover:-translate-y-2 transition-all duration-300 group cursor-pointer"
                @click="setFilter('stock_status', 'out')"
                :class="loading ? 'opacity-70 grayscale cursor-not-allowed' : ''">

                <div
                    class="absolute -top-6 left-4 h-16 w-16 grid place-items-center rounded-xl bg-gradient-to-tr from-rose-500 to-orange-300 shadow-lg shadow-rose-900/40 border border-rose-300 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-times-circle text-2xl drop-shadow-md text-rose-700"></i>
                </div>

                <div class="p-4 text-right pt-6">
                    <p class="block antialiased font-sans text-sm font-bold tracking-wider text-rose-600 uppercase">
                        Out of Stock
                    </p>
                    <h4 class="block antialiased text-3xl font-bold text-rose-800 drop-shadow-md font-mono"
                        x-text="stats.out_of_stock ?? 0">
                    </h4>
                </div>
                <div class="mx-4 mb-4 border-t border-rose-300 pt-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600"
                            :class="{ 'animate-pulse': stats.out_of_stock > 0 }"></span>
                        <span class="text-xs text-rose-700 font-medium">Empty Batches</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Inventory List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header with Filters -->
            <div class="mb-0 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 tracking-tight flex items-center gap-3">
                            <i class="fas fa-warehouse text-blue-600"></i>
                            Batch Management
                            <span class="text-lg font-normal text-gray-600">
                                (<span x-text="pagination.total"></span> records)
                            </span>
                        </h2>
                        <p class="text-sm text-navy-600 mt-1">
                            Monitor medicine batches, expiry dates, and stock levels
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Records per page -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Show:</span>
                            <select x-model="filters.length" @change="fetchInventory()"
                                class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-2">
                            <button @click="clearFilters()"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-sync-alt"></i>
                                Refresh
                            </button>
                            <button @click="showAdvancedFilters = !showAdvancedFilters"
                                :class="showAdvancedFilters ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' :
                                    'bg-gradient-to-r from-orange-500 to-orange-600 text-white'"
                                class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                                <i class="fas fa-filter"></i>
                                Filters
                            </button>
                            <!-- Add Stock Button -->
                            <a href="{{ route('pharmacy.inventory.create') }}"
                                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg transition-colors text-sm font-medium shadow-md hover:shadow-lg">
                                <i class="fas fa-plus-circle"></i>
                                Add Stock
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div x-show="showAdvancedFilters" x-transition
                    class="mt-6 bg-white p-6 rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 shadow-lg">

                    <!-- First Row - Search, Status, Category -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="filters.search" @input.debounce.500ms="fetchInventory()"
                                placeholder="Search by name, brand, batch..."
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white font-medium text-slate-700">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select x-model="filters.stock_status" @change="fetchInventory()"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white font-bold text-slate-600">
                                <option value="All">All Stock Status</option>
                                <option value="low">Low Stock</option>
                                <option value="out">Out of Stock</option>
                                <option value="near_expiry">Near Expiry</option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <select x-model="filters.category" @change="fetchInventory()"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white font-bold text-slate-600">
                                <option value="All">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Second Row - Sort By, Clear Button -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <!-- Sort By -->
                        <div>
                            <select x-model="filters.sort_by" @change="fetchInventory()"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white font-bold text-slate-600">
                                <option value="expiry_date">Expiry Date (Earliest)</option>
                                <option value="name">Medicine Name (A-Z)</option>
                                <option value="stock">Stock (Low to High)</option>
                                <option value="stock_desc">Stock (High to Low)</option>
                            </select>
                        </div>

                        <div></div>

                        <!-- Clear All Filters Button -->
                        <div class="flex items-end">
                            <button @click="clearFilters()"
                                class="w-full flex items-center justify-center text-white py-2.5
                       text-center bg-gradient-to-r from-rose-500 to-rose-600
                       rounded-lg font-medium hover:from-rose-600 hover:to-rose-700
                       disabled:opacity-50 disabled:cursor-not-allowed transition-all
                       gap-2 shadow-md hover:shadow-lg h-[46px]">
                                <i class="fas fa-filter-circle-xmark"></i>
                                Clear All Filters
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Summary -->
                    <div x-show="filters.search || filters.category !== 'All' || filters.stock_status !== 'All'"
                        class="flex flex-wrap items-center gap-2 mt-4 pt-3 border-t border-purple-200">
                        <span class="text-xs font-medium text-gray-500">Active filters:</span>

                        <template x-if="filters.search">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs">
                                <i class="fas fa-search"></i>
                                <span x-text="filters.search"></span>
                                <button @click="filters.search = ''; fetchInventory()" class="ml-1 hover:text-blue-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <template x-if="filters.stock_status !== 'All'">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs">
                                <i class="fas fa-tag"></i>
                                <span x-text="getStockStatusLabel(filters.stock_status)"></span>
                                <button @click="filters.stock_status = 'All'; fetchInventory()" class="ml-1 hover:text-green-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </template>

                        <button @click="clearFilters()" class="text-xs text-rose-600 hover:text-rose-800 underline ml-2">
                            Clear all
                        </button>
                    </div>
                </div>
            </div>

            <!-- Batches Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-100 to-indigo-100">
                        <tr>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-pills text-blue-500"></i>
                                    Medicine & Batch
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tag text-purple-500"></i>
                                    Category & Form
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cubes text-emerald-500"></i>
                                    Stock Level
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-orange-500"></i>
                                    Expiration
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-cyan-500"></i>
                                    Pricing
                                </div>
                            </th>
                            <th scope="col"
                                class="px-5 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cogs text-orange-500"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" x-show="!loading && medicines && medicines.length > 0">
                        <template x-for="batch in medicines" :key="batch.id">
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                <!-- Medicine Info -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-base font-black text-slate-800 leading-tight" x-text="batch.medicine_name"></div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold" x-text="'#' + batch.batch_number"></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Form -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-md text-[10px] w-fit" x-text="batch.category_name"></span>
                                        <span class="text-slate-500 font-bold text-[10px] uppercase tracking-wider px-1" x-text="batch.form"></span>
                                    </div>
                                </td>

                                <!-- Stock Level -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col min-w-[120px]">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <span class="text-xl font-black" :class="batch.stock <= batch.reorder_level ? 'text-rose-600' : 'text-emerald-600'" x-text="batch.stock"></span>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">Remaining</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                            <div class="h-full transition-all duration-500"
                                                :class="batch.stock_color"
                                                :style="'width: ' + batch.stock_percentage + '%'"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Expiry -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-black text-slate-700" x-text="batch.expiry_date"></span>
                                            <template x-if="batch.is_about_to_expire">
                                                <i class="fas fa-exclamation-triangle text-amber-500 animate-pulse" title="Near Expiry"></i>
                                            </template>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest" 
                                              :class="batch.is_about_to_expire ? 'text-amber-600' : 'text-slate-400'"
                                              x-text="batch.is_about_to_expire ? 'Expiring Soon' : 'Valid Batch'"></span>
                                    </div>
                                </td>

                                <!-- Pricing -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-slate-500">
                                            <span class="text-[10px] font-bold uppercase">CP:</span>
                                            <span class="font-bold font-mono" x-text="batch.unit_price"></span>
                                        </div>
                                        <div class="text-sm text-indigo-700">
                                            <span class="text-[10px] font-bold uppercase">SP:</span>
                                            <span class="font-black font-mono" x-text="batch.sale_price"></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a :href="batch.view_url" class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="View History">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button @click="showUpdateStockModal(batch.id, batch.medicine_name)" 
                                                class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Adjust Stock">
                                            <i class="fas fa-sliders"></i>
                                        </button>
                                        <a :href="batch.edit_url" class="p-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition-all shadow-sm" title="Edit Batch">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Loading State -->
                    <tbody x-show="loading">
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-4 shadow-inner"></div>
                                    <p class="text-slate-600 font-black uppercase tracking-widest text-xs">Loading Inventory System...</p>
                                    <p class="text-[10px] text-slate-400 mt-1 font-bold">PLEASE WAIT WHILE WE SYNCHRONIZE BATCH DATA</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Empty State -->
                    <tbody x-show="!loading && (!medicines || medicines.length === 0)">
                        <tr>
                            <td colspan="6" class="px-6 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 mb-6 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 shadow-inner">
                                        <i class="fas fa-box-open text-5xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-slate-400">Inventory Empty</h3>
                                    <p class="text-slate-300 mt-2 font-medium max-w-sm mx-auto">
                                        No active batches found matching your current filters. Start by adding new stock or adjust your search.
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
            <div x-show="!loading && medicines && medicines.length > 0" class="bg-white px-6 py-4 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <!-- Pagination Info -->
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Showing <span class="text-slate-700 font-black" x-text="pagination.from"></span> to
                        <span class="text-slate-700 font-black" x-text="pagination.to"></span> of
                        <span class="text-slate-700 font-black" x-text="pagination.total"></span> batches
                    </div>

                    <!-- Pagination Controls -->
                    <nav class="flex items-center space-x-2">
                        <!-- First Page -->
                        <button @click="changePage(1)" :disabled="pagination.current_page === 1"
                            class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <i class="fas fa-angle-double-left"></i>
                        </button>

                        <!-- Previous Page -->
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <!-- Page Numbers -->
                        <template x-for="page in getPageRange()" :key="page">
                            <button @click="page !== '...' && changePage(page)"
                                :class="page === pagination.current_page ?
                                    'bg-gradient-to-br from-indigo-600 to-purple-700 text-white border-none shadow-md' :
                                    'border-slate-200 text-slate-600 hover:bg-slate-50'"
                                :disabled="page === '...'"
                                class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all">
                                <span x-text="page"></span>
                            </button>
                        </template>

                        <!-- Next Page -->
                        <button @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <!-- Last Page -->
                        <button @click="changePage(pagination.last_page)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
                            <i class="fas fa-angle-double-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function inventoryManager() {
            return {
                // State
                medicines: [],
                loading: false,
                showAdvancedFilters: false,
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
                    this.filters = { ...this.defaultFilters };
                    this.pagination.current_page = 1;
                    this.fetchInventory();
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
                                to: Math.min(result.pagination.current_page * result.pagination.per_page, result.pagination.total)
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
