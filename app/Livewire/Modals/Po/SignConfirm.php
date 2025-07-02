<?php

namespace App\Livewire\Modals\Po;

use App\Models\Tracker;
use Livewire\Component;
use App\Enum\StatusEnum;
use App\Livewire\Tables\ListPoApproverTable;
use App\Models\Approver;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Traits\TrackerTrait;
use setasign\Fpdi\Tcpdf\Fpdi;
use LivewireUI\Modal\ModalComponent;

class SignConfirm extends ModalComponent
{
    use TrackerTrait;

    public HeaderPo $po;
    public $remark = '';
    public $isCheker = true;
    public function mount($isCheck)
    {
        $this->isCheker = $isCheck;
    }

    public function render()
    {
        return view('livewire.modals.sign-confirm');
    }

    public function reject()
    {
        $this->po->update([
            'pending_remark' => $this->remark,
            'status' => StatusEnum::PENDING
        ]);

        $this->addTrack(
            $this->po->no_po,
            'PO Pending',
            'Purchase Order dipending oleh ' . auth()->user()->name,
            '<i class="bi bi-clock-history"></i>',
            'bg-red-500'
        );

        $this->dispatch('success-notif', message: 'Berhasil mereject PO');
        $this->dispatch('pg:eventRefresh-default')->to(ListPoApproverTable::class);
        $this->closeModal();
    }

    public function confirm()
    {
        if ($this->isCheker) {
            $this->checkPo();
        } else {
            $this->signPo();
        }

        $this->dispatch('pg:eventRefresh-default')->to(ListPoApproverTable::class);
        $this->closeModal();
    }


    private function checkPo()
    {
        $file = DetailPo::where('header_id', $this->po->id)->latest()->first();
        $checker = Approver::where('user_id', $this->po->approver_1)->first();


        // Path ke PDF asli
        $pdfContent = storage_path('app/public/' . $file->file);


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
        $x_mm = $this->po->x_coor * 0.352778;
        $y_mm = $this->po->y_coor * 0.352778;

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
                $stampPath = storage_path('app/public/img/barcode/paraf_ddw.png');
                $pdf->Image($stampPath, $x_mm + 30, $y_mm_tcpdf - 40, 6, 6, 'PNG'); // Sesuaikan posisi dan ukuran
                // $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
            }
        }
        $this->po->update([
            'status' => StatusEnum::CHECKED->value
        ]);
        $this->addTrack(
            $this->po->no_po,
            'PO Checked',
            'Purchase Order Berhasil dicek oleh ' . auth()->user()->name,
            '<i class="bi bi-check2-circle"></i>',
            'bg-blue-700'
        );

        // Menyimpan kembali file asli
        $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file
        $this->dispatch('success-notif', message: 'Berhasi Check Document');
        $this->dispatch('refresh-dashboard');
    }

    private function signPo()
    {
        $file = DetailPo::where('header_id', $this->po->id)->latest()->first();
        $checker = Approver::where('user_id', $this->po->approver_2)->first();


        // Path ke PDF asli
        $pdfContent = storage_path('app/public/' . $file->file);


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
        $x_mm = $this->po->x_coor * 0.352778;
        $y_mm = $this->po->y_coor * 0.352778;

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
                $stampPath = storage_path('app/public/img/barcode/ttd_dyu.png');
                // $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 30, 0.5, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                //stamp dyu
                $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 25, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                // stmap yazaki
                $pdf->Image(storage_path('app/public/img/barcode/stamp-yazaki.png'), $x_mm - 10, $y_mm_tcpdf - 18, 40, 10, 'PNG'); // Sesuaikan posisi dan ukuran

                // Metodde QR Code
                // $stampPath = storage_path('app/public/' . $checker->barcode_path);
                // // $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 30, 0.5, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                // //stamp dyu
                // $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                // //stmap yazaki
                // //$pdf->Image(storage_path('app/public/img/stamp-yazaki.png'), $x_mm -10 + 3, $y_mm_tcpdf -20, 40, 10, 'PNG'); // Sesuaikan posisi dan ukuran
            }
        }
        $this->po->update([
            'status' => StatusEnum::SIGNED->value
        ]);

        $this->addTrack(
            $this->po->no_po,
            'PO Signed',
            'Purchase Order Berhasil di sign oleh ' . auth()->user()->name,
            '<i class="bi bi-clipboard-check-fill"></i>',
            'bg-green-600'
        );

        //set password
        // $pdf->SetProtection(
        //     [
        //         'copy',        // Izinkan menyalin konten
        //         'modify',      // Izinkan modifikasi
        //     ],
        //     'posai123',   // Password pengguna (user password)
        //     'bwt123'   // Password pemilik (owner password)
        // );

        // Menyimpan kembali file asli
        $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file

        $this->dispatch('success-notif', message: 'Berhasi Sign Dokumen');
        $this->dispatch('refresh-dashboard');
    }
}
