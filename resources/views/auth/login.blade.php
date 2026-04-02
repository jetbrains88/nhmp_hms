<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> NHMP - Health Care+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Heartbeat Animation for the Logo */
        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            15% {
                transform: scale(1.15);
            }

            30% {
                transform: scale(1);
            }

            45% {
                transform: scale(1.15);
            }

            60% {
                transform: scale(1);
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-heartbeat {
            animation: heartbeat 2s infinite ease-in-out;
        }

        /* Moving Background Gradient */
        .animated-bg {
            /*background: linear-gradient(-45deg, #2563eb, #3b82f6, #06b6d4, #0891b2);*/
            background: linear-gradient(-45deg, #2563eb, #3b82f6, #4338CA, #6D28D9);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="h-screen w-full flex items-center justify-center bg-gray-100 p-4">

    <div
        class="bg-white rounded-3xl shadow-2xl flex flex-col md:flex-row w-full max-w-4xl overflow-hidden h-[700px] border border-gray-100">

        <div class="md:w-1/2 animated-bg p-12 flex flex-col justify-between relative text-white">
            <div
                class="absolute top-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full mix-blend-overlay filter blur-xl -translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full mix-blend-overlay filter blur-2xl translate-x-1/3 translate-y-1/3">
            </div>

            <div class="relative z-10">
                <div
                    class="bg-white/20 backdrop-blur-sm p-3 rounded-xl inline-block mb-6 shadow-inner border border-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white animate-heartbeat" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>

                </div>
                <h1 class="text-4xl font-bold mb-2">Health Care<span class="font-light opacity-80">+</span></h1>
                <p class="text-white text-sm opacity-90">Reception & Patient Management System</p>
            </div>

            <div class="relative z-10">
                <div class="space-y-4">
                    <!-- Fast Patient Queueing -->
                    <div
                        class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <span class="text-sm font-medium">Fast Patient Queueing</span>
                            <div class="text-xs text-white italic mt-1">"No, delays during patients processing"</div>
                        </div>
                    </div>

                    <!-- Instant Vitals Tracking -->
                    <div
                        class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                        <div class="flex-1">
                            <span class="text-sm font-medium">Instant Vitals Tracking</span>
                            <div class="text-xs text-white italic mt-1">"Live vitals monitoring across platform"</div>
                        </div>
                    </div>

                    <!-- E-Consultation -->
                    <div
                        class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5 text-white-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                            </path>
                        </svg>
                        <div class="flex-1">
                            <span class="text-sm font-medium">Secure E-Consultation</span>
                            {{--                        <div class="text-xs text-white/60 mt-1">Active: 3 video calls | Today: 24 consultations</div> --}}
                            <div class="text-xs text-white-200 italic mt-1">"Remote care that bridges distances"</div>
                        </div>
                    </div>

                    <!-- Pharmacy Inventory -->
                    <div
                        class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5 text-white-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <div class="flex-1">
                            <span class="text-sm font-medium">Health Care Pharmacy Inventory</span>
                            {{--                        <div class="text-xs text-white/60 mt-1">Low stock: 8 items | Expiring soon: 12 meds</div> --}}
                            <div class="text-xs text-white-200 italic mt-1">"Real-time tracking prevents shortages"
                            </div>
                        </div>
                    </div>

                    <!-- Lab Testing Integration -->
                    <div
                        class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5 text-white-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                        <div class="flex-1">
                            <span class="text-sm font-medium">Integrated Lab Testing</span>
                            {{--                        <div class="text-xs text-white/60 mt-1">Pending results: 18 | Completed today: 42 tests</div> --}}
                            <div class="text-xs text-white-200 italic mt-1">"Seamless diagnostics at your fingertips"
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-xs text-blue-200 mt-8 relative z-10">© 2025 NHMP | Health Care+ Smart Hospital OS. Secure
                Login.</p>
        </div>

        <div class="md:w-1/2 p-12 bg-white flex flex-col justify-center relative">

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                <p class="text-gray-500 text-sm">Please enter your credentials to access the reception dashboard.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="group">
                    <label
                        class="block text-xs font-bold text-gray-600 uppercase mb-2 group-focus-within:text-blue-600 transition-colors">Email
                        Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </span>
                        <input type="email" name="email" required
                            class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                            placeholder="reception@clinic.com">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="group">
                    <label
                        class="block text-xs font-bold text-gray-600 uppercase mb-2 group-focus-within:text-blue-600 transition-colors">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-10 py-3 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-blue-600 transition-colors">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="#"
                        class="text-sm text-blue-600 font-semibold hover:text-blue-700 hover:underline">Forgot
                        Password?</a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transform transition-all active:scale-95 shadow-lg hover:shadow-blue-500/30 flex items-center justify-center gap-2">
                    Sign In
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="fixed top-0 left-0 w-full h-full -z-10 bg-gray-50 overflow-hidden">
        <div
            class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-blue-200 mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
        </div>
        <div
            class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-cyan-200 mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
        </div>
        <div
            class="absolute -bottom-[20%] left-[20%] w-[40%] h-[40%] rounded-full bg-purple-200 mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000">
        </div>
    </div>

    <script>
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</body>

</html>
