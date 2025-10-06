<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Request Details
            </h2>
            <a href="{{ route('vendor.dashboard') }}" class="text-blue-600 hover:text-blue-900">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:vendors.vendor-request-show :maintenanceRequest="$maintenanceRequest" />
        </div>
    </div>
</x-app-layout>
