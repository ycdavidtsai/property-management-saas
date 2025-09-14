<?php
namespace App\Livewire\Leases;

use App\Models\Lease;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LeaseShow extends Component
{
    public $lease;

    // LeaseShow.php - Update mount method
    public function mount($lease = null)
    {
        if ($lease) {
            // Lease passed from controller
            $this->lease = $lease;
        } else {
            // Fallback for direct component usage
            abort(404, 'Lease not found');
        }
    }

    public function terminateLease()
    {
        // Check permissions
        // if (!auth()->user()->can('leases.delete')) {
        if (\Illuminate\Support\Facades\Gate::denies('leases.delete')) {
            session()->flash('error', 'You do not have permission to terminate leases.');
            return;
        }

        $this->lease->terminate();
        session()->flash('message', 'Lease terminated successfully!');
        return redirect()->route('leases.index');
    }

    public function render()
    {
        return view('livewire.leases.lease-show');
    }
}
