<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(isset($showLeaseMetrics) && $showLeaseMetrics)
                <!-- Lease Metrics Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lease Overview</h3>
                    @livewire('dashboard.lease-metrics')
                </div>
            @endif

            <livewire:properties.dashboard />
        </div>
    </div>
</x-app-layout>
