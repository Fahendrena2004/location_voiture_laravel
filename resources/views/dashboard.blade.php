@php
    $stats = [
        'clients' => \App\Models\Client::count(),
        'voitures' => \App\Models\Voiture::count(),
        'locations' => \App\Models\Location::where('statut', 'en cours')->count(),
        'revenu' => \App\Models\Paiement::sum('montant'),
        'maintenance_count' => \App\Models\Voiture::where('statut', 'en entretien')->count(),
        'maintenance_cars' => \App\Models\Voiture::where('statut', 'en entretien')->get(),
    ];
@endphp

<x-layouts::app :title="__('Tableau de Bord')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 rounded-xl p-4">
        <!-- Stats Header -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl glass shadow-premium border-l-4 border-blue-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Chiffre d'Affaires</p>
                        <h3 class="text-3xl font-bold mt-1">{{ number_format($stats['revenu'], 2) }} €</h3>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-xl text-blue-600">
                        <flux:icon name="banknotes" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-4 font-medium flex items-center gap-1">
                    <flux:icon name="arrow-trending-up" class="size-3" /> +12.5% vs mois dernier
                </p>
            </div>

            <div class="p-6 rounded-2xl glass shadow-premium border-l-4 border-emerald-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Locations Actives</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $stats['locations'] }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl text-emerald-600">
                        <flux:icon name="calendar" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 mt-4 font-medium italic">En cours d'utilisation</p>
            </div>

            <div class="p-6 rounded-2xl glass shadow-premium border-l-4 border-amber-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Flotte Totale</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $stats['voitures'] }}</h3>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/30 rounded-xl text-amber-600">
                        <flux:icon name="truck" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-amber-600 mt-4 font-medium">{{ $stats['maintenance_count'] }} en maintenance</p>
            </div>

            <div class="p-6 rounded-2xl glass shadow-premium border-l-4 border-purple-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-neutral-500 uppercase tracking-wider">Clients Fidèles</p>
                        <h3 class="text-3xl font-bold mt-1">{{ $stats['clients'] }}</h3>
                    </div>
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/30 rounded-xl text-purple-600">
                        <flux:icon name="users" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 mt-4 font-medium">Inscrits au programme</p>
            </div>
        </div>

        <!-- Quick Access Grid -->
        <div class="space-y-4">
            <flux:heading size="lg">Gestion Rapide</flux:heading>
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                <a href="{{ route('clients.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="users" class="size-6 mx-auto text-neutral-400 group-hover:text-primary" />
                    <p class="text-sm font-medium">Clients</p>
                </a>
                <a href="{{ route('voitures.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="truck" class="size-6 mx-auto text-neutral-400" />
                    <p class="text-sm font-medium">Véhicules</p>
                </a>
                <a href="{{ route('entretiens.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="wrench" class="size-6 mx-auto text-neutral-400" />
                    <p class="text-sm font-medium">Entretiens</p>
                </a>
                <a href="{{ route('locations.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="calendar" class="size-6 mx-auto text-neutral-400" />
                    <p class="text-sm font-medium">Locations</p>
                </a>
                <a href="{{ route('paiements.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="credit-card" class="size-6 mx-auto text-neutral-400" />
                    <p class="text-sm font-medium">Paiements</p>
                </a>
                <a href="{{ route('factures.index') }}" wire:navigate class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 hover:bg-white dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3">
                    <flux:icon name="document-text" class="size-6 mx-auto text-neutral-400" />
                    <p class="text-sm font-medium">Factures</p>
                </a>
            </div>
        </div>

        <!-- Recent Activity Placeholder -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 p-6 rounded-2xl glass border border-neutral-200 dark:border-neutral-800">
                <flux:heading size="lg" class="mb-4">Disponibilité de la Flotte</flux:heading>
                <div class="h-64 relative overflow-hidden rounded-xl bg-neutral-50 dark:bg-neutral-900 flex items-center justify-center border border-dashed border-neutral-300 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-neutral-200 dark:stroke-neutral-800" />
                    <p class="relative z-10 text-neutral-400 text-sm">Graphique d'utilisation bientôt disponible</p>
                </div>
            </div>
            <div class="p-6 rounded-2xl glass border border-neutral-200 dark:border-neutral-800">
                <flux:heading size="lg" class="mb-4">Alertes Maintenance</flux:heading>
                <div class="space-y-4">
                    @forelse($stats['maintenance_cars'] as $car)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30">
                            <flux:icon name="wrench" class="size-5 text-amber-600" />
                            <div>
                                <p class="text-sm font-medium text-amber-900 dark:text-amber-100">{{ $car->marque }} {{ $car->modele }}</p>
                                <p class="text-xs text-amber-600 italic">Maintenance préventive</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <flux:icon name="check-circle" class="size-12 mx-auto text-green-400 mb-2" />
                            <p class="text-sm text-neutral-500">Tout les véhicules sont opérationnels</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>