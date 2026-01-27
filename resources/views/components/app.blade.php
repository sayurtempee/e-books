<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{--  Vite JS  --}}
    @vite(['resources/js/app.js'])

    <!-- Google Font: Lato -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Link CDN Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Link CDN Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Link CDN Sweat2Alert -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Link CDN AlpineJS -->


    <!-- Title -->
    <title>@yield('title') | E - BOOKS</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/favicon-book.svg') }}">

    <!-- style custom css -->
    <style>
        body {
            font-family: 'Lato', sans-serif;
        }

        #global-loader {
            transition: opacity 0.3s ease;
        }

        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spinSlow 2s linear infinite;
        }
    </style>
</head>

<body>
    {{-- GLOBAL LOADING SCREEN --}}
    <div id="global-loader"
        class="fixed inset-0 z-[9999]
        flex items-center justify-center
        bg-teal-500/20 backdrop-blur-xl">

        <img src="{{ asset('image/favicon-book.svg') }}" alt="Loading" class="w-20 h-20 animate-spin-slow">
    </div>

    <!-- Body Content -->
    @yield('body-content')

    <!-- Include Alert JS -->
    @include('components.scriptJS')
</body>

</html>
