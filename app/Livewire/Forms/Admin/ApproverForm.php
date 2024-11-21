<?php

namespace App\Livewire\Forms\Admin;

use Livewire\Form;
use App\Models\Approver;
use App\Models\User;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApproverForm extends Form
{
    public ?Approver $approver;

    public $userId = '';
    public $inisial = '';
    public $userLevel = 'checker';
    public $signerName = '';
    //variabel for qr code path
    public $qrCode = '';



    public function store()
    {
        $this->validate([
            'userId' => 'required',
            'signerName' => 'required|unique:approvers,signer_name',
            'inisial' => 'required'
        ]);

        $destinationPath = 'img/barcode/' . $this->inisial . '.png';
        // Ensure the destination directory exists
        if (!Storage::disk('public')->exists('img/barcode')) {
            Storage::disk('public')->makeDirectory('img/barcode');
        }

        // Move the file to the new location
        Storage::disk('public')->move($this->qrCode, $destinationPath);

        // Delete the temporary file if it still exists
        if (Storage::disk('public')->exists($this->qrCode)) {
            Storage::disk('public')->delete($this->qrCode);
        }

        //Update user role
        DB::table('users')
            ->where('id', $this->userId)
            ->update(['role' => $this->userLevel]);

        // Create approver
        Approver::create([
            'user_id' => $this->userId,
            'barcode_path' => $destinationPath,
            'signer_name' => $this->signerName,
        ]);
        //refresh the datatable

        $this->reset();
    }

    public function setApprover(Approver $approver){
        $this->approver = $approver;
        $this->userId = $approver->user_id;
        $this->userLevel = $approver->user->role->value;
        $this->signerName = $approver->signer_name;
    }

    public function update(){
        $this->validate([
            'userId' => 'required',
            'signerName' => ['required', Rule::unique('approvers', 'signer_name')->ignore($this->approver->id)],
        ]);

        //penampung data
        $data = [
            'user_id' => $this->userId,
            'signer_name' => $this->signerName,
        ];

        //delete barcode if inisial not null
        if($this->inisial){
            Storage::disk('public')->delete($this->approver->barcode_path);
            //upload new barcode
            $destinationPath = 'img/barcode/' . $this->inisial . '.png';
            // Move the file to the new location
            Storage::disk('public')->move($this->qrCode, $destinationPath);
            //add bacodePath to data
            $data['barcode_path'] = $destinationPath;
        }

        $this->approver->update($data);
        User::find($this->userId)->update(['role' => $this->userLevel]);
        $this->reset();

    }
}
