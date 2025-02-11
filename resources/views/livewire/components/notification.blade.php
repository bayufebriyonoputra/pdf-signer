<?php

use App\Models\EmailFailed;
use function Livewire\Volt\{state, with};

with(
    fn() => [
        'notif' => EmailFailed::where('is_read', false)->count(),
        'notifList' => EmailFailed::latest()->get(),
    ],
);

$read = function($id){
    EmailFailed::find($id)->update([
        'is_read' => true
    ]);
};

$readAll = function(){
    EmailFailed::where('is_read', false)->update([
        'is_read' => true
    ]);
};

$deleteAll = function(){
    EmailFailed::query()->delete();
}

?>
<div x-data="{ open: false }">
    <!-- Tombol Notifikasi -->
    <div class="p-6">
        <button @click="open = true" class="relative px-4 py-2 text-white bg-blue-600 rounded-lg">
            🔔 Notifikasi
            <span class="absolute p-2 text-white bg-red-600 rounded-full -left-4 -top-4">{{ $notif }}</span>
        </button>
    </div>

    <!-- Drawer Notifikasi -->
    <div>
        <!-- Overlay -->
        <div x-show="open" @click="open = false" class="fixed inset-0 transition-opacity " x-transition.opacity></div>

        <!-- Drawer -->
        <div x-show="open" x-transition:enter="transition transform ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 z-30 h-full p-5 overflow-y-auto bg-white shadow-lg w-80">

            <!-- Header Drawer -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h2 class="text-lg font-semibold">Notifikasi</h2>
                <button @click="open = false" class="text-gray-500 hover:text-red-600">&times;</button>
            </div>

            <!-- List Notifikasi -->
            <div class="flex flex-col justify-between mt-4 ">
                <!-- Button -->
                <div class="flex justify-center gap-4">
                    <button wire:click="readAll" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">Mark All As Read</button>
                    <button wire:click="deleteAll" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">Delete All</button>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach ($notifList as $n)
                        <div wire:click="read({{ $n->id }})" wire:key="notif-{{ $n->id }}" @class(['p-3  rounded-lg shadow hover:cursor-pointer', 'bg-blue-500 text-white' => !$n->is_read, 'bg-gray-100 text-black' => $n->is_read])>
                            <p class="text-sm">📩 Email dengan no PO {{ $n->no_po }} gagal dikirim</p>
                            <span @class(['text-xs', 'text-gray-500' => $n->is_read, 'text-gray-200' => !$n->is_read])>{{ \Carbon\Carbon::parse($n->created_at)->locale('id_ID')->diffForHumans() }}</span>
                        </div>
                    @endforeach
                    
                </div>


            </div>
        </div>
    </div>
</div>
