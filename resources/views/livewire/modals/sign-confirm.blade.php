<div>
    <!-- Modal -->
    <div x-data="{open: false}" class="p-6 w-full">
        <div class="flex items-center mb-4">
            <i class="text-lg text-sky-500 bi bi-info-circle-fill me-3"></i>
            <h2 class="text-lg font-semibold">Confirmation</h2>
        </div>
        <p class="mb-6">Apakah Anda ingin pending atau approve PO ini?</p>
        <div class="flex justify-end">
            <button id="cancelBtn" @click="open = !open" class="px-4 py-2 mr-2 text-white bg-red-500 rounded hover:bg-red-600">Pending</button>
            <button wire:click="confirm" type="button"  id="confirmBtn" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">Confirm</button>
        </div>

        <!-- Reject Remark -->
        <div x-show="open">
            <form wire:submit="reject">
                <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Please leave a remark</label>
                <input wire:model="remark" type="text" id="first_name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Ex: Belum sesuai regulasi..." required />

                    <button type="submit" class="px-4 py-2 mt-3 text-white bg-red-500 rounded-md hover:bg-red-600">Send</button>
            </form>
        </div>
    </div>
</div>
