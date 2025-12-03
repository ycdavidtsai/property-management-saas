<x-admin-layout>
    <x-slot name="header">Organization Management</x-slot>

    <div class="mb-6">
        <p class="text-gray-600">Manage all organizations on the platform. View details, usage statistics, and control access.</p>
    </div>

    @livewire('admin.organization-list')
</x-admin-layout>
