<?php

namespace Tests\Feature;

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
        // Simulez un utilisateur connecté avec actingAs
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        //Effectuez une requête POST à la route /chirps avec un contenu.
        $response = $this->post('/chirps',['message'=> 'Le premier chirp de Hillary !']);
        $response = $this->post('/chirps',['message'=> 'Le deuxieme chirp de Hillary !']);

        //Vérifiez que le "chirp" est enregistré en base de données.
        $response->assertStatus(302);
        $this->assertDatabaseHas('chirps', [
            'message' => 'Le premier chirp de Hillary !',
            'user_id' => $utilisateur->id,
            ]);
            $this->assertDatabaseHas('chirps', [
                'message' => 'Le deuxieme chirp de Hillary !',
                'user_id' => $utilisateur->id,
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
}
