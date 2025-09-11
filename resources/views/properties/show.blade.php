<x-app-layout>
    <x-slot name="title">{{ $property->name }}</x-slot>

    <div class="space-y-6">
        <livewire:properties.property-show :property="$property" />
    </div>
</x-app-layout>
