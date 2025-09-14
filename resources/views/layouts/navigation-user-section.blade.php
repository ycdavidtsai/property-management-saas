{{-- Navigation User Section Component - resources/views/layouts/navigation-user-section.blade.php --}}

<div x-data="{ userDropdownOpen: false }" class="w-full">
    {{-- User Info Button --}}
    <button @click="userDropdownOpen = !userDropdownOpen"
            class="group block w-full rounded-md p-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:bg-gray-50 focus:text-gray-900">
        <div class="flex items-center">
            {{-- User Avatar --}}
            <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-400">
                <span class="text-sm font-medium leading-none text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>
            </div>
            
            {{-- User Info --}}
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>
            
            {{-- Dropdown Arrow --}}
            <div class="ml-2 flex-shrink-0">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500 transition-transform duration-200"
                     :class="{ 'rotate-180': userDropdownOpen }"
                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="userDropdownOpen" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="userDropdownOpen = false"
         class="mt-2 w-full origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        
        {{-- Profile Link --}}
        <a href="{{ route('profile.edit') }}" 
           class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" 
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            Your Profile
        </a>

        {{-- User Management (Admin only) --}}
        @if(App\Services\RoleService::roleHasPermission(auth()->user()->role, 'users.view'))
            <a href="#" 
               class="group flex items-center px-4 py-2 text-sm text-gray-400 cursor-not-allowed">
                <svg class="mr-3 h-5 w-5 text-gray-300" 
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                User Management
                <span class="ml-auto text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded-full">Soon</span>
            </a>
        @endif

        {{-- Organization Settings (Admin/Manager only) --}}
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <a href="#" 
               class="group flex items-center px-4 py-2 text-sm text-gray-400 cursor-not-allowed">
                <svg class="mr-3 h-5 w-5 text-gray-300" 
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Organization Settings
                <span class="ml-auto text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded-full">Soon</span>
            </a>
        @endif

        {{-- Divider --}}
        <div class="border-t border-gray-100"></div>

        {{-- Sign Out --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="group flex w-full items-center px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" 
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Sign out
            </button>
        </form>
    </div>
</div>