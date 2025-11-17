<?php

namespace App\Livewire\Communications;

use App\Models\BroadcastMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class BroadcastHistory extends Component
{
    use WithPagination;

    public $selectedBroadcast = null;
    public $showDetails = false;
    public $filters = [
        'status' => '',
        'channel' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    protected $queryString = ['filters'];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    // public function viewDetails($broadcastId)
    // {
    //     $this->selectedBroadcast = BroadcastMessage::with(['sender', 'notifications'])
    //         ->where('organization_id', Auth::user()->organization_id)
    //         ->findOrFail($broadcastId);

    //     $this->showDetails = true;
    // }

    public function viewDetails($broadcastId)
    {
        $this->selectedBroadcast = BroadcastMessage::with([
            'sender',
            'notifications.toUser' // ← Load the actual recipient users
        ])
        ->where('organization_id', Auth::user()->organization_id)
        ->findOrFail($broadcastId);

        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->selectedBroadcast = null;
        $this->showDetails = false;
    }

    public function deleteBroadcast($broadcastId)
    {
        $broadcast = BroadcastMessage::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($broadcastId);

        // Only allow deleting draft or failed broadcasts
        if (in_array($broadcast->status, ['draft', 'failed'])) {
            $broadcast->delete();
            session()->flash('message', 'Broadcast deleted successfully.');
            $this->closeDetails();
        } else {
            session()->flash('error', 'Cannot delete sent broadcasts.');
        }
    }

    public function resetFilters()
    {
        $this->filters = [
            'status' => '',
            'channel' => '',
            'date_from' => '',
            'date_to' => '',
        ];
        $this->resetPage();
    }

    public function render()
    {
        $query = BroadcastMessage::where('organization_id', Auth::user()->organization_id)
            ->with('sender')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['channel']) {
            $query->whereJsonContains('channels', $this->filters['channel']);
        }

        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $broadcasts = $query->paginate(10);

        return view('livewire.communications.broadcast-history', [
            'broadcasts' => $broadcasts,
        ]);
    }
}
