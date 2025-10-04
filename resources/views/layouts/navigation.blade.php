{{-- Sidebar Navigation Component --}}
<div x-data="{
    sidebarOpen: false,
    userDropdownOpen: false
}" class="h-screen flex overflow-hidden bg-gray-100">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 flex z-40 md:hidden"
         style="display: none;">

        {{-- Overlay background --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-gray-600 bg-opacity-75"
             aria-hidden="true"></div>

        {{-- Mobile sidebar --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-white">

            {{-- Close button --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="sidebarOpen = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Mobile sidebar content --}}
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-4">
                    <x-application-logo class="h-8 w-auto" />
                    <span class="ml-2 text-xl font-semibold text-gray-900">Elandlord.click</span>
                </div>
                <nav class="mt-5 px-2 space-y-1">
                    @include('layouts.navigation-items')
                </nav>
            </div>

            {{-- Mobile user section --}}
            <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                @include('layouts.navigation-user-section')
            </div>
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64">
            <div class="flex flex-col h-0 flex-1 border-r border-gray-200 bg-white">

                {{-- Logo and brand --}}
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <x-application-logo class="h-8 w-auto" />
                        <span class="ml-2 text-xl font-semibold text-gray-900">Elandlord.click</span>
                    </div>

                    {{-- Organization info --}}
                    @if(auth()->user()->role !== 'tenant')
                        <div class="mt-4 px-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Organization</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">{{ auth()->user()->organization->name }}</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ App\Services\RoleService::getRoleDisplayName(auth()->user()->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tenant Property info , added by DT--}}
                    @if(auth()->user()->role === 'tenant')
                        <div class="mt-4 px-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Property Address:</div>
                                <div class="mt-1 text-sm font-medium text-gray-900">{{ auth()->user()->activeLease()->unit->property->address }}</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ App\Services\RoleService::getRoleDisplayName(auth()->user()->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Navigation items --}}
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        @include('layouts.navigation-items')
                    </nav>
                </div>

                {{-- Desktop user section --}}
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    @include('layouts.navigation-user-section')
                </div>
            </div>
        </div>
    </div>

    {{-- Main content area --}}
    <div class="flex flex-col w-0 flex-1 overflow-hidden">

        {{-- Top bar with mobile menu button --}}
        <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
            <button @click="sidebarOpen = true"
                    class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Page content --}}
        <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
            {{ $slot ?? '' }}
        </main>
    </div>
</div>
