<div class="px-6 py-4">
    <h1 class="text-xl font-semibold text-gray-700">Send Email</h1>

    <form class="space-y-5" wire:submit="sendEmail">
        <div>
            <label for="greeting" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Header</label>
            <input wire:model="greeting"  type="text" id="greeting"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Ex: Selamat Pagi" required />
        </div>

        <div>
            <label for="noPo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No PO</label>
            <input wire:model="noPo" type="text" id="noPo"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Ex: POXXXX" required />
        </div>

        <div>
            <label for="news" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">News</label>
            <input wire:model="news" type="text" id="news"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Ex: POXXXX" required />
        </div>

        <!-- File -->

        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload Attachment</label>
        <input wire:model="files"
            class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
            aria-describedby="file_input_help" id="file" type="file">
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX.
            2Mb).</p>
        <p wire:loading.block  wire:target="files, sendEmail" class="text-sm font-bold text-gray-600">Loading....</p>

        <button wire:loading.attr="disabled" wire:loading.class="bg-blue-900" wire:target="files, sendEmail"  type="submit" class="px-4 py-2 font-bold text-white bg-blue-600 rounded-md hover:bg-blue-700">Kirim</button>

    </form>
</div>
