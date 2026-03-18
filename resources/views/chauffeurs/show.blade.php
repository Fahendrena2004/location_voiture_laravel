<x-layouts::app :title="__('Détails Chauffeur')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('chauffeurs.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">{{ __('Détails du Chauffeur') }}</flux:heading>
            </div>
            <flux:button href="{{ route('chauffeurs.edit', $chauffeur) }}" wire:navigate icon="pencil" variant="primary">{{ __('Modifier') }}</flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="flex items-center justify-between border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">{{ __('Disponibilité') }}</flux:label>
                @if($chauffeur->disponible)
                    <flux:badge color="green" inset="left">{{ __('Disponible') }}</flux:badge>
                @else
                    <flux:badge color="red" inset="left">{{ __('Indisponible') }}</flux:badge>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">{{ __('Nom') }}</flux:label>
                    <div class="font-medium text-lg">{{ $chauffeur->nom }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">{{ __('Prénom') }}</flux:label>
                    <div class="font-medium text-lg">{{ $chauffeur->prenom }}</div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">{{ __('Téléphone') }}</flux:label>
                    <div class="font-medium text-lg">{{ $chauffeur->telephone }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">{{ __('Catégorie de Permis') }}</flux:label>
                    <div class="font-medium text-lg">{{ $chauffeur->categorie_permis }}</div>
                </div>
            </div>

            <div class="pt-4">
                <flux:heading size="lg" class="mb-4">{{ __('Historique des Locations') }}</flux:heading>
                @if($chauffeur->locations->isEmpty())
                    <flux:text class="italic">{{ __('Aucune location enregistrée pour ce chauffeur.') }}</flux:text>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Date') }}</flux:table.column>
                            <flux:table.column>{{ __('Véhicule') }}</flux:table.column>
                            <flux:table.column>{{ __('Statut') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($chauffeur->locations->take(5) as $location)
                                <flux:table.row>
                                    <flux:table.cell>{{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}</flux:table.cell>
                                    <flux:table.cell>{{ $location->voiture->marque }} {{ $location->voiture->modele }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge variant="{{ $location->statut === 'en cours' ? 'warning' : ($location->statut === 'terminée' ? 'success' : 'danger') }}" size="sm">
                                            {{ ucfirst($location->statut) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
