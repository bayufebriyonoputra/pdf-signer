<?php

use App\Livewire\Invoice\MasterInvoice;
use App\Livewire\Invoice\MasterVendor;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('role:admin')->prefix('invoice')->group(function(){
    Route::get('/master-invoice', MasterInvoice::class);
    Route::get('/master-vendor', MasterVendor::class);
});
