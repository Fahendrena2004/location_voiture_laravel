<div
    class="bg-white dark:bg-neutral-800 p-8 rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-sm print:shadow-none print:border-none">
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
            <div class="font-bold text-lg">{{ $facture->location->client->nom }}
                {{ $facture->location->client->prenom }}
            </div>
            <flux:text size="sm">{{ $facture->location->client->adresse ?? 'Adresse non renseignée' }}</flux:text>
            <flux:text size="sm">Tél : {{ $facture->location->client->telephone }}</flux:text>
            <flux:text size="sm">{{ $facture->location->client->email }}</flux:text>
        </div>
        <div>
            <flux:label class="text-xs uppercase text-neutral-500 mb-1">Information Location :</flux:label>
            <flux:text size="sm"><span class="font-medium">Véhicule(s) :</span> {{ Str::limit($facture->location->voitures->pluck('marque')->join(', '), 40) }}
            </flux:text>
            <flux:text size="sm"><span class="font-medium">Période :</span> du
                {{ \Carbon\Carbon::parse($facture->location->date_debut)->format('d/m/Y') }} au
                {{ \Carbon\Carbon::parse($facture->location->date_fin)->format('d/m/Y') }}
            </flux:text>
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
            @php $jours = \Carbon\Carbon::parse($facture->location->date_debut)->diffInDays(\Carbon\Carbon::parse($facture->location->date_fin)) ?: 1; @endphp
            @foreach($facture->location->voitures as $voiture)
            <tr>
                <td class="py-4">
                    <div class="font-medium">Location de véhicule</div>
                    <div class="text-xs text-neutral-500">{{ $voiture->marque }}
                        {{ $voiture->modele }}
                    </div>
                </td>
                <td class="py-4 text-right font-medium">
                    {{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier * $jours) }}
                </td>
            </tr>
            @endforeach
            @foreach($facture->location->chauffeurs as $chauffeur)
            <tr>
                <td class="py-4">
                    <div class="font-medium">Prestation Chauffeur</div>
                    <div class="text-xs text-neutral-500">{{ $chauffeur->nom }} {{ $chauffeur->prenom }}</div>
                </td>
                <td class="py-4 text-right font-medium">
                    {{ \App\Helpers\CurrencyHelper::format(20 * $jours) }}
                </td>
            </tr>
            @endforeach
            @if(($facture->location->penalite ?? 0) > 0)
                <tr>
                    <td class="py-4">
                        <div class="font-medium text-red-600 dark:text-red-400">Pénalité de retard</div>
                        <div class="text-xs text-neutral-500">Pour dépassement de la date de retour prévue</div>
                    </td>
                    <td class="py-4 text-right text-red-600 dark:text-red-400 font-medium">+
                        {{ \App\Helpers\CurrencyHelper::format($facture->location->penalite) }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary -->
    <div class="flex justify-end">
        <div class="w-64 space-y-2">
            <div class="flex justify-between border-t border-neutral-200 dark:border-neutral-700 pt-2">
                <span class="font-bold uppercase">Total TTC</span>
                <span
                    class="font-bold text-lg text-primary">{{ \App\Helpers\CurrencyHelper::format($facture->montant_total) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-neutral-500">Statut</span>
                <flux:badge variant="{{ $facture->statut === 'payée' ? 'success' : 'warning' }}" inset="left">
                    {{ ucfirst($facture->statut) }}
                </flux:badge>
            </div>
        </div>
    </div>

    @if($facture->statut === 'payée' && $facture->location->paiements && $facture->location->paiements->count() > 0)
        <div class="mb-12">
            <flux:label class="text-xs uppercase text-neutral-500 mb-2">Historique des Paiements</flux:label>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    @foreach($facture->location->paiements as $paiement)
                        <tr>
                            <td class="py-2 text-neutral-600 dark:text-neutral-400 w-32">
                                {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                            <td class="py-2 text-neutral-600 dark:text-neutral-400">
                                {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                                @if($paiement->mode_paiement === 'mobile_money' && $paiement->numero_mobile)
                                    <span class="text-xs opacity-75 ml-1">(N° {{ $paiement->numero_mobile }})</span>
                                @elseif($paiement->mode_paiement === 'bancaire')
                                    <span class="text-xs opacity-75 ml-1">(Bord. {{ $paiement->numero_bordereau }} @if($paiement->nom_banque) - {{ $paiement->nom_banque }} @endif)</span>
                                @endif
                            </td>
                            <td class="py-2 text-right font-medium text-neutral-900 dark:text-white">
                                {{ \App\Helpers\CurrencyHelper::format($paiement->montant) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div class="mt-24 pt-8 border-t border-neutral-100 dark:border-neutral-700 text-center text-xs text-neutral-500">
        Merci de votre confiance !
    </div>
</div>