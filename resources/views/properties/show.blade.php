<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">{{ str($property->listing_type->value)->replace('_', ' ')->title() }}</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-900">{{ $property->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $property->full_address }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">Price</p>
                <p class="text-3xl font-semibold text-slate-900">{{ $property->currency }} {{ $property->price ? number_format((float) $property->price) : 'On request' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-8">
            <div class="space-y-8">
                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    @if ($property->primaryImage)
                        <img src="{{ Storage::url($property->primaryImage->path) }}" alt="{{ $property->title }}" class="h-[420px] w-full object-cover" loading="eager">
                    @endif
                    <div class="grid grid-cols-2 gap-3 p-4 md:grid-cols-4">
                        @foreach ($property->images as $image)
                            <img src="{{ Storage::url($image->thumbnail_path) }}" alt="{{ $image->alt_text ?: $property->title }}" class="h-28 w-full rounded-2xl object-cover" loading="lazy">
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Overview</h3>
                    <p class="mt-4 leading-7 text-slate-600">{{ $property->description }}</p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            'Bedrooms' => $property->bedrooms ?? 'NA',
                            'Bathrooms' => $property->bathrooms ?? 'NA',
                            'Area' => ($property->area_value ? number_format((float) $property->area_value) : 'NA').' '.$property->area_unit,
                            'Status' => str($property->status->value)->replace('_', ' ')->title(),
                        ] as $label => $value)
                            <div class="rounded-2xl border border-slate-200 px-4 py-4">
                                <p class="text-sm text-slate-500">{{ $label }}</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Amenities</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            'Parking' => $property->has_parking,
                            'Pool' => $property->has_pool,
                            'Air Conditioning' => $property->has_air_conditioning,
                            'Furnished' => $property->is_furnished,
                            'Gym' => $property->has_gym,
                            'Security' => $property->has_security,
                            'Pets Allowed' => $property->pets_allowed,
                        ] as $label => $enabled)
                            <div class="rounded-2xl border px-4 py-3 text-sm {{ $enabled ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Location</h3>
                    <div
                        class="mt-4 h-80 overflow-hidden rounded-2xl"
                        data-properties-map
                        data-markers='@json($property->latitude && $property->longitude ? [["title" => $property->title, "latitude" => (float) $property->latitude, "longitude" => (float) $property->longitude, "url" => route("properties.show", $property)]] : [])'
                    ></div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Send an inquiry</h3>
                    <form method="POST" action="{{ route('properties.inquiries.store', $property) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', auth()->user()->name ?? '')" required />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" class="mt-1 block w-full" :value="old('email', auth()->user()->email ?? '')" required />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Phone" />
                            <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', auth()->user()->phone ?? '')" />
                        </div>
                        <div>
                            <x-input-label for="preferred_contact_method" value="Preferred Contact Method" />
                            <select id="preferred_contact_method" name="preferred_contact_method" class="mt-1 block w-full rounded-2xl border-slate-300">
                                @foreach (\App\Enums\InquiryPreferredContactMethod::cases() as $method)
                                    <option value="{{ $method->value }}">{{ str($method->value)->title() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="message" value="Message" />
                            <textarea id="message" name="message" rows="5" class="mt-1 block w-full rounded-2xl border-slate-300" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Send inquiry</button>
                    </form>
                </section>

                @auth
                    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Report this listing</h3>
                        <form method="POST" action="{{ route('properties.report', $property) }}" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="reason" value="Reason" />
                                <x-text-input id="reason" name="reason" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="details" value="Details" />
                                <textarea id="details" name="details" rows="4" class="mt-1 block w-full rounded-2xl border-slate-300"></textarea>
                            </div>
                            <button type="submit" class="rounded-full border border-red-300 px-5 py-2 text-sm font-semibold text-red-700">Submit report</button>
                        </form>
                    </section>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
