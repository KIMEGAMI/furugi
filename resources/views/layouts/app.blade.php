<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'furugi') }}</title>

    <x-pwa-head />
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
            background-color: rgba(0, 0, 0, 0.32) !important;
            border-color: rgba(127, 250, 244, 0.24) !important;
            backdrop-filter: blur(12px);
        }

        .furugi-app-shell main a.bg-white,
        .furugi-app-shell main button.bg-white {
            background-color: #ffffff !important;
            color: #111827 !important;
            border-color: rgba(10, 186, 181, 0.35) !important;
            backdrop-filter: none;
        }

        .furugi-app-shell main a.bg-white:hover,
        .furugi-app-shell main button.bg-white:hover {
            background-color: #ecfeff !important;
            color: #111827 !important;
        }

        .furugi-app-shell main a.bg-white *,
        .furugi-app-shell main button.bg-white * {
            color: #111827 !important;
        }

        .furugi-app-shell main .bg-white.text-white,
        .furugi-app-shell main .bg-white .text-white {
            color: #7ffaf4 !important;
        }

        .furugi-app-shell main dialog.bg-white,
        .furugi-app-shell main dialog .bg-white {
            background-color: #ffffff !important;
            color: #082f2e !important;
            border-color: rgba(10, 186, 181, 0.28) !important;
            backdrop-filter: none;
        }

        .furugi-app-shell main dialog,
        .furugi-app-shell main dialog * {
            text-shadow: none !important;
        }

        .furugi-app-shell main dialog {
            background-color: #ffffff !important;
            color: #082f2e !important;
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
        .furugi-app-shell main .text-slate-950,
        .furugi-app-shell main .text-cyan-50,
        .furugi-app-shell main .text-cyan-100 {
            color: #bafffb !important;
        }

        .furugi-app-shell main dialog .text-blue-900,
        .furugi-app-shell main dialog .text-slate-900,
        .furugi-app-shell main dialog .text-slate-950,
        .furugi-app-shell main dialog .text-slate-800 {
            color: #082f2e !important;
        }

        .furugi-app-shell main .text-slate-700,
        .furugi-app-shell main .text-slate-800,
        .furugi-app-shell main .text-cyan-100,
        .furugi-app-shell main .text-slate-600,
        .furugi-app-shell main .text-cyan-200 {
            color: #7ffaf4 !important;
        }

        .furugi-app-shell main dialog .text-slate-500,
        .furugi-app-shell main dialog .text-slate-600,
        .furugi-app-shell main dialog .text-slate-700 {
            color: #315c5a !important;
        }

        .furugi-app-shell main dialog .text-blue-700,
        .furugi-app-shell main dialog .text-cyan-700 {
            color: #075f5c !important;
        }

        .furugi-app-shell main .text-cyan-200,
        .furugi-app-shell main .text-cyan-200 {
            color: #7ffaf4 !important;
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

        .furugi-app-shell main dialog table {
            background-color: #ffffff !important;
            color: #082f2e !important;
        }

        .furugi-app-shell main dialog table thead,
        .furugi-app-shell main dialog table .bg-slate-100,
        .furugi-app-shell main dialog table .bg-gray-50 {
            background-color: #d8fffb !important;
            color: #063b3a !important;
        }

        .furugi-app-shell main dialog th,
        .furugi-app-shell main dialog td {
            border-color: #86e7e4 !important;
            color: #082f2e !important;
        }

        .furugi-app-shell main dialog tbody tr:nth-child(even) {
            background-color: #f7fffe !important;
        }

        .furugi-app-shell table tbody {
            background-color: transparent !important;
        }

        .furugi-app-shell main .bg-slate-50,
        .furugi-app-shell main .bg-slate-100,
        .furugi-app-shell main .bg-gray-50,
        .furugi-app-shell main .bg-gray-100 {
            background-color: rgba(255, 255, 255, 0.10) !important;
            color: #7ffaf4 !important;
        }

        .furugi-app-shell main dialog .bg-slate-50,
        .furugi-app-shell main dialog .bg-slate-100,
        .furugi-app-shell main dialog .bg-gray-50,
        .furugi-app-shell main dialog .bg-gray-100 {
            background-color: #f0fffd !important;
            color: #082f2e !important;
        }

        .furugi-app-shell main dialog button.bg-slate-100 {
            background-color: #e0fffb !important;
            color: #063b3a !important;
            border: 1px solid #86e7e4;
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
