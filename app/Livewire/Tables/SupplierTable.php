<?php

namespace App\Livewire\Tables;

use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

final class SupplierTable extends PowerGridComponent
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

    public function header(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Bulk Delete (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md')
                ->dispatch('bulkDelete', [])
        ];
    }

    public function datasource(): Builder
    {
        return Supplier::query()->orderByDesc('created_at');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),
            Column::make('Email', 'email')
                ->searchable()
                ->sortable(),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }


    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Supplier $row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bi bi-pencil-square"></i>')
                ->class('bg-amber-400 hover:bg-amber-600 text-white py-2 px-4 rounded-md')
                ->dispatch('setSupplier', ['id' => $row->id]),
            Button::add('delete')
                ->slot('<i class="bi bi-trash"></i>')
                ->class('bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md')
                ->dispatch('delete', ['id' => $row->id])
        ];
    }

    #[On('bulkDelete')]
    public function bulkDelete(): void
    {
        if ($this->checkboxValues) {
            Supplier::destroy($this->checkboxValues);
            $this->js('window.pgBulkActions.clearAll()');
        }
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
