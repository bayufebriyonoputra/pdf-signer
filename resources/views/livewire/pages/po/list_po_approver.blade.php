<?php

use function Livewire\Volt\{state, layout};

layout('layouts.admin');

?>

<div>
    <!-- Dashboard -->
    <livewire:components.dashboard />

    <div
        class="p-4 w-full max-w-none bg-white rounded-lg border border-gray-200 shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-lg font-semibold text-gray-700">List Purchase Order Siap Aprove</p>

        <!-- Table -->
        <livewire:tables.list-po-approver-table />
    </div>
</div>
