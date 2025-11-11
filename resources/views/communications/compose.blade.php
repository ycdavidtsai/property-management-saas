<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Send Broadcast Message') }}
            </h2>
            <a href="{{ route('communications.index') }}"
               class="text-gray-600 hover:text-gray-800">
                ← Back to Communications
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:communications.broadcast-composer />
        </div>
    </div>
</x-app-layout>
