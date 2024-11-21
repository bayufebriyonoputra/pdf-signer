<?php

use function Livewire\Volt\{state, layout};

layout('layouts.admin');

?>

<div>

    <!-- Table -->
    <livewire:tables.list-po-admin-table />
</div>

<x-slot:script>
    <script src="{{ asset('js/pdfjs.min.js') }}"></script>
    <script>
        // Tentukan lokasi pdf.worker.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('js/pdf_worker.js') }}';
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
