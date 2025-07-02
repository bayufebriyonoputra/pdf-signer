<?php

use App\Models\User;
use App\Enum\RoleEnum;
use App\Models\Supplier;
use App\Livewire\Tables\PoTable;
use App\Livewire\Forms\Po\PurchaseOrderForm;
use function Livewire\Volt\{state, layout, form, usesFileUploads, on};

usesFileUploads();
form(PurchaseOrderForm::class);
state([
    'approverPertama' => User::where('role', RoleEnum::CHECKER)->get(),
    'approverKedua' => User::where('role', RoleEnum::SIGNER)->get(),
    'suppliers' => Supplier::where('is_active', true)->get(),
]);

$save = function () {
    $this->form->store();
    $this->dispatch('success-notif', message: 'Berhasil Menambahkan Purchase Order');
    $this->dispatch('removeFile');
    $this->dispatch('clearChoices');
    $this->dispatch('pg:eventRefresh-default')->to(PoTable::class);
};

on([
    'saveCoordinates' => function ($coordinates) {
        if (!$coordinates) {
            $this->dispatch('error-notif', message: 'Lokasi untuk sign tidak ditemukan coba pilih signer yang sesuai');
            $this->dispatch('removeFile');
            return;
        }
        $this->form->xCoor = $coordinates['x'];
        $this->form->yCoor = $coordinates['y'];
    },
]);

layout('layouts.admin');

?>

