<?php

namespace App\Livewire\Invoice;

use App\Models\Vendor;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.invoice')]
class MasterVendor extends Component
{


    use WithPagination;


    public $vendorId;

    public $isEdit = false;
    public $name = "";
    public $top;
    public $email = '';
    public $search = "";
    public $emails = [];

    public function render()
    {
        return view('livewire.invoice.master-vendor', [
            'vendors' => Vendor::where('name', 'like', "%$this->search%")
                ->orWhere('top', 'like', "%$this->search%")
                ->latest()
                ->paginate(10)
        ]);
    }



    public function save()
    {
        $this->validate();

        if (!$this->isEdit) {
            Vendor::create([
                'name' => $this->name,
                'top' => $this->top,
                'email' => $this->email
            ]);
            $this->resetField();
            $this->dispatch('success-notif', message: 'Berhasil menambahkan data');
        } else {
            Vendor::find($this->vendorId)
                ->update([
                    'name' => $this->name,
                    'top' => $this->top,
                    'email' => implode('|', $this->emails)
                ]);
            $this->resetField();
            $this->dispatch('success-notif', message: 'Berhasil mengedit data');
        }
    }

    public function removeEmail($index)
    {
        unset($this->emails[$index]);
        $this->emails = array_values($this->emails);
    }

    public function addEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);
        // Cek apakah email sudah ada di dalam list
        if (in_array($this->email, $this->emails)) {
            // Tambahkan pesan error langsung ke field 'email'
            $this->addError('email', 'Email sudah ada di dalam daftar.');
            return;
        }

        $this->emails[] = $this->email;
        $this->email = '';
    }

    public function setEdit($vendorId)
    {
        $this->isEdit  = true;
        $vendor = Vendor::find($vendorId);
        $this->vendorId = $vendor->id;
        $this->name = $vendor->name;
        $this->top = $vendor->top;
        $this->emails = explode('|', $vendor->email);
    }

    public function destroy($id)
    {
        Vendor::destroy($id);
        $this->dispatch('success-notif', message: 'Berhasil hapus data');
    }


    public function resetField()
    {
        $this->isEdit = false;
        $this->name = "";
        $this->top = 0;
        $this->email = '';
        $this->emails = [];
    }


    protected function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
            ],
            'top' => [
                'required',
                'numeric'
            ],
        ];

        if ($this->isEdit && $this->vendorId) {
            $rules['name'][] = Rule::unique('vendors', 'name')->ignore($this->vendorId);
        } else {
            $rules['name'][] = 'unique:vendors,name';
        }
        return $rules;
    }

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'name.unique' => 'Nama sudah digunakan',
        'email.unique' => 'Email sudah digunakan'
    ];
}
