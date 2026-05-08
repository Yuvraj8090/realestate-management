<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Module Preview</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $title }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $description }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-900">What comes next in this area</h3>
                </div>
                <div class="space-y-4 px-6 py-6">
                    @foreach ($items as $item)
                        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 px-4 py-4">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-amber-500"></div>
                            <p class="text-sm leading-6 text-slate-700">{{ $item }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
