<?php

namespace App\Livewire\Forms\Po;

use Carbon\Carbon;
use Livewire\Form;
use App\Models\Tracker;
use App\Enum\StatusEnum;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use Livewire\WithFileUploads;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Enum\JenisTransaksiEnum;
use App\Traits\TrackerTrait;
use Livewire\Attributes\Validate;

class PurchaseOrderForm extends Form
{
    use WithFileUploads, TrackerTrait;

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
    public bool $revised = false;

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
        if($this->revised){
            $this->stampRevised();
        }
        $this->file = $this->file->store('img/PO', 'public');

        DetailPo::create([
            'header_id' => $header->id,
            'file' => $this->file
        ]);

        if(!$this->revised){
            Tracker::create([
                'no_po' => $this->noPo,
                'message' => 'PO Created',
                'description' => 'Purchase Order Berhasil dibuat oleh ' . auth()->user()->name,
                'icon' => '<i class="bi bi-folder-plus"></i>',
                'additional_class' => 'bg-cyan-500'
            ]);
        }


        $this->reset();
    }

    private function stampRevised(){


            // Path ke PDF asli
            $pdfContent = $this->file->getRealPath();

            // Membuat instance FPDI (extends TCPDF)
            $pdf = new Fpdi();

            // Menyimpan konfigurasi dasar PDF
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Electronics PO');
            $pdf->SetTitle('Po Digitaly Signed');

            $pageHeight_mm = $pdf->getPageHeight();
            $pageWidth_mm = $pdf->getPageWidth();


            // Mendapatkan ukuran halaman saat ini
            $pageWidth = $pdf->getPageWidth();
            $pageHeight = $pdf->getPageHeight();

            // Konversi posisi x dan y dari satuan points ke milimeter
            $x_mm = $this->xCoor * 0.352778;
            $y_mm = $this->yCoor * 0.352778;

            // Balik koordinat y untuk menyesuaikan titik asal dari bawah ke atas
            $y_mm_tcpdf = $pageHeight - $y_mm;
            // Memuat file PDF asli
            $pageCount = $pdf->setSourceFile($pdfContent);

            // Import setiap halaman dari PDF asli
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pdf->AddPage();
                $tplId = $pdf->importPage($pageNo);
                $pdf->useTemplate($tplId, 0, 0, null, null, true);

                // Menambahkan gambar stamp di halaman terakhir, misalnya
                if ($pageNo === $pageCount) {
                    $img = public_path('img/Revised.png');
                    $pdf->Image($img, $x_mm - 100 , $y_mm_tcpdf - 50, 60, 30, 'PNG'); // Sesuaikan posisi dan ukuran
                    // $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                    //setText
                    $pdf->SetFont('helvetica', '', 12); // Atur font dan ukuran teks
                    $pdf->SetTextColor(255, 0, 0); // Warna merah (RGB: 255, 0, 0)
                    $pdf->Text($x_mm - 85, $y_mm_tcpdf - 35, Carbon::now()->format('d M y'));
                }
            }

            $this->addTrack(
                $this->noPo,
                'PO Created With Revised',
                'Purchase Order berhasil dibuat dan di set revisi langsung oleh ' . auth()->user()->name,
                '<i class="bi bi-arrow-counterclockwise"></i>',
                'bg-amber-600'
            );

            // Menyimpan kembali file asli
            $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file

    }
}
