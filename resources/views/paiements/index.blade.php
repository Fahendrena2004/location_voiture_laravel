<x-layouts::app :title="__('Paiements')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Paiements</flux:heading>
            <flux:button href="{{ route('paiements.create') }}" wire:navigate icon="plus" variant="primary">Nouveau
                Paiement</flux:button>
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
                            <flux:table.cell>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                            </flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($paiement->montant) }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <flux:badge variant="neutral" inset="left" class="w-fit">
                                        {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                                    </flux:badge>
                                    @if($paiement->mode_paiement === 'mobile_money' && $paiement->numero_mobile)
                                        <span class="text-xs text-neutral-500 mt-1">N° {{ $paiement->numero_mobile }}</span>
                                    @elseif($paiement->mode_paiement === 'bancaire')
                                        <span class="text-xs text-neutral-500 mt-1">Bordereau :
                                            {{ $paiement->numero_bordereau }} @if($paiement->nom_banque) (Banque :
                                            {{ $paiement->nom_banque }}) @endif</span>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="outline" size="sm" icon="ellipsis-vertical"
                                        class="rounded-full" />

                                    <flux:menu>
                                        <flux:menu.item href="{{ route('paiements.show', $paiement) }}" wire:navigate
                                            icon="eye">Voir</flux:menu.item>
                                        <flux:menu.item href="{{ route('paiements.edit', $paiement) }}" wire:navigate
                                            icon="pencil">Modifier</flux:menu.item>

                                        <flux:menu.separator />

                                        <form action="{{ route('paiements.destroy', $paiement) }}" method="POST"
                                            onsubmit="return confirm('Êtes-vous sûr ?')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:menu.item type="submit" as="button" icon="trash" variant="danger">
                                                Supprimer</flux:menu.item>
                                        </form>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>