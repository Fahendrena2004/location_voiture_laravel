<x-layouts::app :title="__('Locations')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Locations</flux:heading>
            @if(auth()->user()->isAdmin())
                <flux:button href="{{ route('locations.create') }}" wire:navigate icon="plus" variant="primary">Nouvelle
                    Location</flux:button>
            @else
                <flux:button href="{{ route('voitures.index') }}" wire:navigate icon="plus" variant="primary">Réserver une
                    voiture</flux:button>
            @endif
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Client</flux:table.column>
                    <flux:table.column>Voiture</flux:table.column>
                    <flux:table.column>Début</flux:table.column>
                    <flux:table.column>Fin</flux:table.column>
                    <flux:table.column>Tarif Total</flux:table.column>
                    <flux:table.column>Chauffeur</flux:table.column>
                    <flux:table.column>Statut & Progression</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($locations as $location)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 200px;"
                                    title="{{ $location->client->nom }} {{ $location->client->prenom }}">
                                    {{ $location->client->nom }} {{ $location->client->prenom }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate space-y-1" style="max-width: 300px;">
                                    @foreach($location->voitures as $voiture)
                                        <div title="{{ $voiture->marque }} {{ $voiture->modele }}" class="text-sm">
                                            {{ $voiture->marque }} {{ $voiture->modele }} <span
                                                class="text-xs text-neutral-500">({{ $voiture->immatriculation }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}
                            </flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}
                            </flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($location->tarif_total) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($location->avec_chauffeur)
                                    @if($location->chauffeurs->isNotEmpty())
                                        <div class="flex -space-x-2">
                                            @foreach($location->chauffeurs as $chauffeur)
                                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 border-2 border-white dark:border-neutral-900 text-xs font-bold text-blue-600 dark:text-blue-300"
                                                    title="{{ $chauffeur->nom }} {{ $chauffeur->prenom }}">
                                                    {{ substr($chauffeur->nom, 0, 1) }}{{ substr($chauffeur->prenom, 0, 1) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <flux:badge variant="ghost" size="sm" class="text-amber-600">En attente de chauffeur
                                        </flux:badge>
                                    @endif
                                @else
                                    <div class="flex justify-start">
                                        <flux:badge variant="ghost" size="sm">Sans chauffeur</flux:badge>
                                    </div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col gap-2 min-w-[150px]">
                                    <div class="flex justify-start">
                                        <flux:badge
                                            variant="{{ $location->statut === 'en cours' ? 'warning' : ($location->statut === 'terminée' ? 'success' : 'danger') }}"
                                            inset="left">
                                            {{ ucfirst($location->statut) }}
                                        </flux:badge>
                                    </div>

                                    @if($location->statut === 'en cours' || $location->statut === 'terminée')
                                        @php
                                            $start = \Carbon\Carbon::parse($location->date_debut);
                                            $end = \Carbon\Carbon::parse($location->date_fin);
                                            $now = now();
                                            $total = $start->diffInDays($end) ?: 1;
                                            $elapsed = $start->diffInDays($now);
                                            $percent = $location->statut === 'terminée' ? 100 : min(100, max(0, ($elapsed / $total) * 100));
                                        @endphp
                                        <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                            <div class="h-full {{ $percent >= 100 ? 'bg-green-500' : 'bg-yolk-500' }} transition-all duration-500"
                                                style="width: {{ $percent }}%"></div>
                                        </div>
                                        <p class="text-[10px] text-zinc-500 font-medium">{{ round($percent) }}% complété</p>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:dropdown>
                                        <flux:button variant="outline" size="sm" icon="ellipsis-vertical"
                                            class="rounded-full" />

                                        <flux:menu>
                                            <flux:menu.item href="{{ route('locations.show', $location) }}" wire:navigate
                                                icon="eye">Voir</flux:menu.item>

                                            @if(auth()->user()->isAdmin())
                                                <flux:menu.item href="{{ route('locations.edit', $location) }}" wire:navigate
                                                    icon="pencil">Modifier</flux:menu.item>

                                                <flux:menu.separator />

                                                <form action="{{ route('locations.destroy', $location) }}" method="POST"
                                                    onsubmit="return confirm('Êtes-vous sûr ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:menu.item type="submit" as="button" icon="trash" variant="danger">
                                                        Supprimer</flux:menu.item>
                                                </form>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>

                                    @if(auth()->user()->isAdmin() && $location->statut === 'en attente')
                                        <div class="flex gap-1">
                                            <form action="{{ route('locations.approve', $location) }}" method="POST">
                                                @csrf
                                                <flux:button type="submit" size="sm" variant="filled"
                                                    class="bg-green-500 hover:bg-green-600 text-white border-0" icon="check"
                                                    title="Valider la réservation" />
                                            </form>
                                            <form action="{{ route('locations.reject', $location) }}" method="POST"
                                                onsubmit="return confirm('Refuser cette réservation ?')">
                                                @csrf
                                                <flux:button type="submit" size="sm" variant="filled"
                                                    class="bg-red-500 hover:bg-red-600 text-white border-0" icon="x-mark"
                                                    title="Refuser" />
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>