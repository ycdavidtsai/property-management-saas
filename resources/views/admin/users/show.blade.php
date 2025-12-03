<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
                Users
            </a>
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $user->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        {{-- User Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-xl font-medium text-gray-600">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-500">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-gray-500 text-sm">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    {{-- Role Badge --}}
                    <span class="px-3 py-1 text-sm font-medium rounded-full
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

                    {{-- Status Badge --}}
                    @if($user->is_active ?? true)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <span class="w-2 h-2 mr-2 bg-red-500 rounded-full"></span>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>

            {{-- Toggle Status Button --}}
            @if($user->id !== auth()->id())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            onclick="return confirm('Are you sure you want to {{ ($user->is_active ?? true) ? 'deactivate' : 'activate' }} this user?')"
                            class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium transition-colors
                                   {{ ($user->is_active ?? true)
                                      ? 'border-red-300 text-red-700 hover:bg-red-50'
                                      : 'border-green-300 text-green-700 hover:bg-green-50' }}"
                        >
                            @if($user->is_active ?? true)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Deactivate User
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activate User
                            @endif
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- User Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Account Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">User ID</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $user->id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Email Verified</dt>
                        <dd class="text-sm">
                            @if($user->email_verified_at)
                                <span class="text-green-600">✓ {{ $user->email_verified_at->format('M j, Y') }}</span>
                            @else
                                <span class="text-red-600">Not verified</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Joined</dt>
                        <dd class="text-sm text-gray-900">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Last Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $user->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Organization Information --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Organization</h3>
                @if($user->organization)
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->organization->name }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($user->organization->subscription_status ?? 'Unknown') }} subscription</p>
                        </div>
                        <a href="{{ route('admin.organizations.show', $user->organization) }}" class="text-blue-600 hover:text-blue-800">
                            View →
                        </a>
                    </div>
                    @if(isset($stats['org_properties']))
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['org_properties'] }}</p>
                                <p class="text-sm text-gray-500">Properties</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['org_tenants'] }}</p>
                                <p class="text-sm text-gray-500">Tenants</p>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-gray-500">No organization assigned (Site Admin)</p>
                @endif
            </div>
        </div>

        {{-- Role-specific Information --}}
        @if($user->role === 'tenant' && isset($stats['active_lease']))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tenant Information</h3>
                @if($stats['active_lease'])
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Current Unit</p>
                            <p class="font-medium text-gray-900">
                                {{ $stats['active_lease']->unit->property->name }} - Unit {{ $stats['active_lease']->unit->unit_number }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lease Period</p>
                            <p class="font-medium text-gray-900">
                                {{ $stats['active_lease']->start_date->format('M j, Y') }} - {{ $stats['active_lease']->end_date->format('M j, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Monthly Rent</p>
                            <p class="font-medium text-gray-900">${{ number_format($stats['active_lease']->rent_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Maintenance Requests</p>
                            <p class="font-medium text-gray-900">{{ $stats['maintenance_requests'] }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No active lease</p>
                @endif
            </div>
        @endif

        @if($user->role === 'vendor' && isset($stats['vendor_profile']))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Vendor Information</h3>
                @if($stats['vendor_profile'])
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Business Name</p>
                            <p class="font-medium text-gray-900">{{ $stats['vendor_profile']->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Vendor Type</p>
                            <p class="font-medium text-gray-900">{{ ucfirst($stats['vendor_profile']->vendor_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Assigned Requests</p>
                            <p class="font-medium text-gray-900">{{ $stats['assigned_requests'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Completed Requests</p>
                            <p class="font-medium text-gray-900">{{ $stats['completed_requests'] }}</p>
                        </div>
                        @if($stats['vendor_profile']->specialties)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Specialties</p>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($stats['vendor_profile']->specialties as $specialty)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-sm rounded">{{ $specialty }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500">No vendor profile linked</p>
                @endif
            </div>
        @endif

        {{-- Back Button --}}
        <div class="mt-6">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Users
            </a>
        </div>
    </div>
</x-admin-layout>
