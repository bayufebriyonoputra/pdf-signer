<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('role:checker,signer')->group(function(){
    Volt::route('/need-approve', 'pages.po.list_po_approver');
});
