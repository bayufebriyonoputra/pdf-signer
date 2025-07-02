<?php

namespace App\Livewire\Tables;

use App\Enum\StatusEnum;
use App\Models\HeaderPo;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
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
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;

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
        return HeaderPo::query()
            ->selectRaw("
            header_pos.*,

            CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', 2), ' ', -1) AS UNSIGNED) AS no_po_number,

            FIELD(SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', -2), ' ', 1),
                'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII') AS no_po_month,

            CAST(SUBSTRING_INDEX(REPLACE(REPLACE(no_po, '-', ' '), '/', ' '), ' ', -1) AS UNSIGNED) AS no_po_year
        ")
            ->with(['approverPertama', 'approverKedua', 'supplier'])
            ->orderByRaw('no_po_year DESC, no_po_month DESC, no_po_number DESC');
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
            ->add('signer', fn($po) => e($po->approverKedua->name ?? '-'))
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
        return [
            Filter::boolean('signer', 'approver_2')
                ->label('Finished', 'Not Yet')
                ->builder(function (Builder $query, string $value) {
                    if ($value == 'true') return $query->WhereNotNull('approver_2');
                    return $query->WhereNull('approver_2');
                }),
        ];
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
                ->dispatch('delete', ['id'  => $row->id]),
            Button::add('fill')
                ->slot('<i class="bi bi-textarea-resize"></i>')
                ->class('bg-emerald-500 hover:bg-emerald-600 py-2 px-4 text-white rounded-md')
                ->openModal('modals.po.fill-detail', ['headerId' => $row->id]),
            Button::add('edit')
                ->slot('<i class="bi bi-pencil-square"></i>')
                ->class('bg-amber-500 hover:bg-amber-600 py-2 px-4 text-white rounded-md')
                ->openModal('modals.po.edit-po', ['headerId' => $row->id]),
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


    public function actionRules($row): array
    {
       return [

            Rule::button('fill')
                ->when(fn($row) => $row->approver_2 != null && $row->status != StatusEnum::NEW)
                ->hide(),
        ];
    }

}
