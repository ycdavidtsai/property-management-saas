{{-- resources/views/tenants/show.blade.php --}}
<x-app-layout>
    {{-- <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tenant Details') }}
            </h2>
            <a href="{{ route('tenants.index') }}"
               class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to Tenants
            </a>
        </div>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:tenants.tenant-show :tenant="$tenant" />
        </div>
    </div>
</x-app-layout>
