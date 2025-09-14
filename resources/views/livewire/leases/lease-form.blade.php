<div>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">
                {{ $isEditing ? 'Edit Lease' : 'Create New Lease' }}
            </h1>
            <p class="mt-2 text-sm text-gray-700">
                {{ $isEditing ? 'Update lease information and tenant assignments.' : 'Add a new lease and assign tenants to a unit.' }}
            </p>
        </div>
    </div>

    <form wire:submit="save" class="mt-6">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

            <!-- Unit Selection -->
            <div class="sm:col-span-3">
                <label for="unit_id" class="block text-sm font-medium leading-6 text-gray-900">Unit *</label>
                <select wire:model.live="unit_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Select a unit</option>
                    @foreach($availableUnits as $unit)
                        <option value="{{ $unit->id }}">
                            {{ $unit->property->name }} - Unit {{ $unit->unit_number }}
                        </option>
                    @endforeach
                </select>
                @error('unit_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Tenant Selection -->
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium leading-6 text-gray-900">Tenants *</label>
                <div class="mt-2 space-y-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-3">
                    @foreach($availableTenants as $tenant)
                        <label class="flex items-center">
                            <input type="checkbox"
                                   wire:model="tenant_ids"
                                   value="{{ $tenant->id }}"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-900">{{ $tenant->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tenant_ids') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Start Date -->
            <div class="sm:col-span-3">
                <label for="start_date" class="block text-sm font-medium leading-6 text-gray-900">Start Date *</label>
                <input type="date"
                       wire:model="start_date"
                       class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                @error('start_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- End Date -->
            <div class="sm:col-span-3">
                <label for="end_date" class="block text-sm font-medium leading-6 text-gray-900">End Date *</label>
                <input type="date"
                       wire:model="end_date"
                       class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                @error('end_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Rent Amount -->
            <div class="sm:col-span-3">
                <label for="rent_amount" class="block text-sm font-medium leading-6 text-gray-900">Monthly Rent *</label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number"
                           wire:model="rent_amount"
                           step="0.01"
                           readonly
                           class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 bg-gray-50 sm:text-sm sm:leading-6">
                </div>
                <p class="mt-1 text-xs text-gray-500">Amount is inherited from the selected unit</p>
                @error('rent_amount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Security Deposit -->
            <div class="sm:col-span-3">
                <label for="security_deposit" class="block text-sm font-medium leading-6 text-gray-900">Security Deposit</label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number"
                           wire:model="security_deposit"
                           step="0.01"
                           class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('security_deposit') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Status (for editing) -->
            @if($isEditing)
            <div class="sm:col-span-3">
                <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                <select wire:model="status"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="active">Active</option>
                    <option value="expiring_soon">Expiring Soon</option>
                    <option value="expired">Expired</option>
                    <option value="terminated">Terminated</option>
                </select>
                @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            @endif

            <!-- Notes -->
            <div class="sm:col-span-6">
                <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes</label>
                <textarea wire:model="notes"
                          rows="3"
                          class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                          placeholder="Additional lease information or special terms..."></textarea>
                @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('leases.index') }}"
               class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                {{ $isEditing ? 'Update Lease' : 'Create Lease' }}
            </button>
        </div>
    </form>
</div>
