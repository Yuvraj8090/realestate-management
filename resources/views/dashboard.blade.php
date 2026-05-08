<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Workspace Overview</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">
                    Welcome back, {{ $user->name }}
                </h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    {{ $user->role->label() }} dashboard for the real estate platform foundation. This first version focuses on onboarding, role separation, and listing-ready data structures.
                </p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                @if ($user->isRole(\App\Enums\UserRole::Company))
                    Verification: {{ str($user->company?->verification_status?->value ?? 'pending')->headline() }}
                @else
                    Account type: {{ $user->role->label() }}
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-8 xl:grid-cols-[1.4fr_1fr]">
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-slate-900">Current build roadmap</h3>
                        <p class="mt-1 text-sm text-slate-500">The project is being assembled in daily slices, starting with platform structure and access control.</p>
                    </div>
                    <div class="space-y-4 px-6 py-6">
                        @foreach ($roadmap as $item)
                            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-4">
                                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></div>
                                <p class="text-sm leading-6 text-slate-700">{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-slate-900">Recent property records</h3>
                        <p class="mt-1 text-sm text-slate-500">Seeded sample data ensures the first dashboards don’t start empty.</p>
                    </div>
                    <div class="divide-y divide-slate-200">
                        @forelse ($recentProperties as $property)
                            <div class="px-6 py-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $property->title }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">{{ $property->city }}, {{ $property->state }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">
                                        {{ str($property->status->value)->replace('_', ' ')->title() }}
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium text-slate-500">
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-800">{{ str($property->listing_type->value)->replace('_', ' ')->title() }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $property->property_type }}</span>
                                    @if ($property->bedrooms)
                                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $property->bedrooms }} bed</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-sm text-slate-500">
                                Property records will appear here as soon as listings are created.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
