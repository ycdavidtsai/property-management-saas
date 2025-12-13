{{-- Mobile-Optimized Vendor Dashboard --}}
{{-- Phase 2: Touch-friendly job management for field use --}}
<div class="min-h-screen bg-gray-100">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
        <div class="px-4 py-4 md:py-6">
            <div class="flex items-center justify-between mb-2 md:mb-4">
                <div>
                    <h1 class="text-xl font-bold">Welcome back!</h1>
                    <p class="text-blue-100 text-sm">{{ Auth::user()->name }}</p>
                </div>
                <a href="{{ route('vendor.profile') }}" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
            </div>

            {{-- Quick Stats - Hidden on mobile (tabs already show counts), visible on desktop --}}
            <div class="grid grid-cols-4 gap-2">
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold">{{ $pendingCount }}</p>
                    <p class="text-xs text-blue-100">Pending</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold">{{ $activeCount }}</p>
                    <p class="text-xs text-blue-100">Active</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-2xl font-bold">{{ $completedThisMonth }}</p>
                    <p class="text-xs text-blue-100">Done</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-lg font-bold">${{ number_format($totalEarnings, 0) }}</p>
                    <p class="text-xs text-blue-100">Earned</p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="px-4 pb-2">
            <div class="flex gap-2">
                <button wire:click="setTab('pending')"
                        class="flex-1 py-2.5 px-4 rounded-t-xl font-medium text-sm transition-colors
                               {{ $activeTab === 'pending' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }}">
                    Pending
                    @if($pendingCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $activeTab === 'pending' ? 'bg-red-500 text-white' : 'bg-red-400 text-white' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </button>
                <button wire:click="setTab('active')"
                        class="flex-1 py-2.5 px-4 rounded-t-xl font-medium text-sm transition-colors
                               {{ $activeTab === 'active' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }}">
                    Active
                    @if($activeCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ $activeTab === 'active' ? 'bg-blue-500 text-white' : 'bg-blue-400 text-white' }}">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
                <button wire:click="setTab('completed')"
                        class="flex-1 py-2.5 px-4 rounded-t-xl font-medium text-sm transition-colors
                               {{ $activeTab === 'completed' ? 'bg-white text-blue-600' : 'bg-white/10 text-white' }}">
                    Done
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition>
            <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
            <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-800 font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="px-4 py-3 bg-white border-b sticky top-0 z-30">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search jobs..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="p-3 border border-gray-300 rounded-xl {{ $priorityFilter ? 'bg-blue-50 border-blue-300' : '' }}">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border z-50">
                    <div class="p-2">
                        <button wire:click="$set('priorityFilter', '')" @click="open = false"
                                class="w-full text-left px-4 py-2 rounded-lg text-sm {{ !$priorityFilter ? 'bg-blue-50 text-blue-700' : 'hover:bg-gray-50' }}">
                            All Priorities
                        </button>
                        <button wire:click="$set('priorityFilter', 'emergency')" @click="open = false"
                                class="w-full text-left px-4 py-2 rounded-lg text-sm {{ $priorityFilter === 'emergency' ? 'bg-red-50 text-red-700' : 'hover:bg-gray-50' }}">
                            🚨 Emergency
                        </button>
                        <button wire:click="$set('priorityFilter', 'high')" @click="open = false"
                                class="w-full text-left px-4 py-2 rounded-lg text-sm {{ $priorityFilter === 'high' ? 'bg-orange-50 text-orange-700' : 'hover:bg-gray-50' }}">
                            🔴 High
                        </button>
                        <button wire:click="$set('priorityFilter', 'medium')" @click="open = false"
                                class="w-full text-left px-4 py-2 rounded-lg text-sm {{ $priorityFilter === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'hover:bg-gray-50' }}">
                            🟡 Medium
                        </button>
                        <button wire:click="$set('priorityFilter', 'low')" @click="open = false"
                                class="w-full text-left px-4 py-2 rounded-lg text-sm {{ $priorityFilter === 'low' ? 'bg-green-50 text-green-700' : 'hover:bg-gray-50' }}">
                            🟢 Low
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Job Cards --}}
    <div class="px-4 py-4 space-y-3 pb-24">
        @forelse($requests as $request)
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden"
                 x-data="{ expanded: false }">
                {{-- Card Header - Always Visible --}}
                <a href="{{ route('vendor.requests.show', $request) }}" class="block p-4 active:bg-gray-50">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $request->title }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $request->property->name }}
                                @if($request->unit)
                                    · Unit {{ $request->unit->unit_number }}
                                @endif
                            </p>
                        </div>
                        {{-- Priority Badge --}}
                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium
                            @switch($request->priority)
                                @case('emergency')
                                    bg-red-100 text-red-700
                                    @break
                                @case('high')
                                    bg-orange-100 text-orange-700
                                    @break
                                @case('medium')
                                    bg-yellow-100 text-yellow-700
                                    @break
                                @default
                                    bg-green-100 text-green-700
                            @endswitch">
                            {{ ucfirst($request->priority) }}
                        </span>
                    </div>

                    {{-- Status & Category --}}
                    <div class="flex items-center gap-2 text-sm">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            @switch($request->status)
                                @case('pending_acceptance')
                                    bg-amber-100 text-amber-700
                                    @break
                                @case('assigned')
                                    bg-blue-100 text-blue-700
                                    @break
                                @case('in_progress')
                                    bg-purple-100 text-purple-700
                                    @break
                                @case('completed')
                                    bg-green-100 text-green-700
                                    @break
                                @default
                                    bg-gray-100 text-gray-700
                            @endswitch">
                            {{ $request->status_label }}
                        </span>
                        <span class="text-gray-400">·</span>
                        <span class="text-gray-500">{{ ucfirst($request->category) }}</span>
                        <span class="text-gray-400">·</span>
                        <span class="text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Description Preview --}}
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $request->description }}</p>
                </a>

                {{-- Quick Actions --}}
                @if($request->status === 'pending_acceptance')
                    <div class="px-4 pb-4 flex gap-2">
                        <a href="{{ route('vendor.requests.show', $request) }}"
                           class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 font-medium rounded-xl text-center text-sm transition-colors">
                            View Details
                        </a>
                        <button wire:click="quickAccept('{{ $request->id }}')"
                                wire:loading.attr="disabled"
                                onclick="event.preventDefault(); event.stopPropagation(); if(confirm('Accept this job?')) { $wire.quickAccept('{{ $request->id }}'); }"
                                class="flex-1 py-3 px-4 bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:bg-green-400 text-white font-medium rounded-xl text-center text-sm transition-colors">
                            <span wire:loading.remove wire:target="quickAccept('{{ $request->id }}')">Accept Job</span>
                            <span wire:loading wire:target="quickAccept('{{ $request->id }}')">...</span>
                        </button>
                    </div>
                @elseif($request->status === 'assigned')
                    <div class="px-4 pb-4 flex gap-2">
                        {{-- Navigate Button --}}
                        <a href="https://maps.google.com/?q={{ urlencode($request->property->address) }}"
                           target="_blank"
                           onclick="event.stopPropagation();"
                           class="py-3 px-4 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </a>
                        <button wire:click="quickStart('{{ $request->id }}')"
                                wire:loading.attr="disabled"
                                onclick="event.preventDefault(); event.stopPropagation(); $wire.quickStart('{{ $request->id }}');"
                                class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:bg-blue-400 text-white font-medium rounded-xl text-center text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span wire:loading.remove wire:target="quickStart('{{ $request->id }}')">Start Work</span>
                            <span wire:loading wire:target="quickStart('{{ $request->id }}')">Starting...</span>
                        </button>
                    </div>
                @elseif($request->status === 'in_progress')
                    <div class="px-4 pb-4 flex gap-2">
                        {{-- Call Tenant --}}
                        @if($request->tenant && $request->tenant->phone)
                            <a href="tel:{{ $request->tenant->phone }}"
                               onclick="event.stopPropagation();"
                               class="py-3 px-4 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </a>
                        @endif
                        {{-- Navigate --}}
                        <a href="https://maps.google.com/?q={{ urlencode($request->property->address) }}"
                           target="_blank"
                           onclick="event.stopPropagation();"
                           class="py-3 px-4 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </a>
                        {{-- View Details / Complete --}}
                        <a href="{{ route('vendor.requests.show', $request) }}"
                           class="flex-1 py-3 px-4 bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-medium rounded-xl text-center text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Complete Work
                        </a>
                    </div>
                @elseif($request->status === 'completed')
                    <div class="px-4 pb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                Completed {{ $request->completed_at?->diffForHumans() }}
                            </span>
                            @if($request->actual_cost)
                                <span class="font-semibold text-green-600">${{ number_format($request->actual_cost, 2) }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    @if($activeTab === 'pending')
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($activeTab === 'active')
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                    @if($activeTab === 'pending')
                        No Pending Jobs
                    @elseif($activeTab === 'active')
                        No Active Jobs
                    @else
                        No Completed Jobs
                    @endif
                </h3>
                <p class="text-gray-500 text-sm">
                    @if($activeTab === 'pending')
                        New job requests will appear here
                    @elseif($activeTab === 'active')
                        Accept a job to get started
                    @else
                        Completed jobs will appear here
                    @endif
                </p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($requests->hasPages())
            <div class="mt-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Bottom Navigation (Optional - if you have other vendor pages) --}}
    {{-- Uncomment if needed
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t safe-area-pb z-40">
        <div class="flex justify-around py-2">
            <a href="{{ route('vendor.dashboard') }}" class="flex flex-col items-center py-2 px-4 text-blue-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Jobs</span>
            </a>
            <a href="{{ route('vendor.profile') }}" class="flex flex-col items-center py-2 px-4 text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </div>
    --}}

    <style>
        .safe-area-pb {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
