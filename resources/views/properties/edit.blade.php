<x-app-layout>
    <x-slot name="title">Edit {{ $property->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Property</h2>
            <p class="text-gray-600">Update property information and manage units</p>
        </div>

        <livewire:properties.property-edit :property="$property" />
    </div>
</x-app-layout>
