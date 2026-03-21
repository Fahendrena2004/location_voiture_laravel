@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin();
    $clientId = $user->client->id ?? null;

    if ($isAdmin) {
        $stats = [
            'clients' => \App\Models\Client::count(),
            'voitures' => \App\Models\Voiture::count(),
            'locations' => \App\Models\Location::where('statut', 'en cours')->count(),
            'revenu' => \App\Models\Paiement::sum('montant'),
            'maintenance_count' => \App\Models\Voiture::where('statut', 'en entretien')->count(),
            'maintenance_cars' => \App\Models\Voiture::where('statut', 'en entretien')->get(),
        ];

        $fleet_data = [
            'disponible' => \App\Models\Voiture::where('statut', 'disponible')->count(),
            'louée' => \App\Models\Voiture::where('statut', 'loué')->count(),
            'entretien' => $stats['maintenance_count'],
        ];
    } else {
        $stats = [
            'clients' => 1,
            'voitures' => \App\Models\Location::where('client_id', $clientId)->distinct('voiture_id')->count(),
            'locations' => \App\Models\Location::where('client_id', $clientId)->where('statut', 'en cours')->count(),
            'revenu' => \App\Models\Paiement::whereHas('location', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })->sum('montant'),
            'maintenance_count' => 0,
            'maintenance_cars' => collect(),
        ];

        $fleet_data = [
            'disponible' => 0,
            'louée' => $stats['locations'],
            'entretien' => 0,
        ];
    }

    // Monthly Revenu (Last 6 months)
    $months = [];
    $revenues = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $months[] = $date->translatedFormat('M');
        $query = \App\Models\Paiement::whereYear('date_paiement', $date->year)
            ->whereMonth('date_paiement', $date->month);

        if (!$isAdmin) {
            $query->whereHas('location', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            });
        }

        $revenues[] = $query->sum('montant');
    }
@endphp

