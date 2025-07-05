<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(isset($head))
        {{$head}}
    @endif

</head>

<body>
    <div class="flex h-screen">
        <x-sidebar.invoice />
        <div class="flex-1 p-6 overflow-y-auto">
            {{-- <div class="flex justify-end w-full mb-5 bg-gray-100 rounded-full shadow-lg  h-fit">
            </div> --}}
            {{ $slot }}
        </div>
    </div>

    @if (isset($script))
        {{ $script }}
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('success-notif', (message) => {
                Toastify({
                    text: message.message,
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    }, // Callback after click
                }).showToast();
            });
            Livewire.on('error-notif', (message) => {
                Toastify({
                    text: message.message,
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    style: {
                        background: "linear-gradient(to right, #ff6f91, #ff807d, #ff966d, #ffae61, #ffc75f)",
                    }, // Callback after click
                }).showToast();
            });
        });
    </script>
    @livewire('wire-elements-modal')
    <x-livewire-alert::scripts />
</body>

</html>
