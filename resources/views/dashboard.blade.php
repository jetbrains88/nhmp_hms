@extends('layouts.app')

@section('title', 'Hospital Analytics Dashboard - HMS')
@section('page-title', 'Dashboard')

@section('content')
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        /* Material Floating Card Effect */
        .material-card {
            border: 0;
            margin-bottom: 30px;
            margin-top: 30px;
            border-radius: 0.75rem;
            background-color: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
        }

        .material-header {
            margin: -20px 15px 0;
            border-radius: 0.5rem;
            padding: 15px;
            position: relative;
            color: white;
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(64, 64, 64, 0.4);
        }

        /* Gradients & Shadows for specific colors */
        .gradient-blue {
            background: linear-gradient(60deg, #26c6da, #00acc1);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(0, 188, 212, 0.4);
        }

        .gradient-indigo {
            background: linear-gradient(60deg, #5c6bc0, #3949ab);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(57, 73, 171, 0.4);
        }

        .gradient-green {
            background: linear-gradient(60deg, #66bb6a, #43a047);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(76, 175, 80, 0.4);
        }

        .gradient-orange {
            background: linear-gradient(60deg, #ffa726, #fb8c00);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(255, 152, 0, 0.4);
        }

        .gradient-red {
            background: linear-gradient(60deg, #ef5350, #e53935);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(244, 67, 54, 0.4);
        }

        .gradient-rose {
            background: linear-gradient(60deg, #ec407a, #d81b60);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(233, 30, 99, 0.4);
        }

        .gradient-dark {
            background: linear-gradient(60deg, #666, #444);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(64, 64, 64, 0.4);
        }

        .gradient-info {
            background: linear-gradient(60deg, #29b6f6, #039be5);
            box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(3, 169, 244, 0.4);
        }

        /* Progress fill animation */
        .progress-fill {
            animation: progressFill 2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes progressFill {
            0% {
                stroke-dashoffset: 502.4;
            }

            100% {
                stroke-dashoffset: {{ 2 * 3.14159 * 80 * (1 - 0) }};
            }
        }

        /* Stock Cards Specifics */
        .stock-card {
            border-radius: 1rem;
            padding: 1.5rem 1.25rem;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .stock-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.3);
        }

        /* Stock card loading animation */
        .stock-card.loading {
            position: relative;
        }

        .stock-card.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            100% {
                left: 100%;
            }
        }

        /* Circular Progress in Stock Cards */
        .circular-progress-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1.25rem 0;
            position: relative;
            width: 160px;
            height: 160px;
        }

        .circular-progress {
<<<<<<< HEAD
=======
            color: #ec407a;
>>>>>>> f3c01c7 (NHMP-HMS STARTED)
            width: 160px;
            height: 160px;
            transform: rotate(-90deg);
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        /* Thicker circle strokes */
        .circular-progress circle {
            transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .circular-progress circle:first-child {
            stroke-width: 24px;
            /* Thicker background circle */
        }

        .circular-progress circle:last-child {
            stroke-width: 24px;
            /* Thicker progress circle */
        }

        /* Percentage text animation */
        .progress-percentage {
            animation: fadeInScale 0.8s ease-out;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Table Styling */
        .data-table th {
            color: #ec407a;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
        }

        /* Welcome card time animation */
        #current-time-large {
            animation: pulse 2s infinite;
            display: inline-block;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        /* Chart loading animation */
        .chart-loading {
            position: relative;
            min-height: 200px;
        }

        .chart-loading::after {
            content: 'Loading...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #666;
            font-size: 14px;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }
        }


        /* Animation for the card headers */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 40px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .material-card {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Staggered delay for each card (4 cards) */
        .material-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .material-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .material-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .material-card:nth-child(4) {
            animation-delay: 0.4s;
        }
    </style>

    <div class="p-4 bg-gray-50 min-h-screen" x-data="dashboard()" x-init="init()" x-cloak>
        <!-- Loading Overlay -->
        <div x-show="isLoading" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-white bg-opacity-80 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="text-center">
                <div class="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                <p class="text-gray-600 font-medium animate-pulse">Loading dashboard...</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Welcome Card -->
            <div
                class="lg:col-span-2 bg-gradient-to-r from-cyan-600 to-blue-600 rounded-xl text-white p-6 shadow-lg transform transition-all duration-500 hover:scale-[1.02]">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                        <p class="text-blue-100 mb-4">
                            {{ now()->format('l, F j, Y') }} •
                            <span id="current-time-large" class="font-mono bg-white/20 px-2 py-1 rounded-lg"></span>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-blue-100">Hospital Status</div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                            <div class="w-2 h-2 bg-green-400 rounded-full absolute"></div>
                            <span class="font-semibold ml-3">Online</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <div class="bg-white/20 p-3 rounded-lg backdrop-blur-sm animate-bounce">
                        <i class="fas fa-hospital text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm">NHMP Hospital Management System</p>
                        <p class="text-xl font-bold">Analytics Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Material Quick Stats -->
            <div
                class="relative flex flex-col rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg transform transition-all duration-500 hover:scale-[1.02]">
                <div
                    class="absolute -top-4 left-4 rounded-xl overflow-hidden bg-gradient-to-br from-orange-500 to-pink-500 text-white shadow-lg w-16 h-16 flex items-center justify-center animate-pulse">
                    <i class="fas fa-user-injured text-xl"></i>
                </div>
                <div class="p-6 pt-8">
                    <p class="text-sm font-normal text-white/80 text-right">Today's Summary</p>
                    <h4 class="text-2xl font-semibold text-right mb-4">12 Patients</h4>

                    <div class="border-t border-white/20 pt-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-white/80">Today's Visits</span>
                            <span class="font-bold text-white relative">
                                <span x-text="stats.todayVisits"></span>
                                <span x-show="stats.todayVisits > 0"
                                    class="absolute -top-1 -right-2 w-2 h-2 bg-green-400 rounded-full animate-ping"></span>
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/80">Pending Visits</span>
                            <span class="font-bold text-white" x-text="stats.pendingVisits"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/80">Pending Prescriptions</span>
                            <span class="font-bold text-white" x-text="pendingPrescriptions || 0"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-navy-700 mb-4 flex items-center gap-2">
            <i class="fas fa-cubes text-green-500 animate-spin-slow"></i> Medicine Stock Health
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
            <!-- Out of Stock Card -->
            <div class="stock-card bg-gradient-to-br from-red-600 to-orange-400 shadow-lg" x-show="!isLoading"
                x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="text-sm font-bold uppercase tracking-widest opacity-90">Out of Stock</div>
                <div class="circular-progress-container">
                    <svg class="circular-progress" viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.2)"
                            stroke-width="24" />
                        <circle cx="100" cy="100" r="80" fill="none" stroke="#fff" stroke-width="24"
                            stroke-linecap="round" stroke-dasharray="502.4"
                            :stroke-dashoffset="502.4 * (1 - outOfStockPercentage / 100)" x-ref="outOfStockCircle">
                        </circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white progress-percentage">{{ $outOfStockPercentage }}%</span>
                    </div>
                </div>
                <div
                    class="mt-2 bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full text-sm font-medium transform transition-all hover:scale-105">
                    {{ $outOfStockCount }} Items
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="stock-card bg-gradient-to-br from-amber-600 to-yellow-400 shadow-lg" x-show="!isLoading"
                x-transition:enter="transition ease-out duration-500 delay-100"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                <div class="text-sm font-bold uppercase tracking-widest opacity-90">Low Stock</div>
                <div class="circular-progress-container">
                    <svg class="circular-progress" viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.2)"
                            stroke-width="24" />
                        <circle cx="100" cy="100" r="80" fill="none" stroke="#fff" stroke-width="24"
                            stroke-linecap="round" stroke-dasharray="502.4"
                            :stroke-dashoffset="502.4 * (1 - lowStockPercentage / 100)">
                        </circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white progress-percentage">{{ $lowStockPercentage }}%</span>
                    </div>
                </div>
                <div
                    class="mt-2 bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full text-sm font-medium transform transition-all hover:scale-105">
                    {{ $lowStockCount }} Items
                </div>
            </div>

<<<<<<< HEAD
            <!-- Good Stock Card -->
            <div class="stock-card bg-gradient-to-br from-blue-600 to-cyan-400 shadow-lg" x-show="!isLoading"
=======
            <!-- Good Stock Card --> 
            <div class="stock-card bg-gradient-to-br from-sky-50 to-cyan-50 rounded-2xl shadow-lg shadow-blue-500/20 border border-blue-200" x-show="!isLoading"
>>>>>>> f3c01c7 (NHMP-HMS STARTED)
                x-transition:enter="transition ease-out duration-500 delay-200"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                <div class="text-sm font-bold uppercase tracking-widest opacity-90">Good Stock</div>
                <div class="circular-progress-container">
                    <svg class="circular-progress" viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.2)"
                            stroke-width="24" />
                        <circle cx="100" cy="100" r="80" fill="none" stroke="#fff" stroke-width="24"
                            stroke-linecap="round" stroke-dasharray="502.4"
                            :stroke-dashoffset="502.4 * (1 - inStockPercentage / 100)">
                        </circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
<<<<<<< HEAD
                        <span class="text-3xl font-bold text-white progress-percentage">{{ $inStockPercentage }}%</span>
=======
                        <span class="text-3xl font-bold text-cyan-700 progress-percentage">{{ $inStockPercentage }}%</span>
>>>>>>> f3c01c7 (NHMP-HMS STARTED)
                    </div>
                </div>
                <div
                    class="mt-2 bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full text-sm font-medium transform transition-all hover:scale-105">
                    {{ $inStockCount }} Items
                </div>
            </div>

            <!-- Critical Stock Card -->
            <div class="stock-card bg-gradient-to-br from-purple-600 to-fuchsia-400 shadow-lg" x-show="!isLoading"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
                <div class="text-sm font-bold uppercase tracking-widest opacity-90">Critical</div>
                <div class="circular-progress-container">
                    <svg class="circular-progress" viewBox="0 0 200 200">
                        <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.2)"
                            stroke-width="24" />
                        <circle cx="100" cy="100" r="80" fill="none" stroke="#fff" stroke-width="24"
                            stroke-linecap="round" stroke-dasharray="502.4"
                            :stroke-dashoffset="502.4 * (1 - criticalStockPercentage / 100)">
                        </circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span
                            class="text-3xl font-bold text-white progress-percentage">{{ $criticalStockPercentage }}%</span>
                    </div>
                </div>
                <div
                    class="mt-2 bg-white/20 backdrop-blur-sm px-4 py-1 rounded-full text-sm font-medium transform transition-all hover:scale-105">
                    {{ $criticalStockCount ?? 0 }} Items
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-navy-700 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-blue-600 animate-pulse"></i> Weekly Insights
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
            <div class="material-card transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <div class="material-header gradient-info">
                    <div class="h-40 relative">
                        <canvas id="chartPatients"></canvas>
                    </div>
                </div>
                <div class="p-4 pt-2">
                    <h6 class="text-gray-800 text-lg font-bold">New Patients</h6>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-emerald-500 font-bold">+12%</span> increase
                    </p>
                    <hr class="my-3 border-gray-100">
                    <div class="flex items-center text-xs text-gray-400 gap-2">
                        <i class="far fa-clock animate-spin-slow"></i> updated 2 days ago
                    </div>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <div class="material-header gradient-green">
                    <div class="h-40">
                        <canvas id="chartVisits"></canvas>
                    </div>
                </div>
                <div class="p-4 pt-2">
                    <h6 class="text-gray-800 text-lg font-bold">Total Visits</h6>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-emerald-500 font-bold">+5%</span> vs last week
                    </p>
                    <hr class="my-3 border-gray-100">
                    <div class="flex items-center text-xs text-gray-400 gap-2">
                        <i class="far fa-clock animate-spin-slow"></i> updated 4 min ago
                    </div>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <div class="material-header gradient-rose">
                    <div class="h-40">
                        <canvas id="chartLab"></canvas>
                    </div>
                </div>
                <div class="p-4 pt-2">
                    <h6 class="text-gray-800 text-lg font-bold">Lab Reports</h6>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-rose-500 font-bold">-2%</span> decrease
                    </p>
                    <hr class="my-3 border-gray-100">
                    <div class="flex items-center text-xs text-gray-400 gap-2">
                        <i class="far fa-clock animate-spin-slow"></i> campaign sent 2 days ago
                    </div>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <div class="material-header gradient-orange">
                    <div class="h-40">
                        <canvas id="chartMedicines"></canvas>
                    </div>
                </div>
                <div class="p-4 pt-2">
                    <h6 class="text-gray-800 text-lg font-bold">Pharmacy Dispense</h6>
                    <p class="text-sm text-gray-500 mt-1">
                        <span class="text-emerald-500 font-bold">+8%</span> revenue growth
                    </p>
                    <hr class="my-3 border-gray-100">
                    <div class="flex items-center text-xs text-gray-400 gap-2">
                        <i class="far fa-clock animate-spin-slow"></i> just updated
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-blue flex justify-between items-center p-4">
                    <div>
                        <h4 class="text-lg font-bold">Monthly Medicines & Lab Reports</h4>
                        <p class="text-blue-100 text-sm opacity-80">Annual Performance Overview</p>
                    </div>
                    <div class="flex gap-1 bg-white/20 p-1 rounded-lg backdrop-blur-sm">
                        <template x-for="year in years" :key="year">
                            <button @click="selectedYear = year; updateCharts()"
                                :class="{
                                    'bg-white text-blue-500 shadow-lg transform scale-105': selectedYear === year,
                                    'text-white hover:bg-white/10 hover:scale-105': selectedYear !== year
                                }"
                                class="px-3 py-1 rounded text-xs font-bold transition-all duration-300" x-text="year">
                            </button>
                        </template>
                    </div>
                </div>
                <div class="p-5">
                    <div class="h-[300px] relative">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-red flex justify-between items-center p-4">
                    <div>
                        <h4 class="text-lg font-bold">Monthly Performance</h4>
                        <p class="text-red-100 text-sm opacity-80">Visits vs. Medicines vs. Labs</p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm animate-pulse">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                </div>
                <div class="p-5">
                    <div class="h-[300px]">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <div class="material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-purple flex justify-between items-center p-4"
                    style="background: linear-gradient(60deg, #ab47bc, #8e24aa); box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(156, 39, 176, 0.4);">
                    <div>
                        <h4 class="text-lg font-bold">Inventory by Category</h4>
                        <p class="text-purple-100 text-sm opacity-80">Remaining Stock Distribution</p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-pills text-white"></i>
                    </div>
                </div>
                <div class="p-5 flex justify-center items-center">
                    <div class="h-[320px] w-full relative flex justify-center">
                        <canvas id="polarStockChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-teal flex justify-between items-center p-4"
                    style="background: linear-gradient(60deg, #26a69a, #00897b); box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(0, 150, 136, 0.4);">
                    <div>
                        <h4 class="text-lg font-bold">Lab Reports Frequency</h4>
                        <p class="text-teal-100 text-sm opacity-80">Test Volume Analysis</p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-microscope text-white"></i>
                    </div>
                </div>
                <div class="p-5 flex justify-center items-center">
                    <div class="h-[320px] w-full relative flex justify-center">
                        <canvas id="radarLabChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-orange p-4">
                    <h4 class="text-lg font-bold mb-1">Most Dispensed Medicines</h4>
                    <p class="text-orange-100 text-sm opacity-80">Top 10 items by volume</p>
                </div>
                <div class="p-5 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-orange-500 border-b border-orange-100">
                                <th class="p-3 text-xs font-bold uppercase">Medicine</th>
                                <th class="p-3 text-xs font-bold uppercase">Code</th>
                                <th class="p-3 text-xs font-bold uppercase">Dispensed</th>
                                <th class="p-3 text-xs font-bold uppercase text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-600">
                            <template x-for="(medicine, index) in topMedicines" :key="medicine.code">
                                <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition-all duration-300 hover:scale-[1.01] hover:shadow-md"
                                    x-show="!isLoading" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform -translate-x-4"
                                    x-transition:enter-end="opacity-100 transform translate-x-0"
                                    :style="`transition-delay: ${index * 50}ms`">
                                    <td class="p-3 font-medium text-gray-800" x-text="medicine.name"></td>
                                    <td class="p-3" x-text="medicine.code"></td>
                                    <td class="p-3">
                                        <span class="font-bold text-gray-700" x-text="medicine.total_dispensed"></span>
                                        <span class="text-xs text-gray-400 ml-1">units</span>
                                    </td>
                                    <td class="p-3 text-right font-bold text-emerald-600"
                                        x-text="'$' + (medicine.revenue ? parseFloat(medicine.revenue).toFixed(2) : '0.00')">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="material-card transform transition-all duration-300 hover:shadow-2xl">
                <div class="material-header gradient-green p-4">
                    <h4 class="text-lg font-bold mb-1">Recent Visits</h4>
                    <p class="text-green-100 text-sm opacity-80">Live patient queue</p>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="(visit, index) in recentVisits" :key="visit.id">
                        <div x-show="!isLoading" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            :style="`transition-delay: ${index * 100}ms`"
                            class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-white hover:shadow-lg transform transition-all duration-300 hover:scale-[1.02] border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-600 font-bold shadow-inner">
                                    <span x-text="(visit.patient?.name || 'U').charAt(0)"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800" x-text="visit.patient?.name || 'N/A'"></p>
                                    <p class="text-xs text-gray-400"
                                        x-text="new Date(visit.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})">
                                    </p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded text-xs font-bold uppercase tracking-wider relative"
                                :class="{
                                    'bg-amber-100 text-amber-600': visit.status === 'waiting',
                                    'bg-blue-100 text-blue-600': visit.status === 'in_progress',
                                    'bg-emerald-100 text-emerald-600': visit.status === 'completed'
                                }">
                                <span x-text="visit.status.replace('_', ' ')"></span>
                                <span x-show="visit.status === 'waiting'"
                                    class="absolute -top-1 -right-1 w-2 h-2 bg-amber-500 rounded-full animate-ping"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div> --}}
    </div>

    <script>
        let performanceChartInstance = null;
        let monthlyChartInstance = null;
        let polarChartInstance = null;
        let radarChartInstance = null;

        function dashboard() {
            return {
                stats: @json($stats),
                monthlyData: @json($monthlyData),
                monthlyPerformance: @json($monthlyPerformance ?? []),
                topMedicines: @json($topMedicines),
                recentVisits: @json($recentVisits),
                pendingPrescriptions: 0,
                years: [2026, 2027, 2028],
                selectedYear: new Date().getFullYear(),
                chartsInitialized: false,
                isLoading: true,
                outOfStockCount: {{ $outOfStockMedicines->count() }},
                lowStockCount: {{ $lowStockMedicines->count() }},
                targetPercentages: {
                    outOfStock: {{ $outOfStockPercentage ?? 0 }},
                    lowStock: {{ $lowStockPercentage ?? 0 }},
                    inStock: {{ $inStockPercentage ?? 0 }},
                    criticalStock: {{ $criticalStockPercentage ?? 0 }}
                },
                outOfStockPercentage: 0,
                lowStockPercentage: 0,
                inStockPercentage: 0,
                criticalStockPercentage: 0,
                medicineStockByCategory: @json($medicineStockByCategory ?? []),
                labReportsByType: @json($labReportsByType ?? []),

                init() {
                    this.formatRevenueData();

                    // Simulate loading
                    setTimeout(() => {
                        this.isLoading = false;
                        
                        // Animate percentages
                        setTimeout(() => {
                            this.outOfStockPercentage = this.targetPercentages.outOfStock;
                            this.lowStockPercentage = this.targetPercentages.lowStock;
                            this.inStockPercentage = this.targetPercentages.inStock;
                            this.criticalStockPercentage = this.targetPercentages.criticalStock;
                        }, 100);

                        this.$nextTick(() => {
                            if (!this.chartsInitialized) {
                                this.setupCharts();
                                this.chartsInitialized = true;
                            }
                        });
                    }, 1000);

                    this.startRealTimeUpdates();
                    this.updateTime();
                },

                formatRevenueData() {
                    if (this.stats.totalRevenue !== undefined) {
                        this.stats.totalRevenue = parseFloat(this.stats.totalRevenue) || 0;
                    }
                    if (this.topMedicines && this.topMedicines.length > 0) {
                        this.topMedicines = this.topMedicines.map(medicine => ({
                            ...medicine,
                            revenue: parseFloat(medicine.revenue) || 0
                        }));
                    }
                },

                setupCharts() {
                    this.destroyCharts();
                    this.createWeeklyCharts();
                    this.createMonthlyChart();
                    this.createDistributionCharts();
                    if (this.monthlyPerformance && this.monthlyPerformance.length > 0) {
                        this.createPerformanceChart();
                    }
                },

                destroyCharts() {
                    // Destroy polar chart
                    if (polarChartInstance) {
                        polarChartInstance.destroy();
                        polarChartInstance = null;
                    }

                    // Destroy radar chart
                    if (radarChartInstance) {
                        radarChartInstance.destroy();
                        radarChartInstance = null;
                    }

                    // Destroy monthly chart
                    if (monthlyChartInstance) {
                        monthlyChartInstance.destroy();
                        monthlyChartInstance = null;
                    }

                    // Destroy performance chart
                    if (performanceChartInstance) {
                        performanceChartInstance.destroy();
                        performanceChartInstance = null;
                    }

                    // Destroy weekly charts
                    ['chartPatients', 'chartVisits', 'chartMedicines', 'chartLab'].forEach(id => {
                        const chart = Chart.getChart(id);
                        if (chart) chart.destroy();
                    });
                },

                createWeeklyCharts() {
                    const chartAnimation = {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    };

                    const whiteChartOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#fff',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return `Value: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: "rgba(255,255,255,0.8)",
                                    font: {
                                        size: 10
                                    },
                                    callback: function(value) {
                                        return value;
                                    }
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.2)',
                                    borderDash: [5, 5],
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    color: "rgba(255,255,255,0.8)",
                                    font: {
                                        size: 10
                                    }
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false
                                }
                            }
                        }
                    };

                    const weekLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

                    // Patients Chart (Bar)
                    new Chart(document.getElementById('chartPatients'), {
                        type: 'bar',
                        data: {
                            labels: weekLabels,
                            datasets: [{
                                label: 'New Patients',
                                data: [50, 20, 10, 22, 50, 10, 40],
                                backgroundColor: "rgba(255, 255, 255, 0.8)",
                                barThickness: 12,
                                borderRadius: 4
                            }]
                        },
                        options: whiteChartOptions
                    });

                    // Visits Chart (Line)
                    new Chart(document.getElementById('chartVisits'), {
                        type: 'line',
                        data: {
                            labels: weekLabels,
                            datasets: [{
                                label: 'Total Visits',
                                data: [30, 90, 40, 140, 290, 290, 340],
                                borderColor: "rgba(255, 255, 255, 0.9)",
                                backgroundColor: "transparent",
                                borderWidth: 5,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: "white"
                            }]
                        },
                        options: whiteChartOptions
                    });

                    // Lab Chart (Bar)
                    new Chart(document.getElementById('chartLab'), {
                        type: 'bar',
                        data: {
                            labels: weekLabels,
                            datasets: [{
                                label: 'Lab Reports',
                                data: [40, 20, 10, 16, 24, 38, 55],
                                backgroundColor: "rgba(255, 255, 255, 0.8)",
                                barThickness: 12,
                                borderRadius: 4
                            }]
                        },
                        options: whiteChartOptions
                    });

                    // Medicines Chart (Line)
                    new Chart(document.getElementById('chartMedicines'), {
                        type: 'line',
                        data: {
                            labels: weekLabels,
                            datasets: [{
                                label: 'Pharmacy Dispense',
                                data: [50, 40, 300, 220, 500, 250, 400],
                                borderColor: "rgba(255, 255, 255, 0.9)",
                                backgroundColor: "transparent",
                                borderWidth: 5,
                                tension: 0.4,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: "white"
                            }]
                        },
                        options: whiteChartOptions
                    });
                },

                createMonthlyChart() {
                    const ctx = document.getElementById('monthlyChart').getContext('2d');
                    monthlyChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.monthlyData.map(d => d.month),
                            datasets: [{
                                    label: 'Medicines',
                                    data: this.monthlyData.map(d => d.medicines),
                                    borderColor: '#00acc1',
                                    backgroundColor: 'rgba(0, 172, 193, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 8
                                },
                                {
                                    label: 'Lab Reports',
                                    data: this.monthlyData.map(d => d.lab_reports),
                                    borderColor: '#e91e63',
                                    backgroundColor: 'transparent',
                                    borderDash: [5, 5],
                                    fill: false,
                                    tension: 0.4,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 8
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 2000,
                                easing: 'easeInOutQuart'
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    enabled: true,
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: '#ddd',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        borderDash: [2, 4],
                                        color: '#f0f0f0',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value;
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                },

                createPerformanceChart() {
                    const ctx = document.getElementById('performanceChart').getContext('2d');

                    if (performanceChartInstance) {
                        performanceChartInstance.destroy();
                    }

                    performanceChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.monthlyPerformance.map(d => d.month),
                            datasets: [{
                                    label: 'Visits',
                                    data: this.monthlyPerformance.map(d => d.visits),
                                    backgroundColor: '#ef5350',
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.8,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Medicines',
                                    data: this.monthlyPerformance.map(d => d.medicines),
                                    backgroundColor: '#ffa726',
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.8,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Lab Reports',
                                    data: this.monthlyPerformance.map(d => d.lab_reports),
                                    backgroundColor: '#66bb6a',
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.8,
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart'
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(50, 50, 50, 0.9)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        borderDash: [4, 4],
                                        color: '#e5e7eb',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        padding: 10,
                                        color: '#6b7280'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#6b7280'
                                    }
                                }
                            }
                        }
                    });
                },

                createDistributionCharts() {
                    // 1. Polar Area Chart (Medicines by Category)
                    const polarCtx = document.getElementById('polarStockChart');
                    if (polarCtx && this.medicineStockByCategory.length > 0) {
                        polarChartInstance = new Chart(polarCtx.getContext('2d'), {
                            type: 'polarArea',
                            data: {
                                labels: this.medicineStockByCategory.map(d => d.category),
                                datasets: [{
                                    data: this.medicineStockByCategory.map(d => d.stock),
                                    backgroundColor: [
                                        'rgba(239, 83, 80, 0.9)',
                                        'rgba(66, 165, 245, 0.9)',
                                        'rgba(102, 187, 106, 0.9)',
                                        'rgba(255, 167, 38, 0.9)',
                                        'rgba(171, 71, 188, 0.9)',
                                        'rgba(38, 198, 218, 0.9)'
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#ffffff',
                                    hoverBackgroundColor: [
                                        'rgba(239, 83, 80, 1)',
                                        'rgba(66, 165, 245, 1)',
                                        'rgba(102, 187, 106, 1)',
                                        'rgba(255, 167, 38, 1)',
                                        'rgba(171, 71, 188, 1)',
                                        'rgba(38, 198, 218, 1)'
                                    ],
                                    hoverBorderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'right',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15,
                                            font: {
                                                size: 11
                                            }
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        borderColor: '#fff',
                                        borderWidth: 1,
                                        padding: 12,
                                        cornerRadius: 6,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                let value = context.raw || 0;
                                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                let percentage = ((value / total) * 100).toFixed(1);
                                                return `${label}: ${value} units (${percentage}%)`;
                                            }
                                        }
                                    }
                                },
                                animation: {
                                    animateRotate: true,
                                    animateScale: true,
                                    duration: 2000,
                                    easing: 'easeOutQuart'
                                }
                            }
                        });
                    }

                    // 2. Radar Chart (Lab Reports by Type)
                    const radarCtx = document.getElementById('radarLabChart');
                    if (radarCtx && this.labReportsByType.length > 0) {
                        radarChartInstance = new Chart(radarCtx.getContext('2d'), {
                            type: 'radar',
                            data: {
                                labels: this.labReportsByType.map(d => d.test_type),
                                datasets: [{
                                    label: 'Test Volume',
                                    data: this.labReportsByType.map(d => d.count),
                                    backgroundColor: 'rgba(0, 150, 136, 0.2)',
                                    borderColor: '#00897b',
                                    pointBackgroundColor: '#00897b',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#00897b',
                                    borderWidth: 3,
                                    tension: 0.3,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        borderColor: '#00897b',
                                        borderWidth: 1,
                                        padding: 12,
                                        cornerRadius: 6,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                let value = context.raw || 0;
                                                return `${label}: ${value} tests`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        angleLines: {
                                            color: 'rgba(0, 0, 0, 0.1)',
                                            lineWidth: 1
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)',
                                            circular: true
                                        },
                                        pointLabels: {
                                            font: {
                                                size: 11,
                                                family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                                            },
                                            color: '#555'
                                        },
                                        ticks: {
                                            display: true,
                                            backdropColor: 'transparent',
                                            color: '#666',
                                            stepSize: 10
                                        }
                                    }
                                },
                                animation: {
                                    duration: 2000,
                                    easing: 'easeOutBounce'
                                },
                                elements: {
                                    line: {
                                        borderWidth: 3
                                    }
                                }
                            }
                        });
                    }
                },

                updateCharts() {
                    this.isLoading = true;

                    fetch(`/dashboard/chart-data?year=${this.selectedYear}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.monthlyAnalytics) this.monthlyData = data.monthlyAnalytics;
                            if (data.monthlyPerformance) this.monthlyPerformance = data.monthlyPerformance;

                            setTimeout(() => {
                                this.destroyCharts();
                                this.setupCharts();
                                this.isLoading = false;
                            }, 500);
                        })
                        .catch(error => {
                            console.error('Error updating charts:', error);
                            this.isLoading = false;
                        });
                },

                startRealTimeUpdates() {
                    setInterval(() => this.updateTime(), 1000);

                    setInterval(() => {
                        fetch('/dashboard/realtime-data')
                            .then(r => r.json())
                            .then(data => {
                                this.stats = data.stats;
                                this.outOfStockCount = data.outOfStockCount;
                                this.pendingPrescriptions = data.pendingPrescriptions;

                                // Fix: Use proper CSS easing values
                                if (this.$refs.outOfStockCircle) {
                                    this.$refs.outOfStockCircle.animate([{
                                            strokeDashoffset: '502.4'
                                        },
                                        {
                                            strokeDashoffset: 502.4 * (1 - this.outOfStockPercentage /
                                                100)
                                        }
                                    ], {
                                        duration: 1000,
                                        easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)' // Standard cubic-bezier instead of 'easeInOutQuart'
                                    });
                                }
                            })
                            .catch(error => console.error('Error fetching real-time data:', error));
                    }, 30000);
                },

                updateTime() {
                    const timeElement = document.getElementById('current-time-large');
                    if (timeElement) {
                        timeElement.textContent = new Date().toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        });
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #26c6da, #00acc1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #00acc1, #00838f);
        }

        /* Slow spin animation */
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }

        /* Hover glow effect */
        .hover-glow:hover {
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.6));
        }

        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Glass morphism effect */
        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
@endsection
