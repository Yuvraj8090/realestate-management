<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Property Discovery</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Search homes, rentals, and short stays</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[320px_1fr] lg:px-8">
            <aside class="space-y-6">
                <form method="GET" action="{{ route('properties.index') }}" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" class="mt-1 block w-full" :value="$filters['search'] ?? ''" placeholder="Title, address, city..." />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        @foreach ([
                            ['property_type', 'Property Type'],
                            ['listing_type', 'Listing Type'],
                            ['city', 'City'],
                            ['locality', 'Neighborhood'],
                            ['postal_code', 'Zip Code'],
                            ['status', 'Status'],
                            ['min_price', 'Min Price'],
                            ['max_price', 'Max Price'],
                            ['min_bedrooms', 'Min Bedrooms'],
                            ['min_bathrooms', 'Min Bathrooms'],
                            ['min_area', 'Min Area'],
                            ['max_area', 'Max Area'],
                        ] as [$field, $label])
                            <div>
                                <x-input-label :for="$field" :value="$label" />
                                <x-text-input :id="$field" :name="$field" class="mt-1 block w-full" :value="$filters[$field] ?? ''" />
                            </div>
                        @endforeach
                    </div>

                    <div class="grid gap-3">
                        @foreach ([
                            'has_parking' => 'Parking',
                            'has_pool' => 'Pool',
                            'has_air_conditioning' => 'Air Conditioning',
                            'is_furnished' => 'Furnished',
                            'has_gym' => 'Gym',
                            'has_security' => 'Security',
                            'pets_allowed' => 'Pets Allowed',
                        ] as $field => $label)
                            <label class="flex items-center gap-3 text-sm text-slate-700">
                                <input type="checkbox" name="{{ $field }}" value="1" class="rounded border-slate-300 text-amber-600" @checked(($filters[$field] ?? null) == 1)>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <x-input-label for="sort" value="Sort By" />
                        <select id="sort" name="sort" class="mt-1 block w-full rounded-2xl border-slate-300">
                            <option value="">Newest</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                            <option value="popularity" @selected(request('sort') === 'popularity')>Popularity</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white">Apply filters</button>
                        <a href="{{ route('properties.index') }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700">Reset</a>
                    </div>
                </form>

                @auth
                    <form method="POST" action="{{ route('saved-searches.store') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        @csrf
                        <input type="hidden" name="filters" value='@json(array_merge($filters, ["sort" => request("sort")]))'>
                        <x-input-label for="save_search_name" value="Save this search" />
                        <x-text-input id="save_search_name" name="name" class="mt-1 block w-full" placeholder="South Mumbai luxury search" />
                        <button type="submit" class="mt-4 rounded-full bg-amber-600 px-5 py-2 text-sm font-semibold text-white">Save search</button>
                    </form>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Saved searches</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($savedSearches as $savedSearch)
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <a href="{{ route('properties.index', $savedSearch->filters) }}" class="font-semibold text-slate-900">{{ $savedSearch->name }}</a>
                                    <form method="POST" action="{{ route('saved-searches.destroy', $savedSearch) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-600">Delete</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No saved searches yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Recent searches</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($recentSearches as $recentSearch)
                                <a href="{{ route('properties.index', $recentSearch->filters) }}" class="block rounded-2xl border border-slate-200 p-4 text-sm text-slate-700">
                                    {{ $recentSearch->label ?: 'Property search' }}
                                </a>
                            @empty
                                <p class="text-sm text-slate-500">Your recent search history will appear here.</p>
                            @endforelse
                        </div>
                    </div>
                @endauth
            </aside>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Map view</h3>
                            <p class="text-sm text-slate-500">Browse the current search results by location.</p>
                        </div>
                    </div>
                    <div
                        class="h-96 overflow-hidden rounded-2xl"
                        data-properties-map
                        data-markers='@json($properties->getCollection()->filter(fn ($property) => $property->latitude && $property->longitude)->map(fn ($property) => ["title" => $property->title, "latitude" => (float) $property->latitude, "longitude" => (float) $property->longitude, "url" => route("properties.show", $property)])->values())'
                    ></div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($properties as $property)
                        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <a href="{{ route('properties.show', $property) }}">
                                <img src="{{ $property->primaryImage ? Storage::url($property->primaryImage->thumbnail_path) : 'https://placehold.co/640x420/e2e8f0/475569?text=No+Image' }}" alt="{{ $property->title }}" class="h-56 w-full object-cover" loading="lazy">
                            </a>
                            <div class="space-y-4 p-5">
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">{{ str($property->listing_type->value)->replace('_', ' ')->title() }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">{{ $property->property_type }}</span>
                                    </div>
                                    <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $property->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $property->city }}, {{ $property->state }}</p>
                                </div>

                                <div class="flex items-center justify-between text-sm text-slate-600">
                                    <span>{{ $property->bedrooms ?? 0 }} bed • {{ $property->bathrooms ?? 0 }} bath</span>
                                    <span>{{ $property->area_value ? number_format((float) $property->area_value) : 'NA' }} {{ $property->area_unit }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-semibold text-slate-900">{{ $property->currency }} {{ $property->price ? number_format((float) $property->price) : 'On request' }}</span>
                                    <a href="{{ route('properties.show', $property) }}" class="font-semibold text-amber-700">View details</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-10 text-center text-slate-500 md:col-span-2 xl:col-span-3">
                            No listings match the current search.
                        </div>
                    @endforelse
                </div>

                <div class="flex items-center justify-between">
                    <div>{{ $properties->links() }}</div>
                    @if ($properties->nextPageUrl())
                        <a href="{{ $properties->nextPageUrl() }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700">Load more</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
