<?php

namespace App\Livewire\Modals\Po;

use App\Models\Tracker;
use App\Enum\StatusEnum;
use App\Models\HeaderPo;
use Livewire\Attributes\On;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Carbon;
use LivewireUI\Modal\ModalComponent;
use App\Models\DetailPo as ModelsDetailPo;
use App\Traits\TrackerTrait;
use Livewire\WithFileUploads;

class DetailPo extends ModalComponent
{
    use WithFileUploads, TrackerTrait;
    public $noPo;
    public $x_cordinat;
    public $y_cordinat;
    public $approverName;
    public bool $isRevised =true;
    public $file;


    public function store(){
        $this->validate([
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        if($this->isRevised){
            $this->revisePo();
        }else{
            $po = HeaderPo::where('no_po', $this->noPo)->first();
            $this->addTrack(
                $po->no_po,
                'PO Revisi',
                'Purchase Order direvisi oleh ' . auth()->user()->name,
                '<i class="bi bi-arrow-counterclockwise"></i>',
                'bg-amber-600'
            );

            $stampedPdf = $this->file->store('img/PO', 'public');
            ModelsDetailPo::create([
                'header_id' => $this->po->id,
                'file' => $stampedPdf
            ]);
            $this->dispatch('success-notif', message:'Berhsil upload file tambahan');
        }
        $this->dispatch('pg:eventRefresh-default');
        $this->closeModal();
    }

    public function mount($noPo){
        $this->noPo = $noPo;
        $this->approverName = HeaderPo::where('no_po', $noPo)->first()->approverKedua->name;
    }
    public function render()
    {
        $headerPoId = HeaderPo::where('no_po', $this->noPo)->first()->id;
        return view('livewire.modals.po.detail-po',[
            'notifications' => Tracker::where('no_po', $this->noPo)->get()->sortByDesc('created_at'),
            'files' => ModelsDetailPo::where('header_id', $headerPoId)->get(),
        ]);
    }

    #[On('set-revised-cordinat')]
    public function setCordinat($coor){
        $this->x_cordinat = $coor['x'];
        $this->y_cordinat = $coor['y'];
    }

    public function revisePo(){

            $po = HeaderPo::where('no_po', $this->noPo)->first();
            $po->update([
                'x_coor' => $this->x_cordinat,
                'y_coor' => $this->y_cordinat
            ]);

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
            $x_mm = $po->x_coor * 0.352778;
            $y_mm = $po->y_coor * 0.352778;

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
            $po->update([
                'status' => StatusEnum::NEW->value
            ]);
            $this->addTrack(
                $po->no_po,
                'PO Revisi',
                'Purchase Order direvisi oleh ' . auth()->user()->name,
                '<i class="bi bi-arrow-counterclockwise"></i>',
                'bg-amber-600'
            );

            // Menyimpan kembali file asli
            $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file
            $stampedPdf = $this->file->store('img/PO', 'public');
            ModelsDetailPo::create([
                'header_id' => $po->id,
                'file' => $stampedPdf
            ]);

            $this->dispatch('success-notif', message: 'Berhasi Revise Document');
        }

}
