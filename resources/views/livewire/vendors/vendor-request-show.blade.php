{{-- Mobile-Optimized Vendor Maintenance Request View --}}
{{-- Phase 2: Touch-friendly interface for field use --}}
<div x-data="{
    showDetails: false,
    showPhotos: false,
    showTimeline: false,
    activeTab: 'overview',
    photoPreview: null
}" class="min-h-screen bg-gray-100 pb-24">

    {{-- Mobile Header - Sticky --}}
    <div class="sticky top-0 z-40 bg-white shadow-sm">
        {{-- Status Bar --}}
        <div class="bg-{{ $maintenanceRequest->status_color ?? 'blue' }}-600 text-white px-4 py-2">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium">{{ $maintenanceRequest->status_label }}</span>
                <span class="text-sm bg-white/20 px-2 py-0.5 rounded">{{ ucfirst($maintenanceRequest->priority) }} Priority</span>
            </div>
        </div>

        {{-- Title Bar --}}
        <div class="px-4 py-3 border-b">
            <div class="flex items-center justify-between">
                <a href="{{ route('vendor.dashboard') }}" class="p-2 -ml-2 rounded-lg active:bg-gray-100">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold text-gray-900 flex-1 mx-3 truncate">{{ $maintenanceRequest->title }}</h1>
                <span class="text-xs text-gray-500">#{{ substr($maintenanceRequest->id, 0, 8) }}</span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2">
            <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-800 font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Pending Acceptance Alert --}}
    @if($maintenanceRequest->status === 'pending_acceptance')
        <div class="mx-4 mt-4 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-amber-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-900">Action Required</h3>
                    <p class="text-sm text-amber-700 mt-1">Review the details and accept or reject this assignment.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tenant Proposed Appointment Alert --}}
    @if($maintenanceRequest->scheduling_status === 'pending_vendor_confirmation' && $maintenanceRequest->scheduled_date)
        <div class="mx-4 mt-4 p-4 bg-blue-50 border-2 border-blue-300 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-blue-900">Tenant Requested a Time</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        <strong>{{ $maintenanceRequest->scheduled_date->format('l, M j, Y') }}</strong><br>
                        {{ date('g:i A', strtotime($maintenanceRequest->scheduled_start_time)) }} - {{ date('g:i A', strtotime($maintenanceRequest->scheduled_end_time)) }}
                    </p>
                    <p class="text-xs text-blue-600 mt-2">Go to the Schedule tab to confirm or propose a different time.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Info Cards --}}
    <div class="px-4 mt-4 grid grid-cols-2 gap-3">
        {{-- Property Card --}}
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center text-gray-500 mb-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-xs font-medium">Property</span>
            </div>
            <p class="font-semibold text-gray-900 text-sm">{{ $maintenanceRequest->property->name }}</p>
            @if($maintenanceRequest->unit)
                <p class="text-xs text-gray-500 mt-0.5">Unit {{ $maintenanceRequest->unit->unit_number }}</p>
            @endif
        </div>

        {{-- Category Card --}}
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center text-gray-500 mb-2">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span class="text-xs font-medium">Category</span>
            </div>
            <p class="font-semibold text-gray-900 text-sm">{{ ucfirst($maintenanceRequest->category) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $maintenanceRequest->created_at->format('M d, Y') }}</p>
        </div>
    </div>

    {{-- Quick Actions: Call & Navigate --}}
    <div class="px-4 mt-3 flex gap-3">
        @if($maintenanceRequest->tenant && $maintenanceRequest->tenant->phone)
            <a href="tel:{{ $maintenanceRequest->tenant->phone }}"
               class="flex-1 bg-white rounded-xl p-4 shadow-sm flex items-center justify-center active:bg-gray-50 border-2 border-transparent active:border-blue-300 transition-colors">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="font-medium text-gray-900">Call Tenant</span>
            </a>
        @endif

        <a href="https://maps.google.com/?q={{ urlencode($maintenanceRequest->property->address) }}"
           target="_blank"
           class="flex-1 bg-white rounded-xl p-4 shadow-sm flex items-center justify-center active:bg-gray-50 border-2 border-transparent active:border-green-300 transition-colors">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="font-medium text-gray-900">Navigate</span>
        </a>
    </div>

    {{-- Tab Navigation - UPDATED with Schedule tab --}}
    <div class="px-4 mt-4">
        <div class="bg-white rounded-xl shadow-sm p-1 flex">
            <button @click="activeTab = 'overview'"
                    :class="activeTab === 'overview' ? 'bg-blue-600 text-white' : 'text-gray-600'"
                    class="flex-1 py-2.5 px-3 rounded-lg font-medium text-sm transition-colors">
                Overview
            </button>

            {{-- Schedule Tab - Only show after vendor accepted --}}
            @if(in_array($maintenanceRequest->status, ['assigned', 'in_progress']))
                <button @click="activeTab = 'schedule'"
                        :class="activeTab === 'schedule' ? 'bg-blue-600 text-white' : 'text-gray-600'"
                        class="flex-1 py-2.5 px-3 rounded-lg font-medium text-sm transition-colors flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Schedule</span>
                    @if($maintenanceRequest->scheduling_status === 'confirmed')
                        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                    @elseif($maintenanceRequest->scheduling_status === 'pending_vendor_confirmation')
                        <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                    @endif
                </button>
            @endif

            <button @click="activeTab = 'updates'"
                    :class="activeTab === 'updates' ? 'bg-blue-600 text-white' : 'text-gray-600'"
                    class="flex-1 py-2.5 px-3 rounded-lg font-medium text-sm transition-colors">
                Updates ({{ $updates->total() }})
            </button>
            <button @click="activeTab = 'add'"
                    :class="activeTab === 'add' ? 'bg-blue-600 text-white' : 'text-gray-600'"
                    class="flex-1 py-2.5 px-3 rounded-lg font-medium text-sm transition-colors">
                + Add
            </button>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="px-4 mt-4">
        {{-- Overview Tab --}}
        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            {{-- Description --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-3">
                <h3 class="font-semibold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Description
                </h3>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $maintenanceRequest->description }}</p>
            </div>

            {{-- Photos --}}
            @if($maintenanceRequest->photos && count($maintenanceRequest->photos) > 0)
                <div class="bg-white rounded-xl shadow-sm p-4 mb-3">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Photos ({{ count($maintenanceRequest->photos) }})
                    </h3>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($maintenanceRequest->photos as $index => $photo)
                            <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100"
                                 @click="photoPreview = '{{ Storage::url($photo) }}'">
                                <img src="{{ Storage::url($photo) }}"
                                     alt="Request photo {{ $index + 1 }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Contact Info --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-3">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Contact Information
                </h3>

                @if($maintenanceRequest->tenant)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Tenant</span>
                            <span class="font-medium text-gray-900">{{ $maintenanceRequest->tenant->name }}</span>
                        </div>
                        @if($maintenanceRequest->tenant->phone)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">Phone</span>
                                <a href="tel:{{ $maintenanceRequest->tenant->phone }}" class="font-medium text-blue-600">
                                    {{ $maintenanceRequest->tenant->phone }}
                                </a>
                            </div>
                        @endif
                        @if($maintenanceRequest->tenant->email)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 text-sm">Email</span>
                                <a href="mailto:{{ $maintenanceRequest->tenant->email }}" class="font-medium text-blue-600 text-sm truncate max-w-[180px]">
                                    {{ $maintenanceRequest->tenant->email }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="border-t mt-3 pt-3">
                    <div class="flex items-start justify-between">
                        <span class="text-gray-500 text-sm">Address</span>
                        <span class="font-medium text-gray-900 text-right text-sm max-w-[200px]">{{ $maintenanceRequest->property->address }}</span>
                    </div>
                </div>
            </div>

            {{-- Request Details --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-3">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Request Details
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 text-sm">Submitted</span>
                        <span class="font-medium text-gray-900 text-sm">{{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    @if($maintenanceRequest->assigned_at)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Assigned</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $maintenanceRequest->assigned_at->format('M d, Y g:i A') }}</span>
                        </div>
                    @endif
                    @if($maintenanceRequest->accepted_at)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Accepted</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $maintenanceRequest->accepted_at->format('M d, Y g:i A') }}</span>
                        </div>
                    @endif
                    @if($maintenanceRequest->started_at)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Work Started</span>
                            <span class="font-medium text-gray-900 text-sm">{{ $maintenanceRequest->started_at->format('M d, Y g:i A') }}</span>
                        </div>
                    @endif
                    {{-- Scheduled Appointment Info --}}
                    @if($maintenanceRequest->scheduled_date)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Scheduled Visit</span>
                            <div class="text-right">
                                <span class="font-medium text-gray-900 text-sm">{{ $maintenanceRequest->scheduled_date->format('M d, Y') }}</span>
                                @if($maintenanceRequest->scheduled_start_time)
                                    <br><span class="text-xs text-gray-600">{{ date('g:i A', strtotime($maintenanceRequest->scheduled_start_time)) }}</span>
                                @endif
                                @if($maintenanceRequest->scheduling_status === 'confirmed')
                                    <span class="ml-1 text-xs text-green-600">✓ Confirmed</span>
                                @elseif($maintenanceRequest->scheduling_status === 'pending_vendor_confirmation')
                                    <span class="ml-1 text-xs text-amber-600">Awaiting your confirmation</span>
                                @elseif($maintenanceRequest->scheduling_status === 'pending_tenant_confirmation')
                                    <span class="ml-1 text-xs text-blue-600">Awaiting tenant</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($maintenanceRequest->estimated_cost)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 text-sm">Estimated Cost</span>
                            <span class="font-medium text-gray-900">${{ number_format($maintenanceRequest->estimated_cost, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================================== --}}
        {{-- SCHEDULE TAB - NEW --}}
        {{-- ================================== --}}
        @if(in_array($maintenanceRequest->status, ['assigned', 'in_progress']))
        <div x-show="activeTab === 'schedule'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Schedule Visit with Tenant
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Coordinate with {{ $maintenanceRequest->tenant->name ?? 'the tenant' }} on when you'll visit.
                    </p>
                </div>

                {{-- Tenant Proposed Alert --}}
                @if($maintenanceRequest->scheduling_status === 'pending_vendor_confirmation')
                    <div class="mx-4 mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-center gap-2 text-amber-800">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-sm">Tenant requested this time - please confirm or propose different time</span>
                        </div>
                    </div>
                @endif

                {{-- Scheduler Component --}}
                <div class="p-4">
                    @livewire('maintenance-requests.appointment-scheduler', [
                        'request' => $maintenanceRequest,
                        'userRole' => 'vendor',
                        'viewOnly' => false
                    ], key('vendor-scheduler-' . $maintenanceRequest->id))
                </div>
            </div>
        </div>
        @endif

        {{-- Updates Tab --}}
        <div x-show="activeTab === 'updates'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="space-y-3">
                @forelse($updates as $update)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                @if($update->update_type === 'comment')
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                @elseif($update->update_type === 'scheduling')
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $update->user->name ?? 'System' }}</p>
                                    <span class="text-xs text-gray-500">{{ $update->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 text-sm whitespace-pre-line">{{ $update->message }}</p>

                                @if($update->photos && count($update->photos) > 0)
                                    <div class="mt-3 grid grid-cols-3 gap-2">
                                        @foreach($update->photos as $photo)
                                            <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100"
                                                 @click="photoPreview = '{{ Storage::url($photo) }}'">
                                                <img src="{{ Storage::url($photo) }}"
                                                     alt="Update photo"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No updates yet</p>
                        <p class="text-gray-400 text-sm mt-1">Add the first update to this request</p>
                    </div>
                @endforelse

                @if($updates->hasPages())
                    <div class="mt-4">
                        {{ $updates->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Add Update Tab --}}
        <div x-show="activeTab === 'add'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <form wire:submit.prevent="addUpdate" class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-4">Add Update</h3>

                {{-- Message Input --}}
                <div class="mb-4">
                    <textarea wire:model="message"
                              rows="4"
                              placeholder="Describe work progress, parts needed, or other details..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    @error('message')
                        <p class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded-lg">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photo Upload --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Photos</label>

                    {{-- Camera Capture Button (Mobile) --}}
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <label class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 active:bg-blue-50 transition-colors">
                            <input type="file"
                                   wire:model="photos"
                                   accept="image/*"
                                   capture="environment"
                                   class="hidden"
                                   id="camera-capture">
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Take Photo</span>
                            </div>
                        </label>

                        <label class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 active:bg-blue-50 transition-colors">
                            <input type="file"
                                   wire:model="photos"
                                   accept="image/*"
                                   multiple
                                   class="hidden"
                                   id="gallery-upload">
                            <div class="text-center">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm text-gray-600">Gallery</span>
                            </div>
                        </label>
                    </div>

                    {{-- Upload Progress --}}
                    <div wire:loading wire:target="photos" class="flex items-center justify-center p-3 bg-blue-50 rounded-xl">
                        <svg class="animate-spin h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-blue-600 font-medium">Uploading...</span>
                    </div>

                    {{-- Photo Preview --}}
                    @if($photos && count($photos) > 0)
                        <div class="mt-3 grid grid-cols-4 gap-2">
                            @foreach($photos as $photo)
                                <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100">
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="photos,addUpdate"
                        class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:bg-gray-400 text-white font-semibold py-4 px-6 rounded-xl transition-colors text-lg">
                    <span wire:loading.remove wire:target="addUpdate">Add Update</span>
                    <span wire:loading wire:target="addUpdate">Adding...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Sticky Bottom Action Bar --}}
    {{-- <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg safe-area-pb z-50"> --}}
    {{-- Sticky Bottom Action Bar - Hidden on Schedule tab --}}
    <div x-show="activeTab !== 'schedule'"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg safe-area-pb z-50">
        <div class="px-4 py-3">
            @if($maintenanceRequest->status === 'pending_acceptance')
                <div class="flex gap-3">
                    <button wire:click="$set('showRejectModal', true)"
                            class="flex-1 bg-red-100 hover:bg-red-200 active:bg-red-300 text-red-700 font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center min-h-[56px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reject
                    </button>
                    <button wire:click="acceptAssignment"
                            wire:loading.attr="disabled"
                            onclick="return confirm('Accept this assignment?')"
                            class="flex-1 bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:bg-green-400 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center min-h-[56px]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span wire:loading.remove wire:target="acceptAssignment">Accept Job</span>
                        <span wire:loading wire:target="acceptAssignment">Accepting...</span>
                    </button>
                </div>
            @elseif($maintenanceRequest->status === 'assigned')
                <button wire:click="updateStatus('in_progress')"
                        wire:loading.attr="disabled"
                        class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 disabled:bg-blue-400 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center min-h-[56px] text-lg">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span wire:loading.remove wire:target="updateStatus">Start Work</span>
                    <span wire:loading wire:target="updateStatus">Starting...</span>
                </button>
            @elseif($maintenanceRequest->status === 'in_progress')
                <button wire:click="$set('showCompleteModal', true)"
                        class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800 disabled:bg-green-400 text-white font-semibold py-4 px-6 rounded-xl transition-colors flex items-center justify-center min-h-[56px] text-lg">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Complete Work
                </button>
            @elseif($maintenanceRequest->status === 'completed')
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <div class="flex items-center justify-center text-green-700">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold">Work Completed</span>
                    </div>
                    @if($maintenanceRequest->completed_at)
                        <p class="text-sm text-green-600 mt-1">{{ $maintenanceRequest->completed_at->format('M d, Y g:i A') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Rejection Modal --}}
    @if($showRejectModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="$set('showRejectModal', false)"></div>

        <div class="fixed inset-x-0 bottom-0 transform transition-transform duration-300 ease-out">
            <div class="bg-white rounded-t-3xl max-h-[85vh] overflow-y-auto safe-area-pb">
                {{-- Handle --}}
                <div class="flex justify-center py-3">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="px-6 pb-4 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Reject Assignment</h3>
                    <p class="text-sm text-gray-500 mt-1">Please select a reason to help the property manager</p>
                </div>

                {{-- Reasons --}}
                <div class="px-6 py-4 space-y-2">
                    @foreach([
                        'too_busy' => ['label' => 'Currently too busy', 'desc' => 'Fully booked right now'],
                        'out_of_area' => ['label' => 'Outside service area', 'desc' => 'Location too far'],
                        'lacks_expertise' => ['label' => 'Needs specialist', 'desc' => 'Requires different skills'],
                        'emergency_unavailable' => ['label' => "Can't handle emergency", 'desc' => 'Unable to respond urgently'],
                        'insufficient_info' => ['label' => 'Need more details', 'desc' => 'Insufficient information'],
                        'other' => ['label' => 'Other reason', 'desc' => 'Explain in notes'],
                    ] as $value => $option)
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors
                            {{ $rejectionReason === $value ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="rejectionReason" value="{{ $value }}" class="hidden">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $option['label'] }}</p>
                                <p class="text-sm text-gray-500">{{ $option['desc'] }}</p>
                            </div>
                            @if($rejectionReason === $value)
                                <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </label>
                    @endforeach
                </div>

                {{-- Additional Notes --}}
                <div class="px-6 py-4 border-t">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                    <textarea wire:model="rejectionNotes"
                              rows="3"
                              placeholder="Any additional details..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
                </div>

                {{-- Actions --}}
                <div class="px-6 pb-6 flex gap-3">
                    <button wire:click="$set('showRejectModal', false)"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button wire:click="rejectAssignment"
                            wire:loading.attr="disabled"
                            @if(!$rejectionReason) disabled @endif
                            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white font-semibold py-4 px-6 rounded-xl transition-colors">
                        <span wire:loading.remove wire:target="rejectAssignment">Reject Job</span>
                        <span wire:loading wire:target="rejectAssignment">Rejecting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Completion Modal --}}
    @if($showCompleteModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-destroy="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="$set('showCompleteModal', false)"></div>

        <div class="fixed inset-x-0 bottom-0 transform transition-transform duration-300 ease-out">
            <div class="bg-white rounded-t-3xl max-h-[85vh] overflow-y-auto safe-area-pb">
                {{-- Handle --}}
                <div class="flex justify-center py-3">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="px-6 pb-4 border-b">
                    <h3 class="text-xl font-bold text-gray-900">Complete Work</h3>
                    <p class="text-sm text-gray-500 mt-1">Add completion details and photos</p>
                </div>

                {{-- Form --}}
                <div class="px-6 py-4 space-y-4">
                    {{-- Completion Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Completion Notes <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="completionNotes"
                                  rows="4"
                                  placeholder="Describe work completed, parts used, etc..."
                                  class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                        @error('completionNotes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actual Cost --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cost (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-lg">$</span>
                            <input type="number"
                                   wire:model="actualCost"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded-xl pl-10 pr-4 py-3 text-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        @if($maintenanceRequest->estimated_cost)
                            <p class="text-sm text-gray-500 mt-1">Estimated: ${{ number_format($maintenanceRequest->estimated_cost, 2) }}</p>
                        @endif
                    </div>

                    {{-- Completion Photos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Completion Photos (Optional)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-400 active:bg-green-50">
                                <input type="file" wire:model="completionPhotos" accept="image/*" capture="environment" class="hidden">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">Take Photo</span>
                                </div>
                            </label>

                            <label class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-green-400 active:bg-green-50">
                                <input type="file" wire:model="completionPhotos" accept="image/*" multiple class="hidden">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">Gallery</span>
                                </div>
                            </label>
                        </div>

                        <div wire:loading wire:target="completionPhotos" class="mt-3 flex items-center justify-center p-3 bg-green-50 rounded-xl">
                            <svg class="animate-spin h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-green-600 font-medium">Uploading...</span>
                        </div>

                        @if($completionPhotos && count($completionPhotos) > 0)
                            <div class="mt-3 grid grid-cols-4 gap-2">
                                @foreach($completionPhotos as $photo)
                                    <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100">
                                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-6 pb-6 flex gap-3">
                    <button wire:click="$set('showCompleteModal', false)"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button wire:click="completeWork"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold py-4 px-6 rounded-xl transition-colors">
                        <span wire:loading.remove wire:target="completeWork">Mark Complete</span>
                        <span wire:loading wire:target="completeWork">Completing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Photo Lightbox --}}
    <div x-show="photoPreview"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] bg-black flex items-center justify-center"
         @click="photoPreview = null">
        <button @click="photoPreview = null" class="absolute top-4 right-4 z-10 p-2 bg-black/50 rounded-full text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <img :src="photoPreview" class="max-w-full max-h-full object-contain" @click.stop>
    </div>

    <style>
        /* Safe area for iOS devices */
        .safe-area-pb {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* Ensure touch targets are at least 44px */
        button, a, label, input[type="radio"], input[type="checkbox"] {
            min-height: 44px;
        }
    </style>
</div>
