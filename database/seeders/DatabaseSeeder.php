<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Voiture;
use App\Models\Chauffeur;
use App\Models\Location;
use App\Models\Paiement;
use App\Models\Facture;
use App\Models\Entretien;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $clientUser = User::firstOrCreate(
            ['email' => 'client@test.com'],
            ['name' => 'Client User', 'password' => bcrypt('password'), 'role' => 'client']
        );

        // 2. Create Clients
        $client1 = Client::firstOrCreate(
            ['telephone' => '034 11 111 11'],
            [
                'user_id' => $clientUser->id,
                'type' => 'personne',
                'nom' => 'Doe',
                'prenom' => 'John',
                'date_naissance' => '1990-01-01',
                'cin' => '123456789012',
                'adresse' => 'Lot IIM 34, Antananarivo'
            ]
        );

        $client2 = Client::firstOrCreate(
            ['telephone' => '032 22 222 22'],
            [
                'type' => 'association',
                'raison_sociale' => 'TechCorp Madagascar',
                'nif' => '123456789',
                'stat' => '987654321',
                'adresse' => 'Immeuble ARO, Ankorondrano'
            ]
        );

        // 3. Ensure Voitures exist (they might already from the previous prompt, but just in case)
        $voiture1 = Voiture::firstOrCreate(
            ['immatriculation' => 'AB-123-CD'],
            ['marque' => 'Toyota', 'modele' => 'Corolla', 'couleur' => 'Blanc', 'nombre_places' => 5, 'prix_journalier' => 45, 'statut' => 'disponible']
        );
        $voiture2 = Voiture::firstOrCreate(
            ['immatriculation' => 'EF-456-GH'],
            ['marque' => 'Peugeot', 'modele' => '208', 'couleur' => 'Rouge', 'nombre_places' => 5, 'prix_journalier' => 38, 'statut' => 'disponible']
        );

        // 4. Ensure Chauffeurs exist
        $chauffeur1 = Chauffeur::firstOrCreate(
            ['telephone' => '034 00 000 01'],
            ['nom' => 'Rakoto', 'prenom' => 'Jean', 'categorie_permis' => 'B', 'disponible' => true]
        );

        // 5. Create Locations
        $location1 = Location::create([
            'client_id' => $client1->id,
            'date_debut' => Carbon::now()->subDays(5),
            'date_fin' => Carbon::now()->subDays(2),
            'date_retour' => Carbon::now()->subDays(2),
            'avec_chauffeur' => true,
            'tarif_total' => (45 + 20) * 4, // 4 days * (Voiture 45 + Chauffeur 20)
            'statut' => 'terminée'
        ]);

        // Attach many-to-many
        $location1->voitures()->attach($voiture1->id);
        $location1->chauffeurs()->attach($chauffeur1->id);

        $location2 = Location::create([
            'client_id' => $client2->id,
            'date_debut' => Carbon::now()->addDays(1),
            'date_fin' => Carbon::now()->addDays(5),
            'avec_chauffeur' => false,
            'tarif_total' => 38 * 4, // 4 days * Voiture 38
            'statut' => 'en cours'
        ]);
        $location2->voitures()->attach($voiture2->id);

        // 6. Create Paiements
        Paiement::create([
            'location_id' => $location1->id,
            'montant' => $location1->tarif_total,
            'date_paiement' => Carbon::now()->subDays(5),
            'mode_paiement' => 'espèces'
        ]);

        Paiement::create([
            'location_id' => $location2->id,
            'montant' => $location2->tarif_total / 2, // 50% advance
            'date_paiement' => Carbon::now(),
            'mode_paiement' => 'mobile_money',
            'numero_mobile' => '034 99 999 99'
        ]);

        // 7. Create Factures
        Facture::create([
            'location_id' => $location1->id,
            'numero_facture' => 'FACT-' . Carbon::now()->format('Ymd') . '-0001',
            'date_facture' => Carbon::now()->subDays(2),
            'montant_total' => $location1->tarif_total,
            'statut' => 'payée'
        ]);

        // 8. Create Entretiens
        Entretien::create([
            'voiture_id' => $voiture1->id,
            'date_entretien' => Carbon::now()->subMonths(1),
            'description' => 'Vidange complète et changement de filtres',
            'cout' => 50.00
        ]);
    }
}
