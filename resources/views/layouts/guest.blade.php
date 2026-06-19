<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'furugi') }}</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-cyan-50 antialiased">
        <div
            class="flex min-h-screen flex-col items-center pt-6 text-white sm:justify-center sm:pt-0"
            style="background-image: linear-gradient(rgba(2, 6, 23, 0.28), rgba(2, 6, 23, 0.52)), url('{{ asset('images/bg.png') }}?v={{ time() }}'); background-size: cover; background-position: center; background-attachment: fixed;"
        >
            <div>
                <a href="/">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="古着管理システム"
                        class="mx-auto h-auto w-40 drop-shadow-2xl"
                    >
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-slate-950/45 px-6 py-4 text-white shadow-2xl backdrop-blur-md sm:max-w-md sm:rounded-3xl border border-cyan-300/20">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
