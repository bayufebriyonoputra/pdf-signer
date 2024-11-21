<?php

use App\Models\Supplier;
use Illuminate\Validation\Rule;
use App\Livewire\Tables\SupplierTable;
use function Livewire\Volt\{state, layout, on};

state([
    'emails' => [],
    'email',
    'name' => '',
    'supplierId' => '',
    'isEdit' => false,
]);

layout('layouts.admin');

$addEmail = function () {
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
};

$removeEmail = function ($index) {
    unset($this->emails[$index]);
    $this->emails = array_values($this->emails);
};

$save = function () {
    if ($this->isEdit) {
        $this->validate([
            'name' => ['required', Rule::unique('suppliers', 'name')->ignore($this->supplierId)],
            'emails' => 'required',
        ]);

        Supplier::find($this->supplierId)->update([
            'name' => $this->name,
            'email' => implode('|', $this->emails),
        ]);
        $this->dispatch('success-notif', message: 'Data berhasil diupdate');
    } else {
        $this->validate([
            'name' => 'required|unique:suppliers,name',
            'emails' => 'required',
        ]);

        Supplier::create([
            'name' => $this->name,
            'email' => implode('|', $this->emails),
        ]);
        $this->dispatch('success-notif', message: 'Berhasil Menambahkan Supplier');
    }
    $this->isEdit = false;
    $this->name = '';
    $this->emails = [];
    $this->supplierId = '';
    $this->dispatch('pg:eventRefresh-default')->to(SupplierTable::class);
};

on([
    'setSupplier' => function ($id) {
        $this->isEdit = true;
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $supplier->id;
        $this->name = $supplier->name;
        $this->emails = explode('|', $supplier->email);
    },
    'delete' => function($id){
        Supplier::destroy($id);
        $this->dispatch('success-notif', message:'Berhasil menghapus data');
        $this->dispatch('pg:eventRefresh-default')->to(SupplierTable::class);
    }
]);

?>

<div>
    <div
        class="p-4 w-full max-w-none bg-white rounded-lg border border-gray-200 shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
        <div x-data ="{open: true}">
            <button x-text="open ? 'Close Form' : 'Open Form'" @click="open = !open" type="button"
                class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-br rounded-lg hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 me-2"
                :class="open ? 'from-pink-500 to-orange-400' : 'from-purple-600 to-blue-500'"></button>
            <form x-show="open" wire:submit="save" class="space-y-6" action="#">
                <h5 class="text-xl font-medium text-gray-900 dark:text-white"><span
                        x-text="$wire.isEdit ? 'Edit' : 'Tambah' "></span> Data Supplier</h5>
                <!-- Body Form -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                            Supplier</label>
                        <input wire:model="name" type="text" name="name" id="name"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ex: PT BWT PERKASA" />
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input @keydown.tab.prevent="" wire:keydown.tab="addEmail" wire:model="email" type="email"
                            name="email" id="email"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ex: PT BWT PERKASA" />
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- List Email yang sudah ditambahkan -->
                        <div class="flex flex-wrap gap-3 mt-3">
                            @foreach ($emails as $index => $email)
                                <span class="relative px-4 py-2 text-sky-600 bg-sky-200 rounded-md">{{ $email }}
                                    <i role="button" wire:click="removeEmail({{ $index }})"
                                        class="absolute top-0 right-0.5 text-xs text-red-500 bi bi-x-circle"></i>
                                </span>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                        class="bi bi-floppy"></i>&nbsp;Save</button>
            </form>
        </div>


        <!-- Table Start -->
        <div class="mt-4">
            <livewire:tables.supplier-table />
        </div>

    </div>
</div>
