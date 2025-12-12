{{-- resources/views/vendor-setup/setup.blade.php --}}
<x-guest-layout>
    @livewire('vendors.vendor-setup-wizard', [
        'token' => $token,
        'vendor' => $vendor,
        'organizationName' => $organizationName,
    ])
</x-guest-layout>
