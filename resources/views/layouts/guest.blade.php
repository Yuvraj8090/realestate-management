<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Real Estate Management') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700;instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(15,23,42,0.05),_transparent_30%),linear-gradient(180deg,_#fffdf8_0%,_#f8fafc_46%,_#eef2f7_100%)] px-4 py-10 sm:px-6">
            <div class="w-full max-w-xl">
                <a href="/">
                    <x-application-logo class="justify-center" />
                </a>
            </div>

            <div class="mt-6 w-full max-w-xl overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 px-6 py-6 shadow-2xl shadow-slate-900/8 backdrop-blur sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
