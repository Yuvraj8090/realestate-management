<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Real Estate Management') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700;instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[radial-gradient(circle_at_top,_rgba(245,158,11,0.14),_transparent_28%),linear-gradient(180deg,_#fffdf8_0%,_#f8fafc_45%,_#edf2f7_100%)] text-slate-900 antialiased">
        <div class="relative isolate overflow-hidden">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between gap-4 rounded-full border border-white/80 bg-white/80 px-5 py-3 shadow-lg shadow-slate-900/5 backdrop-blur">
                    <x-application-logo />
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 transition hover:text-slate-900">Sign in</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Create account</a>
                        @endauth
                    </div>
                </div>
            </div>

            <section class="mx-auto grid max-w-7xl gap-12 px-4 pb-16 pt-8 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8 lg:pb-24 lg:pt-14">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.36em] text-amber-700">Launch Week Foundation</p>
                    <h1 class="mt-5 text-5xl font-semibold tracking-tight text-slate-950 sm:text-6xl">
                        Modern real estate operations for companies, owners, and brokers.
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        This Laravel platform is being built for multi-role property management across sales, rentals, and short stays with a clean, premium workflow from the first release onward.
                    </p>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:bg-slate-800">Register as company, owner, or broker</a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-950">Access existing account</a>
                    </div>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Core roles</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">4</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Listing modes</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">3+</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Built for</p>
                            <p class="mt-2 text-3xl font-semibold text-slate-900">Scale</p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-5">
                    <div class="rounded-[2rem] border border-slate-200 bg-white/90 p-6 shadow-2xl shadow-slate-900/8">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Included in this first build</p>
                        <div class="mt-6 space-y-4">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <h2 class="font-semibold text-slate-900">Role-based onboarding</h2>
                                <p class="mt-1 text-sm text-slate-600">Separate entry points for firms, direct owners, brokers, and the platform administrator.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <h2 class="font-semibold text-slate-900">Listing-ready schema</h2>
                                <p class="mt-1 text-sm text-slate-600">Database tables are prepared for sales, long-term rentals, and Airbnb-style short stays.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <h2 class="font-semibold text-slate-900">Protected workspaces</h2>
                                <p class="mt-1 text-sm text-slate-600">Each role gets its own dashboard path so features can expand cleanly day by day.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-900 bg-slate-900 p-6 text-white shadow-2xl shadow-slate-900/15">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300">Next up</p>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-300">
                            <li>Property CRUD with multi-image galleries</li>
                            <li>Advanced search and filter experience</li>
                            <li>Inquiry flow and broker lead management</li>
                            <li>Admin moderation and verification workflows</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
