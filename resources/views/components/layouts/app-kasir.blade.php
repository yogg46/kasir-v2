<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'POS System' }}</title>

    <!-- Tailwind CSS -->
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    {{-- <script src=""></script> --}}


    <!-- Alpine.js -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-950 text-white">
    {{ $slot }}

    @livewireScripts

    @stack('script')

    <script>
        function initFullscreenButton() {
    const fullscreenButton = document.getElementById('fullscreen-btn');
    if (!fullscreenButton) return;

    fullscreenButton.onclick = () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.error(`Gagal masuk fullscreen: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    };
}

document.getElementById('fullscreen-btn').addEventListener('click', () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch((err) => {
            console.error(`Error attempting to enable full-screen mode: ${err.message} (${err.name})`);
        })
    } else {
        document.exitFullscreen();
    }
})
        // Helper function untuk format Rupiah
        window.formatRupiah = function(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    </script>
</body>

</html>
