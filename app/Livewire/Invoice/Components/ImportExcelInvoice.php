<?php

namespace App\Livewire\Invoice\Components;

use App\Livewire\Invoice\MasterInvoice as InvoiceMasterInvoice;
use App\Models\MasterInvoice;
use App\Models\Vendor;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class ImportExcelInvoice extends Component
{

    use WithFileUploads;


    public $file;

    public function render()
    {
        return view('livewire.invoice.components.import-excel-invoice');
    }


    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $path = $this->file->storeAs('temp', 'uploaded_file.xlsx', 'local');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/{$path}"));

        $data = $spreadsheet->getActiveSheet()->removeRow(1, 3)->toArray();
        // dd($data);

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                //cari vendor dulu
                $vendor = Vendor::where('name', $row[0])->first();
                if (!$vendor) {
                    throw new \Exception('Gagal Nama Vendor ' . $row[0] . ' Tidak ditemukan');
                }
                $data = [
                    'vendor_id' => $vendor->id,
                    'no_po' => $row[1],
                    'total' => $row[2],
                    'no_invoice' => $row[3],
                    'tgl_invoice' => $row[4],
                    'tgl_pembayaran' => $row[5],
                    'pic_perusahaan' => $row[6],
                ];
                MasterInvoice::create($data);
            }
            DB::commit();
            $this->dispatch('success-notif', message: 'File berhasil diimport');
            $this->dispatch('reRender')->to(InvoiceMasterInvoice::class);
        } catch (\ValueError $e) {
            DB::rollback();
            $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollback();
            $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
        }
    }
}
