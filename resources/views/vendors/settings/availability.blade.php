<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li>
                        <a href="{{ route('vendor.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </li>
                    <li>
                        <span class="text-gray-900 font-medium">Availability Settings</span>
                    </li>
                </ol>
            </nav>

            {{-- Back Link (Mobile) --}}
            <div class="mb-4 sm:hidden">
                <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center text-blue-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            {{-- Livewire Component --}}
            <livewire:vendors.vendor-availability-settings />
        </div>
    </div>
</x-app-layout>
