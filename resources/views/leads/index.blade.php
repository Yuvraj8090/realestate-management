<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Lead Management</p>
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Inquiry and broker lead dashboard</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($leads as $lead)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-6 lg:flex-row lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">{{ str($lead->status->value)->title() }}</span>
                                    @if ($lead->is_converted)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-800">Converted</span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold text-slate-900">{{ $lead->property->title }}</h3>
                                <p class="text-sm text-slate-500">{{ $lead->inquiry?->name }} • {{ $lead->inquiry?->email }} • {{ $lead->inquiry?->phone }}</p>
                                <p class="rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-700">{{ $lead->inquiry?->message }}</p>
                            </div>

                            <form method="POST" action="{{ route('leads.update', $lead) }}" class="w-full max-w-xl space-y-4">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <x-input-label :for="'status-'.$lead->id" value="Lead Status" />
                                        <select :id="'status-'.$lead->id" id="status-{{ $lead->id }}" name="status" class="mt-1 block w-full rounded-2xl border-slate-300">
                                            @foreach (\App\Enums\LeadStatus::cases() as $status)
                                                <option value="{{ $status->value }}" @selected($lead->status === $status)>{{ str($status->value)->title() }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if (auth()->user()->isRole(\App\Enums\UserRole::SuperAdmin))
                                        <div>
                                            <x-input-label :for="'assigned-'.$lead->id" value="Assign To User ID" />
                                            <x-text-input :id="'assigned-'.$lead->id" id="assigned-{{ $lead->id }}" name="assigned_to_user_id" class="mt-1 block w-full" :value="$lead->assigned_to_user_id" />
                                        </div>
                                    @endif

                                    <div>
                                        <x-input-label :for="'booking-'.$lead->id" value="Booking Reference" />
                                        <x-text-input :id="'booking-'.$lead->id" id="booking-{{ $lead->id }}" name="booking_reference" class="mt-1 block w-full" :value="$lead->booking_reference" />
                                    </div>

                                    <label class="mt-7 flex items-center gap-3 text-sm text-slate-700">
                                        <input type="checkbox" name="is_converted" value="1" class="rounded border-slate-300 text-amber-600" @checked($lead->is_converted)>
                                        <span>Mark as converted</span>
                                    </label>
                                </div>

                                <div>
                                    <x-input-label :for="'note-'.$lead->id" value="Add note" />
                                    <textarea :id="'note-'.$lead->id" id="note-{{ $lead->id }}" name="note" rows="3" class="mt-1 block w-full rounded-2xl border-slate-300"></textarea>
                                </div>

                                @if ($lead->notes->isNotEmpty())
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <h4 class="text-sm font-semibold text-slate-900">Lead notes</h4>
                                        <div class="mt-3 space-y-3">
                                            @foreach ($lead->notes as $note)
                                                <div class="rounded-2xl bg-white p-3 text-sm text-slate-700">
                                                    <p>{{ $note->note }}</p>
                                                    <p class="mt-2 text-xs text-slate-500">{{ $note->user->name }} • {{ $note->created_at->diffForHumans() }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <button type="submit" class="rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white">Update lead</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-10 text-center text-slate-500">
                        No leads are available yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $leads->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
