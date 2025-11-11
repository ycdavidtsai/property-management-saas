<?php

namespace App\Livewire\Communications;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class NotificationCenter extends Component
{
    use WithPagination;

    public $selectedNotification = null;
    public $showDetails = false;
    public $filters = [
        'type' => '',
        'channel' => '',
        'status' => '',
        'unread_only' => false,
    ];

    protected $queryString = ['filters'];

    public function mount()
    {
        // Mark notifications as "seen" when user opens notification center
        // (different from "read" - seen means viewed in list, read means opened)
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function viewNotification($notificationId)
    {
        $this->selectedNotification = Notification::where('to_user_id', Auth::id())
            ->with(['fromUser', 'notifiable'])
            ->findOrFail($notificationId);

        // Mark as read
        if (!$this->selectedNotification->read_at) {
            $this->selectedNotification->update(['read_at' => now()]);
        }

        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->selectedNotification = null;
        $this->showDetails = false;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('to_user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->update(['read_at' => now()]);

        session()->flash('message', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Notification::where('to_user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        session()->flash('message', 'All notifications marked as read.');
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::where('to_user_id', Auth::id())
            ->findOrFail($notificationId);

        $notification->delete();

        session()->flash('message', 'Notification deleted successfully.');
        $this->closeDetails();
    }

    public function resetFilters()
    {
        $this->filters = [
            'type' => '',
            'channel' => '',
            'status' => '',
            'unread_only' => false,
        ];
        $this->resetPage();
    }

    public function getUnreadCountProperty()
    {
        return Notification::where('to_user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        $query = Notification::where('to_user_id', Auth::id())
            ->with(['fromUser'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filters['type']) {
            $query->where('type', $this->filters['type']);
        }

        if ($this->filters['channel']) {
            $query->where('channel', $this->filters['channel']);
        }

        if ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['unread_only']) {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(15);

        return view('livewire.communications.notification-center', [
            'notifications' => $notifications,
        ]);
    }
}
