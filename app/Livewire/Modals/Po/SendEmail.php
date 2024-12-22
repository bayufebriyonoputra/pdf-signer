<?php

namespace App\Livewire\Modals\Po;

use App\Enum\StatusEnum;
use Livewire\Component;
use App\Mail\SendPoMail;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Traits\TrackerTrait;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use LivewireUI\Modal\ModalComponent;

class SendEmail extends ModalComponent
{
    use WithFileUploads, TrackerTrait;

    public $files;
    public $greeting = 'Dear Supplier';
    public $noPo;
    public $news = 'Untuk setiap pengerjaan jasa di PT. SAI, harap mengirimkan form JSA H-2 sebelum pengerjaan ke email lucky.m@sai.co.id';

    public HeaderPo $po;

    public function mount($id){
        $this->po = HeaderPo::with('supplier')->find($id);
        $this->noPo = $this->po->no_po;
    }
    public function render()
    {
        return view('livewire.modals.po.send-email');
    }

    public function sendEmail(){
        $details = [
            'noPo' => $this->noPo,
            'news' => $this->news,
            'supplier' => $this->po->supplier->name,
            'greeting' => $this->greeting,
            'attachments' => []
        ];
        if($this->files){
            $details['attachments'][] = $this->files->getRealPath();
        }
        $filePo = DetailPo::where('header_id', $this->po->id)->get()->sortByDesc('created_at')->first();
        $details['attachment_po'] = storage_path("app/public/$filePo->file");

        $emailSupplier = $this->po->supplier->email;
        $listEmail = explode('|', $emailSupplier);
        Mail::to($listEmail)
            ->cc(['purchasing02@sai.co.id', 'deni@sai.co.id'])
            ->send(new SendPoMail($details));
        if($this->po->status != StatusEnum::CANCEL && $this->po->status != StatusEnum::REVISE){
            $this->po->update([
                'status' => StatusEnum::SENDED
            ]);
        }

        try{
            $this->addTrack(
               $this->noPo,
                'PO Sended',
                'Purchase Order berhasil dikirim  oleh ' .  auth()->user()->name,
                '<i class="bi bi-envelope-check-fill"></i>',
                'bg-teal-600'
            );
        }catch(Exception $e){
            Log::error('Terajdi kesalahan : ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }

        $this->dispatch('success-notif', message:'Berhasil mengirim email');
        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }
}
