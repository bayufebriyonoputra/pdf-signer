<div>
    <div class="max-w-md p-6 mx-auto">
        <h3 class="mb-4 font-semibold text-gray-600">Uploaded Files</h3>


        <!-- Attachment Card -->
        <div class="flex flex-col p-4 mb-4 space-y-6 bg-white rounded-lg shadow-lg">
            <!-- File -->
            @foreach ($files as $file)
            <div class="flex items-center space-x-4">
                <!-- ICon Pdf -->
                <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                    <span class="font-bold text-blue-500">PDF</span>
                </div>
                <!-- Detail -->
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-800">Diupload : {{ \Carbon\Carbon::parse($file->created_at)->locale('id')->isoFormat('DD MMM YYYY hh:MM:ss') }}</span>
                        <a href="{{asset("storage/$file->file")}}" target="_blank" type="button" class="self-start px-4 py-2 text-white bg-blue-500 rounded-md w-max hover:bg-blue-700"><i class="bi bi-eye-fill"></i></a>
                    </div>
                </div>
            </div>
            @endforeach

            <!--Button -->
            <div x-data="{open:false}">
                @if(auth()->user()->role == \App\Enum\RoleEnum::ADMIN)
                <button @click="open = !open" class="w-full px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <i class="bi bi-file-earmark-plus me-3"></i>Tambahkan File</button>
                @endif
                <!-- Upload File -->
                <div x-show="open" class="mt-5">
                    <form wire:submit="store">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                            file</label>
                        <input wire:model="file" id="fileRevise" @change="$wire.dispatch('upload-revise', {approverName : $wire.approverName})"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            aria-describedby="file_input_help" id="file_input" type="file">
                            @error('file')
                            <p class="text-sm font-bold text-red-500">{{$message}}</p>
                            @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">Accepted only pdf files</p>

                        <!-- Checkbox -->
                        <div class="flex items-center mt-4">
                            <input wire:model="isRevised" id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="default-checkbox" class="text-sm font-medium text-gray-900 ms-2 dark:text-gray-300">Is Revised</label>
                        </div>
                        <div class="flex justify-center">
                            <button id="btnSubmit" type="submit"  class="py-2 mt-5 w-full max-w-[12rem] text-white bg-blue-600 rounded-md hover:bg-blue-700">Submit</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>


        <div class="p-6 mt-6 bg-white rounded-lg shadow-md">
            <h2 class="mb-4 text-xl font-bold">Purchase Order Status</h2>
            <!-- Track Step -->
            @foreach ($notifications as $notif)
                <div class="flex items-center mb-4">
                    <div
                        class="flex justify-center items-center w-10 h-10 text-white {!! $notif->additional_class !!} rounded-full">
                        {!! $notif->icon !!}
                    </div>
                    <div class="w-full ml-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="flex-grow text-sm font-medium text-gray-700">{{ $notif->message }}</p>
                            <p class="ml-4 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($notif->created_at)->locale('id')->isoFormat('DD MMM YYYY HH:mm:ss') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $notif->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>



</div>
