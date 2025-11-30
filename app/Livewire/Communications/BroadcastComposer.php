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
    // SMS character limits based on Twilio recommendations
    const SMS_RECOMMENDED_LIMIT = 320;      // Twilio recommended max
    const SMS_SINGLE_SEGMENT = 160;         // Single SMS segment
    const SMS_ABSOLUTE_MAX = 1600;          // Twilio absolute max

    public $title = '';
    public $message = '';
    public $channel = 'email';              // Changed from array to single value (either/or)
    public $recipientType = 'all_tenants';
    public $selectedProperties = [];
    public $selectedUnits = [];
    public $selectedUsers = [];
    public $previewRecipients = [];
    public $recipientCount = 0;
    public $showPreview = false;

    // Character count tracking
    public $characterCount = 0;
    public $segmentCount = 1;

    protected function rules()
    {
        $messageMaxLength = $this->channel === 'sms' ? self::SMS_RECOMMENDED_LIMIT : 10000;

        return [
            'title' => 'required|string|max:255',
            'message' => "required|string|min:10|max:{$messageMaxLength}",
            'channel' => 'required|in:email,sms',
            'recipientType' => 'required|in:all_tenants,property,unit,specific_users',
        ];
    }

    protected $messages = [
        'title.required' => 'Please enter a title for your message.',
        'message.required' => 'Please enter your message content.',
        'message.min' => 'Message must be at least 10 characters.',
        'message.max' => 'Message exceeds the maximum length for the selected channel.',
        'channel.required' => 'Please select a delivery channel (Email or SMS).',
    ];

    public function mount()
    {
        $this->channel = 'email';
        $this->updateCharacterCount();
    }

    /**
     * When channel changes, validate message length for SMS
     */
    public function updatedChannel($value)
    {
        $this->updateCharacterCount();

        // Clear validation errors when switching channels
        $this->resetErrorBag('message');

        // If switching to SMS and message is too long, show warning
        if ($value === 'sms' && strlen($this->message) > self::SMS_RECOMMENDED_LIMIT) {
            $this->addError('message', 'Your message exceeds the recommended SMS limit of ' . self::SMS_RECOMMENDED_LIMIT . ' characters. Please shorten it or switch to Email.');
        }
    }

    /**
     * Update character count when message changes
     */
    public function updatedMessage($value)
    {
        $this->updateCharacterCount();
    }

    /**
     * Calculate character count and SMS segments
     */
    protected function updateCharacterCount()
    {
        $this->characterCount = strlen($this->message);

        // Calculate SMS segments (160 chars for first, 153 for subsequent due to headers)
        if ($this->characterCount <= 160) {
            $this->segmentCount = 1;
        } else {
            $this->segmentCount = ceil(($this->characterCount) / 153);
        }
    }

    /**
     * Get the character limit based on selected channel
     */
    public function getCharacterLimitProperty()
    {
        return $this->channel === 'sms' ? self::SMS_RECOMMENDED_LIMIT : 10000;
    }

    /**
     * Get remaining characters
     */
    public function getRemainingCharactersProperty()
    {
        $limit = $this->channel === 'sms' ? self::SMS_RECOMMENDED_LIMIT : 10000;
        return $limit - $this->characterCount;
    }

    /**
     * Check if message is over SMS limit
     */
    public function getIsOverSmsLimitProperty()
    {
        return $this->channel === 'sms' && $this->characterCount > self::SMS_RECOMMENDED_LIMIT;
    }

    public function updatedRecipientType()
    {
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

        // Additional SMS validation
        if ($this->channel === 'sms' && $this->characterCount > self::SMS_RECOMMENDED_LIMIT) {
            $this->addError('message', 'SMS messages must be ' . self::SMS_RECOMMENDED_LIMIT . ' characters or less. Current: ' . $this->characterCount);
            return;
        }

        if ($this->recipientCount === 0) {
            $this->previewRecipientsList();

            if ($this->recipientCount === 0) {
                $this->addError('recipientType', 'Cannot send to 0 recipients. Please adjust your selection.');
                return;
            }
        }

        $broadcastService = app(BroadcastService::class);

        $filters = $this->buildFilters();

        // Convert single channel to array for BroadcastService compatibility
        $channels = [$this->channel];

        $broadcast = $broadcastService->createBroadcast(
            Auth::user(),
            $this->title,
            $this->message,
            $channels,
            $this->recipientType,
            $filters
        );

        $channelLabel = $this->channel === 'email' ? 'email' : 'SMS';
        session()->flash('message', "Broadcast {$channelLabel} sent successfully to {$broadcast->recipient_count} recipients!");

        $this->dispatch('broadcast-sent', ['broadcast_id' => $broadcast->id]);

        // Reset form
        $this->reset(['title', 'message', 'selectedProperties', 'selectedUnits', 'selectedUsers', 'previewRecipients', 'recipientCount', 'showPreview', 'characterCount']);
        $this->channel = 'email';
        $this->recipientType = 'all_tenants';
        $this->segmentCount = 1;
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
            'smsLimit' => self::SMS_RECOMMENDED_LIMIT,
            'smsSegmentSize' => self::SMS_SINGLE_SEGMENT,
        ]);
    }
}
