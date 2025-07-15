<?php

use App\Models\Setting;
use Livewire\Volt\Volt;
use App\Models\MasterInvoice;
use Illuminate\Support\Facades\Route;

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

Route::middleware('role:admin,signer,checker')->group(function () {
    Volt::route('/dashboard', 'pages.admin.dashboard');
    Volt::route('/master-approver', 'pages.admin.master_approver');
    Volt::route('/master-user', 'pages.admin.master_user');


    Volt::route('/purchase-order', 'pages.po.purchase_order');
    Volt::route('/list-po', 'pages.po.list_po');
    Volt::route('/master-supplier', 'pages.admin.master_supplier');
    Volt::route('/po-pending', 'pages.po.po_pending');
    Volt::route('/po-excel', 'pages.po.import_excel');
});

Route::middleware('role:user,admin,signer,checker')->group(function () {
    Volt::route('/po-reminder', 'pages.po.list_po_reminder');
});

Route::get('/tes/{text}', function ($text) {
    return bcrypt($text);
});

Route::get('/tes-pdf', function () {
    $invoices = MasterInvoice::whereIn('id', [1])
        ->with('vendor')
        ->get();

    $countVendors = $invoices->pluck('vendor.id')->unique();

    // jika hanya ada satu jenis vendor maka kirim

    $invoiceCounter = Setting::where('name', 'invoice_counter')->first();

    $noRecords = '';

    if (now()->day === 1 && $invoiceCounter->value != 1) {
        // $noUrut = 001;
        $bulan = toRoman(now()->month);
        $tahun = now()->isoFormat("YY");
        $noVP = "REC-001/PUR-SAI/$bulan/$tahun";

        $noRecords = $noVP;
        $invoiceCounter->value = "1";
        $invoiceCounter->save();
    } else {
        $count = intval($invoiceCounter->value);
        $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);
        $bulan = toRoman(now()->month);
        $tahun = now()->isoFormat("YY");
        $noRecords = "REC-$noUrut/PUR-SAI/$bulan/$tahun";

        $invoiceCounter->value = strval($count + 1);
        $invoiceCounter->save();
    }

    return view('pdf-template.invoice-tt', [
        'data' => $invoices,
        'noRecord' => $noRecords
    ]);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/approver.php';
require __DIR__ . '/invoice.php';
