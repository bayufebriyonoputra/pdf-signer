<div class="max-w-6xl mx-auto px-4">


    <div class="max-w-6xl mx-auto px-4 py-8">

        <p class="text-lg font-bold">Form Tambah Data</p>
        <div class="my-4">
            <form wire:submit='save'>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-">Nama Vendor</label>
                        <input wire:model='name' type="text" id="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="Nama Vendor" required />
                        @error('name')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- TOP --}}
                    <div>
                        <label for="top" class="block mb-2 text-sm font-medium text-gray-">Term Of
                            Payment</label>
                        <input wire:model='top' type="number" id="top"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="30 Days" required />
                        @error('top')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input @keydown.tab.prevent="" wire:keydown.tab="addEmail" wire:model="email" type="email"
                            name="email" id="email"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ex: PT BWT PERKASA" />
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- List Email yang sudah ditambahkan -->
                        <div class="flex flex-wrap gap-3 mt-3">
                            @foreach ($emails as $index => $email)
                                <span class="relative px-4 py-2 text-sky-600 bg-sky-200 rounded-md">{{ $email }}
                                    <i role="button" wire:click="removeEmail({{ $index }})"
                                        class="absolute top-0 right-0.5 text-xs text-red-500 bi bi-x-circle"></i>
                                </span>
                            @endforeach
                        </div>
                    </div>

                </div>

                <button type="submit"
                    class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Save</button>


            </form>
        </div>

        {{-- <p class="text-gray-600 text-xs">Atau import dengan excel download format <span
                class="text-blue-500 underline hover:cursor-pointer">disini</span></p>
        <div class="flex gap-2 items-center">
            <input
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 h-fit"
                id="file_input" type="file">

            <button type="submit"
                    class="w-fit  mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Save</button>

        </div> --}}

        <!-- Search & Actions -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="relative w-full sm:w-64">
                <input wire:model.live='search' type="text" placeholder="Cari data..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1116.65 2.5a7.5 7.5 0 010 15z" />
                    </svg>
                </div>
            </div>

        </div>


        <div class="overflow-x-auto  rounded-lg shadow-md border border-gray-200">
            <table class="whitespace-nowrap divide-y divide-gray-200 bg-white table-auto min-w-max">
                <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>Name</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>TOP</span>
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold">
                            <div class="flex flex-col leading-tight">
                                <span>Email</span>
                            </div>
                        </th>

                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach ($vendors as $vendor)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $vendor->name ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $vendor->top }} DAYS</td>
                            <td class="px-6 py-4 whitespace-normal break-words max-w-xs">
                                {{ $vendor->email }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click='setEdit({{ $vendor->id }})'
                                        class="bg-yellow-300 hover:bg-yellow-400 px-4 py-2 rounded-md text-white"><i
                                            class="bi bi-pencil-square"></i></button>
                                    <button wire:click='destroy({{ $vendor->id }})'
                                        class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md text-white"><i
                                            class="bi bi-trash"></i></button>
                                </div>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>


    <!-- Pagination -->
    {{ $vendors->links(data: ['scrollTo' => false]) }}
</div>
