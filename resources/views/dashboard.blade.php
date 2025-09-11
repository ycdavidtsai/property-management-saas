<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-900">Property Overview</h2>

        <livewire:properties.dashboard />
    </div>
</x-app-layout>