<x-layouts::app :title="__('Tableau de Bord')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 overflow-y-auto custom-scrollbar">
        <!-- Stats Header (Restored to Large Model) -->
        @if($isAdmin)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">Chiffre d'Affaires</p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white truncate">
                            {{ \App\Helpers\CurrencyHelper::format($stats['revenu']) }}</h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-100 dark:bg-blue-900/30 rounded-full text-yolk-600 dark:text-blue-400">
                        <flux:icon name="banknotes" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-4 font-medium flex items-center gap-1">
                    <flux:icon name="arrow-trending-up" class="size-3" /> +12.5% vs mois dernier
                </p>
            </div>

            <div
                class="p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            Locations Actives</p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white">{{ $stats['locations'] }}</h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-100 dark:bg-emerald-900/30 rounded-full text-yolk-600 dark:text-emerald-400">
                        <flux:icon name="calendar" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-zinc-500 dark:text-neutral-500 mt-4 font-medium italic">En cours d'utilisation
                </p>
            </div>

            <div
                class="p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-400 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            Flotte Totale</p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white">{{ $stats['voitures'] }}</h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-50 dark:bg-amber-900/30 rounded-full text-yolk-500 dark:text-amber-400">
                        <flux:icon name="truck" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-yolk-600 dark:text-amber-400 mt-4 font-medium">{{ $stats['maintenance_count'] }}
                    en maintenance</p>
            </div>

            <div class="p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-300 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">Clients Fidèles</p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white">{{ $stats['clients'] }}</h3>
                    </div>
                    <div class="shrink-0 p-3 bg-yolk-100 dark:bg-purple-900/30 rounded-full text-yolk-600 dark:text-purple-400">
                        <flux:icon name="users" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-zinc-500 dark:text-neutral-500 mt-4 font-medium">Inscrits au programme</p>
            </div>
        </div>
        @endif

        <!-- Middle Row (Centered Links & Maintenance) -->
        <div class="grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-3' : '' }} gap-6">
            <div class="{{ $isAdmin ? 'lg:col-span-2' : '' }} space-y-4">
                <flux:heading size="lg" class="text-yolk-600">{{ $isAdmin ? "Gestion Rapide" : "Mes Documents" }}</flux:heading>
                <div class="grid grid-cols-2 lg:grid-cols-{{ $isAdmin ? '6' : '3' }} gap-4">
                    @if($isAdmin)
                    <a href="{{ route('clients.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="users" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Clients</p>
                    </a>
                    <a href="{{ route('voitures.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="truck" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Véhicules</p>
                    </a>
                    <a href="{{ route('entretiens.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="wrench" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Entretiens</p>
                    </a>
                    @endif
                    <a href="{{ route('locations.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="calendar" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Locations</p>
                    </a>
                    <a href="{{ route('paiements.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="credit-card" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Paiements</p>
                    </a>
                    <a href="{{ route('factures.index') }}" wire:navigate
                        class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                        <flux:icon name="document-text" class="size-6 mx-auto text-yolk-600" />
                        <p class="text-sm font-medium">Factures</p>
                    </a>
                </div>
            </div>

            @if($isAdmin)
            <div class="space-y-4">
                <flux:heading size="lg" class="text-yolk-600">Maintenance</flux:heading>
                <div class="space-y-3">
                    @php $maintenance_cars = $stats['maintenance_cars']; @endphp
                    @forelse ($maintenance_cars as $car)
                        <div
                            class="flex items-center gap-3 p-3 rounded-xl bg-yolk-100 dark:bg-amber-900/20 border border-yolk-200 dark:border-amber-900/30 group hover:bg-yolk-200 transition-colors">
                            <flux:icon name="wrench" class="size-5 text-yolk-600" />
                            <div>
                                <p class="text-sm font-bold text-yolk-900 dark:text-amber-100">{{ $car->marque }}
                                    {{ $car->modele }}</p>
                                <p class="text-xs text-yolk-600">En entretien</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 rounded-xl border border-dashed border-yolk-300 opacity-60">
                            <flux:icon name="check-circle" class="size-10 mx-auto text-yolk-400 mb-2" />
                            <p class="text-xs text-zinc-500">Aucune alerte</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Bottom Section (Charts) -->
        @if($isAdmin)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 p-6 rounded-2xl glass border border-yolk-200 dark:border-neutral-800">
                <div class="flex items-center justify-between mb-6">
                    <flux:heading size="lg" class="text-yolk-600 dark:text-white">Évolution des Revenus</flux:heading>
                    <flux:badge variant="neutral" size="sm">6 derniers mois</flux:badge>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="p-6 rounded-2xl glass border border-yolk-200 dark:border-neutral-800">
                <flux:heading size="lg" class="mb-6 text-yolk-600 dark:text-white">Disponibilité de la Flotte
                </flux:heading>
                <div class="h-64 relative flex items-center justify-center">
                    <canvas id="fleetChart"></canvas>
                </div>
            </div>
        </div>
        @endif

    </div>

    @if($isAdmin)
    <script>
        document.addEventListener('livewire:navigated', () => { initCharts(); });
        document.addEventListener('DOMContentLoaded', () => { initCharts(); });

        function initCharts() {
            const ctxRevenue = document.getElementById('revenueChart');
            const ctxFleet = document.getElementById('fleetChart');
            if (!ctxRevenue || !ctxFleet) return;

            if (window.revenueChartInstance) window.revenueChartInstance.destroy();
            if (window.fleetChartInstance) window.fleetChartInstance.destroy();

            window.revenueChartInstance = new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Revenu (' + '{{ \App\Helpers\CurrencyHelper::getCurrency() === 'EUR' ? "€" : "Ar" }}' + ')',
                        data: {!! json_encode($revenues) !!},
                        borderColor: '#fbbf24',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fbbf24',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            window.fleetChartInstance = new Chart(ctxFleet, {
                type: 'doughnut',
                data: {
                    labels: ['Disponible', 'Louée', 'Maintenance'],
                    datasets: [{
                        data: [{!! $fleet_data['disponible'] !!}, {!! $fleet_data['louée'] !!}, {!! $fleet_data['entretien'] !!}],
                        backgroundColor: ['#10b981', '#3b82f6', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    </script>
    @endif
</x-layouts::app>