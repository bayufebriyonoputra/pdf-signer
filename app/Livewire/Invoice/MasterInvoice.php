<?php

namespace App\Livewire\Invoice;

use App\Models\MasterInvoice as ModelsMasterInvoice;
use App\Models\Vendor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.invoice')]
class MasterInvoice extends Component
{
    use WithPagination;

    public $vendors;
    public $isEdit = false;

    // field
    public $invId;
    public $vendorId;
    public $noPo;
    public $noInvoice;
    public $tglInvoice;
    public $tglPembayaran;
    public $total;
    public $picPerusahaan;
    public $note;




    public function mount()
    {
        $this->vendors = Vendor::all();
    }


    #[On('reRender')]
    public function render()
    {
        return view('livewire.invoice.master-invoice', [
            'invoices' => ModelsMasterInvoice::latest()->paginate(10)
        ]);
    }

    public function save()
    {
        if (!$this->isEdit) {
            ModelsMasterInvoice::create([
                'vendor_id' => $this->vendorId,
                'no_po' => $this->noPo,
                'no_invoice' => $this->noInvoice,
                'tgl_invoice' => $this->tglInvoice,
                'tgl_pembayaran' => $this->tglPembayaran,
                'total' => $this->total,
                'pic_perusahaan' => $this->picPerusahaan,
                'note' => $this->note
            ]);
            $this->resetField();
            $this->dispatch('success-notif', message: 'Data berhasil dibuat');
        } else {
            ModelsMasterInvoice::find($this->invId)->update([
                'vendor_id' => $this->vendorId,
                'no_po' => $this->noPo,
                'no_invoice' => $this->noInvoice,
                'tgl_invoice' => $this->tglInvoice,
                'tgl_pembayaran' => $this->tglPembayaran,
                'total' => $this->total,
                'pic_perusahaan' => $this->picPerusahaan,
                'note' => $this->note
            ]);
            $this->resetField();
            $this->dispatch('success-notif', message: 'Data diubah dibuat');
        }
    }

    public function setEdit($id)
    {
        $inv = ModelsMasterInvoice::find($id);

        $this->invId = $id;
        $this->vendorId = $inv->vendor_id;
        $this->dispatch('setChoices', id: $inv->vendor_id);
        $this->isEdit = true;
        $this->noPo = $inv->no_po;
        $this->noInvoice = $inv->no_invoice;
        $this->tglInvoice = $inv->tgl_invoice;
        $this->tglPembayaran = $inv->tgl_pembayaran;
        $this->total = $inv->total;
        $this->picPerusahaan = $inv->pic_perusahaan;
        $this->note = $inv->note;
    }

    public function destroy($id)
    {
        ModelsMasterInvoice::destroy($id);
        $this->dispatch('success-notif', message: 'Berhasil menghapus data');
    }


    public function resetField()
    {
        $this->isEdit = false;

        // field
        $this->noPo = '';
        $this->noInvoice = null;
        $this->tglInvoice = null;
        $this->tglPembayaran = null;
        $this->total = 0;
        $this->picPerusahaan = '';
        $this->note = '';
        $this->invId = null;
    }
}
