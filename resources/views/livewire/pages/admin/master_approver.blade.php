<?php

use App\Models\User;
use App\Enum\RoleEnum;
use App\Models\Approver;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Livewire\Tables\ApproverTable;
use App\Livewire\Forms\Admin\ApproverForm;
use function Livewire\Volt\{state, layout, with,on, form};
state([
    'users' => User::query()->get(),
    'qrCode' => null,
    'isEdit' => false,
]);
form(ApproverForm::class);

layout('layouts.admin');
$generateBarcode = function () {
    //Delete all temp file before continue
    Storage::disk('public')->delete(Storage::disk('public')->files('temp'));
    $path = 'temp/' . uniqid() . '.png';

    // Generate QR code using the builder
    $builder = Builder::create()
        ->writer(new PngWriter())
        ->data($this->form->inisial)
        ->size(200)
        ->margin(10)
        ->build();

    // Save the QR code to a file
    $builder->saveToFile(Storage::disk('public')->path($path));

    $this->qrCode = $path;
    $this->form->qrCode = $path;
};

$save = function () {
    if($this->isEdit){
        $this->form->update();
        $this->qrCode = null;
        $this->dispatch('success-notif', message: 'Data berhasil diupdate');
    }else{
        $this->form->store();
        $this->qrCode = null;
        $this->dispatch('success-notif', message:'Data berhasil ditambahakan');
    }
    $this->isEdit = false;
    $this->dispatch('pg:eventRefresh-default')->to(ApproverTable::class);
};
//listeners
on([
    'edit-approver' => function($id){
       $this->form->setApprover(Approver::find($id));
       $this->isEdit = true;
    }
]);

?>

<div>


    <div
        class="p-4 w-full max-w-none bg-white rounded-lg border border-gray-200 shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
        <div x-data ="{open: false}">
            <button x-text="open ? 'Close Form' : 'Open Form'" @click="open = !open" type="button" class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-br rounded-lg hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 me-2" :class="open ? 'from-pink-500 to-orange-400' : 'from-purple-600 to-blue-500'"></button>
            <form x-show="open" wire:submit="save" class="space-y-6" action="#">
                <h5 class="text-xl font-medium text-gray-900 dark:text-white"><span x-text="$wire.isEdit ? 'Edit' : 'Tambah' "></span> Data Approver</h5>
                <!-- Body Form -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Form User Id -->
                    <div>
                        <label for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Users</label>
                        <select wire:model="form.userId" id="countries"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected>Choose a user</option>
                            @foreach ($users as $user)
                                <option wire:key="{{ $user->id }}" value="{{$user->id}}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('form.userId')
                            <p class="text-sm text-red-600">{{$message}}</p>
                        @enderror
                    </div>
                    <!-- Inisial -->
                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Inisial</label>
                        <input wire:model="form.inisial" wire:change="generateBarcode" type="text" name="inisial"
                            id="inisial"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ex: DYU" :required="!$wire.isEdit" />
                            <p x-show="$wire.isEdit" class="text-sm text-slate-300">Note : Jika Barcode tidak berubah anda tidak perluh merubah field ini.</p>
                        @error('form.inisial')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tracker -->
                    <div>
                        <label for="signer_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Signer
                            Name</label>
                        <input wire:model="form.signerName" type="text" name="signer_track" id="signerTrack"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="EX : DIKKY YUSTIAN" />
                        @error('form.signerName')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Form User Level -->
                    <div>
                        <label for="level"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Level</label>
                        <select wire:model="form.userLevel" id="level"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="{{ \App\Enum\RoleEnum::CHECKER->value }}">
                                {{ \App\Enum\RoleEnum::CHECKER->label() }}</option>
                            <option value="{{ \App\Enum\RoleEnum::SIGNER->value }}">
                                {{ \App\Enum\RoleEnum::SIGNER->label() }}</option>
                        </select>
                    </div>
                </div>

                <!-- Image For Barcode -->
                @if ($qrCode)
                    <div class="flex justify-center">
                        <figure class="max-w-lg">
                            <img class="max-w-full h-auto rounded-lg" src="{{ asset("storage/$qrCode") }}"
                                alt="image description">
                            <figcaption class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">Barcode of :
                                {{ $this->form->inisial }}
                            </figcaption>
                        </figure>
                    </div>
                @endif


                <!-- Submit Button -->
                <button type="submit"
                    class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                        class="bi bi-floppy"></i>&nbsp;Save</button>
            </form>
        </div>


        <!-- Table Start -->
        <div class="mt-4">
            <livewire:tables.approver-table />
        </div>

    </div>
</div>
