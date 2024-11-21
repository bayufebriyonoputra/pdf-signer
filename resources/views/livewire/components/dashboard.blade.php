<?php

use App\Enum\RoleEnum;
use App\Enum\StatusEnum;
use App\Models\HeaderPo;
use function Livewire\Volt\{state, mount, on};

state(['open', 'checked', 'signed', 'all', 'done']);

mount(function () {
    if (auth()->user()->role == RoleEnum::CHECKER) {
        $this->open = HeaderPo::where('approver_1', auth()->user()->id)
            ->where('status', StatusEnum::NEW)
            ->count();
    } elseif (auth()->user()->role == RoleEnum::SIGNER) {
        $this->open = HeaderPo::where('approver_2', auth()->user()->id)
            ->where('status', StatusEnum::CHECKED)
            ->count();
    } else {
        $this->open = HeaderPo::where('status', StatusEnum::NEW)
            ->orWhere('status', StatusEnum::CHECKED)
            ->count();
    }
    $this->signed = HeaderPo::where('status', StatusEnum::SIGNED)
        ->orWhere('status', StatusEnum::SENDED)
        ->orWhere('status', StatusEnum::CONFIRMED)
        ->orWhere('status', StatusEnum::DONE)
        ->count();
    $this->all = HeaderPo::count();
});

on([
    'refresh-dashboard' => function () {
        if (auth()->user()->role == RoleEnum::CHECKER) {
            $this->open = HeaderPo::where('approver_1', auth()->user()->id)
                ->where('status', StatusEnum::NEW)
                ->count();
        } elseif (auth()->user()->role == RoleEnum::SIGNER) {
            $this->open = HeaderPo::where('approver_2', auth()->user()->id)
                ->where('status', StatusEnum::CHECKED)
                ->count();
        } else {
            $this->open = HeaderPo::where('status', StatusEnum::NEW)
                ->orWhere('status', StatusEnum::CHECKED)
                ->count();
        }
        $this->signed = HeaderPo::where('status', StatusEnum::SIGNED)
            ->orWhere('status', StatusEnum::SENDED)
            ->orWhere('status', StatusEnum::CONFIRMED)
            ->orWhere('status', StatusEnum::DONE)
            ->count();
        $this->all = HeaderPo::count();
    },
]);

?>

<div>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <div class="p-2 mr-2 text-white bg-purple-500 rounded-full">
                    <i class="p-2 bi bi-house-door-fill"></i>
                </div>
                <h1 class="text-xl font-bold">Dashboard</h1>
            </div>
            <div class="text-gray-500">
                <span>Overview</span>
                <i class="bi bi-info-circle-fill"></i>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="p-6 text-white bg-gradient-to-r from-pink-500 to-orange-400 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Purchase Order Open</h2>
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="mb-2 text-4xl font-bold">{{ $open }}</div>

            </div>
            <div class="p-6 text-white bg-gradient-to-r from-blue-500 to-indigo-400 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Purhase Order Sign</h2>
                    <i class="bi bi-bookmark-fill"></i>
                </div>
                <div class="mb-2 text-4xl font-bold">{{ $signed }}</div>

            </div>
            <div class="p-6 text-white bg-gradient-to-r from-teal-400 to-green-400 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Total Purchase Order</h2>
                    <i class="bi bi-gem"></i>
                </div>
                <div class="mb-2 text-4xl font-bold">{{ $all }}</div>
            </div>
        </div>
    </div>
</div>
