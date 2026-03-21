<div
    class="relative bg-white dark:bg-neutral-900 print:bg-white rounded-3xl print:rounded-none shadow-2xl print:shadow-none overflow-hidden border border-neutral-100 dark:border-neutral-800 print:border-none invoice-container">

    <!-- Bande décorative supérieure -->
    <div class="h-4 bg-gradient-to-r from-red-500 to-red-600 no-print"></div>

    <!-- Filigrane (Watermark) -->
    @if($facture->statut === 'payée')
        <div
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.03] dark:opacity-[0.05] print:opacity-10 pointer-events-none rotate-[-30deg] z-0">
            <span
                class="text-[10rem] font-black text-green-600 uppercase tracking-widest border-[12px] border-green-600 p-8 rounded-[3rem]">Payée</span>
        </div>
    @elseif($facture->statut === 'annulée')
        <div
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-[0.03] dark:opacity-[0.05] print:opacity-10 pointer-events-none rotate-[-30deg] z-0">
            <span
                class="text-[10rem] font-black text-red-600 uppercase tracking-widest border-[12px] border-red-600 p-8 rounded-[3rem]">Annulée</span>
        </div>
    @endif

    <div class="p-10 sm:p-14 print:p-0 relative z-10 print-text-black">
        <!-- En-tête -->
        <div
            class="flex flex-col sm:flex-row print:flex-row justify-between items-start mb-12 print:mb-4 gap-8 print:gap-2">
            <div class="flex items-start gap-5">
                <div
                    class="w-16 h-16 print:w-12 print:h-12 bg-red-600 text-white rounded-2xl flex items-center justify-center shadow-lg print:border print:border-red-600 print:text-red-600 print:bg-transparent print:shadow-none transform -rotate-3 transition-transform hover:rotate-0">
                    <flux:icon.document-text variant="solid" class="w-8 h-8 print:w-6 print:h-6" />
                </div>
                <div>
                    <h1
                        class="text-3xl print:text-2xl font-black text-neutral-900 dark:text-white print:text-black tracking-tight">
                        {{ config('app.name', 'AutoLocate') }}
                    </h1>
                    <p
                        class="text-sm print:text-xs font-bold text-red-600 dark:text-red-400 print:text-red-600 mt-1 uppercase tracking-widest">
                        Location sur mesure</p>
                    <div
                        class="mt-4 print:mt-1 text-sm print:text-xs text-neutral-500 print:text-black space-y-1 print:space-y-0 font-medium">
                        <p>123 Avenue de l'Indépendance</p>
                        <p>101 Antananarivo, Madagascar</p>
                        <p class="mt-2 print:mt-0 text-neutral-700 dark:text-neutral-400 print:text-black">+261 34 00
                            000 00</p>
                        <p class="text-neutral-700 dark:text-neutral-400 print:text-black">contact@autolocate.mg</p>
                    </div>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <h2
                    class="text-5xl print:text-3xl font-black text-neutral-200 dark:text-neutral-800 print:text-neutral-300 uppercase tracking-tighter mb-4 print:mb-2">
                    Facture</h2>
                <div
                    class="bg-neutral-50 dark:bg-neutral-800/50 print:bg-transparent print:border-neutral-300 inline-block text-left p-4 print:p-2 rounded-xl border border-neutral-100 dark:border-neutral-700/50 shadow-inner print:shadow-none">
                    <div class="flex justify-between gap-8 print:gap-4 mb-2 print:mb-1">
                        <span
                            class="text-xs font-bold text-neutral-400 print:text-neutral-500 uppercase tracking-wider">Numéro</span>
                        <span
                            class="text-sm font-bold text-neutral-900 dark:text-white print:text-black">{{ $facture->numero_facture }}</span>
                    </div>
                    <div class="flex justify-between gap-8 print:gap-4">
                        <span
                            class="text-xs font-bold text-neutral-400 print:text-neutral-500 uppercase tracking-wider">Date</span>
                        <span
                            class="text-sm font-bold text-neutral-900 dark:text-white print:text-black">{{ \Carbon\Carbon::parse($facture->date_facture)->format('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information du Client -->
        <div
            class="mb-10 print:mb-4 border-l-4 border-red-600 pl-6 print:pl-3 py-2 print:py-1 bg-gradient-to-r from-red-50 to-transparent dark:from-red-900/10 print:bg-none rounded-r-2xl border-solid break-inside-avoid">
            <h3
                class="text-xs font-bold uppercase tracking-widest text-red-600 dark:text-red-400 print:text-red-600 mb-2 print:mb-1">
                Facturé à</h3>
            <p class="font-black text-2xl print:text-xl text-neutral-900 dark:text-white print:text-black">
                {{ $facture->location->client->nom }} {{ $facture->location->client->prenom }}
            </p>
            <div
                class="text-sm print:text-xs text-neutral-600 dark:text-neutral-400 print:text-black mt-2 print:mt-1 space-y-1 print:space-y-0 font-medium">
                <p>{{ $facture->location->client->adresse ?? 'Adresse non renseignée' }}</p>
                <p>Tél : <span
                        class="text-neutral-900 dark:text-neutral-200 print:text-black">{{ $facture->location->client->telephone }}</span>
                </p>
                <p>Email : <span
                        class="text-neutral-900 dark:text-neutral-200 print:text-black">{{ $facture->location->client->email }}</span>
                </p>
            </div>
        </div>

        <!-- Résumé courte de la location -->
        <div
            class="mb-10 print:mb-4 bg-neutral-50 dark:bg-neutral-800/50 print:bg-transparent rounded-2xl p-6 print:p-2 flex flex-wrap gap-10 print:gap-4 border border-neutral-100 dark:border-neutral-800 print:border-neutral-300 break-inside-avoid">
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-wider text-neutral-400 print:text-neutral-500 mb-2 print:mb-1">
                    Véhicule(s)</p>
                <div class="flex flex-col gap-1">
                    @foreach($facture->location->voitures as $voiture)
                        <div>
                            <span class="text-sm print:text-xs font-bold text-neutral-900 dark:text-white print:text-black">
                                {{ $voiture->marque }} {{ $voiture->modele }}
                            </span>
                            <span
                                class="text-xs font-medium text-neutral-500 print:text-black mt-1 px-1 bg-neutral-200 dark:bg-neutral-700 print:bg-transparent print:border print:border-neutral-300 rounded inline-block">
                                {{ $voiture->immatriculation }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-wider text-neutral-400 print:text-neutral-500 mb-2 print:mb-1">
                    Période de location</p>
                <p
                    class="text-sm print:text-xs font-bold text-neutral-900 dark:text-white print:text-black bg-white dark:bg-neutral-800 print:bg-transparent border border-neutral-200 dark:border-neutral-700 print:border-none rounded-lg px-3 print:px-0 py-1 inline-block">
                    {{ \Carbon\Carbon::parse($facture->location->date_debut)->format('d/m/Y') }}
                    <span class="inline-block mx-2 text-neutral-400 font-normal">→</span>
                    {{ \Carbon\Carbon::parse($facture->location->date_fin)->format('d/m/Y') }}
                </p>
                @php $jours = \Carbon\Carbon::parse($facture->location->date_debut)->diffInDays(\Carbon\Carbon::parse($facture->location->date_fin)) ?: 1; @endphp
                <p class="text-xs font-medium text-neutral-500 print:text-black mt-2 print:mt-1">Durée : {{ $jours }}
                    jour(s)</p>
            </div>
            <div>
                <p
                    class="text-xs font-bold uppercase tracking-wider text-neutral-400 print:text-neutral-500 mb-2 print:mb-1">
                    Chauffeur</p>
                @if($facture->location->chauffeurs->isNotEmpty())
                    <p
                        class="text-sm print:text-xs font-bold text-neutral-900 dark:text-white print:text-black flex items-center gap-2 mb-1">
                        <span
                            class="w-2 h-2 rounded-full bg-green-500 print:border print:border-green-600 display-inline-block"></span>
                        Inclus ({{ $facture->location->chauffeurs->count() }})
                    </p>
                    <div class="space-y-1">
                        @foreach($facture->location->chauffeurs as $chauffeur)
                            <p class="text-xs font-medium text-neutral-500 print:text-black">
                                - {{ $chauffeur->nom }} {{ $chauffeur->prenom }}
                            </p>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm print:text-xs font-medium text-neutral-400 print:text-neutral-500">Non inclus</p>
                @endif
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="mb-10 print:mb-4 relative w-full">
            <table class="w-full text-left table-fixed">
                <thead>
                    <tr class="border-b-2 border-neutral-900 dark:border-white print:border-black">
                        <th
                            class="py-3 px-2 text-xs font-black uppercase tracking-widest text-neutral-900 dark:text-white print:text-black w-2/3">
                            Description</th>
                        <th
                            class="py-3 px-2 text-xs font-black uppercase tracking-widest text-neutral-900 dark:text-white print:text-black text-right w-1/6">
                            Durée</th>
                        <th
                            class="py-3 px-2 text-xs font-black uppercase tracking-widest text-neutral-900 dark:text-white print:text-black text-right w-1/6">
                            Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800 print:divide-neutral-300">
                    @php
                        $montantHt = $facture->montant_total - ($facture->location->penalite ?? 0);
                    @endphp
                    @foreach($facture->location->voitures as $voiture)
                        <tr
                            class="group hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors break-inside-avoid">
                            <td class="py-4 print:py-2 px-2">
                                <div
                                    class="font-bold text-neutral-900 dark:text-white print:text-black text-sm print:text-xs">
                                    Location {{ $voiture->marque }} {{ $voiture->modele }}</div>
                                <div class="text-xs font-medium text-neutral-500 print:text-neutral-600 mt-1">
                                    Mise à disposition du véhicule avec assurance incluse.</div>
                            </td>
                            <td
                                class="py-4 print:py-2 px-2 text-right text-sm font-bold text-neutral-600 dark:text-neutral-400 print:text-black">
                                {{ $jours }} jour(s)
                            </td>
                            <td
                                class="py-4 print:py-2 px-2 text-right font-black text-neutral-900 dark:text-white print:text-black whitespace-nowrap text-base print:text-sm">
                                {{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier * $jours) }}
                            </td>
                        </tr>
                    @endforeach
                    @foreach($facture->location->chauffeurs as $chauffeur)
                        <tr
                            class="group hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors break-inside-avoid">
                            <td class="py-4 print:py-2 px-2">
                                <div
                                    class="font-bold text-neutral-900 dark:text-white print:text-black text-sm print:text-xs">
                                    Prestation Chauffeur</div>
                                <div class="text-xs font-medium text-neutral-500 print:text-neutral-600 mt-1">
                                    {{ $chauffeur->nom }} {{ $chauffeur->prenom }}
                                </div>
                            </td>
                            <td
                                class="py-4 print:py-2 px-2 text-right text-sm font-bold text-neutral-600 dark:text-neutral-400 print:text-black">
                                {{ $jours }} jour(s)
                            </td>
                            <td
                                class="py-4 print:py-2 px-2 text-right font-black text-neutral-900 dark:text-white print:text-black whitespace-nowrap text-base print:text-sm">
                                {{ \App\Helpers\CurrencyHelper::format(20 * $jours) }}
                            </td>
                        </tr>
                    @endforeach
                    @if(($facture->location->penalite ?? 0) > 0)
                        <tr class="group hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors break-inside-avoid">
                            <td class="py-6 print:py-2 px-2">
                                <div
                                    class="font-bold text-red-600 dark:text-red-400 print:text-red-600 text-base print:text-sm">
                                    Pénalité de retard</div>
                                <div class="text-sm print:text-xs font-medium text-red-500/80 print:text-red-600 mt-1">
                                    Dépassement constaté par rapport à la date de retour prévue au contrat.</div>
                            </td>
                            <td class="py-6 print:py-2 px-2 text-right text-sm font-bold text-red-400 print:text-black">-
                            </td>
                            <td
                                class="py-6 print:py-2 px-2 text-right font-black text-red-600 dark:text-red-400 print:text-red-600 whitespace-nowrap text-lg print:text-base">
                                + {{ \App\Helpers\CurrencyHelper::format($facture->location->penalite) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Section Totale & Paiement -->
        <div
            class="flex flex-col md:flex-row print:flex-row justify-between items-end gap-10 print:gap-4 print:items-start break-inside-avoid">
            <!-- Modalités -->
            <div
                class="w-full md:w-1/2 print:w-1/2 p-6 print:p-0 rounded-2xl border-2 border-dashed border-neutral-200 dark:border-neutral-700 print:border-none bg-transparent">
                <p
                    class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white print:text-black mb-4 print:mb-2">
                    Modalités de Paiement</p>
                <div
                    class="text-sm print:text-xs font-medium text-neutral-600 dark:text-neutral-400 print:text-black space-y-3 print:space-y-1">
                    <div class="flex items-center gap-3 print:gap-2">
                        <span
                            class="w-8 h-8 print:w-6 print:h-6 rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-600 flex items-center justify-center font-bold text-xs print:bg-transparent print:border print:border-black print:text-black">BNI</span>
                        <p>IBAN : <strong class="text-neutral-900 dark:text-white print:text-black">MG00 0000 0000 0000
                                0000 00</strong></p>
                    </div>
                    <div class="flex items-center gap-3 print:gap-2">
                        <span
                            class="w-8 h-8 print:w-6 print:h-6 rounded-lg bg-green-100 dark:bg-green-900 text-green-600 flex items-center justify-center font-bold text-xs print:bg-transparent print:border print:border-black print:text-black">MV</span>
                        <p>MVola : <strong class="text-neutral-900 dark:text-white print:text-black">034 00 000
                                00</strong></p>
                    </div>
                </div>
            </div>

            <!-- Bloc Total Premium -->
            <div class="w-full md:w-1/2 print:w-1/2 max-w-sm ml-auto">
                <div
                    class="bg-neutral-900 dark:bg-black print:bg-transparent print:border-2 print:border-neutral-800 text-white print:text-black rounded-3xl print:rounded-xl p-8 print:p-4 shadow-2xl print:shadow-none relative overflow-hidden print:overflow-visible">
                    <!-- Accent rouge caché lors de l'impression -->
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-600/20 blur-3xl rounded-full no-print">
                    </div>

                    <div class="flex justify-between items-center mb-3 print:mb-2 relative z-10">
                        <span class="text-neutral-400 print:text-neutral-600 font-bold text-sm print:text-xs">Sous-total
                            HT</span>
                        <span class="font-bold text-lg print:text-sm">
                            {{ \App\Helpers\CurrencyHelper::format($facture->montant_total) }}
                        </span>
                    </div>
                    <div
                        class="flex justify-between items-center mb-4 print:mb-2 pb-4 print:pb-2 border-b border-neutral-700 print:border-neutral-300 relative z-10">
                        <span class="text-neutral-400 print:text-neutral-600 font-bold text-sm print:text-xs">TVA
                            (0%)</span>
                        <span class="font-bold text-lg print:text-sm">
                            {{ \App\Helpers\CurrencyHelper::format(0) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-end relative z-10">
                        <span
                            class="text-xl print:text-lg font-black text-neutral-100 print:text-black uppercase tracking-wider">Net
                            à payer</span>
                        <span class="text-3xl print:text-2xl font-black text-red-500 print:text-red-600">
                            {{ \App\Helpers\CurrencyHelper::format($facture->montant_total) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Paiements Reçus -->
        @if($facture->statut === 'payée' && $facture->location->paiements && $facture->location->paiements->count() > 0)
            <div
                class="mt-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl print:bg-transparent print:border-green-600 print:border break-inside-avoid">
                <p class="text-xs font-bold uppercase text-green-700 dark:text-green-400 print:text-green-600 mb-2">
                    Paiements Reçus</p>
                <div class="space-y-1">
                    @foreach($facture->location->paiements as $paiement)
                        <div class="flex justify-between text-sm text-green-800 dark:text-green-300 print:text-black">
                            <span>
                                <span
                                    class="opacity-75">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</span>
                                -
                                <strong>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</strong>
                                @if($paiement->mode_paiement === 'mobile_money' && $paiement->numero_mobile) <span
                                class="opacity-75">(N° {{ $paiement->numero_mobile }})</span> @endif
                                @if($paiement->mode_paiement === 'bancaire') <span class="opacity-75">(Bord.
                                    {{ $paiement->numero_bordereau }} @if($paiement->nom_banque) - {{ $paiement->nom_banque }}
                                @endif)</span> @endif
                            </span>
                            <span class="font-bold">{{ \App\Helpers\CurrencyHelper::format($paiement->montant) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pied de page -->
        <div
            class="mt-12 print:mt-6 pt-6 print:pt-4 border-t border-neutral-200 dark:border-neutral-800 print:border-neutral-300 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs font-bold text-neutral-400 print:text-black break-inside-avoid">
            <p>Le paiement est dû à réception. Pénalité de 10%/jour de retard.</p>
            <p
                class="text-neutral-900 dark:text-white print:text-black tracking-widest uppercase flex items-center gap-2">
                MERCI DE VOTRE CONFIANCE
            </p>
        </div>

    </div>
</div>