<?php

namespace App\Livewire\Forms\Po;

use App\Enum\JenisTransaksiEnum;
use Livewire\Form;
use App\Enum\StatusEnum;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Models\Tracker;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class PurchaseOrderForm extends Form
{
    use WithFileUploads;

    public $noPo = '';
    #[Validate('required|mimes:pdf|max:2048')]
    public $file;
    public $approverPertama = null;
    public $approverKedua = '';
    public $dueDate = '';
    public $supplierId = '';
    public $jenisTransaksi = 'barang';
    public $xCoor = null;
    public $yCoor = null;

    public function store(): void
    {
        $this->validate([
            'noPo' => 'required|unique:header_pos,no_po',
            'file' => 'required|mimes:pdf|max:2048',
            'dueDate' => 'required',
            'supplierId' => 'required',
            'approverKedua' => 'required'
        ]);


        $header = HeaderPo::create([
            'no_po' => $this->noPo,
            'status' => StatusEnum::NEW->value,
            'approver_1' => $this->approverPertama,
            'approver_2' => $this->approverKedua,
            'x_coor' => $this->xCoor,
            'y_coor' => $this->yCoor,
            'due_date' => $this->dueDate,
            'supplier_id' => $this->supplierId,
            'jenis_transaksi' => $this->jenisTransaksi
        ]);

        $this->file = $this->file->store('img/PO', 'public');

        DetailPo::create([
            'header_id' => $header->id,
            'file' => $this->file
        ]);

        Tracker::create([
            'no_po' => $this->noPo,
            'message' => 'PO Created',
            'description' => 'Purchase Order Berhasil dibuat oleh ' . auth()->user()->name,
            'icon' => '<i class="bi bi-folder-plus"></i>',
            'additional_class' => 'bg-cyan-500'
        ]);

        $this->reset();


    }
}
