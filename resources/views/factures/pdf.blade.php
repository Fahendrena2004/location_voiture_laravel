<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture {{ $facture->numero_facture }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
        }

        .header table {
            width: 100%;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .company-info {
            text-align: right;
            font-size: 12px;
            color: #666;
        }

        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .details-container {
            width: 100%;
            margin-bottom: 30px;
        }

        .details-container table {
            width: 100%;
        }

        .details-container td {
            vertical-align: top;
            width: 50%;
        }

        .box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9fafb;
        }

        .box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
            color: #4b5563;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #d1d5db;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table .text-right {
            text-align: right;
        }

        .totals-container {
            width: 100%;
        }

        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
        }

        .totals-table .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #d1d5db;
            color: #111827;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }

        .status-payee {
            background-color: #10b981;
        }

        .status-attente {
            background-color: #f59e0b;
        }

        .status-annulee {
            background-color: #ef4444;
        }

        .clear {
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo">Location Voiture</div>
                </td>
                <td class="company-info">
                    <strong>Location Voiture Madagascar</strong><br>
                    Lot IIM 34, Antananarivo<br>
                    Tél : +261 34 00 000 00<br>
                    Email : contact@location.mg
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-title">
        FACTURE N° {{ $facture->numero_facture }}
        <span style="font-size: 14px; display: block; margin-top: 5px; color: #666; text-transform: none;">
            Date de facturation : {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}
        </span>
    </div>

    <div class="details-container">
        <table>
            <tr>
                <td style="padding-right: 10px;">
                    <div class="box">
                        <h3>Client</h3>
                        <strong>{{ $facture->location->client->type === 'association' ? $facture->location->client->raison_sociale : $facture->location->client->nom . ' ' . $facture->location->client->prenom }}</strong><br>
                        Adresse : {{ $facture->location->client->adresse ?? 'N/A' }}<br>
                        Tél : {{ $facture->location->client->telephone ?? 'Non renseigné' }}<br>
                        Email : {{ $facture->location->client->user->email ?? 'Non renseigné' }}<br>
                        @if($facture->location->client->type === 'association')
                            NIF : {{ $facture->location->client->nif }} | STAT : {{ $facture->location->client->stat }}
                        @else
                            CIN : {{ $facture->location->client->cin }}
                        @endif
                    </div>
                </td>
                <td style="padding-left: 10px;">
                    <div class="box">
                        <h3>Détails de la Location</h3>
                        <strong>Période :</strong>
                        {{ \Carbon\Carbon::parse($facture->location->date_debut)->format('d/m/Y') }}
                        au {{ \Carbon\Carbon::parse($facture->location->date_fin)->format('d/m/Y') }}<br>

                        @php
                            $start = \Carbon\Carbon::parse($facture->location->date_debut);
                            $end = \Carbon\Carbon::parse($facture->location->date_fin);
                            $days = ceil(abs($end->diffInDays($start))) + 1;
                        @endphp
                        <strong>Durée :</strong> {{ $days }} jour(s)<br>

                        <strong>Statut de la facture :</strong>
                        <span
                            class="status-badge status-{{ strtolower(str_replace('é', 'e', explode(' ', $facture->statut)[0])) }}">
                            {{ $facture->statut }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Prix Unitaire</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->location->voitures as $voiture)
                <tr>
                    <td>
                        <strong>Location de véhicule :</strong><br>
                        {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }})
                    </td>
                    <td class="text-right">{{ $days }} jour(s)</td>
                    <td class="text-right">{{ number_format($voiture->prix_journalier, 2, ',', ' ') }} €</td>
                    <td class="text-right">{{ number_format($voiture->prix_journalier * $days, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach

            @if($facture->location->avec_chauffeur)
                <tr>
                    <td>
                        <strong>Option : Chauffeur</strong><br>
                        @if($facture->location->chauffeurs->isNotEmpty())
                            {{ $facture->location->chauffeurs->count() }} chauffeur(s) assigné(s)
                        @endif
                    </td>
                    <td class="text-right">{{ $days }} jour(s)</td>
                    <td class="text-right">20,00 €</td>
                    @php
                        $nbChauffeurs = max(1, $facture->location->chauffeurs->count());
                    @endphp
                    <td class="text-right">{{ number_format(20 * $days * $nbChauffeurs, 2, ',', ' ') }} €</td>
                </tr>
            @endif

            @if($facture->location->statut === 'terminée' && $facture->location->date_retour)
                @php
                    $retour = \Carbon\Carbon::parse($facture->location->date_retour);
                    if ($retour->gt($end)) {
                        $lateDays = ceil(abs($retour->diffInDays($end)));
                        $penalite = $lateDays * 10;
                    } else {
                        $lateDays = 0;
                        $penalite = 0;
                    }
                @endphp
                @if($lateDays > 0)
                    <tr>
                        <td>
                            <strong>Pénalité de retard</strong><br>
                            Retour prévu le {{ $end->format('d/m/Y') }}, effectué le {{ $retour->format('d/m/Y') }}
                        </td>
                        <td class="text-right">{{ $lateDays }} jour(s)</td>
                        <td class="text-right">10,00 €</td>
                        <td class="text-right">{{ number_format($penalite, 2, ',', ' ') }} €</td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td>Sous-total :</td>
                <td class="text-right">{{ number_format($facture->montant_total, 2, ',', ' ') }} €</td>
            </tr>
            <tr>
                <td>TVA (0%) :</td>
                <td class="text-right">0,00 €</td>
            </tr>
            <tr class="total-row">
                <td>Total TTC :</td>
                <td class="text-right">{{ number_format($facture->montant_total, 2, ',', ' ') }} €</td>
            </tr>
            @php
                $dejaPaye = $facture->location->paiements->sum('montant');
            @endphp
            @if($dejaPaye > 0)
                <tr>
                    <td style="color: #10b981;">Montant Payé :</td>
                    <td class="text-right" style="color: #10b981;">- {{ number_format($dejaPaye, 2, ',', ' ') }} €</td>
                </tr>
                <tr class="total-row">
                    <td>Solde à payer :</td>
                    <td class="text-right">{{ number_format(max(0, $facture->montant_total - $dejaPaye), 2, ',', ' ') }} €
                    </td>
                </tr>
            @endif
        </table>

        @if(isset($comptesPaiement) && $comptesPaiement->count() > 0)
            <div style="float: left; width: 50%;">
                <strong style="text-transform: uppercase;">Modalités de Paiement</strong><br>
                @foreach($comptesPaiement as $compte)
                    {{ $compte->nom }} : <strong>{{ $compte->details }}</strong><br>
                @endforeach
            </div>
        @endif

        <div class="clear"></div>
    </div>

    <div class="footer">
        Merci pour votre confiance.<br>
        En cas de question concernant cette facture, merci de nous contacter.
    </div>
</body>

</html>