<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VendorDashboard extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $statusFilter = 'all';
    public $priorityFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'priorityFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile associated with this account.');
        }

        $query = MaintenanceRequest::with(['property', 'unit', 'tenant', 'assignedBy'])
            ->where('assigned_vendor_id', $vendor->id);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('property', function ($pq) {
                        $pq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Priority filter
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.vendors.vendor-dashboard', [
            'requests' => $requests,
        ]);
    }
}
