<?php

namespace Tests\Feature;

use App\Models\Itineraire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ItineraireApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $itineraireData;

    public function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur pour l'authentification
        $this->user = User::factory()->create();

        // Données de test pour un itinéraire
        $this->itineraireData = [
            'titre' => 'Voyage en Provence',
            'categorie' => 'Nature',
            'duree' => '3 jours',
            'image_path' => 'images/provence.jpg',
        ];
    }

    /** @test */
    public function test_unauthenticated_users_cannot_access_itineraires_endpoints()
    {
        // Tentative d'accès sans authentification
        $response = $this->getJson('/api/itineraires');
        $response->assertStatus(401); // Non autorisé
    }

    /** @test */
    public function test_can_get_all_itineraires()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Créer plusieurs itinéraires
        Itineraire::factory()->count(3)->create();

        // Tester l'endpoint index
        $response = $this->getJson('/api/itineraires');
        
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function test_returns_appropriate_message_when_no_itineraires()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Tester l'endpoint index sans itinéraires
        $response = $this->getJson('/api/itineraires');
        
        $response->assertStatus(200);
        $response->assertJson(['message' => 'No record available']);
    }

    /** @test */
    public function test_can_create_itineraire()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Tester l'endpoint store
        $response = $this->postJson('/api/itineraires', $this->itineraireData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Itineraire Created Successfully'
        ]);
        
        // Vérifier que l'itinéraire a été créé en base de données
        $this->assertDatabaseHas('itineraires', [
            'titre' => 'Voyage en Provence',
            'categorie' => 'Nature'
        ]);
    }

    /** @test */
    public function test_validation_fails_when_creating_itineraire_with_missing_fields()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Données incomplètes
        $incompleteData = [
            'titre' => 'Voyage en Provence',
            // Categorie manquante
            'duree' => '3 jours',
            // Image path manquante
        ];

        // Tester l'endpoint store avec données incomplètes
        $response = $this->postJson('/api/itineraires', $incompleteData);
        
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['categorie', 'image_path']);
    }

    /** @test */
    public function test_can_show_single_itineraire()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Créer un itinéraire
        $itineraire = Itineraire::create($this->itineraireData);

        // Tester l'endpoint show
        $response = $this->getJson("/api/itineraires/{$itineraire->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $itineraire->id,
                'titre' => 'Voyage en Provence',
                'categorie' => 'Nature',
                'duree' => '3 jours',
                'image_path' => 'images/provence.jpg',
            ]
        ]);
    }

    /** @test */
    public function test_can_update_itineraire()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Créer un itinéraire
        $itineraire = Itineraire::create($this->itineraireData);

        // Données mises à jour
        $updatedData = [
            'titre' => 'Voyage en Corse',
            'categorie' => 'Aventure',
            'duree' => '5 jours',
            'image_path' => 'images/corse.jpg',
        ];

        // Tester l'endpoint update
        $response = $this->putJson("/api/itineraires/{$itineraire->id}", $updatedData);
        
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Itineraire Updated Successfully'
        ]);
        
        // Vérifier que l'itinéraire a été mis à jour en base de données
        $this->assertDatabaseHas('itineraires', [
            'id' => $itineraire->id,
            'titre' => 'Voyage en Corse',
            'categorie' => 'Aventure'
        ]);
    }

    /** @test */
    public function test_validation_fails_when_updating_itineraire_with_missing_fields()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Créer un itinéraire
        $itineraire = Itineraire::create($this->itineraireData);

        // Données incomplètes pour la mise à jour
        $incompleteData = [
            'titre' => 'Voyage en Corse',
            // Categorie manquante
            'duree' => '5 jours',
            // Image path manquante
        ];

        // Tester l'endpoint update avec données incomplètes
        $response = $this->putJson("/api/itineraires/{$itineraire->id}", $incompleteData);
        
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['categorie', 'image_path']);
    }

    /** @test */
    public function test_can_delete_itineraire()
    {
        // Authentifier l'utilisateur
        Sanctum::actingAs($this->user);

        // Créer un itinéraire
        $itineraire = Itineraire::create($this->itineraireData);

        // Tester l'endpoint destroy
        $response = $this->deleteJson("/api/itineraires/{$itineraire->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Itineraire deleted successfully'
        ]);
        
        // Vérifier que l'itinéraire a été supprimé de la base de données
        $this->assertDatabaseMissing('itineraires', ['id' => $itineraire->id]);
    }
}