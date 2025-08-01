<?php

namespace App\Livewire\Invoice;

use App\Models\Setting;
use Livewire\Component;
use App\Mail\SendInvoiceMail;
use App\Models\MasterInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.invoice')]
class ListEmailInvoice extends Component
{

    public $ids = [];
    public $customMessage = '';
    public $mode = "belum";

    public function render()
    {

        $list = MasterInvoice::whereNotNull('tgl_pembayaran')
            ->where('is_emailed', $this->mode === 'belum' ? false : true)
            ->latest()
            ->get();


        return view('livewire.invoice.list-email-invoice', [
            'invoices' => $list
        ]);
    }

    public function batchSend()
    {

        // hapus semua file
        $files = Storage::disk('public')->files('temp');
        Storage::disk('public')->delete($files);

        // dd($this->ids);
        $invoices = MasterInvoice::whereIn('id', $this->ids)
            ->with('vendor')
            ->get();

        $countVendors = $invoices->pluck('vendor.id')->unique();

        // jika hanya ada satu jenis vendor maka kirim
        if ($countVendors->count() === 1) {

            $invoiceCounter = Setting::where('name', 'invoice_counter')->first();

            $noRecords = '';
            $lastReset = Setting::where('name', 'last_reset')->first();
            $today = today();


            if (now()->day === 1 && $lastReset->value != $today) {
                // $noUrut = 001;
                $invoiceCounter->value = "1";
                $lastReset->value = $today;
                $invoiceCounter->save();
                $lastReset->save();
            }
            $count = intval($invoiceCounter->value);
            $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
            $bulan = toRoman(now()->month);
            $tahun = now()->isoFormat("YY");
            $noRecords = "REC-$noUrut/PUR-SAI/$bulan/$tahun";


            $invoiceCounter->value = $count + 1;
            $invoiceCounter->save();


            $pdf = Pdf::loadView('pdf-template.invoice-tt', [
                'data' => $invoices,
                'noRecord' => $noRecords
            ]);
            $fileName = str_replace('/', '-', $noRecords) . ' ' . $invoices->first()->vendor->name . '.pdf';
            $filePath = public_path('temp/' . $fileName);
            $pdf->save($filePath);

            $listEmails = explode('|', $invoices->first()->vendor->email);

            $data = [
                'custom_message' => $this->customMessage,
                'filename' => $fileName
            ];


            Mail::to($listEmails)
                ->cc(['purchasing01@sai.co.id'])
                ->send(new SendInvoiceMail($data));

            MasterInvoice::whereIn('id', $this->ids)
                ->update([
                    'is_emailed' => true
                ]);



            $this->dispatch('success-notif', message: 'Berhasil kirim email');
            $this->ids = [];
        } else {
            $this->dispatch('error-notif', message: "Terjadi kesalahan pastikan anda memilih vendor yang sama");
        }
    }
}
