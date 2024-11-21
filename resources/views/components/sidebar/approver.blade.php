<aside class="flex overflow-y-auto flex-col px-5 py-8 w-64 h-screen bg-white border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">
    <a href="#">
        <img class="w-auto h-7" src="https://merakiui.com/images/logo.svg" alt="">
    </a>

    <div class="flex flex-col flex-1 justify-between mt-6">
        <nav class="-mx-3 space-y-6">
            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Approve PO</label>


                <a class="{{request()->is('need-approve') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/need-approve">
                    <i class="bi bi-people-fill"></i>
                    <span class="mx-2 text-sm font-medium">Purchase Order</span>
                </a>

            </div>
            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Misc</label>
                <livewire:layout.navigation />

            </div>
        </nav>
    </div>
</aside>
