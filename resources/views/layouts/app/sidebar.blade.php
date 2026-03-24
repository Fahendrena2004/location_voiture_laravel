<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <style>
        :root {
            @if(App\Helpers\CurrencyHelper::getCurrency() === 'EUR')
                --color-accent: #2563eb !important;
                --color-accent-content: #2563eb !important;
            @else --color-accent: #dc2626 !important;
                --color-accent-content: #dc2626 !important;
            @endif --color-primary: var(--color-accent) !important;
        }

            {
                {
                -- Also override current items and specific flux elements --
            }
        }

        [data-flux-item][data-current],
        .bg-primary,
        .text-primary {
            background-color: var(--color-accent) !important;
        }

        .text-accent,
        .fill-accent {
            color: var(--color-accent) !important;
            fill: var(--color-accent) !important;
        }
    </style>
</head>

<body class="min-h-screen">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-yolk-200 bg-yolk-100 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <div class="px-2 mb-4" x-data="{
                query: '',
                results: [],
                loading: false,
                async search() {
                    if (this.query.length < 2) {
                        this.results = [];
                        return;
                    }
                    this.loading = true;
                    try {
                        const response = await fetch(`/search?q=${encodeURIComponent(this.query)}`);
                        this.results = await response.json();
                    } catch (e) {
                        console.error('Search failed', e);
                    } finally {
                        this.loading = false;
                    }
                }
            }">
                <div class="relative">
                    <flux:input type="search" placeholder="Recherche globale..." icon="magnifying-glass" x-model="query"
                        @input.debounce.300ms="search" @focus="if(query.length >= 2) search()"
                        class="bg-white/50 dark:bg-zinc-800/50" />

                    <div x-show="(results.length > 0 || loading) && query.length >= 2"
                        class="absolute z-50 w-full mt-2 bg-white dark:bg-zinc-800 rounded-xl shadow-xl border border-yolk-200 dark:border-zinc-700 overflow-hidden"
                        x-cloak x-on:click.away="results = []; loading = false">
                        <div class="p-2 max-h-96 overflow-y-auto">
                            <!-- Loading State -->
                            <div x-show="loading" class="flex items-center justify-center p-4">
                                <flux:icon.loading class="size-6 text-yolk-600" />
                            </div>

                            <!-- Results List -->
                            <template x-show="!loading" x-for="result in results" x-bind:key="result.url">
                                <a x-bind:href="result.url"
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-yolk-100 dark:hover:bg-zinc-700 transition-colors group"
                                    wire:navigate>
                                    <div
                                        class="p-2 rounded-full bg-yolk-50 dark:bg-zinc-900 text-yolk-600 dark:text-yolk-400">
                                        <template x-if="result.type === 'Client'">
                                            <flux:icon name="users" class="size-4" />
                                        </template>
                                        <template x-if="result.type === 'Voiture'">
                                            <flux:icon name="truck" class="size-4" />
                                        </template>
                                        <template x-if="result.type === 'Location'">
                                            <flux:icon name="calendar" class="size-4" />
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold truncate dark:text-white" x-text="result.title"></p>
                                        <p class="text-xs text-zinc-500 truncate"
                                            x-text="result.type + ' • ' + result.subtitle"></p>
                                    </div>
                                </a>
                            </template>

                            <!-- Empty State -->
                            <div x-show="!loading && results.length === 0" class="p-4 text-center">
                                <p class="text-sm text-zinc-500 italic">Aucun résultat trouvé pour "<span
                                        x-text="query"></span>"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <flux:sidebar.group :heading="__('Menu Principal')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                @if(auth()->user()->isAdmin())
                    <flux:sidebar.item icon="shield-check" :href="route('users.index')"
                        :current="request()->routeIs('users.*')" wire:navigate>
                        {{ __('Utilisateurs') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('clients.index')"
                        :current="request()->routeIs('clients.*')" wire:navigate>
                        {{ __('Clients') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('chauffeurs.index')"
                        :current="request()->routeIs('chauffeurs.*')" wire:navigate>
                        {{ __('Chauffeurs') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wrench" :href="route('entretiens.index')"
                        :current="request()->routeIs('entretiens.*')" wire:navigate>
                        {{ __('Entretiens') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="banknotes" :href="route('comptes.index')"
                        :current="request()->routeIs('comptes.*')" wire:navigate>
                        {{ __('Comptes Admin') }}
                    </flux:sidebar.item>
                @endif
                <flux:sidebar.item icon="truck" :href="route('voitures.index')"
                    :current="request()->routeIs('voitures.*')" wire:navigate>
                    {{ __('Voitures') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="calendar" :href="route('locations.index')"
                    :current="request()->routeIs('locations.*')" wire:navigate>
                    {{ __('Locations') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="credit-card" :href="route('paiements.index')"
                    :current="request()->routeIs('paiements.*')" wire:navigate>
                    {{ __('Paiements') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" :href="route('factures.index')"
                    :current="request()->routeIs('factures.*')" wire:navigate>
                    {{ __('Factures') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <div class="px-2 mb-4">
            <form action="{{ route('currency.switch') }}" method="POST"
                class="flex items-center gap-1 p-1 bg-yolk-200/50 dark:bg-zinc-800 rounded-lg">
                @csrf
                <button type="submit" name="currency" value="EUR"
                    class="flex-1 py-1 px-2 text-xs font-bold rounded-md transition-all {{ App\Helpers\CurrencyHelper::getCurrency() === 'EUR' ? 'bg-blue-600 text-white shadow-md' : 'text-yolk-600 hover:bg-yolk-200' }}">
                    EUR (€)
                </button>
                <button type="submit" name="currency" value="MGA"
                    class="flex-1 py-1 px-2 text-xs font-bold rounded-md transition-all {{ App\Helpers\CurrencyHelper::getCurrency() === 'MGA' ? 'bg-red-600 text-white shadow-md' : 'text-yolk-600 hover:bg-yolk-200' }}">
                    MGA (Ar)
                </button>
            </form>
        </div>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.separator />

                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>