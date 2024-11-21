<?php

use App\Models\User;
use App\Livewire\Tables\UserTable;
use App\Livewire\Forms\Admin\UserForm;
use function Livewire\Volt\{layout, form,state, on};

    form(UserForm::class);
    layout('layouts.admin');
    state([
        'isEdit' => false
    ]);

    $save = function(){
        if($this->isEdit){
            $this->form->update();
            $this->dispatch('success-notif', message: 'Data user berhasil diedit');
            $this->dispatch('pg:eventRefresh-default')->to(UserTable::class);
        }else{
            $this->form->store();
            $this->dispatch('success-notif', message:'Data user berhasil ditambahkan');
            $this->dispatch('pg:eventRefresh-default')->to(UserTable::class);
        }
        $this->isEdit = false;
    };

    on([
            'edit-user' => function ($id) {
            $this->form->setUser(User::find($id));
            $this->isEdit = true;
        },
    ]);
?>

<div>
    <div
        class="p-4 w-full max-w-none bg-white rounded-lg border border-gray-200 shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
       <div x-data="{open: false}">
        <button x-text="open ? 'Close Form' : 'Open Form'" @click="open = !open" type="button" class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-br rounded-lg hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-pink-200 dark:focus:ring-pink-800 me-2" :class="open ? 'from-pink-500 to-orange-400' : 'from-purple-600 to-blue-500'"></button>

        <form x-show="open" wire:submit="save" class="space-y-6" action="#">
            <h5 class="text-xl font-medium text-gray-900 dark:text-white"><span x-text="$wire.isEdit ? 'Edit' : 'Tambah'"></span> Data User</h5>
            <!-- Body Form -->
            <div class="grid grid-cols-2 gap-3">

                  <!-- Name -->
                  <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input wire:model="form.name" type="text" name="name" id="signerTrack"
                        class="@error('form.name') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="EX : DIKKY YUSTIAN" />
                    @error('form.name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                  <!-- Email -->
                  <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input wire:model="form.email" type="email" name="email" id="email"
                        class="@error('form.email') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="EX : dikky@mail.com" />
                    @error('form.email')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                  <!-- Password -->
                  <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                    <input wire:model="form.password" type="password" name="password" id="password"
                        class=" block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white @error('form.password') form-error @enderror"
                        placeholder="*****" />
                        <p x-show="$wire.isEdit" class="text-sm text-slate-400">Note: Kosongkan jika tidak ingin mengubah password</p>
                    @error('form.password')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>



                <!-- Form User Level -->
                <div>
                    <label for="level"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                    <select wire:model="form.role" id="level"
                        class="@error('form.role') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Choose One</option>
                        <option value="{{ \App\Enum\RoleEnum::USER->value }}">
                            {{ \App\Enum\RoleEnum::USER->label() }}</option>
                        <option value="{{ \App\Enum\RoleEnum::ADMIN->value }}">
                            {{ \App\Enum\RoleEnum::ADMIN->label() }}</option>
                    </select>
                    @error('form.role')
                        <p class="text-sm text-red-600">{{$message}}</p>
                    @enderror
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
            <livewire:tables.user-table />
        </div>

    </div>
</div>
