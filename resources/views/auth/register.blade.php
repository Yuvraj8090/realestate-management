<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', \App\Enums\UserRole::PropertyOwner->value) }}' }">
        @csrf

        <div class="space-y-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.32em] text-amber-700">Create account</p>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">Start your real estate workspace</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Choose the account type that matches how you’ll list and manage properties.</p>
            </div>

            <div>
                <x-input-label for="role" :value="__('Account Type')" />
                <select id="role" name="role" x-model="role" class="mt-1 block w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="name" :value="__('Full Name')" />
                    <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Phone Number')" />
                    <x-text-input id="phone" class="mt-1 block w-full" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div x-show="role === '{{ \App\Enums\UserRole::Company->value }}'" x-cloak class="space-y-4 rounded-3xl border border-amber-200 bg-amber-50/70 p-5">
                <div>
                    <x-input-label for="company_name" :value="__('Company / Firm Name')" />
                    <x-text-input id="company_name" class="mt-1 block w-full" type="text" name="company_name" :value="old('company_name')" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="company_registration_number" :value="__('Registration Number')" />
                        <x-text-input id="company_registration_number" class="mt-1 block w-full" type="text" name="company_registration_number" :value="old('company_registration_number')" />
                        <x-input-error :messages="$errors->get('company_registration_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="company_license_number" :value="__('License Number')" />
                        <x-text-input id="company_license_number" class="mt-1 block w-full" type="text" name="company_license_number" :value="old('company_license_number')" />
                        <x-input-error :messages="$errors->get('company_license_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <a class="text-sm font-medium text-slate-600 transition hover:text-slate-900" href="{{ route('login') }}">
                    {{ __('Already registered? Sign in') }}
                </a>

                <x-primary-button class="justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold normal-case tracking-normal text-white shadow-lg shadow-slate-900/10 hover:bg-slate-800 focus:bg-slate-800 active:bg-slate-950">
                    {{ __('Create account') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
