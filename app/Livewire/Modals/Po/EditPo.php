<?php

namespace App\Livewire\Modals\Po;

use App\Models\User;
use App\Enum\RoleEnum;
use Livewire\Component;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Models\Supplier;
use App\Traits\TrackerTrait;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use LivewireUI\Modal\ModalComponent;

class EditPo extends ModalComponent
{

    use WithFileUploads;
    use TrackerTrait;


    public $headerId;
    public $x_cordinat;
    public $y_cordinat;
    public $file;
    public $approver_2;
    public $approver_1;
    public $signerName;
    public $supplierId;

    public $stats;

    public function mount($headerId)
    {
        $this->headerId = $headerId;
        $header = HeaderPo::find($headerId);
        $this->x_cordinat = $header->x_coor;
        $this->y_cordinat = $header->y_coor;
        $this->approver_1 = $header->approver_1;
        $this->approver_2 = $header->approver_2;
        $this->signerName = $header->approverKedua->name;
        $this->supplierId = $header->supplier_id;
    }

    public function setSignerName()
    {
        $this->signerName = User::find($this->approver_2)->name;
    }


    #[On('set-revised-cordinat')]
    public function setCordinat($coor)
    {
        $this->x_cordinat = $coor['x'];
        $this->y_cordinat = $coor['y'];
    }

    public function store()
    {

        $po = HeaderPo::find($this->headerId);
        $po->update([
            'x_coor' => $this->x_cordinat,
            'y_coor' => $this->y_cordinat,
            'approver_1' => $this->approver_1,
            'approver_2' => $this->approver_2,
            'supplier_id' => $this->supplierId
        ]);
        if ($this->file) {
            $stampedPdf = $this->file->store('img/PO', 'public');
            DetailPo::create([
                'header_id' => $this->headerId,
                'file' => $stampedPdf
            ]);
        }
        $this->dispatch('success-notif', message: 'Berhsil upload file tambahan');
        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modals.po.edit-po', [
            'suppliers' => Supplier::get()->sortBy('name'),
            'signer' => User::where('role', RoleEnum::SIGNER)->get(),
            'checker' => User::where('role', RoleEnum::CHECKER)->get()
        ]);
    }

    public function changeStats(){
        $header = HeaderPo::find($this->headerId);
        $header->status = $this->stats;
        $header->save();
        $this->addTrack(
            $header->no_po,
            'Manual Change',
            "Status diubah manual ke $this->stats"
        );

        $this->dispatch('success-notif', message: 'Berhsil Mengubah Status');
        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }
}
