<!-- resources/views/properties/create.blade.php -->
<x-app-layout>
    <x-slot name="title">Add Property</x-slot>

    <div class="max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Property</h2>

        <livewire:properties.property-form />
        {{-- @livewire('properties.property-form') --}}


    </div>
</x-app-layout>
