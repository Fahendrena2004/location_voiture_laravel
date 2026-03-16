<x-layouts::app :title="__('Facture') . ' ' . $facture->numero_facture">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-4xl mx-auto">
        <div class="flex items-center justify-between no-print mb-4">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('factures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Facture {{ $facture->numero_facture }}</flux:heading>
            </div>
            <flux:button onclick="window.print()" icon="printer" variant="primary">Imprimer</flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-8 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm print:shadow-none print:border-none">
            <!-- Header -->
            <div class="flex justify-between items-start mb-12">
                <div>
                    <flux:heading size="lg" class="text-primary">{{ config('app.name') }}</flux:heading>
                    <flux:text size="sm">123 Rue de la Location</flux:text>
                    <flux:text size="sm">75000 Paris, France</flux:text>
                    <flux:text size="sm">contact@monapplication.fr</flux:text>
                </div>
                <div class="text-right">
                    <flux:heading size="xl" class="uppercase">Facture</flux:heading>
                    <flux:text class="font-medium">{{ $facture->numero_facture }}</flux:text>
                    <flux:text size="sm">Date : {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}</flux:text>
                </div>
            </div>

            <!-- Details -->
            <div class="grid grid-cols-2 gap-12 mb-12">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500 mb-1">Facturé à :</flux:label>
                    <div class="font-bold text-lg">{{ $facture->location->client->nom }} {{ $facture->location->client->prenom }}</div>
                    <flux:text size="sm">{{ $facture->location->client->adresse }}</flux:text>
                    <flux:text size="sm">Tél : {{ $facture->location->client->telephone }}</flux:text>
                    <flux:text size="sm">{{ $facture->location->client->email }}</flux:text>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500 mb-1">Information Location :</flux:label>
                    <flux:text size="sm"><span class="font-medium">Véhicule :</span> {{ $facture->location->voiture->marque }} {{ $facture->location->voiture->modele }} ({{ $facture->location->voiture->immatriculation }})</flux:text>
                    <flux:text size="sm"><span class="font-medium">Période :</span> du {{ \Carbon\Carbon::parse($facture->location->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($facture->location->date_fin)->format('d/m/Y') }}</flux:text>
                </div>
            </div>

            <!-- Table -->
            <table class="w-full mb-12">
                <thead>
                    <tr class="border-b-2 border-neutral-100 dark:border-neutral-700">
                        <th class="py-3 text-left">Description</th>
                        <th class="py-3 text-right">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    <tr>
                        <td class="py-4">
                            <div class="font-medium">Location de véhicule</div>
                            <div class="text-xs text-neutral-500">{{ $facture->location->voiture->marque }} {{ $facture->location->voiture->modele }}</div>
                        </td>
                        <td class="py-4 text-right">{{ number_format($facture->montant_total, 2) }} €</td>
                    </tr>
                </tbody>
            </table>

            <!-- Summary -->
            <div class="flex justify-end">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between border-t border-neutral-200 dark:border-neutral-700 pt-2">
                        <span class="font-bold uppercase">Total TTC</span>
                        <span class="font-bold text-lg">{{ number_format($facture->montant_total, 2) }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-neutral-500">Statut</span>
                        <flux:badge variant="{{ $facture->statut === 'payée' ? 'success' : 'warning' }}" inset="left">
                            {{ ucfirst($facture->statut) }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-24 pt-8 border-t border-neutral-100 dark:border-neutral-700 text-center text-xs text-neutral-500">
                Merci de votre confiance !
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print\:shadow-none { shadow: none !important; }
            .print\:border-none { border: none !important; }
        }
    </style>
</x-layouts::app>
