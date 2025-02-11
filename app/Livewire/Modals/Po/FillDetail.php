<?php

namespace App\Livewire\Modals\Po;

use App\Models\User;
use App\Enum\RoleEnum;
use App\Models\DetailPo;
use Livewire\Component;
use App\Models\HeaderPo;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use setasign\Fpdi\Tcpdf\Fpdi;
use LivewireUI\Modal\ModalComponent;

class FillDetail extends ModalComponent
{

    use WithFileUploads;

    public $headerId;
    public $x_cordinat;
    public $y_cordinat;
    public $file;
    public $approver_2;
    public $approver_1;
    public $signerName;

    public function mount($headerId)
    {
        $this->headerId = $headerId;
    }

    public function setSignerName()
    {
        $this->signerName = User::find($this->approver_2)->name;
    }

    public function render()
    {
        return view('livewire.modals.po.fill-detail', [
            'signer' => User::where('role', RoleEnum::SIGNER)->get(),
            'checker' => User::where('role', RoleEnum::CHECKER)->get()
        ]);
    }

    #[On('set-revised-cordinat')]
    public function setCordinat($coor)
    {
        $this->x_cordinat = $coor['x'];
        $this->y_cordinat = $coor['y'];
    }

    public function store()
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        $po = HeaderPo::find($this->headerId);
        $po->update([
            'x_coor' => $this->x_cordinat,
            'y_coor' => $this->y_cordinat,
            'approver_1' => $this->approver_1,
            'approver_2' => $this->approver_2
        ]);

        $stampedPdf = $this->file->store('img/PO', 'public');
        DetailPo::create([
            'header_id' => $this->headerId,
            'file' => $stampedPdf
        ]);
        $this->dispatch('success-notif', message: 'Berhsil upload file tambahan');
        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }
}