<div>
    <div
        class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow max-w-none sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
        <form wire:submit="save" class="space-y-6" action="#">
            <h5 class="mb-5 text-xl font-medium text-gray-900 dark:text-white">Tambahkan Purchase Order</h5>
            <a wire:navigate href="/po-excel" class="px-4 py-2 mt-4 text-white bg-green-400 rounded-md hover:bg-green-600">Import Data</a href="/po-excel">
           
            <!-- Body Form -->
            <div class="grid grid-cols-2 gap-3">

                <!-- No PO -->
                <div>
                    <label for="noPo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NO
                        PO</label>
                    <input wire:model="form.noPo" type="text" name="name" id="signerTrack"
                        class="@error('form.noPo') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Isikan NO Purchase Order" />
                    @error('form.noPo')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="dueDate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Due
                        Date</label>
                    <input wire:model="form.dueDate" type="date" name="name" id="dueDate"
                        class="@error('form.dueDate') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:placeholder-gray-400 dark:text-white" />
                    @error('form.dueDate')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier -->
                <div wire:ignore class="col-span-full">
                    <label for="approverPertama"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                    <select id="supplier"
                        class="@error('form.supplierId') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($suppliers as $s)
                            <option wire:key="{{ $s->id }}" value="{{ $s->id }}">{{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.supplierId')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Approver 1 -->
                <div>
                    <label for="approverPertama"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approver
                        Pertama</label>
                    <select wire:model="form.approverPertama" id="aproverPertama"
                        class="@error('form.approverPertama') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Choose One</option>
                        @foreach ($approverPertama as $ap)
                            <option value="{{ $ap->id }}" wire:key="{{ $ap->id }}">{{ $ap->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.approverPertama')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Approver 2 -->
                <div>
                    <label for="approverKedua"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approver Kedua</label>
                    <select id="approver2" wire:model="form.approverKedua" id="aproverKedua"
                        class="@error('form.approverKedua') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Choose One</option>
                        @foreach ($approverKedua as $ak)
                            <option value="{{ $ak->id }}" wire:key="{{ $ak->id }}">{{ $ak->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('form.approverKedua')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Upload File -->
                <div class="col-span-full">
                    <div wire:ignore>
                        <label for="file"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload
                            File PO Anda</label>
                        <input wire:model="form.file" type="file" id="fileInput" />
                    </div>
                    @error('form.file')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Radio Jenis Transaksi -->

                <div class="col-span-full">
                    <p class="mb-2 text-sm font-semibold text-gray-800">Jenis Transaksi</p>
                    <div class="flex space-x-3">
                        <div
                            class="flex items-center w-full max-w-xs border border-gray-200 rounded ps-4 dark:border-gray-700">
                            <input wire:model="form.jenisTransaksi" id="bordered-radio-1" type="radio"
                                value="{{ \App\Enum\JenisTransaksiEnum::BARANG->value }}" name="jenisTransaksi"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="bordered-radio-1"
                                class="w-full py-4 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">{{ \App\Enum\JenisTransaksiEnum::BARANG->label() }}</label>
                        </div>
                        <div
                            class="flex items-center w-full max-w-xs border border-gray-200 rounded ps-4 dark:border-gray-700">
                            <input wire:model="form.jenisTransaksi" id="bordered-radio-2" type="radio"
                                value="{{ \App\Enum\JenisTransaksiEnum::JASA->value }}" name="jenisTransaksi"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="bordered-radio-2"
                                class="w-full py-4 text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">{{ \App\Enum\JenisTransaksiEnum::JASA->label() }}</label>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Revisi -->
                <div class="flex items-center mb-4">
                    <input wire:model="form.revised" id="revised" type="checkbox"  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="revised" class="text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">Revised?</label>
                </div>
            </div>



            <!-- Submit  -->
            <button type="submit"
                class="px-5 py-2.5 mb-2 text-sm font-medium text-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 rounded-lg shadow-lg hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 me-2"><i
                    class="bi bi-floppy"></i>&nbsp;Save</button>
        </form>



        <!-- Table Start -->
        <div class="mt-4">
            <livewire:tables.po-table />
        </div>

    </div>
</div>

<x-slot:script>
    <script src="{{ asset('js/pdfjs.min.js') }}"></script>
    <script>
        // Tentukan lokasi pdf.worker.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('js/pdf_worker.js') }}';
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const inputElement = document.querySelector('input[type="file"]');
            const pond = FilePond.create(inputElement);
            pond.setOptions({
                allowMultiple: false,
                acceptedFileTypes: ['application/pdf'],
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort) => {
                        @this.upload('form.file', file, load, error, progress);
                        const approveSelect = document.getElementById("approver2");
                        const selectedApprover = approveSelect.options[approveSelect.selectedIndex];
                        console.log(selectedApprover.text);
                        const fileReader = new FileReader();
                        fileReader.onload = function() {
                            const pdfData = new Uint8Array(this.result);
                            console.log('tes1');

                            // Muat PDF menggunakan PDF.js
                            pdfjsLib.getDocument(pdfData).promise.then(pdf => {
                                pdf.getPage(1).then(page => {
                                    // Dapatkan teks dan posisi
                                    page.getTextContent().then(textContent => {
                                        let foundCoordinates = null;

                                        console.log(textContent);
                                        textContent.items.forEach(item => {
                                            //console.log('tes2');
                                            if (item.str.includes(
                                                    selectedApprover
                                                    .text
                                                )) {
                                                const x = item
                                                    .transform[4];
                                                const y = item
                                                    .transform[5];
                                                foundCoordinates = {
                                                    x,
                                                    y
                                                };
                                                console.log(
                                                    "Posisi Signature: ",
                                                    x, y);
                                            }
                                            //console.log(foundCoordinates);
                                        });

                                        // Kirimkan koordinat ke Livewire jika ditemukan
                                        Livewire.dispatch(
                                            'saveCoordinates', {
                                                coordinates: foundCoordinates
                                            });
                                    });
                                });
                            });

                            // Panggil `load` untuk menyelesaikan proses unggahan
                            load(file.name);
                        };
                        fileReader.readAsArrayBuffer(file);
                    }
                }
            });


            // Reset FilePond ketika dispatch
            Livewire.on("removeFile", () => {
                pond.removeFiles();
            });

            const selectElement = document.querySelector("#supplier");
               const choices =  new Choices(selectElement, {
                    searchEnabled: true,
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: "Choose Supplier"
                })

            selectElement.addEventListener('change', () => {
                @this.set('form.supplierId', selectElement.value);
            });

            Livewire.on('setChoices', (c) => {
                choices.setChoiceByValue(c.id);
            });
            Livewire.on('clearChoices', ()=>{
                choices.removeActiveItems();
            });

        });

        //script dengan jsPdf
        document.getElementById("fileInput").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const fileReader = new FileReader();
                fileReader.onload = function() {
                    const pdfData = new Uint8Array(this.result);

                    // Memuat PDF
                    pdfjsLib.getDocument(pdfData).promise.then(pdf => {
                        pdf.getPage(1).then(page => {
                            // Dapatkan teks dan posisi dari halaman pertama
                            page.getTextContent().then(textContent => {
                                textContent.items.forEach(item => {
                                    if (item.str.includes("Signature")) {
                                        // Mendapatkan posisi teks "Signature"
                                        const x = item.transform[4];
                                        const y = item.transform[5];
                                        console.log("Posisi Signature: ", x, y);

                                        // Logika untuk menambahkan gambar stamp di dekat posisi
                                        // addStampAtPosition(x, y + 10); // Contoh posisi y sedikit di bawah teks
                                    }
                                });
                            });
                        });
                    });
                };
                fileReader.readAsArrayBuffer(file);
            }
        });
    </script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            //Fungsi untuk event listener saat input chaged oleh modal detail po
            Livewire.on('upload-revise', (data) => {
                const uploadRevise = document.getElementById('fileRevise');
                const btnSubmit = document.getElementById('btnSubmit');
                const fileRevise = uploadRevise.files[0];


                document.getElementById('btnSubmit').disabled = true;
                document.getElementById('btnSubmit').innerText = "Getting coordinates....";


               console.log(data.approverName)

                if (!fileRevise) {
                    console.error("No file selected.");
                    return;
                }


                const fileReader = new FileReader();
                fileReader.onload = function() {
                    const pdfData = new Uint8Array(this.result);

                    // Load PDF using PDF.js
                    pdfjsLib.getDocument(pdfData).promise.then(pdf => {
                        pdf.getPage(1).then(page => {
                            // Get text and positions
                            page.getTextContent().then(textContent => {
                                let foundCoordinates = null;

                                textContent.items.forEach(item => {
                                    if (item.str.includes(
                                            data.approverName)) {
                                        const x = item.transform[4];
                                        const y = item.transform[5];
                                        foundCoordinates = {
                                            x,
                                            y
                                        };
                                        console.log(
                                            "Text Coordinates: ", x,
                                            y);
                                    }
                                });

                                // Dispatch coordinates to Livewire if found
                                if (foundCoordinates) {
                                    Livewire.dispatch('set-revised-cordinat', {
                                        coor: foundCoordinates
                                    });
                                    console.log('found')
                                } else {
                                    console.log("Text not found.");
                                }
                            });
                        });
                    }).catch(err => {
                        console.error("Error loading PDF: ", err);
                    });
                };

                fileReader.readAsArrayBuffer(fileRevise);

            });
        });
    </script>
</x-slot:script>
