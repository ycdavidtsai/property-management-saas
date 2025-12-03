<?php

namespace App\Livewire\Admin;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorList extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Vendor::query()
            ->with(['organization', 'user']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Type filter
        if ($this->typeFilter) {
            $query->where('vendor_type', $this->typeFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $vendors = $query->paginate(20);

        // Get assignment counts for each vendor
        $vendorIds = $vendors->pluck('id');
        $assignmentCounts = MaintenanceRequest::whereIn('assigned_vendor_id', $vendorIds)
            ->selectRaw('assigned_vendor_id, count(*) as total, sum(case when status = "completed" then 1 else 0 end) as completed')
            ->groupBy('assigned_vendor_id')
            ->get()
            ->keyBy('assigned_vendor_id');

        // Stats summary
        $stats = [
            'total' => Vendor::count(),
            'global' => Vendor::where('vendor_type', 'global')->count(),
            'private' => Vendor::where('vendor_type', 'private')->count(),
        ];

        return view('livewire.admin.vendor-list', [
            'vendors' => $vendors,
            'assignmentCounts' => $assignmentCounts,
            'stats' => $stats,
        ]);
    }
}
