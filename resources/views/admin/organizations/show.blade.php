<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.organizations.index') }}" class="text-gray-500 hover:text-gray-700">
                Organizations
            </a>
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $organization->name }}</span>
        </div>
    </x-slot>

    {{-- Organization Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h2>
                <p class="text-gray-500 mt-1">
                    Created {{ $organization->created_at->format('F j, Y') }}
                    ({{ $organization->created_at->diffForHumans() }})
                </p>
            </div>
            <div class="flex items-center space-x-3">
                {{-- Status Badge --}}
                @if($organization->subscription_status === 'active')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                        Active
                    </span>
                @elseif($organization->subscription_status === 'trialing')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <span class="w-2 h-2 mr-2 bg-yellow-500 rounded-full"></span>
                        Trial
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
                        Inactive
                    </span>
                @endif

                {{-- Toggle Status Button --}}
                <form action="{{ route('admin.organizations.toggle', $organization) }}" method="POST" class="inline">
                    @csrf
                    <button
                        type="submit"
                        onclick="return confirm('Are you sure you want to {{ $organization->subscription_status === 'active' ? 'deactivate' : 'activate' }} this organization?')"
                        class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium transition-colors
                               {{ $organization->subscription_status === 'active'
                                  ? 'border-red-300 text-red-700 hover:bg-red-50'
                                  : 'border-green-300 text-green-700 hover:bg-green-50' }}"
                    >
                        @if($organization->subscription_status === 'active')
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Deactivate
                        @else
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Activate
                        @endif
                    </button>
                </form>
            </div>
        </div>

        {{-- Subscription Info --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Subscription Tier</p>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($organization->subscription_tier ?? 'None') }}</p>
            </div>
            @if($organization->trial_ends_at)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Trial Ends</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $organization->trial_ends_at->format('M j, Y') }}</p>
                </div>
            @endif
            @if($organization->stripe_customer_id)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Stripe Customer</p>
                    <p class="text-sm font-mono text-gray-900">{{ $organization->stripe_customer_id }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['users'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Properties</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['properties'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Units</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['units'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Active Leases</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_leases'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Maintenance Requests</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['maintenance_requests'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Open Maintenance</p>
            <p class="text-2xl font-bold {{ $stats['open_maintenance'] > 0 ? 'text-yellow-600' : 'text-green-600' }}">{{ $stats['open_maintenance'] }}</p>
        </div>
    </div>

    {{-- Users & Properties --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Users List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Users ({{ $users->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($users as $user)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role === 'manager') bg-blue-100 text-blue-800
                                @elseif($user->role === 'landlord') bg-indigo-100 text-indigo-800
                                @elseif($user->role === 'tenant') bg-green-100 text-green-800
                                @elseif($user->role === 'vendor') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($user->role) }}
                            </span>
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        No users found
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Properties List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Properties ({{ $properties->count() }})</h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($properties as $property)
                    <div class="px-6 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $property->name }}</p>
                                <p class="text-xs text-gray-500">{{ $property->address }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-900">{{ $property->units_count }} units</span>
                                <p class="text-xs text-gray-500">{{ ucfirst($property->type) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500">
                        No properties found
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('admin.organizations.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Organizations
        </a>
    </div>
</x-admin-layout>
