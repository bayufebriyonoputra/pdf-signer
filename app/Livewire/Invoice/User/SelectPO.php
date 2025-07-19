<?php

namespace App\Livewire\Invoice\User;

use App\Models\MasterInvoice;
use App\Models\Vendor;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SelectPO extends Component
{

    public $vendors;
    public $vendorId;
    public $ids = [];
    public $pic;

    public function updatedSelectAll($value)
    {
        $visibleIds = \App\Models\MasterInvoice::query()
            ->where('vendor_id', $this->vendorId)
            ->whereNull('tgl_pembayaran')
            ->pluck('id')
            ->toArray();

        if ($value) {
            $this->ids = $visibleIds;
        } else {
            $this->ids = array_values(array_diff($this->ids, $visibleIds));
        }
    }


    public function mount()
    {
        $this->vendors = Vendor::all();
    }

    public function render()
    {
        return view('livewire.invoice.user.select-p-o', [
            'po' => MasterInvoice::where('vendor_id', $this->vendorId)
                ->whereNull('tgl_pembayaran')
                ->get()
        ]);
    }

    public function confirm()
    {
        if (!$this->ids) {
            $this->dispatch('error-notif', message: "Minimal satu po harus dipilih");
            return;
        }

        $selectedVendor = Vendor::find($this->vendorId);

        $tglPayment = calculateWednesdayDate(now(), $selectedVendor->top);


        MasterInvoice::whereIn('id', $this->ids)
            ->update([
                'pic_perusahaan' => $this->pic,
                'tgl_pembayaran' => $tglPayment
            ]);

        $this->dispatch('success-notif', message: 'Berhasil confirm');
        $this->ids = [];
        $this->vendorId = null;
        $this->dispatch('clearChoices');
    }
}
