<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Broadcast History') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('communications.compose') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Send New Broadcast
                </a>
                <a href="{{ route('communications.index') }}"
                   class="text-gray-600 hover:text-gray-800">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:communications.broadcast-history />
        </div>
    </div>
</x-app-layout>
