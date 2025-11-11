<?php

namespace App\Livewire\Communications;

use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use App\Services\BroadcastService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BroadcastComposer extends Component
{
    public $title = '';
    public $message = '';
    public $channels = ['email'];
    public $recipientType = 'all_tenants';
    public $selectedProperties = [];
    public $selectedUnits = [];
    public $selectedUsers = [];
    public $previewRecipients = [];
    public $recipientCount = 0;
    public $showPreview = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'required|string|min:10',
        'channels' => 'required|array|min:1',
        'recipientType' => 'required|in:all_tenants,property,unit,specific_users',
    ];

    protected $messages = [
        'title.required' => 'Please enter a title for your message.',
        'message.required' => 'Please enter your message content.',
        'message.min' => 'Message must be at least 10 characters.',
        'channels.required' => 'Please select at least one channel (Email or SMS).',
    ];

    public function mount()
    {
        // Default to email only
        $this->channels = ['email'];
    }

    public function updatedRecipientType()
    {
        // Clear selections when recipient type changes
        $this->selectedProperties = [];
        $this->selectedUnits = [];
        $this->selectedUsers = [];
        $this->previewRecipients = [];
        $this->recipientCount = 0;
        $this->showPreview = false;
    }

    public function updatedSelectedProperties()
    {
        $this->showPreview = false;
    }

    public function updatedSelectedUnits()
    {
        $this->showPreview = false;
    }

    public function updatedSelectedUsers()
    {
        $this->showPreview = false;
    }

    public function previewRecipientsList()
    {
        $broadcastService = app(BroadcastService::class);

        $filters = $this->buildFilters();

        $preview = $broadcastService->previewRecipients(
            Auth::user()->organization_id,
            $this->recipientType,
            $filters
        );

        $this->previewRecipients = $preview['recipients'];
        $this->recipientCount = $preview['count'];
        $this->showPreview = true;

        if ($this->recipientCount === 0) {
            $this->addError('recipientType', 'No recipients found with the selected criteria.');
        }
    }

    public function send()
    {
        $this->validate();

        if ($this->recipientCount === 0) {
            $this->previewRecipientsList();

            if ($this->recipientCount === 0) {
                $this->addError('recipientType', 'Cannot send to 0 recipients. Please adjust your selection.');
                return;
            }
        }

        $broadcastService = app(BroadcastService::class);

        $filters = $this->buildFilters();

        $broadcast = $broadcastService->createBroadcast(
            Auth::user(),
            $this->title,
            $this->message,
            $this->channels,
            $this->recipientType,
            $filters
        );

        session()->flash('message', "Broadcast message sent successfully to {$broadcast->recipient_count} recipients!");

        // Emit event for parent components
        $this->dispatch('broadcast-sent', ['broadcast_id' => $broadcast->id]);

        // Reset form
        $this->reset(['title', 'message', 'selectedProperties', 'selectedUnits', 'selectedUsers', 'previewRecipients', 'recipientCount', 'showPreview']);
        $this->channels = ['email'];
        $this->recipientType = 'all_tenants';
    }

    protected function buildFilters(): ?array
    {
        $filters = null;

        switch ($this->recipientType) {
            case 'property':
                if (!empty($this->selectedProperties)) {
                    $filters = ['property_ids' => $this->selectedProperties];
                }
                break;

            case 'unit':
                if (!empty($this->selectedUnits)) {
                    $filters = ['unit_ids' => $this->selectedUnits];
                }
                break;

            case 'specific_users':
                if (!empty($this->selectedUsers)) {
                    $filters = ['user_ids' => $this->selectedUsers];
                }
                break;
        }

        return $filters;
    }

    public function render()
    {
        $properties = Property::where('organization_id', Auth::user()->organization_id)
            ->orderBy('name')
            ->get();

        $units = Unit::whereHas('property', function ($query) {
            $query->where('organization_id', Auth::user()->organization_id);
        })
        ->with('property')
        ->orderBy('unit_number')
        ->get();

        $tenants = User::where('organization_id', Auth::user()->organization_id)
            ->where('role', 'tenant')
            ->whereHas('leases', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        return view('livewire.communications.broadcast-composer', [
            'properties' => $properties,
            'units' => $units,
            'tenants' => $tenants,
        ]);
    }
}
