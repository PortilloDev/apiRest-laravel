<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


use App\Post;
use App\User;

class PostControllerTest extends TestCase
{

    use RefreshDatabase;


    public function test_store()
    {
        //comando para ver los errores más especificamente
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json(
            'POST',
            '/api/posts',
            ['title' => 'El post de prueba']
        );

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => "El post de prueba"])
            ->assertStatus(201); //Ok, creado un recurso

        $this->assertDatabaseHas('posts', ['title' => "El post de prueba"]);
    }

    public function test_validate_title()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->json(
            'POST',
            '/api/posts',
            ['title' => '']
        );

        //Http 422 -> solicitud bien realizada, pero no completada
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_show()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json(
            'GET',
            "/api/posts/$post->id",
            ['title' => 'El post de prueba']
        );

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); // Ok,acceso
    }

    public function test_404_show()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json(
            'GET',
            '/api/posts/1000'
        );

        $response->assertStatus(404); // post no existe
    }

    public function test_update()
    {
        //comando para ver los errores más especificamente
        //$this->withoutExceptionHandling();

        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json(
            'PUT',
            "/api/posts/$post->id",
            ['title' => 'nuevo']
        );

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => "nuevo"])
            ->assertStatus(200); //Ok

        $this->assertDatabaseHas('posts', ['title' => "nuevo"]);
    }

    public function test_delete()
    {
        //comando para ver los errores más especificamente
        $this->withoutExceptionHandling();

        $post = factory(Post::class)->create();
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json(
            'DELETE',
            "/api/posts/$post->id"
        );

        $response->assertSee(null)
            ->assertStatus(204); //Sin contenido

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        //comando para ver los errores más especificamente
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        factory(Post::class, 5)->create();

        $response = $this->actingAs($user, 'api')->json(
            'GET',
            '/api/posts'
        );

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'created_at', 'updated_at']
            ]
        ])->assertStatus(200); //ok


    }

    public function test_guest()
    {
        $this->json('GET',     '/api/posts')->assertStatus(401); //Http 401: no autorizado
        $this->json('POST',    '/api/posts')->assertStatus(401); //Http 401: no autorizado
        $this->json('GET',     '/api/posts/1000')->assertStatus(401); //Http 401: no autorizado
        $this->json('PUT',     '/api/posts/1000')->assertStatus(401); //Http 401: no autorizado
        $this->json('DELETE',  '/api/posts/1000')->assertStatus(401); //Http 401: no autorizado

    }
}
