<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-[#f8fafc] text-slate-900 pb-20 md:pb-0">
        <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/60 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="group flex items-center space-x-2">
                            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-indigo-200 shadow-lg group-hover:scale-105 transition-transform">
                                <span class="text-white font-bold text-sm">$</span>
                            </div>
                            <span class="text-lg md:text-xl font-extrabold tracking-tight text-slate-800 group-hover:text-indigo-600 transition-colors">MoneyManager</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-6">
                        @auth
                            <div class="hidden md:flex items-center space-x-1">
                                <a href="{{ route('accounts') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('accounts') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition-colors">Accounts</a>
                                <a href="{{ route('transactions') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('transactions') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition-colors">Transactions</a>
                                <a href="{{ route('categories') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('categories') ? 'text-indigo-600' : 'text-slate-600 hover:text-indigo-600' }} transition-colors">Categories</a>
                            </div>
                            <div class="h-6 w-px bg-slate-200 mx-2 hidden md:block"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-[10px] md:text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors flex items-center space-x-1">
                                    <span class="hidden sm:inline">Logout</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-md shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        @auth
        <!-- Mobile Bottom Navigation -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-slate-200 z-[100] pb-safe">
            <div class="flex items-center justify-around h-20 px-4">
                <a href="{{ route('accounts') }}" class="flex flex-col items-center justify-center space-y-1 w-full {{ request()->routeIs('accounts') ? 'text-indigo-600' : 'text-slate-400' }}">
                    <div class="p-2 rounded-xl {{ request()->routeIs('accounts') ? 'bg-indigo-50' : '' }} transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Akun</span>
                </a>
                
                <a href="{{ route('transactions') }}" class="flex flex-col items-center justify-center space-y-1 w-full {{ request()->routeIs('transactions') ? 'text-indigo-600' : 'text-slate-400' }}">
                    <div class="p-2 rounded-xl {{ request()->routeIs('transactions') ? 'bg-indigo-50' : '' }} transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Histori</span>
                </a>

                <a href="{{ route('categories') }}" class="flex flex-col items-center justify-center space-y-1 w-full {{ request()->routeIs('categories') ? 'text-indigo-600' : 'text-slate-400' }}">
                    <div class="p-2 rounded-xl {{ request()->routeIs('categories') ? 'bg-indigo-50' : '' }} transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Kategori</span>
                </a>
            </div>
        </nav>
        @endauth

        <main class="py-10 md:py-12 min-h-[calc(100vh-64px)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        <footer class="py-8 border-t border-slate-200 bg-white mb-20 md:mb-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-400 text-[10px] md:text-xs font-medium">
                &copy; {{ date('Y') }} MoneyManager. Built for clarity & focus.
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
