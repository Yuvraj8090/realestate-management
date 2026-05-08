<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Admin Moderation</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Review and verify property listings</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.4fr_0.6fr] lg:px-8">
            <div class="space-y-6">
                <form method="GET" action="{{ route('admin.properties.index') }}" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <label class="text-sm font-semibold text-slate-700">Moderation queue</label>
                    <select name="moderation_status" class="mt-2 block w-full rounded-2xl border-slate-300" onchange="this.form.submit()">
                        @foreach (\App\Enums\PropertyModerationStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ str($status->value)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </form>

                <form id="bulk-moderation-form" method="POST" action="{{ route('admin.properties.bulk') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900">Bulk moderation</h3>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" class="rounded border-slate-300" data-check-all data-target-form="bulk-moderation-form">
                            Select all
                        </label>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <select name="moderation_status" class="rounded-2xl border-slate-300">
                            @foreach (\App\Enums\PropertyModerationStatus::cases() as $status)
                                <option value="{{ $status->value }}">{{ str($status->value)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        <x-text-input name="rejection_reason" placeholder="Optional rejection reason" />
                    </div>
                    <textarea name="moderation_notes" rows="3" class="mt-4 block w-full rounded-2xl border-slate-300" placeholder="Optional moderation notes"></textarea>

                    <button type="submit" class="mt-6 rounded-full bg-amber-600 px-5 py-2 text-sm font-semibold text-white">Apply bulk action</button>
                </form>

                <div class="space-y-4">
                    @foreach ($properties as $property)
                        <label class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <input type="checkbox" name="property_ids[]" value="{{ $property->id }}" form="bulk-moderation-form" class="mt-1 rounded border-slate-300">
                            <img src="{{ $property->primaryImage ? Storage::url($property->primaryImage->thumbnail_path) : 'https://placehold.co/160x120/e2e8f0/475569?text=No+Image' }}" alt="{{ $property->title }}" class="h-20 w-28 rounded-2xl object-cover" loading="lazy">
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $property->title }}</h4>
                                        <p class="text-sm text-slate-500">{{ $property->user->name }} • {{ $property->city }}, {{ $property->state }}</p>
                                    </div>
                                    <a href="{{ route('properties.edit', $property) }}" class="text-sm font-semibold text-amber-700">Edit</a>
                                </div>
                                <div class="mt-3">
                                    <form method="POST" action="{{ route('admin.properties.update', $property) }}" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <div class="grid gap-3 md:grid-cols-3">
                                            <select name="moderation_status" class="rounded-2xl border-slate-300">
                                                @foreach (\App\Enums\PropertyModerationStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" @selected($property->moderation_status === $status)>{{ str($status->value)->replace('_', ' ')->title() }}</option>
                                                @endforeach
                                            </select>
                                            <x-text-input name="rejection_reason" :value="$property->rejection_reason" placeholder="Rejection reason" />
                                            <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                                        </div>
                                        <textarea name="moderation_notes" rows="2" class="block w-full rounded-2xl border-slate-300" placeholder="Moderation notes">{{ $property->moderation_notes }}</textarea>
                                    </form>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div>{{ $properties->links() }}</div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Recent admin activity</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($logs as $log)
                            <div class="rounded-2xl border border-slate-200 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">{{ $log->action }}</p>
                                <p class="mt-1 text-slate-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No admin actions logged yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Reported listings</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($openReports as $report)
                            <div class="rounded-2xl border border-slate-200 p-4 text-sm text-slate-700">
                                <p class="font-semibold text-slate-900">{{ $report->property->title }}</p>
                                <p class="mt-1">{{ $report->reason }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No reported listings right now.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
