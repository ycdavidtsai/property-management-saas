<?php

namespace App\Livewire\Vendors;

use App\Models\MaintenanceRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VendorDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'pending'; // pending, active, completed
    public $search = '';
    public $priorityFilter = '';

    // Stats
    public $pendingCount = 0;
    public $activeCount = 0;
    public $completedThisMonth = 0;
    public $totalEarnings = 0;

    protected $queryString = [
        'activeTab' => ['except' => 'pending'],
        'search' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadStats();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedActiveTab()
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    protected function loadStats()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return;
        }

        // Pending acceptance count
        $this->pendingCount = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'pending_acceptance')
            ->count();

        // Active jobs (assigned + in_progress)
        $this->activeCount = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();

        // Completed this month
        $this->completedThisMonth = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();

        // Total earnings this month
        $this->totalEarnings = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('actual_cost') ?? 0;
    }

    public function getRequestsProperty()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->id)->first();

        if (!$vendor) {
            return collect();
        }

        $query = MaintenanceRequest::where('assigned_vendor_id', $vendor->id)
            ->with(['property', 'unit', 'tenant']);

        // Filter by tab
        switch ($this->activeTab) {
            case 'pending':
                $query->where('status', 'pending_acceptance');
                break;
            case 'active':
                $query->whereIn('status', ['assigned', 'in_progress']);
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('property', function ($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('address', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Priority filter
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        // Order by priority for pending/active, by date for completed
        if ($this->activeTab === 'completed') {
            $query->orderBy('completed_at', 'desc');
        } else {
            $query->orderByRaw("FIELD(priority, 'emergency', 'high', 'medium', 'low')")
                  ->orderBy('created_at', 'asc');
        }

        return $query->paginate(10);
    }

    /**
     * Quick accept from dashboard
     */
    public function quickAccept($requestId)
    {
        $request = MaintenanceRequest::findOrFail($requestId);

        // Verify this vendor is assigned
        $vendor = Vendor::where('user_id', Auth::id())->first();
        if (!$vendor || $request->assigned_vendor_id !== $vendor->id) {
            session()->flash('error', 'You are not authorized to accept this request.');
            return;
        }

        if ($request->status !== 'pending_acceptance') {
            session()->flash('error', 'This request is no longer pending acceptance.');
            return;
        }

        $request->update([
            'status' => 'assigned',
            'accepted_at' => now(),
        ]);

        // Create timeline entry
        \App\Models\MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $request->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => Auth::user()->name . ' accepted the assignment',
            'is_internal' => false,
        ]);

        session()->flash('success', 'Job accepted! Tap to view details.');
        $this->loadStats();
    }

    /**
     * Quick start work from dashboard
     */
    public function quickStart($requestId)
    {
        $request = MaintenanceRequest::findOrFail($requestId);

        $vendor = Vendor::where('user_id', Auth::id())->first();
        if (!$vendor || $request->assigned_vendor_id !== $vendor->id) {
            session()->flash('error', 'You are not authorized to update this request.');
            return;
        }

        if ($request->status !== 'assigned') {
            session()->flash('error', 'Invalid status for starting work.');
            return;
        }

        $request->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        \App\Models\MaintenanceRequestUpdate::create([
            'maintenance_request_id' => $request->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_change',
            'message' => 'Work started',
            'is_internal' => false,
        ]);

        session()->flash('success', 'Work started! Good luck!');
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.vendors.vendor-dashboard', [
            'requests' => $this->requests,
        ]);
    }
}
