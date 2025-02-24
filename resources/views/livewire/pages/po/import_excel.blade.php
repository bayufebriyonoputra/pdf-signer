<?php

use App\Enum\StatusEnum;
use App\Models\Approver;
use App\Models\HeaderPo;
use App\Models\Supplier;
use App\Models\EmailFailed;
use Illuminate\Support\Facades\DB;
use function Livewire\Volt\{state, layout, usesFileUploads};
usesFileUploads();
state(['file', 'files' => []]);

layout('layouts.admin');

$save = function ($version) {
    $this->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv',
    ]);

    $path = $this->file->storeAs('temp', 'uploaded_file.xlsx', 'local');
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/{$path}"));

    $data = $spreadsheet->getActiveSheet()->removeRow(1, 3)->toArray();

    DB::beginTransaction();
    try {
        foreach ($data as $row) {
            //cari vendor dulu
            if ($version == 1) {
                $vendor = Supplier::where('name', $row[2])->first();
                if (!$vendor) {
                    throw new \Exception('Gagal Nama Vendor ' . $row[2] . ' Tidak ditemukan');
                }
                $data = [
                    'supplier_id' => $vendor->id,
                    'no_po' => $row[0],
                    'status' => StatusEnum::NEW->value,
                    'due_date' => $row[1],
                    'jenis_transaksi' => $row[3],
                ];
                HeaderPo::create($data);
            } else {
                //cari vendor
                $vendor = Supplier::where('name', $row[2])->first();
                if (!$vendor) {
                    throw new \Exception('Gagal Nama Vendor ' . $row[2] . ' Tidak ditemukan');
                }
                //cari approver 1
                $approver1 = Approver::where('signer_name', $row[4])->first();
                if (!$approver1) {
                    throw new \Exception('Gagal nama approver ' . $row[4] . ' Tidak ditemukan');
                }
                //cari approver 2
                $approver2 = Approver::where('signer_name', $row[5])->first();
                if (!$approver2) {
                    throw new \Exception('Gagal nama approver ' . $row[5] . ' Tidak ditemukan');
                }

                $data = [
                    'supplier_id' => $vendor->id,
                    'no_po' => str_replace('/', '-', $row[0]),
                    'status' => StatusEnum::NEW->value,
                    'due_date' => $row[1],
                    'jenis_transaksi' => $row[3],
                    'approver_1' => $approver1->user_id,
                    'approver_2' => $approver2->user_id,
                ];
                HeaderPo::create($data);
            }
        }
        DB::commit();
        $this->dispatch('success-notif', message: 'File berhasil diimport');
    } catch (\ValueError $e) {
        DB::rollback();
        $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
    } catch (\Exception $e) {
        DB::rollback();
        $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
    } catch (\Throwable $e) {
        DB::rollback();
        $this->dispatch('error-notif', message: 'Terjadi kesalahan ' . $e->getMessage());
    }
};

$storeFiles = function () {
    $this->validate([
        'files.*' => 'extensions:pdf',
    ]);

    $poNotFound = [];

    foreach ($this->files as $f) {
        $fileName = pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME);
        $po = HeaderPo::where('no_po', $fileName)->first();
        if (!$po) {
            $poNotFound[] = $fileName;
            continue;
        }
        $response = Http::asMultipart()->post('http://127.0.0.1:3000/extract-coordinates', [
            'file' => fopen($f->getRealPath(), 'r'),
            'approver' => $po->approverKedua->name,
        ]);

        if ($response->successful()) {
            $po->x_coor = $response['coordinates']['x'];
            $po->y_coor = $response['coordinates']['y'];
            $po->save();
        } else {
            $poNotFound[] = $fileName;
        }
    }

    if ($poNotFound) {
        EmailFailed::create([
            'no_po' => implode(', ', $poNotFound),
            'message' => 'File pdf berikut tidak ditemukan PO Nya',
        ]);
    }

    $this->dispatch('success-notif', message: 'Proses upload telah selesai');
};

?>

<div>

    <div class="w-full px-6 py-12 rounded-md shadow-lg ">
        <form wire:submit="save(1)" class="mt-12 space-y-4 " action="#">
            <a href="{{ asset('excel/import-po.xlsx') }}"
                class="px-4 py-2 mb-5 text-white rounded-md bg-emerald-500 hover:bg-emerald-600">Download
                Excel</a>
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Import Purchase Order</h5>
            <!-- Body Form -->
            <div class="grid grid-cols-2 gap-3">
                <!-- File -->
                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                        file</label>
                    <input wire:model='file'
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                        id="file_input" type="file">
                </div>
            </div>

            <!-- Submit  -->
            <button wire:loading.attr='disabled' wire:loading.class='bg-blue-200 cursor-not-allowed' wire:target="save"
                type="submit"
                class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                    class="bi bi-floppy"></i>&nbsp;Save</button>
        </form>
    </div>

    <!-- Versi 2 -->

    <div class="w-full px-6 py-12 rounded-md shadow-lg ">
        <form wire:submit="save(2)" class="mt-12 space-y-4 " action="#">
            <a href="{{ asset('excel/import-pov2.xlsx') }}"
                class="px-4 py-2 mb-5 text-white rounded-md bg-emerald-500 hover:bg-emerald-600">Download
                Excel</a>
            <h5 class="text-xl font-medium text-gray-900 dark:text-white">Import Purchase Order V2</h5>
            <!-- Body Form -->
            <div class="grid grid-cols-2 gap-3">
                <!-- File -->
                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                        file Excel</label>
                    <input wire:model='file'
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                        id="file_input" type="file">
                </div>
            </div>

            <!-- Submit  -->
            <button wire:loading.attr='disabled' wire:loading.class='bg-blue-200 cursor-not-allowed' wire:target="save"
                type="submit"
                class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                    class="bi bi-floppy"></i>&nbsp;Save</button>
        </form>


        <!-- File Pdf -->
        <form wire:submit="storeFiles" class="mt-12 space-y-4 " action="#">
            <!-- Body Form -->
            <div class="grid grid-cols-2 gap-3">
                <!-- File -->
                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                        file PO (pdf)</label>
                    <input wire:model='files'
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                        id="file_input" type="file" multiple>
                </div>
            </div>

            <p wire:loading.block wire:target='files' class="text-sm font-semibold text-black">Loading...</p>

            <!-- Submit  -->
            <button wire:loading.attr='disabled' wire:loading.class='bg-blue-200 cursor-not-allowed' wire:target="save"
                type="submit"
                class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                    class="bi bi-floppy"></i>
                    <span wire:loading.remove wire:target='storeFiles' class="ms-2">Save</span>
                    <span wire:loading wire:target='storeFiles' class="ms-2 hover:cursor-not-allowed">Loading...</span>
                </button>
        </form>
    </div>

</div>
