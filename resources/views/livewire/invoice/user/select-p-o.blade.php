<div class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-3xl bg-white shadow-2xl rounded-2xl p-8">
        <!-- Search Input -->
        <!-- vendor -->
        <div wire:ignore class="">
            <label for="vendor" class="block mb-2 text-sm font-medium text-gray-900">Vendor</label>
            <select id="vendor"
                class="@error('vendor') form-error @enderror block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 ">
                @foreach ($vendors as $v)
                    <option wire:key="{{ $v->id }}" value="{{ $v->id }}">{{ $v->name }}
                    </option>
                @endforeach
            </select>
            @error('vendor')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Table -->

        @if ($po->count())
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-blue-100 text-gray-700 text-sm uppercase tracking-wider">
                            <th class="px-4 py-3">
                                <input wire:model='updatedSelectAll' type="checkbox" class="accent-blue-600" />
                            </th>
                            <th class="px-4 py-3">No PO</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($po as $p)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-4 py-3">
                                    <input type="checkbox" wire:model.live='ids' value="{{ $p->id }}"
                                        class="accent-blue-600" />
                                </td>
                                <td class="px-4 py-3">{{ $p->no_po }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex gap-2 mt-4">
                    <form wire:submit='confirm'>
                        <input wire:model='pic' type="text" class="bg-gray-50 rounded-md px-4 py-2 text-black" placeholder="PIC Pengirim Invoice" required>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 rounded-md text-white px-4 py-2">Confirm</button>
                    </form>
                </div>

            </div>
        @else
            <p class="text-gray-600 text-sm text-center mt-4">Tidak ada Data</p>
        @endif

    </div>
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
