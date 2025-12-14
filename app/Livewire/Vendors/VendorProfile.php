<?php

namespace App\Livewire\Vendors;

use App\Models\Vendor;
use App\Models\VendorPromotionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class VendorProfile extends Component
{
    public $vendor;

    // For promotion request (only invited private vendors)
    public $showPromotionModal = false;
    public $promotionReason = '';
    public $hasRequestedPromotion = false;
    public $promotionRequest = null;

    protected $rules = [
        'promotionReason' => 'required|string|min:20|max:500',
    ];

    protected $messages = [
        'promotionReason.required' => 'Please explain why you want to become a global vendor.',
        'promotionReason.min' => 'Please provide more detail (at least 20 characters).',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->vendor = Vendor::where('user_id', $user->id)->first();

        if (!$this->vendor) {
            session()->flash('error', 'Vendor profile not found.');
            return redirect()->route('vendor.dashboard');
        }

        // Check if already requested promotion (only for eligible vendors)
        if ($this->canRequestPromotion()) {
            $this->checkPromotionStatus();
        }
    }

    /**
     * Check if vendor is fully active
     */
    public function isActive(): bool
    {
        return $this->vendor && $this->vendor->setup_status === 'active';
    }

    /**
     * Check if vendor is pending admin approval
     */
    public function isPendingApproval(): bool
    {
        return $this->vendor && $this->vendor->setup_status === 'pending_approval';
    }

    /**
     * Check if vendor was rejected
     */
    public function isRejected(): bool
    {
        return $this->vendor && $this->vendor->setup_status === 'rejected';
    }

    /**
     * Check if vendor is private (org-specific)
     */
    public function isPrivate(): bool
    {
        return $this->vendor && $this->vendor->vendor_type === 'private';
    }

    /**
     * Check if vendor is global
     */
    public function isGlobal(): bool
    {
        return $this->vendor && $this->vendor->vendor_type === 'global';
    }

    /**
     * Check if vendor self-registered
     */
    public function isSelfRegistered(): bool
    {
        return $this->vendor && $this->vendor->registration_source === 'self_registered';
    }

    /**
     * Check if vendor was invited by a landlord
     */
    public function isInvited(): bool
    {
        return $this->vendor && $this->vendor->registration_source === 'invited';
    }

    /**
     * Can this vendor request promotion to global?
     * Only ACTIVE, PRIVATE, INVITED vendors can request promotion
     * Self-registered vendors automatically become global when approved
     */
    public function canRequestPromotion(): bool
    {
        return $this->isActive()
            && $this->isPrivate()
            && !$this->isSelfRegistered()  // Self-registered will be global after approval
            && !$this->hasRequestedPromotion;
    }

    protected function checkPromotionStatus()
    {
        $this->promotionRequest = VendorPromotionRequest::where('vendor_id', $this->vendor->id)
            ->latest()
            ->first();

        $this->hasRequestedPromotion = $this->promotionRequest &&
            in_array($this->promotionRequest->status, ['pending', 'approved']);
    }

    public function openPromotionModal()
    {
        if (!$this->canRequestPromotion()) {
            session()->flash('error', 'You cannot request promotion at this time.');
            return;
        }
        $this->showPromotionModal = true;
    }

    public function closePromotionModal()
    {
        $this->showPromotionModal = false;
        $this->promotionReason = '';
        $this->resetValidation();
    }

    public function submitPromotionRequest()
    {
        if (!$this->canRequestPromotion()) {
            session()->flash('error', 'You cannot request promotion at this time.');
            return;
        }

        $this->validate();

        try {
            VendorPromotionRequest::create([
                'vendor_id' => $this->vendor->id,
                'reason' => $this->promotionReason,
                'status' => 'pending',
            ]);

            $this->hasRequestedPromotion = true;
            $this->closePromotionModal();

            session()->flash('success', 'Your promotion request has been submitted. We will review it shortly.');

            Log::info('Vendor promotion request submitted', [
                'vendor_id' => $this->vendor->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to submit promotion request', [
                'vendor_id' => $this->vendor->id,
                'error' => $e->getMessage(),
            ]);
            session()->flash('error', 'Failed to submit request. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.vendors.vendor-profile');
    }
}
