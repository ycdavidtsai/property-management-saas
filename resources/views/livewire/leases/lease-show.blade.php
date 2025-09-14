<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">Lease Details</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $lease->unit->property->name }} - Unit {{ $lease->unit->unit_number }}</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none space-x-3">
            @if(App\Services\RoleService::canEditLeases(auth()->user()->role))
                <a href="{{ route('leases.edit', $lease->id) }}"
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Edit Lease
                </a>
            @endif

            @if($lease->status !== 'terminated' && App\Services\RoleService::canDeleteLeases(auth()->user()->role))
                <button wire:click="terminateLease"
                        onclick="return confirm('Are you sure you want to terminate this lease?')"
                        class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                    Terminate Lease
                </button>
            @endif
        </div>
    </div>

    <!-- Lease Information -->
    <div class="mt-6 border-t border-gray-100">
        <dl class="divide-y divide-gray-100">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Property</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $lease->unit->property->name }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Unit</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $lease->unit->unit_number }}</dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Tenants</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <ul class="space-y-1">
                        @foreach($lease->tenants as $tenant)
                            <li>{{ $tenant->name }} ({{ $tenant->email }})</li>
                        @endforeach
                    </ul>
                </dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Lease Period</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    {{ $lease->start_date->format('M j, Y') }} - {{ $lease->end_date->format('M j, Y') }}
                </dd>
            </div>
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Monthly Rent</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">${{ number_format($lease->rent_amount, 2) }}</dd>
            </div>
            @if($lease->security_deposit)
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Security Deposit</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">${{ number_format($lease->security_deposit, 2) }}</dd>
            </div>
            @endif
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Status</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $lease->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $lease->status === 'expiring_soon' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $lease->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $lease->status === 'terminated' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $lease->status)) }}
                    </span>
                </dd>
            </div>
            @if($lease->notes)
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <dt class="text-sm font-medium leading-6 text-gray-900">Notes</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $lease->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
