<?php

namespace App\Livewire\Tables;

use App\Models\HeaderPo;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use App\Enum\StatusEnum;
use App\Traits\TrackerTrait;

final class PoPendingTable extends PowerGridComponent
{
    use WithExport, TrackerTrait;

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
        return HeaderPo::query()
            ->with(['approverPertama', 'approverKedua'])
            ->where('status', StatusEnum::PENDING);
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
            ->add('is_remindered')
            ->add('is_remindered_label', fn($po) => $po->is_remindered ? 'Sudah' : 'Belum')
            ->add('approver_1')
            ->add('approver_2')
            ->add('pending_remark')
            ->add('checker', fn($po) => e($po->approverPertama->name) ?? 'Skipped')
            ->add('signer', fn($po) => e($po->approverKedua->name))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('NO PO', 'no_po')
                ->searchable()
                ->sortable(),
            Column::make('Approver Pertama', 'checker')
                ->searchable(),
            Column::make('Approver Kedua', 'signer')
                ->searchable(),
            Column::make('Remark', 'pending_remark'),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('active')]
    public function edit($rowId): void
    {
       $po = HeaderPo::find($rowId);
       $po->update([
        'status' => StatusEnum::NEW,
        'pending_remark' => null
       ]);

       $this->addTrack($po->no_po,'PO Reactivated', 'Purchase Order Di Aktifkan Kembali oleh ' . auth()->user()->name,
                        '<i class="bi bi-arrow-counterclockwise"></i>', 'bg-indigo-700');
       $this->dispatch('success-notif', message:'PO Berhasil diaktifkan kembali');
       $this->dispatch('pg:eventRefresh-default')->self();


    }

    public function actions(HeaderPo $row): array
    {
        return [
            Button::add('reactived')
                ->slot('<i class="bi bi-arrow-counterclockwise"></i>')
                ->class('bg-emerald-500 hover:bg-emerald-600 px-4 py-2 text-white rounded-lg')
                ->dispatch('active', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
