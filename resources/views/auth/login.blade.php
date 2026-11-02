@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="fixed inset-0 w-screen h-screen flex flex-col lg:flex-row bg-[#0b0e14] dark:bg-[#10141D] m-0 p-0 overflow-hidden">
        
        <!-- LEFT SIDE: Professional Enterprise Brand Banner (Centered & Prominent) -->
        <div class="lg:w-1/2 h-full bg-[#0b0e14] text-white flex flex-col justify-between p-8 lg:p-12 relative overflow-hidden border-r border-slate-800/80">
            <!-- Subtle Background Glow -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Centered Branding & Hero Section -->
            <div class="flex-1 flex flex-col items-center justify-center text-center space-y-6 relative z-10">
                <!-- Brand Name & Exponent -->
                <div class="space-y-2">
                    <h2 class="text-4xl lg:text-5xl font-black tracking-wider text-white drop-shadow-md">
                        STEVEN HYDROTECH
                    </h2>
                    <div class="flex items-center justify-center gap-3">
                        <div class="h-[2px] w-14 bg-sky-400"></div>
                        <span class="text-sm uppercase tracking-[0.35em] text-sky-300 font-extrabold">E X P O N E N T</span>
                        <div class="h-[2px] w-14 bg-sky-400"></div>
                    </div>
                </div>

                <!-- Prominent Centered Standalone Logo -->
                <div class="w-40 h-40 flex items-center justify-center p-2 my-2">
                    <img src="{{ asset('images/logo.svg') }}" alt="Steven Hydrotech Logo" class="w-full h-full object-contain drop-shadow-2xl">
                </div>

                <!-- Subtitle -->
                <p class="text-xs text-sky-200 font-bold tracking-[0.3em] uppercase pt-2">
                    WATER TREATMENT AND SUPPLY SERVICES
                </p>
            </div>

            <!-- Bottom Features Grid (All 4 Features) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 border-t border-slate-800/80 relative z-10">
                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-xs space-y-1">
                    <div class="text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-200">Predict Demand</h4>
                    <p class="text-[10px] text-slate-400 leading-snug">Forecast requirements using analytics.</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-xs space-y-1">
                    <div class="text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-200">Optimize Orders</h4>
                    <p class="text-[10px] text-slate-400 leading-snug">Determine ideal restock quantities.</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-xs space-y-1">
                    <div class="text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-200">Reduce Costs</h4>
                    <p class="text-[10px] text-slate-400 leading-snug">Lower holding costs & avoid stockouts.</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/80 backdrop-blur-xs space-y-1">
                    <div class="text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-200">Smart Decisions</h4>
                    <p class="text-[10px] text-slate-400 leading-snug">Real-time insights for management.</p>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Clean Login Form -->
        <div class="lg:w-1/2 h-full flex flex-col justify-center items-center p-8 lg:p-16 bg-white dark:bg-[#10141D] overflow-y-auto">
            <div class="w-full max-w-md space-y-6">
                <div class="text-center lg:text-left">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Welcome!</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Sign in to your account to continue</p>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="loginname" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                            Email address or Username
                        </label>
                        <input type="text" name="loginname" id="loginname" value="{{ old('loginname') }}" required
                               placeholder="admin@stevenhydrotech.com"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                                      focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div>
                        <label for="loginpassword" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" name="loginpassword" id="loginpassword" required
                                   placeholder="••••••••••••"
                                   class="w-full rounded-xl bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-3 pr-10 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-2 focus:ring-sky-500">

                            <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 cursor-pointer">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="remember" id="remember"
                                   class="rounded border-gray-300 dark:border-slate-700 text-sky-600 focus:ring-sky-500">
                            <label for="remember" class="text-gray-600 dark:text-slate-300">Remember Me</label>
                        </div>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit"
                            class="w-full bg-white border-2 border-sky-600 text-sky-700 hover:bg-sky-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-sky-600/80 dark:text-sky-400 dark:hover:bg-sky-600 dark:hover:text-white font-semibold py-3.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Sign In to System
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('loginpassword');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
        });
    }
</script>
@endpush