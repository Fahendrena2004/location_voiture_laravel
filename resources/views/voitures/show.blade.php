<x-layouts::app :title="__('Détails Voiture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('voitures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Détails de la Voiture</flux:heading>
        </div>
        @if(auth()->user()->isAdmin())
            <flux:button href="{{ route('voitures.edit', $voiture) }}" wire:navigate icon="pencil" variant="primary">
                Modifier</flux:button>
        @endif
    </div>

    <div
        class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
        <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Marque</flux:label>
                <div class="font-medium">{{ $voiture->marque }}</div>
            </div>
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Modèle</flux:label>
                <div class="font-medium">{{ $voiture->modele }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Immatriculation</flux:label>
                <div class="font-medium">{{ $voiture->immatriculation }}</div>
            </div>
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Couleur</flux:label>
                <div class="font-medium">{{ $voiture->couleur ?? 'Non spécifiée' }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Prix Journalier</flux:label>
                <div class="font-medium">{{ number_format($voiture->prix_journalier, 2) }} €</div>
            </div>
            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Statut</flux:label>
                <div>
                    <flux:badge
                        variant="{{ $voiture->statut === 'disponible' ? 'success' : ($voiture->statut === 'louée' ? 'warning' : 'danger') }}"
                        inset="left">
                        {{ ucfirst($voiture->statut) }}
                    </flux:badge>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-layouts::app>