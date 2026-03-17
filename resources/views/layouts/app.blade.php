<!DOCTYPE html>
<html lang="en" data-theme="{{ auth()->user()->getPreference('theme', 'vibrant') }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MedCare | Smart Hospital OS')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="current-branch" content="{{ session('current_branch_id') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Notification JS -->
    <script defer src="{{ asset('js/notification.js') }}"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        picotee: '#2E2787',
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        },
                        secondary: {
                            500: '#10b981',
                            600: '#059669',
                        },
                        // Role-specific accent colors
                        doctor: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                        },
                        pharmacy: {
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        reception: {
                            500: '#ec4899',
                            600: '#db2777',
                        },
                        lab: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                        },
                        nurse: {
                            500: '#10b981',
                            600: '#059669',
                        },
                        admin: {
                            500: '#6366f1',
                            600: '#4f46e5',
                        },
                        'tech-blue': {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                        },
                        md: {
                            green: '#4caf50',
                            teal: '#009688',
                            cyan: '#00bcd4',
                            blue: '#2196f3',
                            navy: '#3c4858',
                            indigo: '#3f51b5',
                            purple: '#9c27b0',
                            red: '#f44336',
                            rose: '#e91e63',
                            yellow: '#ffeb3b',
                            orange: '#ff9800',
                            black: '#212529',
                            gray: '#6c757d',
                            'light-gray': '#d2d2d2',
                            white: '#fff',

                        },
                        'purple-dark': { // Or any name you prefer, e.g., 'my-purple'
                            50: '#F4F2FA', // Example: Lighter shade from generator
                            100: '#EAE7F5',
                            200: '#D0C9E8',
                            300: '#B6B0D9',
                            400: '#9D97C5',
                            500: '#837BC0', // Your base color might land here
                            600: '#6861A2', // Or here, adjust based on generator
                            700: '#4E4685',
                            800: '#342E67',
                            900: '#1A174A',
                            950: '#0E0C2B', // Example: Darkest shade from generator
                        },
                        navy: { // Your custom color name (e.g., 'midnight', 'deep-blue')
                            50: '#e0e3f1', // Very light shade
                            100: '#c2c7e3',
                            200: '#a3adce',
                            300: '#8592b9',
                            400: '#6676a4',
                            500: '#485b8f', // Mid-tone
                            600: '#2a407a',
                            700: '#1a326d',
                            800: '#12245c', // Darker shade
                            900: '#0a164e', // Near your target
                            950: '#07104a', // Even darker
                            // You can also just define a single color if you don't need shades:
                            // 'midnight': '#10165c',
                        },
                        "jade": {
                            50: "#BDFFDF",
                            100: "#6CFFC2",
                            200: "#0DE8A4",
                            300: "#0ACC90",
                            400: "#07B17C",
                            500: "#059669",
                            600: "#037753",
                            700: "#02593D",
                            800: "#013D29",
                            900: "#002316",
                            950: "#00150B"
                        },
                        "maroon": {
                            50: "#FEF1F1",
                            100: "#FCDEDE",
                            200: "#FAC1C1",
                            300: "#F89D9D",
                            400: "#F77B7B",
                            500: "#F64A4A",
                            600: "#E22525",
                            700: "#B91C1C",
                            800: "#811010",
                            900: "#490505",
                            950: "#330303"
                        },
                        "violet-x": {
                            50: "#F2EFFE",
                            100: "#E5DFFD",
                            200: "#CBBEFB",
                            300: "#B39DFA",
                            400: "#9D7BF8",
                            500: "#8856F5",
                            600: "#7422F1",
                            700: "#5817BA",
                            800: "#3D0D85",
                            900: "#240554",
                            950: "#17023B"
                        },
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'glow': '0 0 15px rgba(59, 130, 246, 0.2)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .menu-item-hover:hover {
            background: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%);
            border-right: 3px solid #3b82f6;
        }

        .menu-item-active {
            background: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%);
            border-right: 3px solid #3b82f6;
            color: #2563eb;
            font-weight: 600;
        }

        /* --- Theme System --- */
        :root, [data-theme="vibrant"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --accent: #f59e0b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --shadow: rgba(79, 70, 229, 0.08);
            --sidebar-active-bg: linear-gradient(90deg, #eef2ff 0%, #ffffff 100%);
        }

        [data-theme="nhmp"] {
            --bg-body: #F1F5F9;
            --bg-card: #FFFFFF;
            --primary: #1E3A8A;
            --primary-light: #eff6ff;
            --accent: #D97706;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --shadow: rgba(30, 58, 138, 0.08);
            --sidebar-active-bg: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%);
        }

        [data-theme="clinical"] {
            --bg-body: #F0F4F8;
            --primary: #2563EB;
            --primary-light: #eff6ff;
            --accent: #0891B2;
            --text-main: #1E293B;
            --shadow: rgba(37, 99, 235, 0.08);
            --sidebar-active-bg: linear-gradient(90deg, #eff6ff 0%, #ffffff 100%);
        }

        [data-theme="green"] {
            --bg-body: #F2FBF7;
            --primary: #10B981;
            --primary-light: #ecfdf5;
            --accent: #14B8A6;
            --text-main: #064E3B;
            --shadow: rgba(16, 185, 129, 0.08);
            --sidebar-active-bg: linear-gradient(90deg, #ecfdf5 0%, #ffffff 100%);
        }

        [data-theme="minimal"] {
            --bg-body: #F8FAFC;
            --primary: #334155;
            --primary-light: #f1f5f9;
            --accent: #64748B;
            --text-main: #020617;
            --shadow: rgba(51, 65, 85, 0.05);
            --sidebar-active-bg: linear-gradient(90deg, #f1f5f9 0%, #ffffff 100%);
        }

        [data-theme="warm"] {
            --bg-body: #FAFAF9;
            --primary: #D95D39;
            --primary-light: #fff7ed;
            --accent: #DDA15E;
            --text-main: #292524;
            --shadow: rgba(217, 93, 57, 0.08);
            --sidebar-active-bg: linear-gradient(90deg, #fff7ed 0%, #ffffff 100%);
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
        }

        .menu-item-active {
            background: var(--sidebar-active-bg) !important;
            border-right: 3px solid var(--primary) !important;
            color: var(--primary) !important;
        }

        .menu-item-hover:hover {
            background: var(--sidebar-active-bg) !important;
            border-right: 3px solid var(--primary) !important;
        }
        
        .bg-primary-theme { background-color: var(--primary); }
        .text-primary-theme { color: var(--primary); }
        .border-primary-theme { border-color: var(--primary); }
        .shadow-theme { box-shadow: 0 4px 20px var(--shadow); }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased text-slate-600 bg-slate-50 overflow-hidden"
    x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        isMobile: window.innerWidth < 1024,
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen },
        notificationsOpen: false,
        profileOpen: false,
        branchSwitcherOpen: false,
        currentBranch: null,
        branches: [],
        async loadBranches() {
            try {
                const response = await fetch('/api/user/branches');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.branches = data;

                const currentBranchElement = document.querySelector('meta[name=current-branch]');
                if (currentBranchElement) {
                    const currentBranchId = currentBranchElement.content;
                    this.currentBranch = this.branches.find(b => b.id == currentBranchId);
                } else if (this.branches.length > 0) {
                    this.currentBranch = this.branches[0];
                }
            } catch (error) {
                console.error('Failed to load branches:', error);
                this.branches = [];
            }
        },
        init() {
            this.loadBranches();
        }
    }"
    @resize.window="isMobile = window.innerWidth < 1024; if(!isMobile) sidebarOpen = true">

    <div class="flex h-screen overflow-hidden relative">
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen && isMobile" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 border-r border-slate-200 shadow-xl sidebar-transition flex flex-col lg:static shadow-theme"
            style="background-color: var(--bg-card);"
            :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-0 -translate-x-full lg:w-0 lg:translate-x-0 lg:overflow-hidden'">

            <!-- Logo Area -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100" style="background-color: var(--bg-card);">
                <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                        <i class="fas fa-hospital text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">MedCare</h1>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold">Hospital OS</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Branch Switcher -->
            <!-- Branch Switcher -->
            <div class="px-4 py-3 border-b border-slate-100" x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2 bg-slate-50 rounded-xl hover:bg-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-code-branch text-slate-400"></i>
                        <span class="text-sm font-medium text-slate-700"
                            x-text="currentBranch?.name || 'Select Branch'"></span>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-slate-400" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute mt-1 w-56 bg-white rounded-xl shadow-lg border border-slate-100 z-50 py-1 max-h-60 overflow-y-auto">
                    <template x-for="branch in branches" :key="branch.id">
                        <button
                            @click="
                    fetch(`/branch/switch/${branch.id}`, { 
                        method: 'POST', 
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        } 
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            return response.json().then(data => {
                                console.error('Failed to switch branch:', data.message || 'Unknown error');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error switching branch:', error);
                    });
                    open = false;
                "
                            class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2">
                            <i class="fas fa-circle text-[8px]"
                                :class="branch.type === 'CMO' ? 'text-purple-500' : 'text-blue-500'"></i>
                            <span x-text="branch.name"></span>
                            <span class="ml-auto text-xs text-slate-400" x-text="branch.type"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Global Search -->
            <div class="hidden lg:block max-w-md mx-4">
                <div class="relative" x-data="{ search: '', results: [], showResults: false, loading: false }" @click.away="showResults = false">
                    <div class="relative">
                        <input type="text" x-model="search"
                            @input.debounce.300ms="if (search.length > 1) { loading = true; fetch(`/api/search?q=${search}`).then(r => r.json()).then(data => { results = data; loading = false; showResults = true; }) } else { results = []; showResults = false; }"
                            @focus="if (results.length > 0) showResults = true"
                            placeholder="Global search (patients, medicines, visits...)"
                            class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                        <i x-show="loading" class="fas fa-spinner fa-spin absolute right-3 top-3 text-slate-400"></i>
                    </div>
                    <div x-show="showResults" x-cloak
                        class="absolute mt-2 w-full bg-white rounded-xl shadow-xl border border-slate-100 z-50 max-h-96 overflow-y-auto">
                        <template x-for="result in results" :key="result.id">
                            <a :href="result.url"
                                class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i
                                            :class="{
                                                'fas fa-user text-blue-500': result.icon === 'user',
                                                'fas fa-pills text-amber-500': result.icon === 'pill',
                                                'fas fa-clipboard text-green-500': result.icon === 'clipboard',
                                                'fas fa-flask text-purple-500': result.icon === 'flask'
                                            }"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-800" x-text="result.title"></div>
                                        <div class="text-xs text-slate-500" x-text="result.subtitle"></div>
                                    </div>
                                    <span class="ml-auto text-xs px-2 py-1 bg-slate-100 rounded-full text-slate-600"
                                        x-text="result.type"></span>
                                </div>
                            </a>
                        </template>
                        <div x-show="results.length === 0 && search.length > 1"
                            class="px-4 py-3 text-sm text-slate-500 text-center">
                            No results found
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 menu-item-hover {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'text-slate-500' }}">
                    <i class="fas fa-home w-6 text-center text-lg"></i>
                    <span class="ml-3 truncate">Dashboard</span>
                </a>

                @auth
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin'))
                        <!-- Admin Menu -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('admin.*') ? 'true' : 'false' }} }" class="mt-2">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-indigo-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-shield-alt w-6 text-center"></i>
                                    <span>Administration</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-pie w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ route('admin.users.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-users w-6 text-center"></i>
                                    <span class="ml-2">Users</span>
                                </a>

                                <a href="{{ route('admin.roles.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-user-tag w-6 text-center"></i>
                                    <span class="ml-2">Roles</span>
                                </a>

                                <a href="{{ route('admin.permissions.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.permissions.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-shield-alt w-6 text-center"></i>
                                    <span class="ml-2">Permissions</span>
                                </a>

                                <a href="{{ route('admin.branches.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.branches.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-code-branch w-6 text-center"></i>
                                    <span class="ml-2">Branches</span>
                                </a>
                                <a href="{{ route('admin.audit.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.audit.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-history w-6 text-center"></i>
                                    <span class="ml-2">Audit Logs</span>
                                </a>
                                @if(Route::has('admin.reports.index'))
                                <a href="{{ route('admin.reports.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-indigo-50 {{ request()->routeIs('admin.reports.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-bar w-6 text-center"></i>
                                    <span class="ml-2">Reports</span>
                                </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('doctor'))
                        <!-- Doctor Menu -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('doctor.*') ? 'true' : 'false' }} }" class="mt-2">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-blue-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-stethoscope w-6 text-center"></i>
                                    <span>Medical</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('doctor.dashboard') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-blue-50 {{ request()->routeIs('doctor.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">
                                    <i class="fas fa-tachometer-alt w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ route('doctor.consultancy') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-blue-50 {{ request()->routeIs('doctor.consultancy*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">
                                    <i class="fas fa-clinic-medical w-6 text-center"></i>
                                    <span class="ml-2">Consultations</span>
                                    @php
                                        $waitingCount = app(
                                            \App\Services\VisitService::class,
                                        )->getWaitingCountForDoctor(auth()->id());
                                    @endphp
                                    @if ($waitingCount > 0)
                                        <span
                                            class="ml-auto bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $waitingCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('doctor.lab-orders.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-blue-50 {{ request()->routeIs('doctor.lab-orders.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">
                                    <i class="fas fa-flask w-6 text-center"></i>
                                    <span class="ml-2">Lab Orders</span>
                                </a>
                                <a href="{{ route('doctor.appointments.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-blue-50 {{ request()->routeIs('doctor.appointments.*') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">
                                    <i class="fas fa-calendar-check w-6 text-center"></i>
                                    <span class="ml-2">Appointments</span>
                                </a>
                                <a href="{{ route('doctor.reports') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-blue-50 {{ request()->routeIs('doctor.reports') ? 'text-blue-600 bg-blue-50' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-line w-6 text-center"></i>
                                    <span class="ml-2">Reports</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('reception'))
                        <!-- Reception Menu -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('reception.*') ? 'true' : 'false' }} }" class="mt-2">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-pink-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-headset w-6 text-center"></i>
                                    <span>Front Desk</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <!-- <a href="{{ Route::has('reception.dashboard') ? route('reception.dashboard') : url('/reception') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.dashboard') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-tachometer-alt w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a> -->
                                <a href="{{ Route::has('reception.patients.create') ? route('reception.patients.create') : url('/reception/patients/create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50">
                                    <i class="fas fa-user-injured w-6 text-center"></i>
                                    <span class="ml-2">Reception Desk</span>
                                </a>
                                <a href="{{ Route::has('reception.patients.index') ? route('reception.patients.index') : url('/reception/patients') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.patients.*') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-users w-6 text-center"></i>
                                    <span class="ml-2">Patients</span>
                                </a>
                                <a href="{{ Route::has('reception.visits.create') ? route('reception.visits.create') : url('/reception/visits/create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50">
                                    <i class="fas fa-user-plus w-6 text-center"></i>
                                    <span class="ml-2">New Visit</span>
                                </a>
                                <a href="{{ Route::has('reception.queue') ? route('reception.queue') : url('/reception/queue') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.queue') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-list-ol w-6 text-center"></i>
                                    <span class="ml-2">Queue</span>
                                </a>
                                <a href="{{ route('reception.appointments.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.appointments.*') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-calendar-alt w-6 text-center"></i>
                                    <span class="ml-2">Appointments</span>
                                </a>
                                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 border-t border-slate-100 pt-2">Setup Config</div>
                                <a href="{{ route('reception.offices.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.offices.*') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-sitemap w-6 text-center"></i>
                                    <span class="ml-2">Offices & HQ</span>
                                </a>
                                <a href="{{ route('reception.designations.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-pink-50 {{ request()->routeIs('reception.designations.*') ? 'text-pink-600 bg-pink-50' : 'text-slate-600' }}">
                                    <i class="fas fa-id-badge w-6 text-center"></i>
                                    <span class="ml-2">Designations</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('pharmacy'))
                        <!-- Pharmacy Menu -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('pharmacy.*') ? 'true' : 'false' }} }" class="mt-2">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-amber-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-pills w-6 text-center"></i>
                                    <span>Pharmacy</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('pharmacy.dashboard') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.dashboard') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-tachometer-alt w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ route('pharmacy.prescriptions.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.prescriptions.*') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-prescription w-6 text-center"></i>
                                    <span class="ml-2">Prescriptions</span>
                                    @php
                                        $pendingCount = \App\Models\Prescription::where(
                                            'branch_id',
                                            session('current_branch_id'),
                                        )
                                            ->whereIn('status', ['pending', 'partially_dispensed'])
                                            ->count();
                                    @endphp
                                    @if ($pendingCount > 0)
                                        <span
                                            class="ml-auto bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('pharmacy.medicines.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.medicines.*') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-capsules w-6 text-center"></i>
                                    <span class="ml-2">Medicines</span>
                                </a>
                                <a href="{{ route('pharmacy.inventory') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.inventory') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-boxes w-6 text-center"></i>
                                    <span class="ml-2">Inventory</span>
                                </a>
                                <a href="{{ Route::has('pharmacy.alerts.index') ? route('pharmacy.alerts.index') : '#' }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.alerts.*') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-exclamation-triangle w-6 text-center"></i>
                                    <span class="ml-2">Alerts</span>
                                    @php
                                        $alertCount = \App\Models\StockAlert::where(
                                            'branch_id',
                                            session('current_branch_id'),
                                        )
                                            ->where('is_resolved', false)
                                            ->count();
                                    @endphp
                                    @if ($alertCount > 0)
                                        <span
                                            class="ml-auto bg-rose-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $alertCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('pharmacy.dispense.history') }}"
                                   class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.dispense.history') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-history w-6 text-center"></i>
                                    <span class="ml-2">History</span>
                                </a>
                                <a href="{{ route('pharmacy.reports') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.reports') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-chart-bar w-6 text-center"></i>
                                    <span class="ml-2">Reports</span>
                                </a>
                                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 border-t border-slate-100 pt-2">Setup Config</div>
                                <a href="{{ route('pharmacy.medicine-categories.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.medicine-categories.*') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-tags w-6 text-center"></i>
                                    <span class="ml-2">Categories</span>
                                </a>
                                <a href="{{ route('pharmacy.medicine-forms.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-amber-50 {{ request()->routeIs('pharmacy.medicine-forms.*') ? 'text-amber-600 bg-amber-50' : 'text-slate-600' }}">
                                    <i class="fas fa-capsules w-6 text-center"></i>
                                    <span class="ml-2">Dosage Forms</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('lab'))
                        <!-- LAB SECTION -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('lab.*') ? 'true' : 'false' }} }" class="mt-4">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-purple-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-flask w-6 text-center"></i>
                                    <span>Laboratory</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <a href="{{ Route::has('lab.dashboard') ? route('lab.dashboard') : url('/lab/dashboard') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-purple-50 {{ request()->routeIs('lab.dashboard') ? 'text-purple-600 bg-purple-50' : 'text-slate-600' }}">
                                    <i class="fas fa-tachometer-alt w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ Route::has('lab.orders.index') ? route('lab.orders.index') : url('/lab/orders') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-purple-50 {{ request()->routeIs('lab.orders.*') ? 'text-purple-600 bg-purple-50' : 'text-slate-600' }}">
                                    <i class="fas fa-vial w-6 text-center"></i>
                                    <span class="ml-2">Lab Orders</span>
                                </a>
                                <a href="{{ Route::has('lab.reports.index') ? route('lab.reports.index') : url('/lab/reports') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-purple-50 {{ request()->routeIs('lab.reports.*') ? 'text-purple-600 bg-purple-50' : 'text-slate-600' }}">
                                    <i class="fas fa-file-pdf w-6 text-center"></i>
                                    <span class="ml-2">Reports</span>
                                </a>
                                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2 border-t border-slate-100 pt-2">Setup Config</div>
                                <a href="{{ route('lab.test-types.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-purple-50 {{ request()->routeIs('lab.test-types.*') ? 'text-purple-600 bg-purple-50' : 'text-slate-600' }}">
                                    <i class="fas fa-microscope w-6 text-center"></i>
                                    <span class="ml-2">Test Types</span>
                                </a>
                                <a href="{{ route('lab.test-parameters.index') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-purple-50 {{ request()->routeIs('lab.test-parameters.*') ? 'text-purple-600 bg-purple-50' : 'text-slate-600' }}">
                                    <i class="fas fa-list-alt w-6 text-center"></i>
                                    <span class="ml-2">Parameters</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (auth()->user()->hasRole('nurse'))
                        <!-- Nurse Menu -->
                        <div x-data="{ isMenuOpen: {{ request()->routeIs('nurse.*') ? 'true' : 'false' }} }" class="mt-2">
                            <button @click="isMenuOpen = !isMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-3 text-sm font-medium text-slate-600 hover:text-green-600 rounded-xl hover:bg-slate-50">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-user-nurse w-6 text-center"></i>
                                    <span>Nursing</span>
                                </span>
                                <i class="fas fa-chevron-down text-xs transition-transform"
                                    :class="{ 'rotate-180': isMenuOpen }"></i>
                            </button>

                            <div x-show="isMenuOpen" x-collapse class="pl-4 space-y-1 mt-1">
                                <a href="{{ Route::has('nurse.dashboard') ? route('nurse.dashboard') : url('/nurse/dashboard') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-green-50 {{ request()->routeIs('nurse.dashboard') ? 'text-green-600 bg-green-50' : 'text-slate-600' }}">
                                    <i class="fas fa-heartbeat w-6 text-center"></i>
                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ Route::has('nurse.vitals.create') ? route('nurse.vitals.create') : url('/nurse/vitals/create') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-green-50">
                                    <i class="fas fa-thermometer-half w-6 text-center"></i>
                                    <span class="ml-2">Record Vitals</span>
                                </a>
                                <a href="{{ Route::has('nurse.patients') ? route('nurse.patients') : url('/nurse/patients') }}"
                                    class="flex items-center px-3 py-2 text-sm rounded-xl hover:bg-green-50 {{ request()->routeIs('nurse.patients') ? 'text-green-600 bg-green-50' : 'text-slate-600' }}">
                                    <i class="fas fa-procedures w-6 text-center"></i>
                                    <span class="ml-2">Patients</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden relative" style="background-color: var(--bg-body);">
            <!-- Header -->
            <header class="h-20 sticky top-0 z-30 border-b border-slate-200 shadow-sm" style="background-color: var(--bg-card);">
                <div class="px-6 h-full flex justify-between items-center">
                    <!-- Left side -->
                    <div class="flex items-center gap-4">
                        <button @click="toggleSidebar()"
                            class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-blue-600 transition-colors">
                            <i class="fas fa-bars text-xl" x-show="!sidebarOpen"></i>
                            <i class="fas fa-chevron-left text-xl" x-show="sidebarOpen"></i>
                        </button>

                        <div class="hidden md:block animate-fade-in">
                            <nav class="flex text-xs text-slate-400 mb-0.5">
                                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                                    <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600"><i
                                                class="fas fa-home"></i></a></li>
                                    <li><i class="fas fa-chevron-right text-[8px]"></i></li>
                                    <li><span class="font-medium text-slate-600">@yield('page-title', 'Dashboard')</span></li>
                                </ol>
                            </nav>
                            <h2 class="text-xl font-bold text-slate-800 leading-tight">@yield('page-title', 'Overview')</h2>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-4">
                        <!-- System Time -->
                        <div class="hidden lg:block text-right mr-2">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider">System Time</div>
                            <div id="current-time" class="text-sm font-mono font-bold text-slate-700"></div>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="{
                            notifications: [],
                            unreadCount: 0,
                            async fetchNotifications() {
                                try {
                                    const response = await fetch('{{ route('notifications.unread-count') }}');
                                    const data = await response.json();
                                    this.unreadCount = data.count;
                                } catch (e) { console.error(e); }
                            },
                            init() {
                                this.fetchNotifications();
                                setInterval(() => this.fetchNotifications(), 60000);
                            }
                        }">
                            <button @click="notificationsOpen = !notificationsOpen"
                                @click.away="notificationsOpen = false"
                                class="relative p-2.5 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                <i class="far fa-bell text-lg"></i>
                                <span class="absolute top-0 right-0 h-3 w-3" x-show="unreadCount > 0">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
                                </span>
                            </button>

                            <div x-show="notificationsOpen" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
                                <div
                                    class="px-4 py-3 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                    <span class="text-sm font-bold text-slate-700">Notifications</span>
                                    <a href="#"
                                        @click.prevent="fetch('{{ route('notifications.mark-all-read') }}', {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => { unreadCount = 0; })"
                                        class="text-xs text-blue-600 cursor-pointer hover:underline">Mark all read</a>
                                </div>
                                <div class="max-h-64 overflow-y-auto p-0">
                                    <div x-show="unreadCount === 0" class="p-4 text-center text-slate-400 text-xs">
                                        No new notifications
                                    </div>
                                    <div x-show="unreadCount > 0" class="p-4 text-center text-blue-600 text-sm">
                                        <span x-text="unreadCount"></span> unread notifications.
                                        <br>
                                        <a href="{{ route('notifications.index') }}"
                                            class="underline text-xs mt-2 block">View all</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile -->
                        <div class="relative pl-2 border-l border-slate-200 ml-2">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                                class="flex items-center gap-3 focus:outline-none">
                                <div class="text-right hidden md:block">
                                    <p class="text-sm font-bold text-slate-700">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">
                                        @php
                                            $roles = auth()->user()->roles->pluck('display_name')->implode(', ');
                                        @endphp
                                        {{ $roles }}
                                    </p>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 p-0.5 shadow-md hover:scale-105 transition-transform">
                                    <div class="w-full h-full rounded-full bg-white flex items-center justify-center">
                                        <span
                                            class="font-bold text-blue-600">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                    </div>
                                </div>
                            </button>

                            <div x-show="profileOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute right-0 mt-4 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 py-2">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.show') }}"
                                    class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                                    <i class="fas fa-user-circle mr-2"></i> Profile
                                </a>
                                <a href="{{ route('profile.settings') }}"
                                    class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <a href="{{ route('profile.change-password') }}"
                                    class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">
                                    <i class="fas fa-key mr-2"></i> Change Password
                                </a>
                                <div class="h-px bg-slate-100 my-1"></div>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="block px-4 py-2 text-sm text-rose-500 hover:bg-rose-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 scroll-smooth min-h-0 min-w-0">
                <!-- Page Content -->
                <div class="animate-fade-in flex-1">
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            <footer
                class="bg-white px-4 lg:px-8 py-4 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 shrink-0 z-30 relative">
                <div>
                    <span class="font-bold text-slate-700">MedCare Hospital OS</span> &copy; {{ date('Y') }} -
                    Multi-Tenant v2.5
                </div>
                <div class="flex items-center gap-4 mt-2 md:mt-0">
                    <span><i class="fas fa-code-branch mr-1"></i> Branch:
                        {{ session('current_branch_name', 'CMO') }}</span>
                    <span><i class="fas fa-server mr-1"></i> v2.5 Stable</span>
                    <span><i class="fas fa-shield-alt mr-1"></i> Secure</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- System Clock Script -->
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });
            const el = document.getElementById('current-time');
            if (el) el.innerHTML = `${dateString} | ${timeString}`;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Flash Notifications from Session
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showSuccess("{{ session('success') }}");
            @endif

            @if (session('error'))
                showError("{{ session('error') }}");
            @endif

            @if (session('warning'))
                showWarning("{{ session('warning') }}");
            @endif

            @if (session('info'))
                showInfo("{{ session('info') }}");
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    showError("{{ $error }}", "Validation Error");
                @endforeach
            @endif
        });
    </script>

    @stack('scripts')
</body>

</html>
