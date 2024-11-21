<?php

namespace App\Livewire\Tables;

use App\Models\Approver;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
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

final class ApproverTable extends PowerGridComponent
{
    use WithExport, LivewireAlert;
    public $approverId;
    protected $listeners = [
        'deleteApprover'
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
        return Approver::query()->with('user');
    }

    public function relationSearch(): array
    {
        return [
            'user' =>[
                'name',
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('barcode_image', function($approver){
                return '<img src="' . asset("storage/$approver->barcode_path") . '" width="80px"/>';
            })
            ->add('user_name', fn($approver) => e($approver->user->name))
            ->add('user_role', fn($approver) => e($approver->user->role->label()))
            ->add('signer_name')
            ->add('created_at');
    }


    public function columns(): array
    {
        return [
            Column::make('Name', 'user_name')->searchable(),
            Column::make('Role', 'user_role')->searchable(),
           Column::make('Barcode', 'barcode_image')->searchable(),
           Column::make('Signer Name', 'signer_name')->searchable(),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }



    public function actions(Approver $row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bi bi-pencil-square"></i>')
                ->class('bg-amber-400 hover:bg-amber-600 text-white font-bold py-2 px-2 rounded')
                ->dispatch('edit-approver', ['id' => $row->id]),
            Button::add('delete')
                ->slot('<i class="bi bi-trash-fill"></i>')
                ->class('bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-2 rounded')
                ->dispatch('confirmDelete', ['id' => $row->id]),
        ];
    }

    #[On('confirmDelete')]
    public function confirmDelete($id){
        $this->approverId = $id;
        $this->alert('warning', 'Are you sure , you want to delete this data ? ', [
            'icon' => 'warning',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Delete',
            'cancelButtonText' => 'Cancel',
            'timer' => null,
            'position' => 'center',
            'onConfirmed' => 'deleteApprover'
        ]);
    }

    public function deleteApprover(){
        $approver = Approver::find($this->approverId);
        Storage::disk('public')->delete($approver->barcode_path);
        $approver->delete();
        $this->dispatch('success-notif', message:'Data berhasil dihapus');
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
