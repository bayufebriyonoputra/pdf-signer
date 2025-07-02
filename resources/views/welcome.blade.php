<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronics PO Signer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .bg-gradient {
            background: linear-gradient(270deg, rgba(74, 144, 226, 0.7), rgba(144, 19, 254, 0.7)), url('{{asset("img/img-bg.jpg")}}');
            background-size: cover;
            background-position: center;
            animation: gradient 15s ease infinite;
        }
    </style>
</head>
<body class="flex justify-center items-center h-screen text-center transition duration-300 bg-gradient" id="body">

    <div>
        <h1 class="mb-4 text-5xl font-bold text-white">Electronics PO Signer</h1>
        <p class="text-lg text-gray-200">Streamline your purchase order process with ease and efficiency.</p>
        <a href="{{url('/login')}}" class="inline-block px-4 py-2 mt-6 font-semibold text-blue-600 bg-white rounded-lg shadow-lg transition duration-300 hover:bg-gray-100">
            E-PO
        </a>

        <a href="{{url('/login')}}" class="inline-block px-4 py-2 mt-6 font-semibold text-blue-600 bg-white rounded-lg shadow-lg transition duration-300 hover:bg-gray-100">
            E-Invoice
        </a>
        <a href="{{url('/login')}}" class="inline-block px-4 py-2 mt-6 font-semibold text-white bg-black rounded-lg shadow-lg transition duration-300 hover:bg-gray-800">
            Guest
        </a>

    </div>

    <script>
        const toggleButton = document.getElementById('toggleDarkMode');
        const body = document.getElementById('body');

        toggleButton.addEventListener('click', () => {
            body.classList.toggle('bg-gray-900');
            body.classList.toggle('text-white');
            body.classList.toggle('bg-gradient');
            toggleButton.classList.toggle('bg-gray-700');
            toggleButton.classList.toggle('text-gray-800');
            toggleButton.classList.toggle('bg-white');
            toggleButton.classList.toggle('text-gray-900');
        });
    </script>

</body>
</html>
