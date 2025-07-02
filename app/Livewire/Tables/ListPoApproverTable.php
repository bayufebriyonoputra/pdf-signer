<?php

namespace App\Livewire\Tables;

use TCPDF;
use App\Enum\RoleEnum;
use App\Models\Tracker;
use App\Enum\StatusEnum;
use App\Models\Approver;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use Livewire\Attributes\On;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ListPoApproverTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = HeaderPo::query()
            ->selectRaw("
            header_pos.*,

            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', 2), ' ', -1) AS UNSIGNED) AS no_po_number,

            FIELD(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', -2), ' ', 1),
                'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII') AS no_po_month,

            CAST(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', -1) AS UNSIGNED) AS no_po_year
        ")
            ->with(['approverPertama', 'approverKedua', 'supplier']);

        if (auth()->user()->role === RoleEnum::CHECKER) {
            $query->where('approver_1', auth()->user()->id)
                ->where('status', StatusEnum::NEW);
        } elseif (auth()->user()->role === RoleEnum::SIGNER) {
            $query->where(function ($q) {
                $q->where('approver_2', auth()->user()->id)
                    ->where('status', StatusEnum::CHECKED);
            })->orWhere(function ($q) {
                $q->whereNull('approver_1')
                    ->where('approver_2', auth()->user()->id)
                    ->where(function ($q2) {
                        $q2->where('status', StatusEnum::NEW)
                            ->orWhere('status', StatusEnum::REVISE);
                    });
            });
        }

        return $query->orderByRaw('no_po_year DESC, no_po_month DESC, no_po_number DESC');
    }

    public function relationSearch(): array
    {
        return [
            'approverPertama' => [
                'name'
            ],
            'approverKedua' => [
                'name'
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_po')
            ->add('status_label', fn($po) => $po->status->badge())
            ->add('checker', fn($po) => e($po->approverPertama->name ?? 'Skipped'))
            ->add('signer', fn($po) => e($po->approverKedua->name))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('No Po', 'no_po')
                ->searchable()
                ->sortable(),

            Column::make('Approver Pertama', 'checker')
                ->searchable(),
            Column::make('Approver Kedua', 'signer')
                ->searchable(),
            Column::make('Status', 'status_label')
                ->searchable()
                ->sortable(),

            Column::action('Action')
        ];
    }
    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(HeaderPo $row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bi bi-eye-fill"></i>')
                ->class('bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg')
                ->openModal('modals.po.detail-po', ['noPo' => $row->no_po]),
            Button::add('check')
                ->slot('<i class="bi bi-check2"></i>')
                ->class('bg-emerald-500 text-white hover:bg-emerald-700 px-4 py-2 rounded-lg')
                ->openModal('modals.po.sign-confirm', ['po' => $row->id, 'isCheck' => true]),
            Button::add('sign')
                ->slot('<i class="bi bi-check-all"></i>')
                ->class('bg-sky-500 text-white hover:bg-sky-700 px-4 py-2 rounded-lg')
                ->openModal('modals.po.sign-confirm', ['po' => $row->id, 'isCheck' => false])
        ];
    }


    public function actionRules($row): array
    {
        return [
            // Hide button cehck for role not checker and status not revise and new
            Rule::button('check')
                ->when(function ($data) {
                    if (auth()->user()->role === RoleEnum::CHECKER && ($data->status === StatusEnum::NEW || $data->status === StatusEnum::REVISE)) {
                        return false;
                    }

                    return true;
                })
                ->hide(),

            Rule::button('sign')
                ->when(function ($data) {

                    if (auth()->user()->role === RoleEnum::SIGNER && ($data->status === StatusEnum::CHECKED)) {
                        return false;
                    } elseif (auth()->user()->role === RoleEnum::SIGNER && $data->approver_1 === null && ($data->status === StatusEnum::NEW || $data->status === StatusEnum::REVISE)) {
                        return false;
                    }

                    return true;
                })
                ->hide(),
        ];
    }

    #[On('checkPo')]
    public function checkPo(HeaderPo $po)
    {
        $file = DetailPo::where('header_id', $po->id)->latest()->first();
        $checker = Approver::where('user_id', $po->approver_1)->first();


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
                $stampPath = storage_path('app/public/' . $checker->barcode_path);
                $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 30, 0.5, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                // $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
            }
        }
        $po->update([
            'status' => StatusEnum::CHECKED->value
        ]);
        Tracker::create([
            'no_po' => $po->no_po,
            'message' => 'PO Checked',
            'description' => 'Purchase Order Berhasil dicek oleh ' . auth()->user()->name,
            'icon' => '<i class="bi bi-check2-circle"></i>',
            'additional_class' => 'bg-blue-700'
        ]);

        // Menyimpan kembali file asli
        $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file
        $this->dispatch('success-notif', message: 'Berhasi Check Document');
    }

    #[On('signPo')]
    public function signPo(HeaderPo $po)
    {
        $file = DetailPo::where('header_id', $po->id)->latest()->first();
        $checker = Approver::where('user_id', $po->approver_1)->first();


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
                $stampPath = storage_path('app/public/' . $checker->barcode_path);
                // $pdf->Image($stampPath, $x_mm, $y_mm_tcpdf - 30, 0.5, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                //stamp dyu
                $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                //stmap yazaki
                $pdf->Image(storage_path('app/public/img/stamp-yazaki.png'), $x_mm - 10 + 3, $y_mm_tcpdf - 20, 40, 10, 'PNG'); // Sesuaikan posisi dan ukuran
            }
        }
        $po->update([
            'status' => StatusEnum::SIGNED->value
        ]);

        Tracker::create([
            'no_po' => $po->no_po,
            'message' => 'PO Signed',
            'description' => 'Purchase Order Berhasil disign oleh ' . auth()->user()->name,
            'icon' => '<i class="bi bi-clipboard-check-fill"></i>',
            'additional_class' => 'bg-green-600'
        ]);

        // Menyimpan kembali file asli
        $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file
        $this->dispatch('success-notif', message: 'Berhasi Sign Dokumen');
    }
}
