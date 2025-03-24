<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Audience;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class AudienceCustomerManager extends Component
{
    public $audienceId = '';
    public $search = '';
    public $selectedCustomers = [];

    public function addCustomersToAudience()
    {
        foreach ($this->selectedCustomers as $customerId) {
            DB::table('audience_customer')->updateOrInsert(
                ['customer_id' => $customerId, 'audience_id' => $this->audienceId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->reset('selectedCustomers');
        session()->flash('message', 'Clientes agregados a la audiencia.');
    }

    public function render()
    {
        $audiences = Audience::all();
        $customers = Customer::where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->limit(20)
            ->get();

        return view('livewire.audience-customer-manager', [
            'audiences' => $audiences,
            'customers' => $customers
        ]);
    }
}
