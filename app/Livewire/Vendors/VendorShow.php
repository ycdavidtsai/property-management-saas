<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class VendorShow extends Component
{
    use WithPagination, AuthorizesRequests;

    public Vendor $vendor;
    public $activeTab = 'details';
    public $requestStatusFilter = 'all';

    protected $queryString = [
        'activeTab' => ['except' => 'details'],
        'requestStatusFilter' => ['except' => 'all'],
    ];

    public function mount(Vendor $vendor)
    {
        $this->authorize('view', $vendor);
        $this->vendor = $vendor;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleVendorStatus()
    {
        $this->authorize('update', $this->vendor);

        $this->vendor->update([
            'is_active' => !$this->vendor->is_active
        ]);

        session()->flash('message', 'Vendor status updated successfully.');
    }

    public function render()
    {
        $maintenanceRequestsQuery = $this->vendor->maintenanceRequests()
            ->with(['property', 'unit', 'tenant']);

        // Apply status filter
        if ($this->requestStatusFilter !== 'all') {
            $maintenanceRequestsQuery->where('status', $this->requestStatusFilter);
        }

        $maintenanceRequests = $maintenanceRequestsQuery
            ->latest()
            ->paginate(10);

        // Get request statistics
        $stats = [
            'total' => $this->vendor->maintenanceRequests()->count(),
            'assigned' => $this->vendor->maintenanceRequests()->where('status', 'assigned')->count(),
            'in_progress' => $this->vendor->maintenanceRequests()->where('status', 'in_progress')->count(),
            'completed' => $this->vendor->maintenanceRequests()->where('status', 'completed')->count(),
        ];

        return view('livewire.vendors.vendor-show', [
            'maintenanceRequests' => $maintenanceRequests,
            'stats' => $stats,
        ]);
    }
}
