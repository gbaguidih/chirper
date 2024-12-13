<?php

namespace Tests\Feature;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_un_utilisateur_peut_creer_un_chirp(): void
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $response = $this->post('/chirps',['message'=> 'Le premier chirp de Hillary !']);
        $response = $this->post('/chirps',['message'=> 'Le deuxieme chirp de Hillary !']);

        $response->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Le premier chirp de Hillary !',
            'user_id' => $utilisateur->id,
        ]);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Le deuxieme chirp de Hillary !',
            'user_id' => $utilisateur->id
        ]);
    }

    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide():void {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        $response = $this->post('/chirps', [
            'message' =>''
        ]);
        $response->assertSessionHasErrors(['message']);
    }

    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres(){
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);
        $response = $this->post('/chirps', [
        'message' => str_repeat('a', 256)
        ]);
        $response->assertSessionHasErrors(['message']);
    }
    // public function test_les_chirps_sont_affiches_sur_la_page_d_accueil(){
    //     $chirps = Chirp::factory()->count(5)->create();
    //     $response = $this -> get('/chirps');
    //     foreach($chirps as $chirp ) {
    //         $response ->assertSee( $chirp->contenu);
    //     }
    // }
    public function test_un_utilisateur_peut_modifier_son_chirp(){
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);
        $response = $this->put("/chirps/{$chirp->id}", [
        'message' => 'Chirp modifié'
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'Chirp modifié',
        ]);
    }
    public function test_un_utilisateur_peut_supprimer_son_chirp(){
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);
        
        $response = $this->delete("/chirps/{$chirp->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('chirps', [
            'id' => $chirp->id,
        ]);
    }
    public function test_un_utilisateur_ne_peut_pas_modifier_ou_supprimer_le_chirp_d_un_autre_utilisateur(){
        $utilisateur1 = User::factory()->create();
        $utilisateur2 = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur1->id]);
        $response = $this->patch("/chirps/{$chirp->id}", [
            'message' => 'L\'utilisateur2 ne peut pas modifier'
            ]);
        $response = $this->delete("/chirps/{$chirp->id}");
        $response->assertStatus(302);

        $this ->actingAs($utilisateur2);
    }
    
    
    



}
