<?php

namespace App\Livewire\Communications;

use App\Models\BroadcastMessage;
use App\Services\BroadcastService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class BroadcastHistory extends Component
{
    use WithPagination;

    public $filterChannel = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    public $selectedBroadcast = null;

    protected $queryString = [
        'filterChannel' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
    ];

    public function updatedFilterChannel()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterChannel', 'filterStatus', 'filterDateFrom', 'filterDateTo']);
        $this->resetPage();
    }

    public function viewDetails($broadcastId)
    {
        $this->selectedBroadcast = BroadcastMessage::with('sender')
            ->where('organization_id', Auth::user()->organization_id)
            ->find($broadcastId);
    }

    public function closeDetails()
    {
        $this->selectedBroadcast = null;
    }

    public function deleteBroadcast($broadcastId)
    {
        $broadcast = BroadcastMessage::where('organization_id', Auth::user()->organization_id)
            ->whereIn('status', ['draft', 'failed'])
            ->find($broadcastId);

        if ($broadcast) {
            $broadcast->delete();
            session()->flash('message', 'Broadcast deleted successfully.');
        }
    }

    public function render()
    {
        $query = BroadcastMessage::with('sender')
            ->where('organization_id', Auth::user()->organization_id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filterChannel === 'email') {
            $query->whereJsonContains('channels', 'email')
                  ->whereJsonDoesntContain('channels', 'sms');
        } elseif ($this->filterChannel === 'sms') {
            $query->whereJsonContains('channels', 'sms');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDateFrom) {
            $query->whereDate('sent_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('sent_at', '<=', $this->filterDateTo);
        }

        $broadcasts = $query->paginate(15);

        // Get current month SMS usage
        $broadcastService = app(BroadcastService::class);
        $currentMonthUsage = $broadcastService->getCurrentMonthSmsUsage(Auth::user()->organization_id);

        return view('livewire.communications.broadcast-history', [
            'broadcasts' => $broadcasts,
            'currentMonthUsage' => $currentMonthUsage,
        ]);
    }
}
