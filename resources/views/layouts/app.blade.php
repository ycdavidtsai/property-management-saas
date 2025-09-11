<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Property Management' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <!-- Sidebar -->
        <aside class="bg-white shadow-lg w-64 hidden lg:block">
            <div class="h-full px-3 py-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Property Management</h2>
                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 rounded hover:bg-gray-100">
                        Dashboard
                    </a>
                    <a href="{{ route('properties.index') }}" class="flex items-center px-3 py-2 text-gray-700 rounded hover:bg-gray-100">
                        Properties
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-gray-700 rounded hover:bg-gray-100">
                        Profile
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                    <div class="flex items-center space-x-4">
                        <span>{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                @if (session('message'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
