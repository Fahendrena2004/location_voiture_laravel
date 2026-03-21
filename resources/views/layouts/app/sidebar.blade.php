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
            <flux:sidebar.group :heading="__('Menu Principal')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                @if(auth()->user()->isAdmin())
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