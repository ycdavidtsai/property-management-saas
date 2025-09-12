{{-- resources/views/livewire/tenants/tenant-show.blade.php --}}
<div class="max-w-4xl mx-auto">

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                    @if($tenant->profile_photo_path)
                        <img class="h-16 w-16 rounded-full object-cover"
                             src="{{ Storage::url($tenant->profile_photo_path) }}"
                             alt="{{ $tenant->name }}">
                    @else
                        <span class="text-gray-600 font-bold text-xl">
                            {{ substr($tenant->name, 0, 1) }}{{ substr(explode(' ', $tenant->name)[1] ?? '', 0, 1) }}
                        </span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                    <p class="text-gray-600">{{ $tenant->email }}</p>
                    <div class="flex items-center mt-2 space-x-3">
                        @if($tenant->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">
                            Joined {{ $tenant->created_at->format('M j, Y') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenants.edit', $tenant) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Edit
                </a>
                <button wire:click="toggleActiveStatus"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                    {{ $tenant->is_active ? 'Deactivate' : 'Activate' }}
                </button>
                <button wire:click="confirmDelete"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->phone ?: 'Not provided' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->tenantProfile?->date_of_birth)
                            {{ $tenant->tenantProfile->date_of_birth->format('M j, Y') }}
                            (Age: {{ $tenant->tenantProfile->age }})
                        @else
                            Not provided
                        @endif
                    </dd>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Employment Status</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->tenantProfile?->employment_status)
                            {{ ucfirst(str_replace('_', ' ', $tenant->tenantProfile->employment_status)) }}
                        @else
                            Not provided
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Monthly Income</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->tenantProfile?->monthly_income)
                            ${{ number_format($tenant->tenantProfile->monthly_income, 2) }}
                        @else
                            Not provided
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">SSN (Last 4)</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->tenantProfile?->ssn_last_four)
                            ***-**-{{ $tenant->tenantProfile->ssn_last_four }}
                        @else
                            Not provided
                        @endif
                    </dd>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $tenant->tenantProfile?->emergency_contact_name ?: 'Not provided' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $tenant->tenantProfile?->emergency_contact_phone ?: 'Not provided' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Relationship</dt>
                    <dd class="text-sm text-gray-900">
                        {{ $tenant->tenantProfile?->emergency_contact_relationship ?: 'Not provided' }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
            <div class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->is_active)
                            <span class="text-green-600 font-medium">Active</span>
                        @else
                            <span class="text-red-600 font-medium">Inactive</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                    <dd class="text-sm text-gray-900">
                        @if($tenant->last_login_at)
                            {{ $tenant->last_login_at->format('M j, Y g:i A') }}
                        @else
                            Never logged in
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->created_at->format('M j, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Updated</dt>
                    <dd class="text-sm text-gray-900">{{ $tenant->updated_at->format('M j, Y g:i A') }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section (if exists) -->
    @if($tenant->tenantProfile?->notes)
        <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $tenant->tenantProfile->notes }}</p>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 text-center mt-4">Delete Tenant</h3>
                    <div class="mt-2 px-4 py-3">
                        <p class="text-sm text-gray-700 text-center">
                            Are you sure you want to delete <strong>{{ $tenant->name }}</strong>?
                            This action cannot be undone and will permanently remove all tenant data.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3 flex space-x-3">
                        <button wire:click="$set('showDeleteModal', false)"
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 flex-1">
                            Cancel
                        </button>
                        <button wire:click="deleteTenant"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 flex-1">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
