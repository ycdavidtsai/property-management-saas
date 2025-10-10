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

    public $search = '';
    public $businessTypeFilter = '';
    public $statusFilter = 'active'; // active, inactive, all

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
        $vendor = Vendor::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($vendorId);

        $this->authorize('update', $vendor);

        $vendor->update(['is_active' => !$vendor->is_active]);

        session()->flash('message', 'Vendor status updated successfully.');
    }

    // public function render()
    // {
    //     $user = Auth::user();

    //     // Base query - filter vendors visible to current organization
    //     $query = Vendor::query()
    //         ->visibleToOrganization($user->organization_id)
    //         ->withCount(['maintenanceRequests' => function($q) use ($user) {
    //             $q->where('organization_id', $user->organization_id);
    //         }]);

    //     // Search
    //     if ($this->search) {
    //         $query->where(function ($q) {
    //             $q->where('name', 'like', '%' . $this->search . '%')
    //                 ->orWhere('email', 'like', '%' . $this->search . '%')
    //                 ->orWhere('phone', 'like', '%' . $this->search . '%');
    //         });
    //     }

    //     // Business Type Filter
    //     if ($this->businessTypeFilter) {
    //         $query->where('business_type', $this->businessTypeFilter);
    //     }

    //     // Status Filter
    //     if ($this->statusFilter === 'active') {
    //         $query->where('is_active', true);
    //     } elseif ($this->statusFilter === 'inactive') {
    //         $query->where('is_active', false);
    //     }

    //     $vendors = $query->orderBy('vendor_type', 'desc') // Global first
    //                     ->orderBy('name')
    //                     ->paginate(15);

    //     // Get distinct business types for filter
    //     $businessTypes = Vendor::visibleToOrganization($user->organization_id)
    //         ->distinct()
    //         ->pluck('business_type')
    //         ->sort();

    //     return view('livewire.vendors.vendor-index', [
    //         'vendors' => $vendors,
    //         'businessTypes' => $businessTypes,
    //     ]);
    // }

    public function render()
    {
        $user = Auth::user();

        // Base query - ONLY vendors in organization's list (pivot table)
        $query = Vendor::query()
            ->whereHas('organizations', function($q) use ($user) {
                $q->where('organization_id', $user->organization_id)
                ->where('organization_vendor.is_active', true);
            })
            ->withCount(['maintenanceRequests' => function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            }]);

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
                        ->paginate(15);

        // Get distinct business types for filter (only from MY vendors)
        $businessTypes = Vendor::whereHas('organizations', function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->distinct()
            ->pluck('business_type')
            ->sort();

        return view('livewire.vendors.vendor-index', [
            'vendors' => $vendors,
            'businessTypes' => $businessTypes,
        ]);
    }
}
