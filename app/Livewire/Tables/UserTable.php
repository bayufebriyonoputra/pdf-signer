<?php

namespace App\Livewire\Tables;

use App\Models\User;
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

final class UserTable extends PowerGridComponent
{
    use WithExport, LivewireAlert;

    public $userId;
     protected $listeners = [
        'deleteUser'
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
        return User::query();
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
            ->add('role_user', fn($user) => e($user->role->label()))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Role', 'role_user')
                ->sortable()
                ->searchable(),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
       $this->dispatch('edit-user', id: $rowId);
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
            ->slot('<i class="bi bi-pencil-square"></i>')
            ->class('bg-amber-400 hover:bg-amber-600 text-white font-bold py-2 px-2 rounded')
            ->dispatch('edit', ['rowId' => $row->id]),
            Button::add('delete')
            ->slot('<i class="bi bi-trash-fill"></i>')
            ->class('bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded')
            ->dispatch('confirmDelete', ['id' => $row->id]),
        ];
    }

    #[On('confirmDelete')]
    public function confirmDelete($id){
        $this->userId = $id;
        $this->alert('warning', 'Are you sure , you want to delete this data ? ', [
            'icon' => 'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText' => 'Cancel',
            'timer' => null,
            'position' => 'center',
            'onConfirmed' => 'deleteUser'
        ]);
    }

    public function deleteUser(){
        User::destroy($this->userId);
        $this->dispatch('pg:eventRefresh-default')->self();
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
