<aside
    class="flex overflow-y-auto flex-col px-5 py-8 w-64 h-screen bg-white border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">


    <a href="#">
        <p class="text-sm font-semibold text-gray-500">Welcome Back, {{ auth()->user()->name }}</p>
    </a>


    <div class="flex flex-col flex-1 justify-between mt-6">
        <nav class="-mx-3 space-y-6">
            <!-- Admin Menu -->
            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Mater Data</label>

                <a class="{{ request()->is('invoice/master-vendor') ? 'active' : '' }} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700"
                    href="/invoice/master-vendor">

                    <i class="bi bi-people"></i>

                    <span class="mx-2 text-sm font-medium">Master Vendor</span>
                </a>

                <a class="{{ request()->is('invoice/master-invoice') ? 'active' : '' }} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700"
                    href="/invoice/master-invoice">

                    <i class="bi bi-receipt"></i>

                    <span class="mx-2 text-sm font-medium">Invoice</span>
                </a>

                <a class="{{ request()->is('invoice/list-email') ? 'active' : '' }} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700"
                    href="/invoice/list-email">

                    <i class="bi bi-envelope"></i>

                    <span class="mx-2 text-sm font-medium">Kirim Email</span>
                </a>



            </div>



            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Misc</label>
                <livewire:layout.navigation />
            </div>
        </nav>
    </div>
</aside>
