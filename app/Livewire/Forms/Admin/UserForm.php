<?php

namespace App\Livewire\Forms\Admin;

use Livewire\Form;
use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;

class UserForm extends Form
{
    public ?User $user;

    #[Validate('required')]
    public $name = '';
    #[Validate('required|email|unique:users,email')]
    public $email = '';
    #[Validate('required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/', message:['regex' => 'Password contains at least one uppercase letter, lowercase letter, and a number'])]
    public $password = '';
    #[Validate('required')]
    public $role = '';



    public function store(){
        $this->validate();
        $this->password = bcrypt($this->password);
        User::create($this->only([
            'name', 'email', 'password', 'role'
        ]));
        $this->reset();
    }

    public function setUser(User $user){
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
    }

    public function update(){
        $this->validate([
            'email' =>['required',  Rule::unique('users', 'email')->ignore($this->user->id)],
            'password' => ['nullable','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/'],
            'name' => 'required|min:3',
            'role' => 'required'
        ],[
            'password.regex' => 'Password contains at least one uppercase letter, lowercase letter, and a number'
        ]);

        $data = $this->only(['email', 'name', 'role']);
        if($this->password){
            $data['password'] = bcrypt($this->password);
        }

        $this->user->update($data);
        $this->reset();
    }


}
