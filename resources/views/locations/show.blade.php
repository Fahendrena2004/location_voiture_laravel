<x-layouts::app :title="__('Détails Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Détails de la Location</flux:heading>
            </div>
            <div class="flex gap-2">
                @if($location->facture)
                    <flux:button href="{{ route('factures.show', $location->facture) }}" wire:navigate icon="document-text" variant="ghost">Voir Facture</flux:button>
                @else
                    <flux:button href="{{ route('factures.create', ['location_id' => $location->id]) }}" wire:navigate icon="plus" variant="ghost">Générer Facture</flux:button>
                @endif
                <flux:button href="{{ route('locations.edit', $location) }}" wire:navigate icon="pencil" variant="primary">Modifier</flux:button>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Client</flux:label>
                    <div class="font-medium">
                        <a href="{{ route('clients.show', $location->client) }}" wire:navigate class="text-primary hover:underline">
                            {{ $location->client->nom }} {{ $location->client->prenom }}
                        </a>
                    </div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Voiture</flux:label>
                    <div class="font-medium">
                        <a href="{{ route('voitures.show', $location->voiture) }}" wire:navigate class="text-primary hover:underline">
                            {{ $location->voiture->marque }} {{ $location->voiture->modele }} ({{ $location->voiture->immatriculation }})
                        </a>
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

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Tarif Total</flux:label>
                    <div class="font-medium">
                        {{ number_format($location->tarif_total, 2) }} 
                        {{ App\Helpers\CurrencyHelper::getCurrency() === 'MGA' ? 'Ar' : '€' }}
                    </div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Statut</flux:label>
                    <div>
                        <flux:badge variant="{{ $location->statut === 'en cours' ? 'warning' : ($location->statut === 'terminée' ? 'success' : 'danger') }}" inset="left">
                            {{ ucfirst($location->statut) }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            @if($location->avec_chauffeur)
            <div class="border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">Chauffeur</flux:label>
                <div class="font-medium">
                    @if($location->chauffeur)
                        <a href="{{ route('chauffeurs.show', $location->chauffeur) }}" wire:navigate class="text-primary hover:underline">
                            {{ $location->chauffeur->nom }} {{ $location->chauffeur->prenom }}
                        </a>
                        <span class="text-neutral-500 text-sm italic ml-2">({{ $location->chauffeur->telephone }})</span>
                    @else
                        <span class="text-red-500 italic">Chauffeur non assigné</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts::app>
