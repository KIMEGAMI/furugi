<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'furugi') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .furugi-app-shell {
            background-image:
                linear-gradient(rgba(2, 6, 23, 0.28), rgba(2, 6, 23, 0.52)),
                url('{{ asset('images/bg.png') }}?v={{ time() }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .furugi-app-shell main > .bg-slate-100,
        .furugi-app-shell main > .bg-gray-100,
        .furugi-app-shell main .bg-slate-100,
        .furugi-app-shell main .bg-gray-100 {
            background-color: transparent !important;
        }

        .furugi-app-shell header {
            background-color: rgba(0, 0, 0, 0.25) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(12px);
        }

.furugi-app-shell main .bg-white {
    background-color: rgba(0, 0, 0, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.10) !important;
    backdrop-filter: blur(12px);
}

        .furugi-app-shell main .bg-blue-50,
        .furugi-app-shell main .bg-red-50,
        .furugi-app-shell main .bg-yellow-50,
        .furugi-app-shell main .bg-green-50 {
            background-color: rgba(255, 255, 255, 0.12) !important;
            border-color: rgba(255, 255, 255, 0.14) !important;
            backdrop-filter: blur(12px);
        }

        .furugi-app-shell main .text-blue-900,
        .furugi-app-shell main .text-slate-900,
        .furugi-app-shell main .text-cyan-50,
        .furugi-app-shell main .text-cyan-100 {
            color: #f8fafc !important;
        }

        .furugi-app-shell main .text-slate-700,
        .furugi-app-shell main .text-cyan-100,
        .furugi-app-shell main .text-slate-600,
        .furugi-app-shell main .text-cyan-200 {
            color: #cbd5e1 !important;
        }

        .furugi-app-shell main .text-cyan-200,
        .furugi-app-shell main .text-cyan-200 {
            color: #94a3b8 !important;
        }

        .furugi-app-shell input,
        .furugi-app-shell textarea,
        .furugi-app-shell select {
            background-color: rgba(255, 255, 255, 0.94) !important;
            color: #0f172a !important;
        }

        .furugi-app-shell table thead,
        .furugi-app-shell table .bg-gray-50 {
            background-color: rgba(255, 255, 255, 0.10) !important;
        }

        .furugi-app-shell table tbody {
            background-color: transparent !important;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="furugi-app-shell min-h-screen bg-slate-950 text-white">
        @include('layouts.navigation')

        @isset($header)
            <header>
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
