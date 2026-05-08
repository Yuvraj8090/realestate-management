<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Property Workspace</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Create a new listing</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @include('properties.partials.form')

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('properties.manage') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Cancel</a>
                    <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white">Create property</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
