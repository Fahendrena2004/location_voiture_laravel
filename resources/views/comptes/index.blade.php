<x-layouts::app :title="__('Comptes de Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Comptes de Paiement</flux:heading>
            <flux:button as="a" href="{{ route('comptes.create') }}" wire:navigate icon="plus" variant="primary">Nouveau
                Compte</flux:button>
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
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Nom du service</flux:table.column>
                    <flux:table.column>Détails (IBAN, N°...)</flux:table.column>
                    <flux:table.column>Statut</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($comptes as $compte)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    <flux:badge variant="{{ $compte->type === 'bancaire' ? 'info' : 'success' }}"
                                        inset="left">
                                        {{ $compte->type === 'bancaire' ? 'Banque' : 'Mobile Money' }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">
                                {{ $compte->nom }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 250px;" title="{{ $compte->details }}">
                                    {{ $compte->details }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    <flux:badge variant="{{ $compte->actif ? 'success' : 'danger' }}" inset="left">
                                        {{ $compte->actif ? 'Actif' : 'Inactif' }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="outline" size="sm" icon="ellipsis-vertical"
                                        class="rounded-full" />

                                    <flux:menu>
                                        <flux:menu.item href="{{ route('comptes.edit', $compte) }}" wire:navigate
                                            icon="pencil">Modifier</flux:menu.item>

                                        <flux:menu.separator />

                                        <form action="{{ route('comptes.destroy', $compte) }}" method="POST"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?')">
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