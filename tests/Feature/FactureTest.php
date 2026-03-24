<?php
namespace Tests\Feature;

use App\Models\Client;
use App\Models\Location;
use App\Models\User;
use App\Models\Voiture;
use App\Models\Facture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_facture()
    {
        $user = User::factory()->create();
        $client = Client::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john@example.com',
            'telephone' => '0123456789',
            'adresse' => 'Paris',
            'type' => 'particulier'
        ]);

        $voiture = Voiture::create([
            'marque' => 'Toyota',
            'modele' => 'Corolla',
            'immatriculation' => 'AB-123-CD',
            'couleur' => 'Rouge',
            'prix_journalier' => 50,
            'statut' => 'disponible'
        ]);

        $location = Location::create([
            'client_id' => $client->id,
            'date_debut' => now()->format('Y-m-d'),
            'date_fin' => now()->addDays(2)->format('Y-m-d'),
            'tarif_total' => 100,
            'statut' => 'en cours',
            'avec_chauffeur' => false
        ]);
        $location->voitures()->attach($voiture->id);

        $response = $this->actingAs($user)->post(route('factures.store'), [
            'location_id' => $location->id,
            'date_facture' => now()->format('Y-m-d'),
            'statut' => 'en attente',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('factures', [
            'location_id' => $location->id,
            'statut' => 'en attente',
        ]);

        $facture = Facture::first();
        $this->assertNotNull($facture->numero_facture);
        $this->assertEquals(100, $facture->montant_total);
    }
}
