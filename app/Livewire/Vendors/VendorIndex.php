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

    public function render()
    {
        $query = Vendor::where('organization_id', Auth::user()->organization_id);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply business type filter
        if ($this->businessTypeFilter) {
            $query->where('business_type', $this->businessTypeFilter);
        }

        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }
        // 'all' shows both active and inactive

        $vendors = $query->withCount('maintenanceRequests')
            ->latest()
            ->paginate(15);

        // Get unique business types for filter dropdown
        $businessTypes = Vendor::where('organization_id', Auth::user()->organization_id)
            ->distinct()
            ->pluck('business_type')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.vendors.vendor-index', [
            'vendors' => $vendors,
            'businessTypes' => $businessTypes,
        ]);
    }
}
