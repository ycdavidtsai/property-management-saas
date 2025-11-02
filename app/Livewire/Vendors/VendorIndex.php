<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VendorIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    // add this so Livewire uses Tailwind-styled pagination links
    protected string $paginationTheme = 'tailwind';

    public $search = '';
    public $businessTypeFilter = '';
    public $statusFilter = 'active';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'businessTypeFilter' => ['except' => ''],
        'statusFilter' => ['except' => 'active'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingBusinessTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleVendorStatus($vendorId)
    {
        // admins can toggle any vendor, others only vendors in their organization
        if (Auth::user()->role === 'admin') {
            $vendor = Vendor::findOrFail($vendorId);
        } else {
            $vendor = Vendor::whereHas('organizations', function ($q) {
                    $q->where('organization_id', Auth::user()->organization_id)
                      ->where('organization_vendor.is_active', true);
                })
                ->findOrFail($vendorId);
        }

        $this->authorize('update', $vendor);

        $vendor->update(['is_active' => !$vendor->is_active]);

        session()->flash('message', 'Vendor status updated successfully.');
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin: list all vendors (private or global)
            $query = Vendor::query()
                ->withCount('maintenanceRequests'); // global count for admin
        } else {
            // Non-admin: only vendors associated with the user's organization
            $query = Vendor::query()
                ->whereHas('organizations', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id)
                      ->where('organization_vendor.is_active', true);
                })
                ->withCount(['maintenanceRequests' => function($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                }]);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Business Type Filter
        if ($this->businessTypeFilter) {
            $query->where('business_type', $this->businessTypeFilter);
        }

        // Status Filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $vendors = $query->orderBy('vendor_type', 'desc') // Private first
                        ->orderBy('name')
                        ->paginate($this->perPage);

        // Get distinct business types for filter
        if ($user->role === 'admin') {
            $businessTypes = Vendor::distinct()->pluck('business_type')->sort();
        } else {
            $businessTypes = Vendor::whereHas('organizations', function($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                })
                ->distinct()
                ->pluck('business_type')
                ->sort();
        }

        return view('livewire.vendors.vendor-index', [
            'vendors' => $vendors,
            'businessTypes' => $businessTypes,
        ]);
    }
}
