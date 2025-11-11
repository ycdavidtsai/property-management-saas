<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class NotificationPreferences extends Component
{
    public array $preferences = [
        'maintenance' => [
            'email' => true,
            'sms' => true,
        ],
        'broadcast' => [
            'email' => true,
            'sms' => true,
        ],
        'payment' => [
            'email' => true,
            'sms' => false,
        ],
        'general' => [
            'email' => true,
            'sms' => false,
        ],
    ];

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();

        // Load user's notification preferences from database if they exist
        if ($user->notification_preferences && is_array($user->notification_preferences)) {
            $this->preferences = array_replace_recursive($this->preferences, $user->notification_preferences);
        }

        Log::info('Mounted notification preferences', [
            'user_id' => $user->id,
            'preferences' => $this->preferences,
        ]);
    }

    public function save()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            Log::info('Saving notification preferences', [
                'user_id' => $user->id,
                'preferences' => $this->preferences,
            ]);

            // Save preferences
            $user->notification_preferences = $this->preferences;
            $user->save();

            // Verify the save
            $user->refresh();

            Log::info('Saved preferences verification', [
                'saved_preferences' => $user->notification_preferences,
            ]);

            session()->flash('message', 'Notification preferences updated successfully!');

        } catch (\Exception $e) {
            Log::error('Exception saving notification preferences', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error saving preferences: ' . $e->getMessage());
        }
    }

    public function toggleChannel($channel, $type)
    {
        $this->preferences[$channel][$type] = !$this->preferences[$channel][$type];
    }

    public function enableAll()
    {
        foreach ($this->preferences as $channel => $types) {
            foreach ($types as $type => $value) {
                $this->preferences[$channel][$type] = true;
            }
        }

        session()->flash('message', 'All notifications enabled.');
    }

    public function disableAll()
    {
        foreach ($this->preferences as $channel => $types) {
            foreach ($types as $type => $value) {
                $this->preferences[$channel][$type] = false;
            }
        }

        session()->flash('message', 'All notifications disabled.');
    }

    public function render()
    {
        return view('livewire.profile.notification-preferences');
    }
}
