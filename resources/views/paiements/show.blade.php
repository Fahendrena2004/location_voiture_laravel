<x-layouts::app :title="__('Détails Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('paiements.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Détails du Paiement</flux:heading>
            </div>
            <flux:button href="{{ route('paiements.edit', $paiement) }}" wire:navigate icon="pencil" variant="primary">Modifier</flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">Location</flux:label>
                <div class="font-medium">
                    <a href="{{ route('locations.show', $paiement->location) }}" wire:navigate class="text-primary hover:underline">
                        {{ $paiement->location->client->nom }} {{ $paiement->location->client->prenom }} - 
                        {{ $paiement->location->voiture->marque }} {{ $paiement->location->voiture->modele }}
                        ({{ \Carbon\Carbon::parse($paiement->location->date_debut)->format('d/m/Y') }})
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Date du paiement</flux:label>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Montant</flux:label>
                    <div class="font-medium">{{ number_format($paiement->montant, 2) }} €</div>
                </div>
            </div>

            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Mode de paiement</flux:label>
                <div class="font-medium">
                    <flux:badge variant="neutral" inset="left">
                        {{ ucfirst($paiement->mode_paiement) }}
                    </flux:badge>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
