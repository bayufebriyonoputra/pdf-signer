<?php

namespace App\Livewire\Invoice;

use App\Mail\SendInvoiceMail;
use App\Models\MasterInvoice;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.invoice')]
class ListEmailInvoice extends Component
{

    public $ids = [];
    public $customMessage = '';

    public function render()
    {
        return view('livewire.invoice.list-email-invoice', [
            'invoices' => MasterInvoice::whereNotNull('tgl_pembayaran')
                ->where('is_emailed', false)
                ->latest()
        ]);
    }

    public function batchSend()
    {
        // dd($this->ids);
        $invoices = MasterInvoice::whereIn('id', $this->ids)
            ->with('vendor')
            ->get();

        $countVendors = $invoices->pluck('vendor.id')->unique();

        // jika hanya ada satu jenis vendor maka kirim
        if ($countVendors->count() === 1) {

            $invoiceCounter = Setting::where('name', 'invoice_counter')->first();

            $noRecords = '';

            if (now()->day === 1 && $invoiceCounter->value != 1) {
                // $noUrut = 001;
                $bulan = toRoman(now()->month);
                $tahun = now()->isoFormat("YY");
                $noVP = "REC-001/PUR-SAI/$bulan/$tahun";

                $noRecords = $noVP;
                $invoiceCounter->value = "1";
                $invoiceCounter->save();
            } else {
                $count = intval($invoiceCounter->value);
                $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
                $bulan = toRoman(now()->month);
                $tahun = now()->isoFormat("YY");
                $noRecords = "REC-$noUrut/PUR-SAI/$bulan/$tahun";

                $invoiceCounter->value = strval($count + 1);
                $invoiceCounter->save();
            }

            $pdf = Pdf::loadView('pdf-template.invoice-tt', [
                'data' => $invoices,
                'noRecord' => $noRecords
            ]);
            $filePath = public_path('temp/tanda-terima-invoice.pdf');
            $pdf->save($filePath);

            $listEmails = explode('|', $invoices->first()->vendor->email);
            $data = [
                'custom_message' => $this->customMessage
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
