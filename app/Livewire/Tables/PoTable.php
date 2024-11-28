<?php

namespace App\Livewire\Tables;

use App\Models\HeaderPo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class PoTable extends PowerGridComponent
{
    use WithExport, LivewireAlert;

    public $poId = null;
    protected $listeners = [
        'deletePo'
    ];

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
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_po')
            ->add('supplier_name', fn($po) => e($po->supplier->name))
            ->add('status_label', fn($po) => e($po->status->label()))
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
            Column::make('Status', 'status_label')
                ->searchable()
                ->sortable(),
            Column::make('Approver Pertama', 'checker')
                ->searchable(),
            Column::make('Approver Kedua', 'signer')
                ->searchable(),
            Column::action('Action')

        ];
    }

    public function filters(): array
    {
        return [];
    }

    // #[\Livewire\Attributes\On('edit')]
    // public function edit($rowId): void
    // {
    //     $this->js('alert('.$rowId.')');
    // }

    public function actions(HeaderPo $row): array
    {
        return [

            Button::add('delete')
                ->slot('<i class="bi bi-trash-fill"></i>')
                ->class('bg-red-500 hover:bg-red-600 py-2 px-4 text-white rounded-md')
                ->dispatch('delete', ['id'  => $row->id])
        ];
    }

    #[On('delete')]
    public function condirmDelete($id)
    {
        $this->poId = $id;
        $this->alert('warning', 'Are you sure , you want to delete this data ? ', [
            'icon' => 'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText' => 'Cancel',
            'timer' => null,
            'position' => 'center',
            'onConfirmed' => 'deletePo'
        ]);
    }

    public function deletePo()
    {

        HeaderPo::destroy($this->poId);
        $this->dispatch('success-notif', message: 'PO berhasil dihapus');
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
