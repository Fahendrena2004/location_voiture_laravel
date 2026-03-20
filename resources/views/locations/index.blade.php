<x-layouts::app :title="__('Locations')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Locations</flux:heading>
            <flux:button href="{{ route('locations.create') }}" wire:navigate icon="plus" variant="primary">Nouvelle Location</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Client</flux:table.column>
                    <flux:table.column>Voiture</flux:table.column>
                    <flux:table.column>Début</flux:table.column>
                    <flux:table.column>Fin</flux:table.column>
                    <flux:table.column>Tarif Total</flux:table.column>
                    <flux:table.column>Chauffeur</flux:table.column>
                    <flux:table.column>Statut</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($locations as $location)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 200px;" title="{{ $location->client->nom }} {{ $location->client->prenom }}">
                                    {{ $location->client->nom }} {{ $location->client->prenom }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 300px;" title="{{ $location->voiture->marque }} {{ $location->voiture->modele }}">
                                    {{ $location->voiture->marque }} {{ $location->voiture->modele }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($location->date_fin)->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($location->tarif_total) }}</flux:table.cell>
                            <flux:table.cell>
                                @if($location->avec_chauffeur && $location->chauffeur)
                                    <div class="flex items-center justify-center gap-1">
                                        <flux:icon name="user" size="sm" class="text-neutral-400" />
                                        <span>{{ $location->chauffeur->nom }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-start">
                                        <flux:badge variant="ghost" size="sm">Sans chauffeur</flux:badge>
                                    </div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    <flux:badge variant="{{ $location->statut === 'en cours' ? 'warning' : ($location->statut === 'terminée' ? 'success' : 'danger') }}" inset="left">
                                        {{ ucfirst($location->statut) }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('locations.show', $location) }}" wire:navigate icon="eye" size="sm" variant="ghost" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300" />
                                    <flux:button href="{{ route('locations.edit', $location) }}" wire:navigate icon="pencil" size="sm" variant="ghost" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" />
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" size="sm" variant="ghost" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" />
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>
