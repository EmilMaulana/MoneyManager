<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <nav class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-indigo-600">MoneyManager</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('transactions') }}" class="text-gray-600 hover:text-indigo-600">Transactions</a>
                            <a href="{{ route('categories') }}" class="text-gray-600 hover:text-indigo-600">Categories</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-red-600">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600">Login</a>
                            <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:text-indigo-800">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-12">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
