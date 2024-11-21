<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: false);
};

?>

<a wire:click="logout" class="flex items-center px-3 py-2 text-gray-600 rounded-lg transition-colors duration-300 transform dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-200 hover:text-gray-700" href="#">
    <i class="bi bi-box-arrow-left"></i>
    <span class="mx-2 text-sm font-medium">Logout</span>
</a>
