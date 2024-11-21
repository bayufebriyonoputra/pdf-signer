<?php

namespace App\Livewire\Tables;

use App\Models\HeaderPo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

final class PoReminderTable extends PowerGridComponent
{
    use WithExport;

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        HeaderPo::query()->find($id)->update([
            $field => e($value),
        ]);
    }

    public function setUp(): array
    {
        $this->showCheckBox(attribute:'is_remindered');

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
        $today = Carbon::today();
        $untilDate = $today->copy()->addDay(4);


        return HeaderPo::query()->where('due_date', '>=',$today)->where('due_date', '<=', $untilDate);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('no_po')
            ->add('status')
            ->add('status_label', fn($po) => $po->status->badge())
            ->add('due_date')
            ->add('jenis_transaksi_label' , fn($po) => e($po->jenis_transaksi->label()))
            ->add('is_remindered')
            ->add('created_at')
            ->add('due_date_formated', fn($po) => Carbon::parse($po->due_date)->format('d M y'));
    }

    public function columns(): array
    {
        return [
            Column::make('No PO', 'no_po')
                ->searchable()
                ->sortable(),
            Column::make('Status', 'status_label'),
            Column::make('Due Date', 'due_date_formated')
                ->sortable()
                ->searchable(),
            Column::make('Jenis Transaksi', 'jenis_transaksi_label'),
          Column::add()
            ->title('Sudah Reminder')
            ->field('is_remindered')
            ->toggleable(hasPermission:true, trueLabel:'Sudah', falseLabel:'Belum'),

            //Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::boolean('is_remindered', 'is_remindered')
                ->label('Sudah Reminder', 'Belum Reminder'),

        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions(HeaderPo $row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: ' . $row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

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
