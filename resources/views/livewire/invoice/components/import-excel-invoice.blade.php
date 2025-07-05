<div>
    <p class="text-gray-600 text-xs">Atau import dengan excel download format <a
            href="{{ asset('/excel/import-invoice.xlsx') }}"
            class="text-blue-500 underline hover:cursor-pointer">disini</a></p>
    <div class="flex gap-2 items-center">
        <input wire:model='file'
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 h-fit"
            id="file_input" type="file">
        @error('file')
            <p class="text-red-500">{{ $message }}</p>
        @enderror

        <button wire:click='import' type="button"
            class="w-fit  mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Save</button>

    </div>
</div>
