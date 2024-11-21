<aside class="flex overflow-y-auto flex-col px-5 py-8 w-64 h-screen bg-white border-r rtl:border-r-0 rtl:border-l dark:bg-gray-900 dark:border-gray-700">
    <a href="#">
        <p class="text-sm font-semibold text-gray-500">Welcome Back, {{auth()->user()->name}}</p>
    </a>

    <div class="flex flex-col flex-1 justify-between mt-6">
        <nav class="-mx-3 space-y-6">
            <!-- Admin Menu -->
            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Mater Data</label>

                <a class="{{request()->is('dashboard') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/dashboard">

                    <i class="bi bi-alarm"></i>

                    <span class="mx-2 text-sm font-medium">Dashboard</span>
                </a>

                <a class="{{request()->is('master-user') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/master-user">
                    <i class="bi bi-people-fill"></i>
                    <span class="mx-2 text-sm font-medium">User</span>
                </a>

                <a class="{{request()->is('master-approver') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/master-approver">
                    <i class="bi bi-person-badge"></i>

                    <span class="mx-2 text-sm font-medium">Approver</span>
                </a>

                <a class="{{request()->is('master-supplier') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/master-supplier">
                    <i class="bi bi-box-seam-fill"></i>

                    <span class="mx-2 text-sm font-medium">Supplier</span>
                </a>
            </div>

            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Purchase Order</label>

                <a  class=" {{request()->is('purchase-order') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/purchase-order">
                    <i class="bi bi-envelope-paper"></i>
                    <span class="mx-2 text-sm font-medium">Purchase Order</span>
                </a>

                <a class="{{request()->is('list-po') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/list-po">
                    <i class="bi bi-card-checklist"></i>
                    <span class="mx-2 text-sm font-medium">List PO All</span>
                </a>

                <a class="{{request()->is('po-pending') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="/po-pending">
                    <i class="bi bi-clock-history"></i>
                    <span class="mx-2 text-sm font-medium">List PO Pending</span>
                </a>
                <a class="{{request()->is('po-reminder') ? 'active' : ''}} flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="{{url('/po-reminder')}}">
                    <i class="bi bi-clock-history"></i>
                    <span class="mx-2 text-sm font-medium">List PO Reminder</span>
                </a>

            </div>

            <div class="space-y-3">
                <label class="px-3 text-xs text-gray-500 uppercase dark:text-gray-400">Approver</label>
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
