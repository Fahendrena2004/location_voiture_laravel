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

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nom</flux:table.column>
                    <flux:table.column>Prénom</flux:table.column>
                    <flux:table.column>Téléphone</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($clients as $client)
                        <flux:table.row>
                            <flux:table.cell>{{ $client->nom }}</flux:table.cell>
                            <flux:table.cell>{{ $client->prenom }}</flux:table.cell>
                            <flux:table.cell>{{ $client->telephone }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('clients.show', $client) }}" wire:navigate icon="eye" size="sm" variant="ghost" />
                                    <flux:button href="{{ route('clients.edit', $client) }}" wire:navigate icon="pencil" size="sm" variant="ghost" />
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
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
