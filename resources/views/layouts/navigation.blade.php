<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-10 w-auto" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="border-amber-500 text-slate-900 focus:border-amber-500 focus:text-slate-900">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index') || request()->routeIs('properties.show')" class="border-amber-500 text-slate-600 hover:text-slate-900 focus:border-amber-500 focus:text-slate-900">
                        {{ __('Browse Properties') }}
                    </x-nav-link>
                    @if (Auth::user()->isRole(\App\Enums\UserRole::SuperAdmin))
                        <x-nav-link :href="route('admin.properties.index')" :active="request()->routeIs('admin.*')" class="border-amber-500 text-slate-600 hover:text-slate-900 focus:border-amber-500 focus:text-slate-900">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isRole(\App\Enums\UserRole::Company))
                        <x-nav-link :href="route('properties.manage')" :active="request()->routeIs('properties.manage') || request()->routeIs('properties.create') || request()->routeIs('properties.edit')" class="border-amber-500 text-slate-600 hover:text-slate-900 focus:border-amber-500 focus:text-slate-900">
                            {{ __('Company Listings') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isRole(\App\Enums\UserRole::Broker))
                        <x-nav-link :href="route('leads.index')" :active="request()->routeIs('leads.*')" class="border-amber-500 text-slate-600 hover:text-slate-900 focus:border-amber-500 focus:text-slate-900">
                            {{ __('Broker Leads') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('properties.manage')" :active="request()->routeIs('properties.manage') || request()->routeIs('properties.create') || request()->routeIs('properties.edit')" class="border-amber-500 text-slate-600 hover:text-slate-900 focus:border-amber-500 focus:text-slate-900">
                            {{ __('My Properties') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <span class="mr-3 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-800">
                    {{ Auth::user()->role->label() }}
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium leading-4 text-slate-600 shadow-sm transition ease-in-out duration-150 hover:text-slate-900 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-slate-100 hover:text-slate-500 focus:bg-slate-100 focus:text-slate-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index') || request()->routeIs('properties.show')">
                {{ __('Browse Properties') }}
            </x-responsive-nav-link>

            @if (Auth::user()->isRole(\App\Enums\UserRole::SuperAdmin))
                <x-responsive-nav-link :href="route('admin.properties.index')" :active="request()->routeIs('admin.*')">
                    {{ __('Admin Panel') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isRole(\App\Enums\UserRole::Company))
                <x-responsive-nav-link :href="route('properties.manage')" :active="request()->routeIs('properties.manage') || request()->routeIs('properties.create') || request()->routeIs('properties.edit')">
                    {{ __('Company Listings') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->isRole(\App\Enums\UserRole::Broker))
                <x-responsive-nav-link :href="route('leads.index')" :active="request()->routeIs('leads.*')">
                    {{ __('Broker Leads') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('properties.manage')" :active="request()->routeIs('properties.manage') || request()->routeIs('properties.create') || request()->routeIs('properties.edit')">
                    {{ __('My Properties') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-slate-200">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                <div class="mt-2 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">{{ Auth::user()->role->label() }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
