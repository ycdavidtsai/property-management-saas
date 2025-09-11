<!-- resources/views/properties/index.blade.php -->
<x-app-layout>
    <x-slot name="title">Properties</x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Properties</h2>
            <a href="{{ route('properties.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add Property
            </a>
        </div>

        <livewire:properties.dashboard />
    </div>
</x-app-layout>
