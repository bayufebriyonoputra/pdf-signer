<div class=" mx-auto px-4">

    <!-- Search & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-64">
            <input type="text" placeholder="Cari data..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1116.65 2.5a7.5 7.5 0 010 15z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Action --}}

        <div class="flex gap-2">
            <input wire:model='customMessage' type="text" id="name"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                placeholder="Custom Message" />
            <button wire:target='batchSend' wire:loading.attr='disabled' wire:click='batchSend'
                class="bg-sky-400 max-w-[200px] w-full hover:bg-sky-500 rounded-md px-4 py-2 text-white"><i class="bi bi-send me-4"></i>Send
                Email</button>
        </div>



    <div class=" mx-auto px-4 py-8">
        <div class="overflow-x-auto  rounded-lg shadow-md border border-gray-200">
            <table class="whitespace-nowrap divide-y divide-gray-200 bg-white table-auto min-w-max">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">✅</th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>Vendor</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>No</span>
                                <span class="text-[11px] normal-case font-medium text-gray-500">PO</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>No</span>
                                <span class="text-[11px] normal-case font-medium text-gray-500">Invoice</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">Total</th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>Tanggal</span>
                                <span class="text-[11px] normal-case font-medium text-gray-500">Invoice</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>Tanggal</span>
                                <span class="text-[11px] normal-case font-medium text-gray-500">Pembayaran</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>PIC</span>
                                <span class="text-[11px] normal-case font-medium text-gray-500">Perusahaan</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">Note</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach ($invoices as $inv)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model.live='ids' value="{{ $inv->id }}"
                                    class="accent-blue-600" />
                            </td>
                            <td class="px-6 py-4">{{ $inv->vendor->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $inv->no_po }}</td>
                            <td class="px-6 py-4">{{ $inv->no_invoice ?? '-' }}</td>
                            <td class="px-6 py-4">{{ rupiah($inv->total) ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $inv->tgl_invoice ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $inv->tgl_pembayaran ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $inv->pic_perusahaan ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $inv->note ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button class="bg-sky-400 hover:bg-sky-500 rounded-md px-4 py-2 text-white"><i
                                            class="bi bi-send"></i></button>
                                </div>
                            </td>
                            {{-- <td class="px-6 py-4">
                            <span
                                class="inline-block rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Lihat</button>
                        </td> --}}
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>


    <!-- Pagination -->
    {{-- {{ $invoices->links(data: ['scrollTo' => false]) }} --}}
</div>

<x-slot:script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const selectElement = document.querySelector("#vendor");
            const choices = new Choices(selectElement, {
                searchEnabled: true,
                removeItemButton: true,
                placeholder: true,
                placeholderValue: "Choose Vendor"
            })

            selectElement.addEventListener('change', () => {
                @this.set('vendorId', selectElement.value);
            });

            Livewire.on('setChoices', (c) => {
                choices.setChoiceByValue(c.id.toString());
            });
            Livewire.on('clearChoices', () => {
                choices.removeActiveItems();
            });
        });
    </script>

</x-slot:script>
