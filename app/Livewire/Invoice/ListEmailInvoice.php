<?php

namespace App\Livewire\Invoice;

use App\Models\MasterInvoice;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.invoice')]
class ListEmailInvoice extends Component
{
    public function render()
    {
        return view('livewire.invoice.list-email-invoice',[
            'invoices' => MasterInvoice::whereNotNull('tgl_pembayaran')
                ->latest()->paginate(10)
        ]);
    }
}
