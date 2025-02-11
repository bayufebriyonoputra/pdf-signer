<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('role:admin,signer,checker')->group(function(){
    Volt::route('/dashboard', 'pages.admin.dashboard');
    Volt::route('/master-approver', 'pages.admin.master_approver');
    Volt::route('/master-user', 'pages.admin.master_user');


    Volt::route('/purchase-order', 'pages.po.purchase_order');
    Volt::route('/list-po', 'pages.po.list_po');
    Volt::route('/master-supplier', 'pages.admin.master_supplier');
    Volt::route('/po-pending', 'pages.po.po_pending');
    Volt::route('/po-excel', 'pages.po.import_excel');
});

Route::middleware('role:user,admin,signer,checker')->group(function(){
    Volt::route('/po-reminder', 'pages.po.list_po_reminder');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/approver.php';
