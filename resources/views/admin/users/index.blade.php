<x-admin-layout>
    <x-slot name="header">User Management</x-slot>

    <div class="mb-6">
        <p class="text-gray-600">Manage all users across the platform. Search, filter by role or organization, and control account access.</p>
    </div>

    @livewire('admin.user-list')
</x-admin-layout>
