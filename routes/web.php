<?php

use App\Models\Setting;
use Livewire\Volt\Volt;
use App\Models\Approver;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Models\MasterInvoice;
use setasign\Fpdi\Tcpdf\Fpdi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('role:admin,signer,checker')->group(function () {
    Volt::route('/dashboard', 'pages.admin.dashboard');
    Volt::route('/master-approver', 'pages.admin.master_approver');
    Volt::route('/master-user', 'pages.admin.master_user');


    Volt::route('/purchase-order', 'pages.po.purchase_order');
    Volt::route('/list-po', 'pages.po.list_po');
    Volt::route('/master-supplier', 'pages.admin.master_supplier');
    Volt::route('/po-pending', 'pages.po.po_pending');
    Volt::route('/po-excel', 'pages.po.import_excel');
});

Route::middleware('role:user,admin,signer,checker')->group(function () {
    Volt::route('/po-reminder', 'pages.po.list_po_reminder');
});

Route::get('/tes/{text}', function ($text) {
    return bcrypt($text);
});

Route::get('/tes-pdf', function () {

    $data = DetailPo::whereIn('header_id', [
        6974,
        6975,
        6976,
        6977,
        6978,
        6979,
        6980,
        6981,
        6982,
        6983,
        6984,
        6985,
        6986,
        6987,
        6988,
        6989,
        6990,
        6991,
        6992,
        6995,
        6996,
        6997,
        6998,
        6999,
        7000,
        7001,
        7002,
        7003,
        7004,
        7005,
        7006,
        7007,
        7008,
        7009,
        7010,
        7011,
        7012,
        7013,
        7014,
        7015,
        7016,
        7017,
        7018,
        7019,
        7020,
        7021,
        7022,
        7023,
        7024,
        7025,
        7026,
        7027,
        7028,
        7033,
        7034,
        7035,
        7036,
        7037,
        7038,
        7039,
        7040,
        7041,
        7042,
        7043,
        7044,
        7045,
        7046,
        7047,
        7048,
        7049,
        7050,
        7051,
        7052,
        7053,
        7054,
        7055,
        7056,
        7057,
        7058,
        7059,
        7060,
        7061,
        7062,
        7063,
        7064,
        7065,
        7066,
        7067,
        7068,
        7069,
        7070,
        7071,
        7072
    ])->get();

    $successCount = 0;
    $errorCount = 0;

    foreach ($data as $d) {
        try {
            $file = DetailPo::find($d->id);
            $checker = Approver::where('user_id', 3)->first();
            $po = HeaderPo::find($d->header_id);

            // Path ke PDF asli
            $pdfContent = storage_path('app/public/' . $file->file);

            // Cek apakah file ada sebelum melanjutkan
            if (!file_exists($pdfContent)) {
                throw new \Exception("File PDF tidak ditemukan: {$pdfContent}");
            }

            // Cek apakah file readable
            if (!is_readable($pdfContent)) {
                throw new \Exception("File PDF tidak dapat dibaca: {$pdfContent}");
            }

            // Cek apakah barcode path ada
            $barcodePath = storage_path('app/public/' . $checker->barcode_path);
            if (!file_exists($barcodePath)) {
                throw new \Exception("File barcode tidak ditemukan: {$barcodePath}");
            }

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

            // Memuat file PDF asli - ini bagian yang sering error
            $pageCount = $pdf->setSourceFile($pdfContent);

            // Import setiap halaman dari PDF asli
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pdf->AddPage();
                $tplId = $pdf->importPage($pageNo);
                $pdf->useTemplate($tplId, 0, 0, null, null, true);

                // Menambahkan gambar stamp di halaman terakhir, misalnya
                if ($pageNo === $pageCount) {
                    $stampPath = storage_path('app/public/img/barcode/ttd_dyu.png');
                    // ttd
                    //stamp dyu
                    // $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 25, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                    // stmap yazaki
                    // $pdf->Image(storage_path('app/public/img/barcode/stamp-yazaki.png'), $x_mm - 10, $y_mm_tcpdf - 18, 40, 10, 'PNG'); // Sesuaikan posisi dan ukuran

                    // Metodde QR Code
                    $stampPath = storage_path('app/public/' . $checker->barcode_path);
                    $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG');
                }
            }

            // Menyimpan kembali file asli
            $pdf->Output($pdfContent, 'F');

            $successCount++;

            // Log success (optional)
            // Log::info("PDF berhasil diproses", [
            //     'file_path' => $pdfContent,
            //     'detail_po_id' => $d->id,
            //     'header_id' => $d->header_id
            // ]);

        } catch (\Exception $e) {
            $errorCount++;

            // Log error dengan detail lengkap
            Log::error("Gagal memproses PDF", [
                'error_message' => $e->getMessage(),
                'file_path' => $pdfContent ?? 'N/A',
                'detail_po_id' => $d->id ?? 'N/A',
                'header_id' => $d->header_id ?? 'N/A',
                'barcode_path' => $barcodePath ?? 'N/A',
                'po_id' => $po->id ?? 'N/A',
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            // Lanjutkan ke iterasi berikutnya
            continue;
        }
    }
});

Route::get('/tes-invoice', function () {
    $invoices = MasterInvoice::whereIn('id', ['23'])
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
    }
});

require __DIR__ . '/auth.php';
require __DIR__ . '/approver.php';
require __DIR__ . '/invoice.php';
