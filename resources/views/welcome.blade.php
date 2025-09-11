<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Property Management') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100 selection:bg-indigo-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <h1 class="text-2xl font-bold text-gray-900">Property Management System</h1>
                    </div>
                    @if (Route::has('login'))
                        <nav class="-mx-3 flex flex-1 justify-end">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        Start Free Trial
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>

                <main class="mt-6">
                    <div class="text-center">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Manage Your Properties with Ease</h2>
                        <p class="text-lg text-gray-600 mb-8">Complete property management solution for landlords and property managers</p>

                        @guest
                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors duration-200 text-center">
                                    Start Your Free Trial
                                </a>
                                <a href="{{ route('login') }}" class="inline-block bg-gray-200 text-gray-600 px-6 py-3 rounded-lg text-lg font-semibold hover:bg-gray-300 transition-colors duration-200 text-center border border-gray-300">
                                    Log In
                                </a>
                            </div>
                        @endguest
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
