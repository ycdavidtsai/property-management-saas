<?php

namespace App\Livewire\Admin;

use App\Models\Vendor;
use App\Models\VendorPromotionRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class PendingVendorList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = 'pending';
    public string $filterType = '';  // 'registration' or 'promotion' or '' for all
    public ?string $selectedRequestId = null;
    public string $rejectionReason = '';
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'pending'],
        'filterType' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    /**
     * Open approve confirmation modal
     */
    public function confirmApprove(string $requestId)
    {
        $this->selectedRequestId = $requestId;
        $this->showApproveModal = true;
    }

    /**
     * Open reject modal
     */
    public function confirmReject(string $requestId)
    {
        $this->selectedRequestId = $requestId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    /**
     * Approve vendor request (registration or promotion)
     */
    public function approveRequest()
    {
        $request = VendorPromotionRequest::with('vendor')->find($this->selectedRequestId);

        if (!$request || !$request->vendor) {
            session()->flash('error', 'Request not found.');
            $this->closeModals();
            return;
        }

        try {
            DB::beginTransaction();

            // Update the promotion request
            $request->update([
                'status' => 'approved',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => 'Approved by admin.',
            ]);

            // Update the vendor - both registration and promotion result in global + active
            $request->vendor->update([
                'setup_status' => 'active',
                'vendor_type' => 'global',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            // Send approval notification
            $this->sendApprovalNotification($request->vendor, $request->request_type);

            $actionType = $request->request_type === 'registration' ? 'registration' : 'promotion';
            session()->flash('success', "Vendor '{$request->vendor->name}' {$actionType} has been approved.");

            Log::info('Vendor request approved', [
                'request_id' => $request->id,
                'vendor_id' => $request->vendor->id,
                'request_type' => $request->request_type,
                'approved_by' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve vendor request', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to approve vendor. Please try again.');
        }

        $this->closeModals();
    }

    /**
     * Reject vendor request
     */
    public function rejectRequest()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10|max:500',
        ], [
            'rejectionReason.required' => 'Please provide a reason for rejection.',
            'rejectionReason.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        $request = VendorPromotionRequest::with('vendor')->find($this->selectedRequestId);

        if (!$request || !$request->vendor) {
            session()->flash('error', 'Request not found.');
            $this->closeModals();
            return;
        }

        try {
            DB::beginTransaction();

            // Update the promotion request
            $request->update([
                'status' => 'rejected',
                'review_notes' => $this->rejectionReason,
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Update the vendor
            $vendorUpdate = [
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $this->rejectionReason,
            ];

            // For registration rejections, also update setup_status
            if ($request->request_type === 'registration') {
                $vendorUpdate['setup_status'] = 'rejected';
            }

            $request->vendor->update($vendorUpdate);

            DB::commit();

            // Send rejection notification
            $this->sendRejectionNotification($request->vendor, $request->request_type);

            $actionType = $request->request_type === 'registration' ? 'registration' : 'promotion request';
            session()->flash('success', "Vendor '{$request->vendor->name}' {$actionType} has been rejected.");

            Log::info('Vendor request rejected', [
                'request_id' => $request->id,
                'vendor_id' => $request->vendor->id,
                'request_type' => $request->request_type,
                'rejected_by' => Auth::id(),
                'reason' => $this->rejectionReason,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject vendor request', [
                'request_id' => $request->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to reject vendor. Please try again.');
        }

        $this->closeModals();
    }

    /**
     * Send approval notification to vendor
     */
    protected function sendApprovalNotification(Vendor $vendor, ?string $requestType)
    {
        try {
            $notificationService = app(NotificationService::class);

            $isRegistration = $requestType === 'registration';
            $subject = $isRegistration
                ? 'Your Vendor Application Has Been Approved!'
                : 'Your Global Vendor Request Has Been Approved!';

            $message = $isRegistration
                ? "Congratulations! Your vendor application for {$vendor->name} has been approved.\n\n" .
                  "You are now a Global Vendor and your profile is visible to all landlords on the platform.\n\n" .
                  "Log in to start receiving job opportunities: " . route('login')
                : "Great news! Your request to become a Global Vendor has been approved.\n\n" .
                  "Your profile for {$vendor->name} is now visible to all landlords on the platform.\n\n" .
                  "Log in to your dashboard: " . route('login');

            // Send email
            if ($vendor->user) {
                $notificationService->sendEmail(
                    $vendor->user,
                    $subject,
                    $message,
                    'general'
                );
            }

            // Send SMS
            if ($vendor->phone) {
                $smsMessage = $isRegistration
                    ? "Congratulations! Your vendor application for {$vendor->name} has been approved. You're now a Global Vendor!"
                    : "Great news! You're now a Global Vendor. Your profile is visible to all landlords!";

                $notificationService->sendSmsDirectly($vendor->phone, $smsMessage);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send approval notification', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send rejection notification to vendor
     */
    protected function sendRejectionNotification(Vendor $vendor, ?string $requestType)
    {
        try {
            $notificationService = app(NotificationService::class);

            $isRegistration = $requestType === 'registration';
            $subject = $isRegistration
                ? 'Update on Your Vendor Application'
                : 'Update on Your Global Vendor Request';

            $message = $isRegistration
                ? "Thank you for your interest in joining our platform.\n\n" .
                  "Unfortunately, we are unable to approve your vendor application for {$vendor->name} at this time.\n\n" .
                  "Reason: {$vendor->rejection_reason}\n\n" .
                  "If you believe this was in error or have questions, please contact our support team."
                : "Thank you for your request to become a Global Vendor.\n\n" .
                  "Unfortunately, we are unable to approve your request for {$vendor->name} at this time.\n\n" .
                  "Reason: {$vendor->rejection_reason}\n\n" .
                  "You can continue to operate as a private vendor.";

            // Send email
            if ($vendor->user) {
                $notificationService->sendEmail(
                    $vendor->user,
                    $subject,
                    $message,
                    'general'
                );
            }

            // Send SMS
            if ($vendor->phone) {
                $smsMessage = $isRegistration
                    ? "Your vendor application for {$vendor->name} was not approved. Please check your email for details."
                    : "Your global vendor request was not approved. Please check your email for details.";

                $notificationService->sendSmsDirectly($vendor->phone, $smsMessage);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send rejection notification', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Close all modals
     */
    public function closeModals()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedRequestId = null;
        $this->rejectionReason = '';
    }

    /**
     * Get selected request for modals
     */
    public function getSelectedRequestProperty()
    {
        return $this->selectedRequestId
            ? VendorPromotionRequest::with('vendor')->find($this->selectedRequestId)
            : null;
    }

    public function render()
    {
        $query = VendorPromotionRequest::query()
            ->with(['vendor', 'requestedBy'])
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterType, function ($q) {
                if ($this->filterType === 'promotion') {
                    // Include both 'promotion' and NULL (legacy records)
                    $q->where(function($query) {
                        $query->where('request_type', 'promotion')
                              ->orWhereNull('request_type');
                    });
                } else {
                    $q->where('request_type', $this->filterType);
                }
            })
            ->when($this->search, function ($q) {
                $q->whereHas('vendor', function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                          ->orWhere('contact_name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                          ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc');

        $requests = $query->paginate(10);

        // Get counts for status badges
        $statusCounts = [
            'pending' => VendorPromotionRequest::where('status', 'pending')->count(),
            'approved' => VendorPromotionRequest::where('status', 'approved')->count(),
            'rejected' => VendorPromotionRequest::where('status', 'rejected')->count(),
        ];

        // Get counts by type (pending only)
        $typeCounts = [
            'registration' => VendorPromotionRequest::where('status', 'pending')
                ->where('request_type', 'registration')->count(),
            'promotion' => VendorPromotionRequest::where('status', 'pending')
                ->where(function($q) {
                    $q->where('request_type', 'promotion')->orWhereNull('request_type');
                })->count(),
        ];

        return view('livewire.admin.pending-vendor-list', [
            'requests' => $requests,
            'statusCounts' => $statusCounts,
            'typeCounts' => $typeCounts,
        ]);
    }
}
