<x-layouts::app :title="__('Clients')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Clients</flux:heading>
            <flux:button href="{{ route('clients.create') }}" wire:navigate icon="plus" variant="primary">Nouveau Client</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Nom / Raison Sociale</flux:table.column>
                    <flux:table.column>Prénom</flux:table.column>
                    <flux:table.column>Téléphone</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($clients as $client)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    @if($client->type === 'personne')
                                        <flux:badge icon="user" variant="ghost" size="sm" class="text-blue-600">Pers.</flux:badge>
                                    @else
                                        <flux:badge icon="building-office" variant="ghost" size="sm" class="text-emerald-600">Assoc.</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                <div class="truncate" style="max-width: 250px;" title="{{ $client->type === 'association' ? $client->raison_sociale : $client->nom }}">
                                    {{ $client->type === 'association' ? $client->raison_sociale : $client->nom }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 150px;" title="{{ $client->type === 'personne' ? ($client->prenom ?? '-') : '-' }}">
                                    {{ $client->type === 'personne' ? ($client->prenom ?? '-') : '-' }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $client->telephone }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('clients.show', $client) }}" wire:navigate icon="eye" size="sm" variant="ghost" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300" />
                                    <flux:button href="{{ route('clients.edit', $client) }}" wire:navigate icon="pencil" size="sm" variant="ghost" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" />
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
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
