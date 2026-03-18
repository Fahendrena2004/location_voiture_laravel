<x-layouts::app :title="__('Chauffeurs')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Gestion des Chauffeurs') }}</flux:heading>
            <flux:button href="{{ route('chauffeurs.create') }}" wire:navigate icon="plus" variant="primary">{{ __('Nouveau Chauffeur') }}</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nom') }}</flux:table.column>
                    <flux:table.column>{{ __('Prénom') }}</flux:table.column>
                    <flux:table.column>{{ __('Téléphone') }}</flux:table.column>
                    <flux:table.column>{{ __('Pérmis') }}</flux:table.column>
                    <flux:table.column>{{ __('Disponibilité') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($chauffeurs as $chauffeur)
                        <flux:table.row>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">{{ $chauffeur->nom }}</flux:table.cell>
                            <flux:table.cell>{{ $chauffeur->prenom }}</flux:table.cell>
                            <flux:table.cell>{{ $chauffeur->telephone }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="ghost" size="sm">{{ $chauffeur->categorie_permis }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($chauffeur->disponible)
                                    <flux:badge color="green" inset="left">{{ __('Disponible') }}</flux:badge>
                                @else
                                    <flux:badge color="red" inset="left">{{ __('Indisponible') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('chauffeurs.show', $chauffeur) }}" wire:navigate icon="eye" size="sm" variant="ghost" />
                                    <flux:button href="{{ route('chauffeurs.edit', $chauffeur) }}" wire:navigate icon="pencil" size="sm" variant="ghost" />
                                    <form action="{{ route('chauffeurs.destroy', $chauffeur) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce chauffeur ?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" size="sm" variant="ghost" class="text-red-500 hover:text-red-600" />
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
