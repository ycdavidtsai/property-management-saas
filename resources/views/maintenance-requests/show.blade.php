<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Maintenance Request #{{ substr($maintenanceRequest->id, 0, 8) }}
            </h2>
            <div class="flex space-x-2">
                @can('update', $maintenanceRequest)
                    <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endcan
                <a href="{{ route('maintenance-requests.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:maintenance-requests.maintenance-request-show :maintenanceRequest="$maintenanceRequest" />
        </div>
    </div>
</x-app-layout>
