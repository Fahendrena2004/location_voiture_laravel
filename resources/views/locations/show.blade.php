<x-layouts::app :title="__('Détails Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Détails de la Location</flux:heading>
            </div>
            <div class="flex gap-2">
                @if($location->facture)
                    <flux:button as="a" href="{{ route('factures.show', $location->facture) }}" wire:navigate
                        icon="document-text" variant="ghost">Voir Facture</flux:button>
                @else
                    <flux:button as="a" href="{{ route('factures.create', ['location_id' => $location->id]) }}"
                        wire:navigate icon="plus" variant="ghost">Générer Facture</flux:button>
                @endif
                <flux:button href="{{ route('locations.edit', $location) }}" wire:navigate icon="pencil"
                    variant="primary">Modifier</flux:button>
            </div>
        </div>

        <div
            class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Client</flux:label>
                    <div class="font-medium">
                        <a href="{{ route('clients.show', $location->client) }}" wire:navigate
                            class="text-primary hover:underline">
                            {{ $location->client->nom }} {{ $location->client->prenom }}
                        </a>
                    </div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Voitures</flux:label>
                    <div class="font-medium space-y-1 mt-1">
                        @foreach($location->voitures as $voiture)
                            <div>
                                <a href="{{ route('voitures.show', $voiture) }}" wire:navigate
                                    class="text-primary hover:underline">
                                    {{ $voiture->marque }} {{ $voiture->modele }}
                                </a>
                                <span class="text-xs text-neutral-500">({{ $voiture->immatriculation }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Date de début</flux:label>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Date de fin</flux:label>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}</div>
                </div>
            </div>

            @if($location->date_retour)
                <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                    <div>
                        <flux:label class="text-xs uppercase text-neutral-500">Date de retour réelle</flux:label>
                        <div class="font-medium">{{ \Carbon\Carbon::parse($location->date_retour)->format('d/m/Y') }}</div>
                    </div>
                    @if($location->penalite > 0)
                        <div>
                            <flux:label class="text-xs uppercase text-red-500">Pénalité de retard</flux:label>
                            <div class="font-medium text-red-500">
                                + {{ \App\Helpers\CurrencyHelper::format($location->penalite) }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Tarif Total</flux:label>
                    <div class="font-medium">
                        {{ \App\Helpers\CurrencyHelper::format($location->tarif_total) }}
                    </div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Statut</flux:label>
                    <div>
                        <flux:badge
                            variant="{{ $location->statut === 'en cours' ? 'warning' : ($location->statut === 'terminée' ? 'success' : 'danger') }}"
                            inset="left">
                            {{ ucfirst($location->statut) }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            @if($location->avec_chauffeur)
                <div class="border-b pb-4 border-neutral-100 dark:border-neutral-700">
                    <flux:label class="text-xs uppercase text-neutral-500">Chauffeur(s) assigné(s)</flux:label>
                    <div class="font-medium space-y-1 mt-1">
                        @if($location->chauffeurs->isNotEmpty())
                            @foreach($location->chauffeurs as $chauffeur)
                                <div>
                                    <flux:icon name="user" size="sm" class="inline text-neutral-400 mr-1" />
                                    <a href="{{ route('chauffeurs.show', $chauffeur) }}" wire:navigate
                                        class="text-primary hover:underline">
                                        {{ $chauffeur->nom }} {{ $chauffeur->prenom }}
                                    </a>
                                    <span class="text-neutral-500 text-sm italic ml-2">({{ $chauffeur->telephone }})</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-amber-600 text-sm italic">Aucun chauffeur assigné pour le moment.</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>