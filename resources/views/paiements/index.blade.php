<x-layouts::app :title="__('Paiements')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Paiements</flux:heading>
            <flux:button href="{{ route('paiements.create') }}" wire:navigate icon="plus" variant="primary">Nouveau Paiement</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Location (Client)</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Montant</flux:table.column>
                    <flux:table.column>Mode</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($paiements as $paiement)
                        <flux:table.row>
                            <flux:table.cell>
                                {{ $paiement->location->client->nom }} {{ $paiement->location->client->prenom }} 
                                ({{ \Carbon\Carbon::parse($paiement->location->date_debut)->format('d/m/Y') }})
                            </flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($paiement->montant) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="neutral" inset="left">
                                    {{ ucfirst($paiement->mode_paiement) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('paiements.show', $paiement) }}" wire:navigate icon="eye" size="sm" variant="ghost" />
                                    <flux:button href="{{ route('paiements.edit', $paiement) }}" wire:navigate icon="pencil" size="sm" variant="ghost" />
                                    <form action="{{ route('paiements.destroy', $paiement) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" size="sm" variant="ghost" />
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
