<div class="px-8 py-10 space-y-8">
    <form wire:submit="store">
        <!-- Supppier -->
        <div>
            <label for="checker" class="block mb-2 text-sm font-medium text-gray-">Select an
                Supplier</label>
            <select wire:model="supplierId" id="supplier"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option selected>Choose a Supplier</option>
                @foreach ($suppliers as $supplier)
                    <option wire:key="check-{{ $supplier->id }}" value="{{ $supplier->id }}">{{ $supplier->name }}
                    </option>
                @endforeach

            </select>
        </div>

        <div>
            <label for="checker" class="block mb-2 text-sm font-medium text-gray-">Select an
                option</label>
            <select wire:model="approver_1" id="checker"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option selected>Choose a checker</option>
                @foreach ($checker as $c)
                    <option wire:key="check-{{ $c->id }}" value="{{ $c->id }}">{{ $c->name }}
                    </option>
                @endforeach

            </select>
        </div>

        <div>
            <label for="signer" class="block mb-2 text-sm font-medium text-gray-">Select an
                option</label>
            <select wire:model.live="approver_2" wire:change="setSignerName" id="signer"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option selected>Choose a signer</option>
                @foreach ($signer as $s)
                    <option wire:key="sig-{{ $s->id }}" value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach

            </select>
        </div>

        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
            file</label>
        <input wire:model="file" id="fileRevise"
            @change="$wire.dispatch('upload-revise', {approverName : $wire.signerName})"
            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
            aria-describedby="file_input_help" id="file_input" type="file">
        @error('file')
            <p class="text-sm font-bold text-red-500">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Accepted only pdf files</p>

        <div class="flex justify-center">
            <button id="btnSubmit" type="submit"
                class="py-2 mt-5 w-full max-w-[12rem] text-white bg-blue-600 rounded-md hover:bg-blue-700">Submit</button>
        </div>
    </form>

    {{-- update status section --}}
    <div>


        <label for="status" class="block mb-2 text-sm font-medium text-gray-900">Ubah Status</label>
        <select wire:model='stats' id="status"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
            <option selected>Choose a status</option>
            <option value="{{ \App\Enum\StatusEnum::NEW }}">Siap Cek</option>
            <option value="{{ \App\Enum\StatusEnum::CHECKED }}">Siap Approve</option>
            <option value="{{ \App\Enum\StatusEnum::SIGNED }}">Finish Approve</option>
            <option value="{{ \App\Enum\StatusEnum::CONFIRMED }}">Confirm</option>
            <option value="{{ \App\Enum\StatusEnum::SENDED }}">Sended</option>
            <option value="{{ \App\Enum\StatusEnum::CANCEL }}">Cancel</option>
            <option value="{{ \App\Enum\StatusEnum::DONE }}">Done</option>
        </select>

        <div class="flex justify-center">
            <button wire:click='changeStats' type="button"
                class="py-2 mt-5 w-full max-w-[12rem] text-white bg-blue-600 rounded-md hover:bg-blue-700">Ubah Status</button>
        </div>

    </div>
</div>
