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
            'maintenance_alerts' => \App\Models\Voiture::where('date_prochain_entretien', '<=', now()->addDays(7))
                ->where('statut', '!=', 'en entretien')
                ->get(),
        ];

        $fleet_data = [
            'disponible' => \App\Models\Voiture::where('statut', 'disponible')->count(),
            'louée' => \App\Models\Voiture::where('statut', 'loué')->count(),
            'entretien' => $stats['maintenance_count'],
        ];
    } else {
        $total_locations = \App\Models\Location::where('client_id', $clientId)->get();
        $total_due = $total_locations->sum('tarif_total');
        $total_paid = \App\Models\Paiement::whereHas('location', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })->sum('montant');

        $stats = [
            'clients' => 1,
            'voitures' => \App\Models\Voiture::whereHas('locations', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })->count(),
            'locations' => \App\Models\Location::where('client_id', $clientId)->where('statut', 'en cours')->count(),
            'revenu' => $total_paid,
            'reste_a_payer' => $total_due - $total_paid,
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
    <style>
        @keyframes pan-in {
            from {
                opacity: 0;
                transform: translateX(-32px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pan-up {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pan-in {
            animation: pan-in 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .pan-up {
            animation: pan-up 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .delay-1 {
            animation-delay: 0.10s;
        }

        .delay-2 {
            animation-delay: 0.16s;
        }

        .delay-3 {
            animation-delay: 0.22s;
        }

        .delay-4 {
            animation-delay: 0.30s;
        }

        .delay-5 {
            animation-delay: 0.38s;
        }

        .delay-6 {
            animation-delay: 0.46s;
        }
    </style>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 overflow-y-auto custom-scrollbar">
        <!-- Stats Header (4 Cards for everyone) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="pan-in delay-1 p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            {{ $isAdmin ? "Chiffre d'Affaires" : "Total Payé" }}
                        </p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white truncate">
                            {{ \App\Helpers\CurrencyHelper::format($stats['revenu']) }}
                        </h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-100 dark:bg-blue-900/30 rounded-full text-yolk-600 dark:text-blue-400">
                        <flux:icon name="banknotes" class="size-6" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $isAdmin ? 'text-green-600' : 'text-zinc-500' }} mt-4 font-medium flex items-center gap-1">
                    @if($isAdmin)
                        <flux:icon name="arrow-trending-up" class="size-3" /> +12.5% vs mois dernier
                    @else
                        {{ __("Historique des paiements") }}
                    @endif
                </p>
            </div>

            <div
                class="pan-in delay-2 p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-500 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            {{ $isAdmin ? "Locations Actives" : "Mes Locations" }}
                        </p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white">{{ $stats['locations'] }}</h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-100 dark:bg-emerald-900/30 rounded-full text-yolk-600 dark:text-emerald-400">
                        <flux:icon name="calendar" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-zinc-500 dark:text-neutral-500 mt-4 font-medium italic">
                    {{ $isAdmin ? "En cours d'utilisation" : "Locations en cours" }}
                </p>
            </div>

            <div
                class="pan-in delay-3 p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-400 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            {{ $isAdmin ? "Flotte Totale" : "Reste à Payer" }}
                        </p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white truncate">
                            {{ $isAdmin ? $stats['voitures'] : \App\Helpers\CurrencyHelper::format($stats['reste_a_payer']) }}
                        </h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-50 dark:bg-amber-900/30 rounded-full text-yolk-500 dark:text-amber-400">
                        <flux:icon name="{{ $isAdmin ? 'truck' : 'credit-card' }}" class="size-6" />
                    </div>
                </div>
                <p
                    class="text-xs {{ $isAdmin ? 'text-yolk-600 dark:text-amber-400' : 'text-red-500' }} mt-4 font-medium">
                    @if($isAdmin)
                        {{ $stats['maintenance_count'] }} en maintenance
                    @else
                        {{ __("Solde en attente") }}
                    @endif
                </p>
            </div>

            <div
                class="pan-in delay-4 p-6 rounded-2xl glass shadow-premium border-l-4 border-yolk-300 group hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-sm font-medium text-zinc-500 dark:text-neutral-500 uppercase tracking-wider truncate">
                            {{ $isAdmin ? "Clients Fidèles" : "Véhicules Loués" }}
                        </p>
                        <h3 class="text-3xl font-bold mt-1 dark:text-white">
                            {{ $isAdmin ? $stats['clients'] : $stats['voitures'] }}
                        </h3>
                    </div>
                    <div
                        class="shrink-0 p-3 bg-yolk-100 dark:bg-purple-900/30 rounded-full text-yolk-600 dark:text-purple-400">
                        <flux:icon name="{{ $isAdmin ? 'users' : 'truck' }}" class="size-6" />
                    </div>
                </div>
                <p class="text-xs text-zinc-500 dark:text-neutral-500 mt-4 font-medium">
                    {{ $isAdmin ? "Inscrits au programme" : "Modèles différents" }}
                </p>
            </div>
        </div>

        <!-- Middle Row (Centered Links & Maintenance) -->
        <div class="pan-up delay-5 grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-3' : '' }} gap-6">
            <div class="{{ $isAdmin ? 'lg:col-span-2' : '' }} space-y-4">
                <flux:heading size="lg" class="text-yolk-600">{{ $isAdmin ? "Gestion Rapide" : "Accès Rapide" }}
                </flux:heading>

                @if($isAdmin)
                    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
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
                        <a href="{{ route('chauffeurs.index') }}" wire:navigate
                            class="p-4 rounded-xl border border-yolk-200 dark:border-neutral-800 hover:bg-yolk-200 dark:hover:bg-neutral-800 hover:shadow-md transition-all text-center space-y-3 group">
                            <flux:icon name="user-group" class="size-6 mx-auto text-yolk-600" />
                            <p class="text-sm font-medium">Chauffeurs</p>
                        </a>
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
                    </div>
                @else
                    <!-- Client Quick Access -->
                    <div class="flex flex-col gap-4">
                        <!-- Main CTA: Book a car -->
                        <a href="{{ route('voitures.index') }}" wire:navigate
                            class="flex items-center gap-4 p-5 rounded-2xl bg-gradient-to-r from-yolk-500 to-yolk-400 hover:from-yolk-600 hover:to-yolk-500 shadow-lg shadow-yolk-500/25 transition-all group">
                            <div class="p-3 bg-black/20 rounded-xl">
                                <flux:icon name="truck" class="size-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-black text-black">Réserver une voiture</p>
                                <p class="text-xs text-black/60">Parcourez notre flotte disponible</p>
                            </div>
                            <flux:icon name="arrow-right"
                                class="size-5 text-black/60 group-hover:translate-x-1 transition-transform" />
                        </a>

                        <!-- Secondary links -->
                        <div class="grid grid-cols-3 gap-3">
                            <a href="{{ route('locations.index') }}" wire:navigate
                                class="p-4 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-center space-y-2 group">
                                <flux:icon name="calendar"
                                    class="size-6 mx-auto text-zinc-500 group-hover:text-yolk-600 transition-colors" />
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Mes Locations</p>
                            </a>
                            <a href="{{ route('paiements.index') }}" wire:navigate
                                class="p-4 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-center space-y-2 group">
                                <flux:icon name="credit-card"
                                    class="size-6 mx-auto text-zinc-500 group-hover:text-yolk-600 transition-colors" />
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Paiements</p>
                            </a>
                            <a href="{{ route('factures.index') }}" wire:navigate
                                class="p-4 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all text-center space-y-2 group">
                                <flux:icon name="document-text"
                                    class="size-6 mx-auto text-zinc-500 group-hover:text-yolk-600 transition-colors" />
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Factures</p>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($isAdmin)
                <div class="space-y-4">
                    <flux:heading size="lg" class="text-red-600 flex items-center gap-2">
                        {{ __('Alertes & Maintenance') }}
                        <flux:icon name="wrench-screwdriver"
                            class="size-5 text-red-500 {{ $stats['maintenance_alerts']->isNotEmpty() ? 'animate-pulse' : '' }}" />
                    </flux:heading>
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Upcoming Maintenance Alerts -->
                        @foreach ($stats['maintenance_alerts'] as $alert)
                            <a href="{{ route('entretiens.create', ['voiture_id' => $alert->id]) }}" wire:navigate
                                class="flex items-center gap-3 p-3 rounded-xl bg-orange-100/50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 group hover:shadow-sm transition-all">
                                <flux:icon name="exclamation-triangle" class="size-5 text-orange-600" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-orange-900 dark:text-orange-100 truncate">
                                        {{ $alert->marque }} {{ $alert->modele }}
                                    </p>
                                    <p class="text-xs text-orange-600">
                                        Prévu le {{ \Carbon\Carbon::parse($alert->date_prochain_entretien)->format('d/m') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach

                        <!-- Current Maintenance -->
                        @foreach ($stats['maintenance_cars'] as $car)
                            <div
                                class="flex items-center gap-3 p-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 group hover:bg-zinc-200 transition-colors">
                                <flux:icon name="wrench" class="size-5 text-zinc-600" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $car->marque }} {{ $car->modele }}
                                    </p>
                                    <p class="text-xs text-zinc-500">En cours</p>
                                </div>
                                </a>
                        @endforeach

                            @if ($stats['maintenance_alerts']->isEmpty() && $stats['maintenance_cars']->isEmpty())
                                <div class="flex flex-col items-center justify-center py-8 text-zinc-400">
                                    <flux:icon name="check-circle" class="size-8 mb-2 opacity-20" />
                                    <p class="text-xs italic">{{ __('Aucune alerte prévue') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
            @endif
            </div>

            <!-- Bottom Section (Charts) -->
            <div class="pan-up delay-6 grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-3' : 'lg:grid-cols-1' }} gap-6">
                <div
                    class="{{ $isAdmin ? 'lg:col-span-2' : '' }} p-6 rounded-2xl glass border border-yolk-200 dark:border-neutral-800">
                    <div class="flex items-center justify-between mb-6">
                        <flux:heading size="lg" class="text-yolk-600 dark:text-white">
                            {{ $isAdmin ? "Évolution des Revenus" : "Mes Dépenses" }}
                        </flux:heading>
                        <flux:badge variant="neutral" size="sm">6 derniers mois</flux:badge>
                    </div>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                @if($isAdmin)
                    <div class="p-6 rounded-2xl glass border border-yolk-200 dark:border-neutral-800">
                        <flux:heading size="lg" class="mb-6 text-yolk-600 dark:text-white">Disponibilité de la Flotte
                        </flux:heading>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="fleetChart"></canvas>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <script>
            document.addEventListener('livewire:navigated', () => { initCharts(); });
            document.addEventListener('DOMContentLoaded', () => { initCharts(); });

            function initCharts() {
                const ctxRevenue = document.getElementById('revenueChart');
                const ctxFleet = document.getElementById('fleetChart');
                if (!ctxRevenue) return;

                if (window.revenueChartInstance) window.revenueChartInstance.destroy();
                if (window.fleetChartInstance) window.fleetChartInstance.destroy();

                window.revenueChartInstance = new Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($months) !!},
                        datasets: [{
                            label: '{{ $isAdmin ? "Revenu" : "Dépenses" }} (' + '{{ \App\Helpers\CurrencyHelper::getCurrency() === 'EUR' ? "€" : "Ar" }}' + ')',
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

                if (ctxFleet) {
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
            }
        </script>
</x-layouts::app>