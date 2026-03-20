<x-layouts::app :title="__('Factures')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Factures</flux:heading>
            <flux:button as="a" href="{{ route('factures.create') }}" wire:navigate icon="plus" variant="primary">Générer une Facture</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>N° Facture</flux:table.column>
                    <flux:table.column>Client</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Montant</flux:table.column>
                    <flux:table.column>Statut</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($factures as $facture)
                        <flux:table.row>
                            <flux:table.cell class="font-medium">{{ $facture->numero_facture }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 250px;" title="{{ $facture->location->client->nom }} {{ $facture->location->client->prenom }}">
                                    {{ $facture->location->client->nom }} {{ $facture->location->client->prenom }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($facture->montant_total) }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    <flux:badge variant="{{ $facture->statut === 'payée' ? 'success' : ($facture->statut === 'en attente' ? 'warning' : 'danger') }}" inset="left">
                                        {{ ucfirst($facture->statut) }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button as="a" href="{{ route('factures.show', $facture) }}" wire:navigate icon="eye" size="sm" variant="ghost" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300" />
                                    <flux:button as="a" href="{{ route('factures.edit', $facture) }}" wire:navigate icon="pencil" size="sm" variant="ghost" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" />
                                    <form action="{{ route('factures.destroy', $facture) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
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
