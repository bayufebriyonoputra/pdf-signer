<?php

namespace App\Livewire\Tables;

use App\Enum\StatusEnum;
use App\Mail\SendPoMail;
use App\Models\Approver;
use App\Models\DetailPo;
use App\Models\HeaderPo;
use App\Models\MasterStep;
use Livewire\Attributes\On;
use App\Traits\TrackerTrait;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
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

final class ListPoAdminTable extends PowerGridComponent
{
    use WithExport, TrackerTrait;

    public string $tableName = 'table-po-admin';


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

    public function header(): array
    {
        return [
            Button::add('bulk-download')
                ->slot('Download PDF (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-md text-white')
                ->dispatch('bulk-download', [])
        ];
    }

    public function datasource(): Builder
    {
        return HeaderPo::query()->with(['approverPertama', 'approverKedua', 'supplier'])->orderByDesc('created_at');
    }

    public function relationSearch(): array
    {
        return [
            'approverPertama' => [
                'name'
            ],
            'approverKedua' => [
                'name'
            ],
            'supplier' => [
                'name'
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_po')
            ->add('supplier_name', fn($po) => e($po->supplier->name))
            ->add('status')
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
            Column::make('Nama Supplier', 'supplier_name')
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
        return [
            Filter::select('status_label', 'status')
                ->dataSource(StatusEnum::toArray())
                ->optionLabel('label')
                ->optionValue('value'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }
    #[On('bulk-download')]
    public function bulkDownload()
    {
        // $headerPo = HeaderPo::whereIn('id', $this->checkboxValues)->get();

        //buat zip
        $zipFileName = 'downloaded-po.zip';
        $zip = new \ZipArchive();
        $zip->open(public_path($zipFileName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach($this->checkboxValues as $c){
            $file = DetailPo::where('header_id', $c)->get()->sortByDesc('created_at')->first();
            //perbaiki nanti seharusnya koneksi ke sql cukup sekali tidak di looping
            $noPo = HeaderPo::where('id', $c)->with('supplier')->first();
            $filePath = storage_path("app/public/$file->file");
            if(file_exists($filePath)){
                $zip->addFile($filePath, $noPo->no_po . '-' . $noPo->supplier->name . '.pdf');
            }
        }

        $zip->close();

        //download zip
        return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);

    }


    public function actions(HeaderPo $row): array
    {
        return [
            Button::add('detail')
                ->slot('<i class="bi bi-eye-fill"></i>')
                ->class('bg-teal-500 hover:bg-teal-600 rounded-md py-2 px-4 text-white')
                ->openModal('modals.po.detail-po', ['noPo' => $row->no_po]),
            Button::add('send')
                ->slot('<i class="bi bi-send-check-fill"></i>')
                ->class('bg-sky-500 hover:bg-sky-600 text-white rounded-md px-4 py-2')
                ->openModal('modals.po.send-email', ['id' => $row->id]),
            Button::add('cancel')
                ->slot('<i class="bi bi-x-octagon-fill"></i>')
                ->class('bg-red-500 hover:bg-red-700 text-white rounded-md px-4 py-2')
                ->dispatch('cancel-po', ['id' => $row->id]),
            Button::add('confirm')
                ->slot('<i class="bi bi-check-square-fill"></i>')
                ->class('bg-green-600 hover:bg-green-800 text-white rounded-md px-4 py-2')
                ->dispatch('confirm-po', ['id' => $row->id]),
            Button::add('done')
                ->slot('<i class="bi bi-list-check"></i>')
                ->class('bg-lime-500 hover:bg-lime-700 text-white px-4 py-2 rounded-md')
                ->dispatch('done-po', ['id' => $row->id]),
            Button::add('undo')
                ->slot('<i class="bi bi-arrow-counterclockwise"></i>')
                ->class('bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-md')
                ->dispatch('undo', ['id' => $row->id]),
        ];
    }

    #[On('done-po')]
    public function donePo(int $id)
    {
        if($this->checkboxValues){
            $pos = HeaderPo::whereIn('id', $this->checkboxValues)->get();
            foreach($pos as $po){
                $po->status = StatusEnum::DONE;
                $po->save();

                $this->addTrack(
                    $po->no_po,
                    'PO Done',
                    'Purchase Order dikonfirmasi selesai oleh' .  auth()->user()->name,
                    '<i class="bi bi-list-check"></i>',
                    'bg-lime-500'
                );

            }

        }else{
            $po =  HeaderPo::find($id);
            $po->update([
                'status' => StatusEnum::DONE
            ]);
            $this->addTrack(
                $po->no_po,
                'PO Done',
                'Purchase Order dikonfirmasi selesai oleh' .  auth()->user()->name,
                '<i class="bi bi-list-check"></i>',
                'bg-lime-500'
            );
        }

        $this->dispatch('success-notif', message: 'Berhasil Done PO');
        $this->dispatch('pg:eventRefresh-default');
    }

    #[On('confirm-po')]
    public function confirmPo(int $id)
    {
        if($this->checkboxValues){
            $headerPo = HeaderPo::whereIn('id', $this->checkboxValues)->get();
            foreach($headerPo as $po){
                $po->status = StatusEnum::CONFIRMED;
                $po->save();
                $this->addTrack(
                    $po->no_po,
                    'PO Confirmed',
                    'Purchase Order Berhasil diconfirm oleh' .  auth()->user()->name,
                    '<i class="bi bi-check-circle-fill"></i>',
                    'bg-indigo-700'
                );

            }
        }else{
            $po =  HeaderPo::find($id);
            $po->update([
                'status' => StatusEnum::CONFIRMED
            ]);
            $this->addTrack(
                $po->no_po,
                'PO Confirmed',
                'Purchase Order Berhasil diconfirm oleh' .  auth()->user()->name,
                '<i class="bi bi-check-circle-fill"></i>',
                'bg-indigo-700'
            );
        }

        $this->dispatch('success-notif', message: 'Berhasil confirm PO');
        $this->dispatch('pg:eventRefresh-default');
    }

    #[On('undo')]
    public function undo(int $id) : void{
        $po = HeaderPo::find($id);
        $step = MasterStep::where('step_name', $po->status->value)->first();
        if($step){
            $po->status = $step->previous_step;
            $po->save();
            $this->addTrack(
                $po->no_po,
                'Action Has Been Undo',
                'Status Purchase di undo oleh ' .  auth()->user()->name,
                '<i class="bi bi-arrow-counterclockwise"></i>',
                'bg-amber-600'
            );
            $this->dispatch('success-notif', message:'Berhasil Undo');

        }else{
            $this->dispatch('error-notif', message:'Status ini tidak dapat di undo😎');
        }
    }

    #[On('cancel-po')]
    public function cancelPo(int $id)
    {
        $po = HeaderPo::find($id);
        $file = DetailPo::where('header_id', $id)->latest()->first();

        // Path ke PDF asli
        $pdfContent = storage_path('app/public/' . $file->file);


        // Membuat instance FPDI (extends TCPDF)
        //make fpdi instance
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
                $img = public_path('img/Cancel.png');
                $pdf->Image($img, $x_mm - 100, $y_mm_tcpdf - 50, 60, 30, 'PNG'); // Sesuaikan posisi dan ukuran
                // $pdf->Image($stampPath, $x_mm + 3, $y_mm_tcpdf - 30, 20, 20, 'PNG'); // Sesuaikan posisi dan ukuran
                //setText
                $pdf->SetFont('helvetica', '', 12); // Atur font dan ukuran teks
                $pdf->SetTextColor(255, 0, 0); // Warna merah (RGB: 255, 0, 0)
                $pdf->Text($x_mm - 85, $y_mm_tcpdf - 35, Carbon::now()->format('d M y'));
            }
        }
        $po->update([
            'status' => StatusEnum::CANCEL->value
        ]);
        $this->addTrack(
            $po->no_po,
            'PO Canceled',
            'Purchase Order dibatalkan oleh ' . auth()->user()->name,
            '<i class="bi bi-x-octagon-fill"></i>',
            'bg-red-500'
        );

        // Menyimpan kembali file asli
        $pdf->Output($pdfContent, 'F'); // 'F' mode untuk overwrite file
        $this->dispatch('success-notif', message: 'Berhasi Cancel Document');
    }


    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            Rule::button('send')
                ->when(fn($row) => $row->status != StatusEnum::SIGNED && $row->status != StatusEnum::CANCEL && $row->status != StatusEnum::REVISE && $row->status != StatusEnum::CANCEL && $row->status != StatusEnum::CONFIRMED &&  $row->status != StatusEnum::SENDED)
                ->hide(),
            Rule::button('cancel')
                ->when(fn($row) => $row->status != StatusEnum::SIGNED && $row->status != StatusEnum::SENDED)
                ->hide(),
            Rule::button('confirm')
                ->when(fn($row) => $row->status != StatusEnum::SENDED)
                ->hide(),
            Rule::button('done')
                ->when(fn($row) => $row->status != StatusEnum::CONFIRMED)
                ->hide()
        ];
    }
}
