<?php
namespace App\Livewire\Leases;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaseForm extends Component
{
    public $lease;
    public $isEditing = false;

    // Form fields
    public $unit_id = '';
    public $tenant_ids = [];
    public $start_date = '';
    public $end_date = '';
    public $rent_amount = '';
    public $security_deposit = '';
    public $notes = '';
    public $status = 'active';

    // Available data
    public $availableUnits;
    public $availableTenants;

    protected $rules = [
        'unit_id' => 'required|exists:units,id',
        'tenant_ids' => 'required|array|min:1',
        'tenant_ids.*' => 'exists:users,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
        'rent_amount' => 'required|numeric|min:0',
        'security_deposit' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string|max:1000',
        'status' => 'required|in:active,expiring_soon,expired,terminated',
    ];

    // Update the mount method in app/Livewire/Leases/LeaseForm.php

    public function mount($lease = null)
    {
        $organizationId = Auth::user()->organization_id;

        // Load available units (vacant or for_lease)
        $this->availableUnits = Unit::with('property')
            ->whereHas('property', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->whereIn('status', ['vacant', 'for_lease'])
            ->get();

        // Load available tenants (users with tenant role and no active lease)
        $this->availableTenants = User::where('organization_id', $organizationId)
            ->where('role', 'tenant')
            ->where('is_active', true)
            ->whereDoesntHave('leases', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        if ($lease) {
            // Editing mode - lease passed from controller
            $this->lease = $lease->load('tenants');
            $this->isEditing = true;
            $this->loadLeaseData();
            
            // For editing: add current unit to available units if not already there
            if (!$this->availableUnits->contains('id', $this->lease->unit_id)) {
                $this->availableUnits->push($this->lease->unit);
            }
            
            // For editing: add current tenants to available tenants if not already there
            foreach ($this->lease->tenants as $tenant) {
                if (!$this->availableTenants->contains('id', $tenant->id)) {
                    $this->availableTenants->push($tenant);
                }
            }
        }
    }

    public function loadLeaseData()
    {
        $this->unit_id = $this->lease->unit_id;
        $this->tenant_ids = $this->lease->tenants->pluck('id')->toArray();
        $this->start_date = $this->lease->start_date->format('Y-m-d');
        $this->end_date = $this->lease->end_date->format('Y-m-d');
        $this->rent_amount = $this->lease->rent_amount;
        $this->security_deposit = $this->lease->security_deposit;
        $this->notes = $this->lease->notes;
        $this->status = $this->lease->status;
    }

    public function updatedUnitId()
    {
        if ($this->unit_id) {
            $unit = Unit::find($this->unit_id);
            $this->rent_amount = $unit->rent_amount ?? '';
        }
    }

    public function save()
    {
        // Adjust validation rules for editing
        if ($this->isEditing) {
            $this->rules['start_date'] = 'required|date';
        }

        $this->validate();

        $data = [
            'organization_id' => Auth::user()->organization_id,
            'unit_id' => $this->unit_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'rent_amount' => $this->rent_amount,
            'security_deposit' => $this->security_deposit,
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        if ($this->isEditing) {
            $this->lease->update($data);
            $lease = $this->lease;
        } else {
            $lease = Lease::create($data);
        }

        // Sync tenants
        $lease->tenants()->sync($this->tenant_ids);

        // Update unit status
        $unit = Unit::find($this->unit_id);
        $unit->update(['status' => 'occupied']);

        session()->flash('message', $this->isEditing ? 'Lease updated successfully!' : 'Lease created successfully!');

        return redirect()->route('leases.index');
    }

    public function render()
    {
        return view('livewire.leases.lease-form');
    }
}
