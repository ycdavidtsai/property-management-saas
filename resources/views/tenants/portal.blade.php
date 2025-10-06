{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Lease Information') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @livewire('tenants.tenant-portal')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

<x-app-layout>
    {{-- Page Header --}}
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:mx-auto lg:max-w-6xl lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                        <div>
                            <div class="flex items-center">
                                <h1 class="ml-3 text-lg font-bold leading-7 text-gray-900 sm:leading-9 sm:truncate">
                                    My Lease Information
                                </h1>
                            </div>
                            <dl class="mt-6 flex flex-col sm:ml-3 sm:mt-1 sm:flex-row sm:flex-wrap">
                                <dt class="sr-only">Description</dt>
                                <dd class="flex items-center text-sm text-gray-500 font-medium capitalize sm:mr-6">
                                    View your current lease details and information
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @livewire('tenants.tenant-portal')
        </div>
    </div>
</x-app-layout>
