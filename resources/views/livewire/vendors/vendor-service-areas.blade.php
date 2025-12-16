<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Service Areas</h2>
        <p class="text-sm text-gray-600 mt-1">Define the geographic areas where you provide services</p>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <span class="text-red-800">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border">
        {{-- Area Type Selection --}}
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Area Type</h3>

            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Zip Codes Option --}}
                <label
                    for="areaType_zip"
                    class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors {{ $areaType === 'zip_codes' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}"
                >
                    <input
                        type="radio"
                        id="areaType_zip"
                        name="areaType"
                        value="zip_codes"
                        wire:model.live="areaType"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                    >
                    <div class="ml-3">
                        <span class="block font-medium text-gray-900">Zip Codes</span>
                        <span class="block text-sm text-gray-500">More precise coverage</span>
                    </div>
                </label>

                {{-- Cities Option --}}
                <label
                    for="areaType_cities"
                    class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors {{ $areaType === 'cities' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}"
                >
                    <input
                        type="radio"
                        id="areaType_cities"
                        name="areaType"
                        value="cities"
                        wire:model.live="areaType"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                    >
                    <div class="ml-3">
                        <span class="block font-medium text-gray-900">Cities / Areas</span>
                        <span class="block text-sm text-gray-500">Broader coverage</span>
                    </div>
                </label>
            </div>

            {{-- Debug: Current selection --}}
            <p class="mt-3 text-sm text-gray-500">
                Selected: <span class="font-medium text-gray-700">{{ $areaType === 'zip_codes' ? 'Zip Codes' : 'Cities / Areas' }}</span>
            </p>
        </div>

        {{-- Areas List --}}
        <div class="p-4 sm:p-6 border-b">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Your {{ $areaType === 'zip_codes' ? 'Zip Codes' : 'Cities' }}
                @if(count($areas) > 0)
                    <span class="text-sm font-normal text-gray-500">({{ count($areas) }} added)</span>
                @endif
            </h3>

            {{-- Current Areas as Tags --}}
            @if(count($areas) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($areas as $index => $area)
                        <span
                            wire:key="area-{{ $index }}-{{ $area }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                        >
                            @if($areaType === 'zip_codes')
                                <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @endif
                            {{ $area }}
                            <button
                                type="button"
                                wire:click="removeArea({{ $index }})"
                                class="ml-2 text-blue-600 hover:text-blue-800 focus:outline-none"
                                title="Remove"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                </div>

                <button
                    type="button"
                    wire:click="clearAll"
                    class="text-sm text-red-600 hover:text-red-800 underline mb-4"
                >
                    Clear all
                </button>
            @else
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg mb-4">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p>No service areas added yet</p>
                    <p class="text-sm">Add {{ $areaType === 'zip_codes' ? 'zip codes' : 'cities' }} below</p>
                </div>
            @endif

            {{-- Add New Area --}}
            <form wire:submit="addArea" class="flex gap-2">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model="newArea"
                        class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="{{ $areaType === 'zip_codes' ? 'Enter zip code (e.g., 90210)' : 'Enter city name (e.g., Los Angeles)' }}"
                        @if($areaType === 'zip_codes')
                            maxlength="10"
                            inputmode="numeric"
                        @else
                            maxlength="100"
                        @endif
                    >
                    @error('newArea')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium whitespace-nowrap"
                >
                    Add
                </button>
            </form>

            <p class="text-xs text-gray-500 mt-2">
                @if($areaType === 'zip_codes')
                    Enter a 5-digit zip code and press Enter or click Add
                @else
                    Enter a city or area name and press Enter or click Add
                @endif
            </p>
        </div>

        {{-- Save Button --}}
        <div class="p-4 sm:p-6 bg-gray-50 rounded-b-lg">
            <div class="flex items-center justify-between">
                <div>
                    @if($hasChanges)
                        <span class="text-sm text-amber-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Unsaved changes
                        </span>
                    @else
                        <span class="text-sm text-gray-500">
                            @if(count($areas) > 0)
                                All changes saved
                            @else
                                Add areas to get started
                            @endif
                        </span>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 font-medium"
                >
                    <span wire:loading.remove wire:target="save">Save Areas</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-medium text-blue-800 mb-1">Why set service areas?</h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Helps property managers find vendors near their properties</li>
            <li>• Ensures you only get job requests in areas you serve</li>
            <li>• Improves your visibility in the vendor marketplace</li>
        </ul>
    </div>
</div>
