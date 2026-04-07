@extends('layouts.app')
@section('title', 'Lab Test Parameters Management - NHMP HMS')
@section('page-title', 'Test Parameters')

@section('content')
<div x-data="testParameterManagement()" x-init="init()" x-cloak class="space-y-8 relative">

    {{-- Futuristic Floating Filter Toggle --}}
    <button @click="showSidebar = true"
        x-show="!showSidebar"
        x-transition:enter="transition ease-out duration-500 delay-100"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed top-1/2 right-0 -translate-y-1/2 z-40 bg-gradient-to-b from-blue-500 to-indigo-700 text-white p-2.5 py-6 rounded-l-2xl shadow-[0_0_30px_-5px_rgba(59,130,246,0.4)] hover:shadow-[-5px_0_40px_-5px_rgba(59,130,246,0.7)] hover:pr-4 transition-all duration-300 flex flex-col items-center gap-4 border-y border-l border-blue-400/50 group cursor-pointer"
        title="Open Parameter Filters">
        <div class="relative">
            <div class="absolute inset-0 bg-white/20 blur-md rounded-full group-hover:bg-white/40 transition-colors duration-300"></div>
            <i class="fas fa-sliders-h relative z-10 drop-shadow-lg group-hover:rotate-90 transition-transform duration-500 text-sm"></i>
        </div>
        <span style="writing-mode: vertical-rl;" class="text-[9px] font-black uppercase tracking-[0.3em] rotate-180 drop-shadow-md text-blue-50">Param Filters</span>
    </button>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">
        <div class="col-span-1 md:col-span-4 relative flex flex-col bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg shadow-blue-500/10 border border-blue-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer" @click="clearFilters()">
            <div class="absolute -top-4 left-4 h-12 w-12 grid place-items-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-lg shadow-blue-900/20 border border-blue-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-stream text-lg text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-2">
                <p class="text-[10px] font-black tracking-widest text-blue-600 uppercase opacity-70">Total Params</p>
                <h4 class="text-3xl font-black text-blue-800 drop-shadow-sm font-mono mt-1" x-text="stats.total">0</h4>
            </div>
            <div class="mx-4 mb-3 border-t border-blue-100 pt-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-[9px] text-blue-700 font-black uppercase tracking-tight">System Variables</span>
                </div>
                <i class="fas fa-chevron-right text-[8px] text-blue-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        <div class="col-span-1 md:col-span-4 relative flex flex-col bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-lg shadow-purple-500/10 border border-purple-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer" @click="showSidebar = true">
            <div class="absolute -top-4 left-4 h-12 w-12 grid place-items-center rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-lg shadow-purple-900/20 border border-purple-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-vial text-lg text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-2">
                <p class="text-[10px] font-black tracking-widest text-purple-600 uppercase opacity-70">Test Types</p>
                <h4 class="text-3xl font-black text-purple-800 drop-shadow-sm font-mono mt-1" x-text="stats.types">0</h4>
            </div>
            <div class="mx-4 mb-3 border-t border-purple-100 pt-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                    <span class="text-[9px] text-purple-700 font-black uppercase tracking-tight">Connected Types</span>
                </div>
                <i class="fas fa-chevron-right text-[8px] text-purple-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        <div class="col-span-1 md:col-span-4 relative flex flex-col bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-lg shadow-emerald-500/10 border border-emerald-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer" @click="showSidebar = true">
            <div class="absolute -top-4 left-4 h-12 w-12 grid place-items-center rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 shadow-lg shadow-emerald-900/20 border border-emerald-300 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-layer-group text-lg text-white drop-shadow-md"></i>
            </div>
            <div class="p-4 text-right pt-2">
                <p class="text-[10px] font-black tracking-widest text-emerald-600 uppercase opacity-70">Param Groups</p>
                <h4 class="text-3xl font-black text-emerald-800 drop-shadow-sm font-mono mt-1" x-text="stats.groups">0</h4>
            </div>
            <div class="mx-4 mb-3 border-t border-emerald-100 pt-2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] text-emerald-700 font-black uppercase tracking-tight">Logical Groupings</span>
                </div>
                <i class="fas fa-chevron-right text-[8px] text-emerald-300 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>
    </div>

    {{-- Main Control Panel --}}
    <div class="mt-8 grid lg:grid-cols-12 gap-6 items-start">
        <div class="space-y-6 transition-all duration-300" :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden transition-all duration-500 flex flex-col min-h-[400px] relative">
                
                {{-- Panel Header --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center border border-blue-200 shadow-sm transition-transform hover:scale-105 duration-300">
                                <i class="fas fa-microscope text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 tracking-tight flex items-center gap-3">
                                    Test Parameters
                                </h2>
                                <p class="text-gray-600 text-sm font-medium mt-1">Configure individual measurable metrics for lab tests</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 items-center">
                            <button @click="openAddModal()" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-500/30 transition-all active:scale-95 group cursor-pointer">
                                <i class="fas fa-plus group-hover:rotate-90 transition-transform duration-300"></i>
                                New Parameter
                            </button>
                            <div class="h-8 w-px bg-blue-100 mx-1"></div>
                            <button @click="showSidebar = !showSidebar" class="w-10 h-10 flex items-center justify-center bg-white border border-blue-200 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors shadow-sm relative group cursor-pointer" :title="showSidebar ? 'Hide Filters' : 'Show Filters'">
                                <i class="fas" :class="showSidebar ? 'fa-eye-slash text-rose-500' : 'fa-filter'"></i>
                                <span x-show="hasActiveFilters()" class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 border-2 border-white rounded-full"></span>
                            </button>
                            <button @click="fetchParameters()" class="w-10 h-10 flex items-center justify-center bg-white border border-blue-200 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors shadow-sm group cursor-pointer" title="Refresh Sync">
                                <i class="fas fa-sync-alt group-hover:rotate-180 transition-transform duration-700" :class="loading ? 'animate-spin text-blue-400' : ''"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bulk Actions Toolbar --}}
                <div x-show="selectedIds.length > 0" class="bg-blue-600 px-6 py-3 flex items-center justify-between text-white sticky top-0 z-10 shadow-2xl">
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-black uppercase tracking-widest border-r border-white/20 pr-4">
                            <span x-text="selectedIds.length"></span> Params Selected
                        </span>
                        <button @click="confirmBulkAction('deactivate')" class="px-3 py-1.5 bg-amber-500 shadow-lg shadow-amber-500/20 hover:bg-amber-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Deactivate Selection</button>
                        <button @click="confirmBulkAction('activate')" class="px-3 py-1.5 bg-emerald-500 shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest transition-all px-4 cursor-pointer">Activate Selection</button>
                    </div>
                    <button @click="selectedIds = []" class="text-[10px] font-black uppercase tracking-widest opacity-70 hover:opacity-100 transition-opacity flex items-center gap-2 cursor-pointer">
                        Dismiss <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gradient-to-r from-slate-50 to-white border-b border-blue-100">
                            <tr>
                                <th class="px-5 py-4 w-12 text-center">
                                    <input type="checkbox" @change="toggleAll($event)" :checked="selectedIds.length === parameters.length && parameters.length > 0" class="w-5 h-5 rounded-lg border-blue-200 text-blue-600 focus:ring-blue-500 transition-all shadow-sm cursor-pointer hover:border-blue-400">
                                </th>
                                <th class="px-5 py-4">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                        <button @click="sortBy('name')" class="flex items-center gap-1.5 hover:text-blue-700 transition-colors group uppercase">
                                            Parameter Name
                                            <i class="fas text-[10px]" :class="getSortIcon('name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-4">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                        <button @click="sortBy('test_type')" class="flex items-center gap-1.5 hover:text-blue-700 transition-colors group uppercase">
                                            Parent Type
                                            <i class="fas text-[10px]" :class="getSortIcon('test_type')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-4">
                                    <div class="flex items-center gap-2.5 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                        <button @click="sortBy('group_name')" class="flex items-center gap-1.5 hover:text-blue-700 transition-colors group uppercase">
                                            Group
                                            <i class="fas text-[10px]" :class="getSortIcon('group_name')"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="px-5 py-4 text-center">
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Ref Range</div>
                                </th>
                                <th class="px-5 py-4 text-center whitespace-nowrap w-44">
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Actions</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="param in parameters" :key="param.id">
                                <tr class="hover:bg-blue-50/40 transition-colors group" :class="density === 'condensed' ? 'bg-white' : ''">
                                    <td class="px-5 border-b border-slate-50 transition-all text-center" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <input type="checkbox" :value="param.id" x-model="selectedIds" class="w-5 h-5 rounded-lg border-blue-200 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer">
                                    </td>
                                    <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform bg-blue-600 text-white shrink-0">
                                                <i class="fas fa-stream text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-navy-800" x-text="param.name"></p>
                                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mt-0.5">Type: <span x-text="param.input_type || 'text'" class="text-gray-600"></span></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 bg-purple-50 text-purple-600 border border-purple-100 rounded text-[9px] font-black uppercase tracking-widest whitespace-nowrap" x-text="param.lab_test_type?.name || 'N/A'"></span>
                                    </td>
                                    <td class="px-5 border-b border-slate-50 transition-all" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded text-[9px] font-black uppercase tracking-widest whitespace-nowrap" x-text="param.group_name || 'Generic'"></span>
                                    </td>
                                    <td class="px-5 border-b border-slate-50 transition-all text-center" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <div x-show="param.input_type === 'number'" class="text-[10px]">
                                            <span class="font-mono font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded shadow-sm border border-indigo-100" x-text="param.min_range + '-' + param.max_range"></span>
                                            <span class="text-slate-500 ml-1 font-black uppercase tracking-wider text-[9px]" x-text="param.unit"></span>
                                        </div>
                                        <div x-show="param.input_type !== 'number'" class="text-[10px] font-black text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-200 truncate max-w-[120px] mx-auto shadow-sm uppercase tracking-tighter" :title="param.reference_range" x-text="param.reference_range || '-'"></div>
                                    </td>
                                    <td class="px-5 border-b border-slate-50 transition-all text-center whitespace-nowrap" :class="density === 'condensed' ? 'py-2' : 'py-4'">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button @click="openViewModal(param)" class="h-8 w-8 flex items-center justify-center bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-500 hover:text-white transition-all shadow-sm border border-sky-100 cursor-pointer" title="View Detail"><i class="fas fa-eye text-[10px]"></i></button>
                                            <button @click="openEditModal(param)" class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-500 hover:text-white transition-all shadow-sm border border-blue-100 cursor-pointer" title="Modify Node"><i class="fas fa-edit text-[10px]"></i></button>
                                            <button @click="confirmDelete(param)" class="h-8 w-8 flex items-center justify-center bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100 cursor-pointer" title="Purge Record"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && parameters.length === 0">
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-xl"><i class="fas fa-ghost text-4xl text-slate-300"></i></div>
                                        <h3 class="text-2xl font-black text-slate-800 mb-2">No Parameters Found</h3>
                                        <button @click="clearFilters()" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-500/40 hover:scale-105 transition-all cursor-pointer">Reset Intelligence</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Loading Overlay --}}
                <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-all">
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-16 h-16 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin shadow-lg"></div>
                        <span class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] animate-pulse">Syncing Vault...</span>
                    </div>
                </div>

                {{-- Pagination --}}
                <div x-show="!loading && parameters.length > 0" class="p-8 bg-slate-50 border-t border-slate-100 mt-auto">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Displaying <span class="text-slate-900" x-text="pagination.from || 0"></span> - <span class="text-slate-900" x-text="pagination.to || 0"></span> of <span class="text-blue-600" x-text="pagination.total"></span> Entries</div>
                        <div class="flex items-center gap-2">
                            <button @click="changePage(1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"><span class="text-[10px] font-black uppercase tracking-widest">First</span></button>
                            <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"><span class="text-[10px] font-black uppercase tracking-widest">Prev</span></button>
                            <div class="flex items-center gap-1.5 mx-2">
                                <template x-for="page in getPageRange()" :key="page">
                                    <button @click="page !== '...' && changePage(page)" :class="page === pagination.current_page ? 'bg-blue-600 text-white shadow-lg border-blue-600' : 'bg-white text-slate-600 border-slate-200'" :disabled="page === '...'" class="w-10 h-10 rounded-xl border text-[10px] font-black transition-all flex items-center justify-center cursor-pointer" x-text="page"></button>
                                </template>
                            </div>
                            <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"><span class="text-[10px] font-black uppercase tracking-widest">Next</span></button>
                            <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" class="px-3 h-10 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 shadow-sm hover:border-blue-600 hover:text-blue-600 disabled:opacity-30 disabled:pointer-events-none transition-all cursor-pointer"><span class="text-[10px] font-black uppercase tracking-widest">Last</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Filters --}}
        <div class="lg:col-span-3 lg:sticky lg:top-0 lg:max-h-[calc(100vh-140px)]" x-show="showSidebar" x-transition.opacity>
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-sm"><i class="fas fa-filter text-sm"></i></div>
                        <h2 class="font-black text-slate-800 text-base tracking-tight uppercase">Param Filters</h2>
                    </div>
                </div>
                <div class="p-5 space-y-5">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">Search Point</label>
                        <input type="text" x-model.debounce.500ms="searchQuery" @input="searchParameters()" placeholder="Search Name..." class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-blue-500 font-bold text-slate-600 text-sm outline-none">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Test Type</label>
                        <div class="relative">
                            <select x-model="filterTestType" @change="searchParameters()" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl font-black text-blue-600 text-[10px] uppercase appearance-none cursor-pointer">
                                <option value="">All Test Types</option>
                                @foreach($testTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 pointer-events-none text-[10px]"></i>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Page Density</label>
                        <div class="grid grid-cols-2 gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/50">
                            <button @click="density = 'condensed'" :class="density === 'condensed' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500'" class="py-2 text-[9px] font-black uppercase rounded-lg transition-all cursor-pointer">Condensed</button>
                            <button @click="density = 'spacious'" :class="density === 'spacious' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500'" class="py-2 text-[9px] font-black uppercase rounded-lg transition-all cursor-pointer">Spacious</button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Records Per Page</label>
                        <div class="relative">
                            <select x-model="pagination.per_page" @change="searchParameters()" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-100 rounded-xl font-black text-blue-600 text-[10px] uppercase appearance-none cursor-pointer">
                                <option value="10">Show 10</option>
                                <option value="50">Show 50</option>
                                <option value="100">Show 100</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-blue-400 pointer-events-none text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <div class="p-5 pt-0 space-y-2.5">
                    <button @click="clearFilters()" class="w-full px-4 py-2.5 bg-rose-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-700 transition-all flex items-center justify-between cursor-pointer"><span>Purge Filters</span><i class="fas fa-eraser"></i></button>
                    <button @click="showSidebar = false" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all flex items-center justify-between cursor-pointer"><span>Hide Pipeline</span><i class="fas fa-eye-slash"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeAddModal()" x-transition.opacity></div>
            <div class="relative bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden" x-transition.scale>
                <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100 flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight" x-text="editing ? 'Edit Test Parameter' : 'Create New Parameter'"></h3>
                    <button @click="closeAddModal()" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 cursor-pointer shadow-sm"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    <form @submit.prevent="saveParameter" class="grid grid-cols-2 gap-5">
                        <div class="col-span-2 md:col-span-1 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Test Type <span class="text-rose-500">*</span></label>
                             <select x-model="form.lab_test_type_id" required class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none cursor-pointer">
                                 <option value="" disabled>Select Type</option>
                                 @foreach($testTypes as $type)
                                     <option value="{{ $type->id }}">{{ $type->name }}</option>
                                 @endforeach
                             </select>
                        </div>
                        <div class="col-span-2 md:col-span-1 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Parameter Name <span class="text-rose-500">*</span></label>
                             <input type="text" x-model="form.name" required placeholder="e.g. Hemoglobin" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-1 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Group Category</label>
                             <input type="text" x-model="form.group_name" placeholder="e.g. Hematology" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-1 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Input Mode <span class="text-rose-500">*</span></label>
                             <select x-model="form.input_type" required class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 cursor-pointer">
                                 <option value="text">Character Input</option>
                                 <option value="number">Numeric Metric</option>
                             </select>
                        </div>
                        <div x-show="form.input_type === 'number'" class="col-span-2 grid grid-cols-3 gap-5 p-5 bg-indigo-50/30 rounded-2xl border border-indigo-100">
                             <div class="space-y-1"><label class="text-[9px] uppercase font-black text-slate-500">Min</label><input type="number" step="0.001" x-model="form.min_range" class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl font-mono text-sm"></div>
                             <div class="space-y-1"><label class="text-[9px] uppercase font-black text-slate-500">Max</label><input type="number" step="0.001" x-model="form.max_range" class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl font-mono text-sm"></div>
                             <div class="space-y-1"><label class="text-[9px] uppercase font-black text-slate-500">Unit</label><input type="text" x-model="form.unit" placeholder="g/dL" class="w-full px-4 py-2.5 bg-white border-2 border-slate-100 rounded-xl font-mono text-sm"></div>
                        </div>
                        <div x-show="form.input_type === 'text'" class="col-span-2 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Characteristic Reference</label>
                             <textarea x-model="form.reference_range" rows="2" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none resize-none"></textarea>
                        </div>
                        <div class="col-span-2 space-y-2">
                             <label class="text-[10px] uppercase tracking-widest font-black text-slate-500">Reporting Priority</label>
                             <input type="number" x-model="form.order" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl font-bold text-slate-800 outline-none">
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button @click="closeAddModal()" class="px-6 py-2.5 bg-white border-2 border-slate-200 text-slate-600 rounded-xl font-black text-xs uppercase cursor-pointer">Abort</button>
                    <button @click="saveParameter()" :disabled="saving" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-black text-xs uppercase shadow-lg shadow-blue-500/30 cursor-pointer disabled:opacity-50">
                        <span x-text="saving ? 'Syncing...' : 'Commit Node'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Generic Confirmation Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showConfirmModal = false" x-transition.opacity></div>
        <div class="relative bg-white w-full max-w-md rounded-[2.5rem] p-8 text-center shadow-2xl border border-slate-100" x-transition.scale>
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center mb-6" :class="confirmConfig.type === 'danger' ? 'bg-rose-100 text-rose-600' : (confirmConfig.type === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600')">
                <i class="fas text-3xl" :class="confirmConfig.icon"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight" x-text="confirmConfig.title"></h3>
            <p class="text-xs font-bold text-slate-500 mb-8 px-4 uppercase tracking-wider leading-relaxed" x-text="confirmConfig.message"></p>
            <div class="flex gap-3">
                <button @click="showConfirmModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase cursor-pointer">Cancel Action</button>
                <button @click="executeConfirmedAction()" :disabled="confirming" class="flex-1 py-3 text-white rounded-2xl font-black text-xs uppercase shadow-xl transition-all cursor-pointer flex items-center justify-center gap-2" :class="confirmConfig.type === 'danger' ? 'bg-rose-600 shadow-rose-500/30 hover:bg-rose-700' : (confirmConfig.type === 'warning' ? 'bg-amber-600 shadow-amber-500/30 hover:bg-amber-700' : 'bg-blue-600 shadow-blue-500/30 hover:bg-blue-700')">
                    <i class="fas fa-spinner fa-spin" x-show="confirming"></i>
                    <span x-text="confirming ? 'Processing...' : confirmConfig.confirmText"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div x-show="showViewModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
         <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeViewModal" x-transition.opacity></div>
         <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100" x-transition.scale>
             <div class="bg-gradient-to-br from-sky-500 to-sky-400 p-8 text-white">
                 <div class="flex items-center gap-4">
                     <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center border border-white/20 shadow-xl"><i class="fas fa-microscope text-xl"></i></div>
                     <div>
                         <h3 class="text-xl font-black tracking-tight uppercase" x-text="dataToView?.name || 'Parameter Vault'"></h3>
                         <p class="text-white/70 text-[10px] font-black uppercase tracking-[0.2em] mt-1">Data Point Intelligence</p>
                     </div>
                 </div>
             </div>
             <div class="p-8 space-y-6">
                 <div class="grid grid-cols-2 gap-8">
                     <div class="space-y-1"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Type Clearance</p><span class="inline-block px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-xs font-black uppercase" x-text="dataToView?.lab_test_type?.name || 'N/A'"></span></div>
                     <div class="space-y-1"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sector / Group</p><span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-black uppercase" x-text="dataToView?.group_name || 'Generic'"></span></div>
                     <div class="space-y-1"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Input Protocol</p><p class="text-sm font-black text-slate-800 uppercase" x-text="dataToView?.input_type"></p></div>
                     <div class="space-y-1"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Normal Variable</p>
                          <div x-show="dataToView?.input_type === 'number'" class="flex items-center gap-1.5"><span class="px-2 py-1 bg-indigo-50 border border-indigo-100 rounded text-xs font-mono font-bold text-indigo-700" x-text="dataToView?.min_range + '-' + dataToView?.max_range"></span><span class="text-[10px] font-black text-slate-400 uppercase" x-text="dataToView?.unit"></span></div>
                          <p x-show="dataToView?.input_type !== 'number'" class="text-sm font-black text-slate-700 uppercase" x-text="dataToView?.reference_range || '-'"></p>
                     </div>
                 </div>
             </div>
             <div class="p-8 pt-0 flex justify-end"><button @click="closeViewModal" class="px-8 py-3 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest cursor-pointer">Dismiss</button></div>
         </div>
    </div>

</div>

<script>
    function testParameterManagement() {
        return {
            showSidebar: false,
            showAddModal: false,
            showConfirmModal: false,
            showViewModal: false,
            loading: false,
            saving: false,
            confirming: false,
            editing: false,
            density: 'spacious',

            parameters: [],
            stats: { total: 0, groups: 0, types: 0 },
            
            searchQuery: '',
            filterTestType: '',
            sortField: 'name',
            sortDirection: 'asc',
            pagination: { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 },
            
            form: { id: null, lab_test_type_id: '', name: '', group_name: '', unit: '', reference_range: '', min_range: null, max_range: null, input_type: 'text', order: 0 },
            selectedIds: [],
            dataToDelete: null,
            dataToView: null,

            confirmConfig: { title: '', message: '', icon: '', confirmText: '', type: 'primary', action: null },

            async init() {
                await this.fetchParameters();
                await this.fetchStats();
            },

            hasActiveFilters() { return this.searchQuery !== '' || this.filterTestType !== ''; },

            getSortIcon(field) {
                if (this.sortField !== field) return 'fa-sort opacity-20';
                return this.sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
            },

            searchParameters() { this.pagination.current_page = 1; this.fetchParameters(); },

            async fetchParameters() {
                this.loading = true;
                const params = new URLSearchParams({ page: this.pagination.current_page, per_page: this.pagination.per_page, sort: this.sortField, direction: this.sortDirection });
                if (this.searchQuery) params.append('search', this.searchQuery);
                if (this.filterTestType) params.append('test_type', this.filterTestType);

                try {
                    const response = await fetch(`/lab/test-parameters/data?${params.toString()}`);
                    const data = await response.json();
                    this.parameters = data.data;
                    this.pagination = { current_page: data.current_page, last_page: data.last_page, per_page: data.per_page, total: data.total, from: data.from, to: data.to };
                } catch (error) { window.Notification.error('Intelligence sync failed'); }
                finally { this.loading = false; }
            },

            async fetchStats() {
                try {
                    const r = await fetch('/lab/test-parameters/stats');
                    if (r.ok) this.stats = await r.json();
                } catch(e) {}
            },

            changePage(p) { if (p >= 1 && p <= this.pagination.last_page) { this.pagination.current_page = p; this.fetchParameters(); } },

            getPageRange() {
                const c = this.pagination.current_page, l = this.pagination.last_page, r = [], d = 2;
                for (let i = Math.max(2, c - d); i <= Math.min(l - 1, c + d); i++) r.push(i);
                if (c - d > 2) r.unshift('...'); if (c + d < l - 1) r.push('...');
                r.unshift(1); if (l > 1) r.push(l); return r;
            },

            sortBy(f) {
                if (this.sortField === f) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                else { this.sortField = f; this.sortDirection = 'asc'; }
                this.fetchParameters();
            },

            clearFilters() {
                this.searchQuery = ''; this.filterTestType = ''; this.pagination.current_page = 1;
                this.fetchParameters(); window.Notification.success('Intelligence Cleaned');
            },

            toggleAll(e) { this.selectedIds = e.target.checked ? this.parameters.map(p => p.id) : []; },

            openAddModal() {
                this.editing = false;
                this.form = { id: null, lab_test_type_id: '', name: '', group_name: '', unit: '', reference_range: '', min_range: null, max_range: null, input_type: 'text', order: 0 };
                this.showAddModal = true;
            },

            openEditModal(p) { this.editing = true; this.form = { ...p }; this.showAddModal = true; },
            closeAddModal() { this.showAddModal = false; },

            openViewModal(p) { this.dataToView = p; this.showViewModal = true; },
            closeViewModal() { this.showViewModal = false; },

            async saveParameter() {
                if (!this.form.name || !this.form.lab_test_type_id) { 
                    window.Notification.warning('Required fields missing'); 
                    return; 
                }
                this.saving = true;
                const url = this.editing ? `/lab/test-parameters/${this.form.id}` : '/lab/test-parameters';
                try {
                    const r = await fetch(url, {
                        method: this.editing ? 'PUT' : 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                            'Accept': 'application/json' 
                        },
                        body: JSON.stringify(this.form)
                    });
                    const d = await r.json();
                    if (r.ok) { 
                        window.Notification.success(d.message || 'Operation successful'); 
                        this.closeAddModal(); 
                        await this.fetchParameters(); 
                        await this.fetchStats(); 
                    }
                    else { window.Notification.error(d.message || 'Transmission failed'); }
                } catch(e) { window.Notification.error('Network blackout'); }
                finally { this.saving = false; }
            },

            confirmDelete(p) {
                this.dataToDelete = p;
                this.confirmConfig = { 
                    title: 'Purge Record', 
                    message: `Targeting "${p.name}" for permanent removal?`, 
                    icon: 'fa-trash-alt', 
                    confirmText: 'Confirm Purge', 
                    type: 'danger', 
                    action: () => this.deleteData() 
                };
                this.showConfirmModal = true;
            },

            async deleteData() {
                this.confirming = true;
                try {
                    const r = await fetch(`/lab/test-parameters/${this.dataToDelete.id}`, { 
                        method: 'DELETE', 
                        headers: { 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        } 
                    });
                    if (r.ok) { 
                        window.Notification.success('Record Erased'); 
                        this.showConfirmModal = false; 
                        await this.fetchParameters(); 
                        await this.fetchStats(); 
                    } else {
                        const d = await r.json();
                        window.Notification.error(d.message || 'Purge failed');
                    }
                } catch(e) { window.Notification.error('Network error during purge'); }
                finally { this.confirming = false; }
            },

            confirmBulkAction(type) {
                const actionText = type === 'activate' ? 'Activation' : 'Deactivation';
                this.confirmConfig = { 
                    title: `Mass ${actionText}`, 
                    message: `Confirm ${type} of ${this.selectedIds.length} records?`, 
                    icon: type === 'activate' ? 'fa-check-circle' : 'fa-times-circle', 
                    confirmText: `Mass ${type === 'activate' ? 'Activate' : 'Deactivate'}`, 
                    type: type === 'activate' ? 'primary' : 'warning', 
                    action: () => this.executeBulkStatusUpdate(type) 
                };
                this.showConfirmModal = true;
            },

            async executeBulkStatusUpdate(status) {
                this.confirming = true;
                try {
                    const r = await fetch('/lab/test-parameters/bulk-status', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ ids: this.selectedIds, status: status })
                    });
                    if (r.ok) { 
                        window.Notification.success('Sector Cleaned'); 
                        this.selectedIds = []; 
                        this.showConfirmModal = false; 
                        await this.fetchParameters(); 
                        await this.fetchStats(); 
                    } else {
                        const d = await r.json();
                        window.Notification.error(d.message || 'Mass update failed');
                    }
                } catch(e) { window.Notification.error('Network error during mass update'); }
                finally { this.confirming = false; }
            },

            executeConfirmedAction() { if (this.confirmConfig.action) this.confirmConfig.action(); }
        }
    }
</script>
@endsection