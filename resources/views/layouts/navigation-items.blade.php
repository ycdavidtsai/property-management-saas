{{-- resources/views/layouts/navigation-items.blade.php --}}
{{-- Simple navigation links - no Alpine.js conflicts --}}

@php
    $user = auth()->user();
    $role = $user->role;
@endphp

{{-- ========================================= --}}
{{-- VENDOR PORTAL (vendor role only)         --}}
{{-- ========================================= --}}
@if($role === 'vendor')

<div class="border-t border-gray-200 my-4"></div>
<div class="px-2 py-1">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendor Portal</span>
</div>

{{-- Vendor Dashboard --}}
<a href="{{ route('vendor.dashboard') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendor.dashboard') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
    </svg>
    Dashboard
</a>

{{-- My Jobs --}}
<a href="{{ route('vendor.requests.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.requests.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendor.requests.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
    </svg>
    My Jobs
</a>

{{-- Calendar (Coming Soon) --}}
<span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
    </svg>
    Calendar
    <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
</span>

{{-- Earnings (Coming Soon) --}}
<span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    Earnings
    <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
</span>

{{-- Settings Section --}}
<div class="border-t border-gray-200 my-4"></div>
<div class="px-2 py-1">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Settings</span>
</div>

{{-- Availability --}}
<a href="{{ route('vendor.settings.availability') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.settings.availability') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendor.settings.availability') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    Availability
</a>

{{-- Service Areas --}}
<a href="{{ route('vendor.settings.service-areas') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.settings.service-areas') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendor.settings.service-areas') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
    </svg>
    Service Areas
</a>

{{-- Listing Profile --}}
<a href="{{ route('vendor.profile') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendor.profile') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendor.profile') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
    Listing Profile
</a>

{{-- Portfolio (Coming Soon) --}}
<span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
    </svg>
    Portfolio
    <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
</span>
<div class="border-t border-gray-200 my-4"></div>
@endif


{{-- ========================================= --}}
{{-- MAIN NAVIGATION                          --}}
{{-- ========================================= --}}

{{-- Dashboard (landlord/manager/admin) --}}
@if(App\Services\RoleService::roleHasPermission($role, 'properties.view'))
<a href="{{ route('dashboard') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('dashboard') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
    </svg>
    Dashboard
</a>
@endif

{{-- Properties --}}
@if(App\Services\RoleService::roleHasPermission($role, 'properties.view'))
<a href="{{ route('properties.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('properties.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('properties.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h1.5m-1.5 3h1.5m-1.5 3h1.5m-1.5 3h1.5" />
    </svg>
    Properties
</a>
@endif

{{-- Tenants --}}
@if(App\Services\RoleService::roleHasPermission($role, 'tenants.view'))
<a href="{{ route('tenants.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenants.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('tenants.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
    </svg>
    Tenants
</a>
@endif

{{-- Leases --}}
@if(App\Services\RoleService::canManageLeases($role))
<a href="{{ route('leases.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leases.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('leases.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
    </svg>
    Leases
</a>
@endif

{{-- Tenant Portal (tenant only) --}}
@if($role === 'tenant')
<a href="{{ route('tenant.portal') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.portal') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('tenant.portal') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
    My Lease
</a>
@endif

{{-- Maintenance (not for vendors - they see it in vendor portal) --}}
@if(App\Services\RoleService::roleHasPermission($role, 'maintenance.view') && $role !== 'vendor')
<a href="{{ route('maintenance-requests.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('maintenance-requests.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('maintenance-requests.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
    </svg>
    Maintenance
</a>
@endif

{{-- Vendors (landlord/manager/admin only) --}}
@if(in_array($role, ['admin', 'manager', 'landlord']))
<a href="{{ route('vendors.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('vendors.*') && !request()->routeIs('vendor.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('vendors.*') && !request()->routeIs('vendor.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
    </svg>
    Vendors
</a>
@endif

{{-- Communications (all roles) --}}
<a href="{{ route('communications.index') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('communications.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('communications.*') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
    </svg>
    Communications
</a>





{{-- ========================================= --}}
{{-- ADMIN SECTION                            --}}
{{-- ========================================= --}}
@if($role === 'admin')

<div class="border-t border-gray-200 my-4"></div>
<div class="px-2 py-1">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</span>
</div>

{{-- Vendor Promotions --}}
@php
    $pendingCount = \App\Models\VendorPromotionRequest::where('status', 'pending')->count();
@endphp
<a href="{{ route('admin.promotion-requests') }}"
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.promotion-requests') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('admin.promotion-requests') ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500' }}"
         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.746 3.746 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
    </svg>
    Vendor Promotions
    @if($pendingCount > 0)
        <span class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full bg-red-500 text-white">{{ $pendingCount }}</span>
    @endif
</a>

@endif


{{-- ========================================= --}}
{{-- COMING SOON (non-vendor roles)           --}}
{{-- ========================================= --}}
@if($role !== 'vendor')
    @if(App\Services\RoleService::roleHasPermission($role, 'payments.view') || App\Services\RoleService::roleHasPermission($role, 'reports.view'))

    <div class="border-t border-gray-200 my-4"></div>
    <div class="px-2 py-1">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Coming Soon</span>
    </div>

    {{-- Payments --}}
    @if(App\Services\RoleService::roleHasPermission($role, 'payments.view'))
    <span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
        <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
        </svg>
        Payments
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
    </span>
    @endif

    {{-- Reports --}}
    @if(App\Services\RoleService::roleHasPermission($role, 'reports.view'))
    <span class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-400 cursor-not-allowed">
        <svg class="mr-3 flex-shrink-0 h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
        </svg>
        Reports
        <span class="ml-auto text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Soon</span>
    </span>
    @endif

    @endif
@endif
