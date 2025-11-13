<!-- Navigation component with notifications bell -->
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(in_array(Auth::user()->role, ['landlord', 'manager', 'admin']))
                        <x-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.*')">
                            {{ __('Properties') }}
                        </x-nav-link>
                        <x-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                            {{ __('Tenants') }}
                        </x-nav-link>
                        <x-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*')">
                            {{ __('Vendors') }}
                        </x-nav-link>
                    @endif

                    <x-nav-link :href="route('maintenance-requests.index')" :active="request()->routeIs('maintenance-requests.*')">
                        {{ __('Maintenance') }}
                    </x-nav-link>

                    <x-nav-link :href="route('communications.index')" :active="request()->routeIs('communications.*')">
                        {{ __('Communications') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown & Notifications -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Notifications Bell -->
                <div class="relative mr-3" x-data="{
                    open: false,
                    unreadCount: {{ Auth::user()->notifications()->whereNull('read_at')->count() }}
                }">
                    <button @click="open = !open"
                            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0"
                              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                              x-text="unreadCount"></span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                         style="display: none;">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @php
                                $recentNotifications = Auth::user()->notifications()
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp

                            @forelse($recentNotifications as $notification)
                                <a href="{{ route('communications.notifications') }}"
                                   class="block p-4 hover:bg-gray-50 border-b last:border-b-0 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}">
                                    <div class="flex items-start">
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            @if($notification->subject)
                                                <p class="text-sm font-medium text-gray-800 truncate">
                                                    {{ $notification->subject }}
                                                </p>
                                            @endif
                                            <p class="text-sm text-gray-600 truncate">
                                                {{ Str::limit($notification->content, 60) }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <p class="text-sm">No notifications</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t bg-gray-50">
                            <a href="{{ route('communications.notifications') }}"
                               class="block text-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('communications.notifications')">
                            {{ __('Notifications') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(in_array(Auth::user()->role, ['landlord', 'manager', 'admin']))
                <x-responsive-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.*')">
                    {{ __('Properties') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tenants.index')" :active="request()->routeIs('tenants.*')">
                    {{ __('Tenants') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*')">
                    {{ __('Vendors') }}
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('maintenance-requests.index')" :active="request()->routeIs('maintenance-requests.*')">
                {{ __('Maintenance') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('communications.index')" :active="request()->routeIs('communications.*')">
                {{ __('Communications') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('communications.notifications')">
                    {{ __('Notifications') }}
                    @if(Auth::user()->notifications()->whereNull('read_at')->count() > 0)
                        <span class="ml-2 px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full">
                            {{ Auth::user()->notifications()->whereNull('read_at')->count() }}
                        </span>
                    @endif
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
