<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Inventory Management</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">Manage your property listings</h2>
            </div>
            <a href="{{ route('properties.create') }}" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white">Add property</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($properties as $property)
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <img src="{{ $property->primaryImage ? Storage::url($property->primaryImage->thumbnail_path) : 'https://placehold.co/640x420/e2e8f0/475569?text=No+Image' }}" alt="{{ $property->title }}" class="h-56 w-full object-cover" loading="lazy">
                        <div class="space-y-4 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $property->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $property->city }}, {{ $property->state }}</p>
                                </div>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">
                                    {{ str($property->moderation_status->value)->replace('_', ' ')->title() }}
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2 text-xs text-slate-500">
                                <span class="rounded-full bg-slate-100 px-3 py-1">{{ str($property->status->value)->replace('_', ' ')->title() }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1">{{ $property->property_type }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1">{{ $property->bedrooms ?? 0 }} bed</span>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <a href="{{ route('properties.show', $property) }}" class="font-semibold text-amber-700">View</a>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('properties.edit', $property) }}" class="font-semibold text-slate-700">Edit</a>
                                    <form method="POST" action="{{ route('properties.destroy', $property) }}" onsubmit="return confirm('Delete this property?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-10 text-center text-slate-500 md:col-span-2 xl:col-span-3">
                        No properties yet. Create your first listing to start building inventory.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
