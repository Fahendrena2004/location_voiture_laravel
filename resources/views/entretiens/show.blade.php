<x-layouts::app :title="__('Détails Entretien')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('entretiens.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Détails de l'Entretien</flux:heading>
            </div>
            <flux:button href="{{ route('entretiens.edit', $entretien) }}" wire:navigate icon="pencil" variant="primary">Modifier</flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">Voiture</flux:label>
                <div class="font-medium">
                    <a href="{{ route('voitures.show', $entretien->voiture) }}" wire:navigate class="text-primary hover:underline">
                        {{ $entretien->voiture->marque }} {{ $entretien->voiture->modele }} ({{ $entretien->voiture->immatriculation }})
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Date de l'entretien</flux:label>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($entretien->date_entretien)->format('d/m/Y') }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Coût</flux:label>
                    <div class="font-medium">{{ number_format($entretien->cout, 2) }} €</div>
                </div>
            </div>

            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Description des travaux</flux:label>
                <div class="font-medium whitespace-pre-line">{{ $entretien->description }}</div>
            </div>
        </div>
    </div>
</x-layouts::app>
