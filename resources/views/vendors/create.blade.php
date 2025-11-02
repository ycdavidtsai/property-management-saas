<x-app-layout>
    {{-- <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('vendors.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Vendor') }}
            </h2>
        </div>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <livewire:vendors.vendor-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
