<x-app-layout>
    {{-- <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Maintenance Requests
            </h2>
            <a href="{{ route('maintenance-requests.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                New Request
            </a>
        </div>
    </x-slot> --}}
{{-- DEBUG - Remove after testing --}}
{{-- <div class="bg-yellow-100 p-4 mb-4">
    <p>Total requests passed: {{ $requests->count() }}</p>
    <p>User role: {{ auth()->user()->role }}</p>
    <p>User org: {{ auth()->user()->organization_id ?? 'null' }}</p>
    @foreach($requests as $r)
        <p>Request ID: {{ $r->id }} | Org: {{ $r->organization_id }}</p>
    @endforeach
</div> --}}
{{-- END DEBUG --}}
    {{-- <div class="py-12"> --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:maintenance-requests.maintenance-request-index />
        </div>
    {{-- </div> --}}
</x-app-layout>
